<?php

namespace App\Http\Controllers\Api\V1\Driver\Common;

use App\Http\Controllers\Controller;
use App\Models\ChatFaq;
use App\Models\DriverChatSession;
use App\Models\DriverMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatSupportController extends Controller
{
    // Fetch FAQs based on type
    public function getFAQs(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:driver,instructor',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }
        if ($request->type == 'driver') {
            // Fetch FAQs where type is 'users' or 'all'
            $faqs = ChatFaq::whereIn('type', ['drivers', 'all'])->select('question', 'answer')->latest()->get();
        } else {
            // Fetch FAQs where type is 'users' or 'all'
            $faqs = ChatFaq::whereIn('type', ['instructors', 'all'])->select('question', 'answer')->latest()->get();
        }
        return jsonResponseData(true, $faqs);
    }

    // Send message to support
    public function sendMessage(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:image|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'type' => 'required|in:driver,instructor',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the current user (assuming Auth is used)
        $user = auth('driver')->user();

        if ($request->type == 'driver') {
            // Check if a chat session already exists
            $chatSession = DriverChatSession::where('driver_id', $user->id)
                ->where('is_active', 1)
                ->latest()
                ->first();

            // If no active chat session exists, create a new one
            if (!$chatSession) {
                $chatSession = DriverChatSession::create([
                    'driver_id' => $user->id,
                    'is_active' => true,
                ]);
            }

            // Initialize the message payload
            $messagePayload = [
                'driver_chat_session_id' => $chatSession->id,
                'driver_id' => $user->id,
                'sender_type' => 'driver',
                'message' => $request->input('message', null),
            ];

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $imagePath = uploadMedia($request->file('image'), 'support_chat');
                $messagePayload['image'] = $imagePath;
            }

            // Create a new message in the chat session
            $message = DriverMessage::create($messagePayload);
        } else {
            // Check if a chat session already exists
            $chatSession = DriverChatSession::where('instructor_id', $user->id)
                ->where('is_active', 1)
                ->latest()
                ->first();

            // If no active chat session exists, create a new one
            if (!$chatSession) {
                $chatSession = DriverChatSession::create([
                    'instructor_id' => $user->id,
                    'is_active' => true,
                ]);
            }

            // Initialize the message payload
            $messagePayload = [
                'driver_chat_session_id' => $chatSession->id,
                'instructor_id' => $user->id,
                'sender_type' => 'driver',
                'message' => $request->input('message', null),
            ];

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $imagePath = uploadMedia($request->file('image'), 'support_chat');
                $messagePayload['image'] = $imagePath;
            }

            // Create a new message in the chat session
            $message = DriverMessage::create($messagePayload);
        }

        // Return a success response with the new message
        return jsonResponseWithData(true, 'Message sent successfully!', $message);
    }

    // Fetch last active chat session with its messages
    public function getLastActiveChatSession(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:driver,instructor',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }
        $user = auth('driver')->user();
        if ($request->type == 'driver') {
            // Get the current user (assuming Auth is used)

            // Fetch the last active chat session for the user
            $chatSession = DriverChatSession::where('driver_id', $user->id)
                ->where('is_active', 1)
                ->with('messages')  // Eager load the messages
                ->latest()
                ->first();
        } else {

            // Fetch the last active chat session for the user
            $chatSession = DriverChatSession::where('instructor_id', $user->id)
                ->where('is_active', 1)
                ->with('messages')  // Eager load the messages
                ->latest()
                ->first();
        }

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
