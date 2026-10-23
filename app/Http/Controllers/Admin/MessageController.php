<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages', ['messages' => ContactMessage::latest()->paginate(15)]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => $message->read_at ? null : now()]);

        return back();
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('status', 'Message deleted.');
    }
}
