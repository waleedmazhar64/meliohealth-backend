<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        $user = $request->user();

        if ($user->profile_image) {
            Storage::disk('public')->delete('profile_images/' . $user->profile_image);
        }

        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->storeAs('profile_images', $filename, 'public');

        $user->update(['profile_image' => $filename]);

        return response()->json([
            'message' => 'Profile image updated',
            'image_url' => asset('storage/profile_images/' . $filename)
        ]);
    }
    
    public function show(Request $request) {
        return $request->user();
    }
    
    public function update(Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email'
        ]);
    
        $request->user()->update($data);
        return response()->json(['message' => 'Profile updated']);
    }
    
    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);
    
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Incorrect current password'], 403);
        }
    
        $request->user()->update([
            'password' => Hash::make($request->new_password)
        ]);
    
        return response()->json(['message' => 'Password changed successfully']);
    }

    public function getSubscription(Request $request) {
        return $request->user()->subscription;
      }
      
      public function updateSubscription(Request $request) {
        $user = $request->user();
        $plan = $request->plan;

        if (empty($plan)) {
            // Cancel subscription
            $user->subscription()?->delete();
            return response()->json(['message' => 'Subscription cancelled']);
        }

        $request->validate([
            'plan' => 'in:basic,premium'
        ]);

        $user->subscription()->updateOrCreate([], [
            'plan' => $plan,
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Subscription updated']);
      }

      public function getCard(Request $request) {
        return response()->json(['cards' => $request->user()->cards]);
      }
      
      public function storeCard(Request $request) {
        $data = $request->validate([
          'cardholder_name' => 'required|string',
          'card_number' => 'required|string',
          'expiry_date' => 'required|string',
          'cvv' => 'required|string',
        ]);
      
        $request->user()->cards()->create($data);
      
        return response()->json(['message' => 'Card saved']);
      }

      public function setActiveCard(Request $request, $id)
      {
          $user = $request->user();

          // deactivate all user's cards
          $user->cards()->update(['is_active' => false]);

          // activate selected card
          $card = $user->cards()->where('id', $id)->firstOrFail();
          $card->is_active = true;
          $card->save();

          return response()->json(['message' => 'Active card updated']);
      }

}
