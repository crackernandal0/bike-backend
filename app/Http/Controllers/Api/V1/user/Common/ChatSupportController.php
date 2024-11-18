<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Models\ChatFaq;
use App\Models\ChatSession;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatSupportController extends Controller
{
    // Fetch FAQs based on type
    public function getFAQs()
    {
        // Fetch FAQs where type is 'users' or 'all'
        $faqs = ChatFaq::whereIn('type', ['users', 'all'])->select('question', 'answer')->latest()->get();

        return jsonResponseData(true, $faqs);
    }

    // Send message to support
    public function sendMessage(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:image|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the current user (assuming Auth is used)
        $user = auth()->user();

        // Check if a chat session already exists
        $chatSession = ChatSession::where('user_id', $user->id)
            ->where('is_active', 1)
            ->latest()
            ->first();

        // If no active chat session exists, create a new one
        if (!$chatSession) {
            $chatSession = ChatSession::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        }

        // Initialize the message payload
        $messagePayload = [
            'chat_session_id' => $chatSession->id,
            'user_id' => $user->id,
            'sender_type' => 'user',
            'message' => $request->input('message', null),
        ];

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = uploadMedia($request->file('image'), 'support_chat');
            $messagePayload['image'] = $imagePath;
        }

        // Create a new message in the chat session
        $message = Message::create($messagePayload);

        // Return a success response with the new message
        return jsonResponseWithData(true, 'Message sent successfully!', $message);
    }

    // Fetch last active chat session with its messages
    public function getLastActiveChatSession()
    {
        // Get the current user (assuming Auth is used)
        $user = auth()->user();

        // Fetch the last active chat session for the user
        $chatSession = ChatSession::where('user_id', $user->id)
            ->where('is_active', 1)
            ->with('messages')  // Eager load the messages
            ->latest()
            ->first();

        // If no active chat session exists, return a message
        if (!$chatSession) {
            return jsonResponse(false, 'No active chat session found');
        }

        // Return the chat session along with its messages
        return jsonResponseWithData(true, 'Last active chat session found', [
            'chat_session' => $chatSession
        ]);
    }
}
