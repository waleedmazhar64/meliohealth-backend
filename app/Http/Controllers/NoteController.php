<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NoteController extends Controller
{
    public function index(Request $request) {
        $notes = Note::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get();
        return response()->json(['notes' => $notes]);
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'patient_name' => 'required|string',
            'dob' => 'required|date',
            'symptoms' => 'required|string',
            'evaluation' => 'required|string',
            'bp' => 'required|string',
            'oxygen' => 'required|string',
            'observation' => 'required|string',
        ]);
    
        $data['user_id'] = $request->user()->id;
        return Note::create($data);
    }

    public function AIstore(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);
    
        $note = new Note();
        $note->user_id = auth()->id();
        $note->ai_notes = $request->text;
        $note->save();
    
        return response()->json(['message' => 'Note saved successfully.']);
    }
    
    public function destroy($id) {
        Note::findOrFail($id)->delete();
        return response()->json(['message' => 'Note deleted']);
    }
    
    public function sendEmail(Request $request, $id) {
        $request->validate(['email' => 'required|email']);
    
        $note = Note::where('user_id', $request->user()->id)->findOrFail($id);
        Mail::raw("Patient Note:\n\n" . json_encode($note, JSON_PRETTY_PRINT), function ($message) use ($request) {
            $message->to($request->email)->subject('Patient Note from MelioHealth');
        });
    
        return response()->json(['message' => 'Email sent']);
    }

    public function download(Request $request, $id)
    {
        $note = Note::where('user_id', $request->user()->id)->findOrFail($id);
        $filename = 'note_' . $note->id . '.txt';

        $content = "Patient Name: {$note->patient_name}\nDOB: {$note->dob}\n\nSymptoms:\n{$note->symptoms}\n\nEvaluation:\n{$note->evaluation}\n\nBP: {$note->bp}\nOxygen: {$note->oxygen}\n\nObservations:\n{$note->observation}";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
