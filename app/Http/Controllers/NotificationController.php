<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $user->notification_settings = $request->validate([
            'new_bid' => 'boolean',
            'item_sold' => 'boolean',
            'new_review' => 'boolean',
            'new_investment' => 'boolean',
        ]);
        $user->save();
        
        return $this->checkSuccess('Settings', );
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url']); 
    }
}