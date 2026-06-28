<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::with('user')->latest()->get();
        return view('staff.contact_messages.index', compact('messages'));
    }
}
