<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ]);

        Mail::to('support@meliohealth.com')->send(new \App\Mail\SupportMessage($validated));

        return response()->json(['message' => 'Support request sent.']);
    }

}
