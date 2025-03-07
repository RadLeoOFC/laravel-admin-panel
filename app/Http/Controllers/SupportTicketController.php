<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller {
    // View all tickets (user sees his own, admin sees all)
    public function index() {
        if (Auth::user()->role === 'admin') {
            $tickets = SupportTicket::with('user', 'messages')->get();
            $existingTicket = null; // Админу не нужен один тикет
        } else {
            $existingTicket = SupportTicket::where('user_id', Auth::id())
                ->where('status', 'open')
                ->with('messages')
                ->first();
            $tickets = $existingTicket ? [$existingTicket] : [];
        }
    
        return view('support.index', compact('tickets', 'existingTicket'));
    }    
    

    // Create a ticket
    public function store(Request $request) {
        $request->validate(['message' => 'required|string|max:2000']);
    
        // Проверяем, есть ли у пользователя уже открытый тикет
        $existingTicket = SupportTicket::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();
    
        if ($existingTicket) {
            return redirect()->back()->with('error', 'You already have an open support ticket.');
        }
    
        // Создаем новый тикет
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'status' => 'open',
            'message' => $request->message,
        ]);
    
        // Сохраняем первое сообщение в тикете
        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);
    
        return redirect()->back()->with('success', 'Support request created.');
    }    
    

    // Sending a message in a ticket
    public function sendMessage(Request $request, SupportTicket $ticket) {
        $request->validate(['message' => 'required|string|max:2000']);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back();
    }

    // Удаление тикета (только для администраторов)
    public function destroy(SupportTicket $ticket) {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can delete tickets.');
        }

        $ticket->messages()->delete(); // Удаляем все сообщения, связанные с тикетом
        $ticket->delete(); // Удаляем сам тикет

        return redirect()->back()->with('success', 'Support ticket deleted.');
    }

}


