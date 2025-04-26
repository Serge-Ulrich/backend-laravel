<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class MessageController extends Controller
{
    // Envoyer un nouveau message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = Message::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès.',
            'data' => $message,
        ], 201);
    }

    // Récupérer les messages reçus par un utilisateur
    public function received($receiver_id)
    {
        $messages = Message::where('receiver_id', $receiver_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($messages);
    }

    // Récupérer les messages envoyés par un utilisateur
    public function sent($sender_id)
    {
        $messages = Message::where('sender_id', $sender_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($messages);
    }
}
