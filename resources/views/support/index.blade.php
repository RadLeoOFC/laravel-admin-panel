<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="fw-bold mb-2" style="font-size: 28px; padding-bottom: 15px">Support Chat</h1>

    <!-- Ticket creation form -->
    @if(Auth::user()->role !== 'admin' && !isset($existingTicket))
        <form action="{{ route('support.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <textarea class="form-control" name="message" placeholder="Describe your issue..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Request</button>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

        </form>
    @elseif(Auth::user()->role === 'admin')
        <div class="alert alert-info">
            Manage messeges for support here.
        </div>
    @else
        <div class="alert alert-info">
            You already have an open support ticket. Continue the conversation below.
        </div>
    @endif

    <hr>

    <h2 class="fw-bold mb-2" style="font-size: 22px; margin: 10px;">Your Tickets</h2>
    @foreach($tickets as $ticket)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Ticket #{{ $ticket->id }} ({{ ucfirst($ticket->status) }})</span>
            @if(Auth::user()->role === 'admin')
                <form action="{{ route('support.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            @endif
        </div>
        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
            @foreach($ticket->messages as $message)
                <div class="mb-2 {{ $message->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">
                    <span class="badge bg-{{ $message->sender_id == auth()->id() ? 'primary' : 'secondary' }}">
                        {{ $message->sender->name }}:
                    </span> 
                    {{ $message->message }}
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <form action="{{ route('support.sendMessage', $ticket->id) }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                    <button type="submit" class="btn btn-success">Send</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

</div>
@endsection

</body>
</html>