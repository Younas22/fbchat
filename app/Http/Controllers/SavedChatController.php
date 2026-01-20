<?php

namespace App\Http\Controllers;

use App\Models\SavedChat;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedChatController extends Controller
{
    /**
     * Conversation ko save karo notes ke saath
     */
    public function store(Request $request, $conversationId)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:5000'
            ]);

            $conversation = Conversation::find($conversationId);

            if (!$conversation || $conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            $savedChat = SavedChat::create([
                'conversation_id' => $conversationId,
                'user_id' => Auth::id(),
                'chat_id' => $conversation->conversation_id,
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat saved',
                'data' => $savedChat
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Saved chats dekho
     */
    public function index()
    {
        try {
            $savedChats = SavedChat::where('user_id', Auth::id())
                ->with(['conversation', 'user'])
                ->orderBy('saved_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $savedChats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Saved chat update karo
     */
    public function update(Request $request, $savedChatId)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:5000'
            ]);

            $savedChat = SavedChat::find($savedChatId);

            if (!$savedChat || $savedChat->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saved chat not found'
                ], 404);
            }

            $savedChat->update([
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat updated',
                'data' => $savedChat
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Saved chat delete karo
     */
    public function destroy($savedChatId)
    {
        try {
            $savedChat = SavedChat::find($savedChatId);

            if (!$savedChat || $savedChat->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saved chat not found'
                ], 404);
            }

            $savedChat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Saved chat deleted'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}