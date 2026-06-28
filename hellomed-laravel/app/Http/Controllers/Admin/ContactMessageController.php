<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::with('user')->latest()->get();
        return view('admin.contact_messages.index', compact('messages'));
    }
}
