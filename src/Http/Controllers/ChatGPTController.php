<?php

namespace Naif\Chatgpt\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Http\Requests\NovaRequest;
use Naif\Chatgpt\Models\ChatGPTNova4;

class ChatGPTController extends Controller
{
    public function ask(NovaRequest $request)
    {
        try {
            // Check if API key exists in the configuration
            if (!Config::get('chatgpt-nova4.chatgpt_api_key')) {
                throw new Exception('ChatGPT API Key not found!', 404);
            }
    
            // Make the API call
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . Config::get('chatgpt-nova4.chatgpt_api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    "model" => "gpt-3.5-turbo",
                    "messages" => [
                        [
                            "role" => "system",
                            "content" => "You are a helpful assistant."
                        ],
                        [
                            "role" => "user",
                            "content" => $request->question,
                        ],
                    ],
                    "max_tokens" => (int)Config::get('chatgpt-nova4.max_tokens'),
                ]);
    
            // Check if the response was successful
            if ($response->successful()) {
                $responseData = $response->json();
    
                // Extract the content from the response
                $answer = $responseData['choices'][0]['message']['content'] ?? null;
                $total_tokens = $responseData['usage']['total_tokens'] ?? 0;
    
                // Save the question and answer to the database
                ChatGPTNova4::create([
                    'question' => $request->question,
                    'answer' => $answer,
                    'total_tokens' => (int)$total_tokens,
                ]);
    
                // Return the response
                return response()->json([
                    'answer' => $answer,
                    'total_tokens' => $total_tokens
                ]);
            } elseif ($response->json('error')) {
                // Handle API errors
                return response()->json([
                    'api_response_error' => $response->json('error.message'),
                ]);
            } else {
                // Handle unexpected response structure
                throw new Exception('Unexpected API response structure.');
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'exception' => true,
                'exception_message' => $e->getMessage()
            ]);
        }
    }
    
    public function history()
    {
        $history = ChatGPTNova4::orderByDesc('id')->paginate();

        return response()->json([
            'history' => $history,
        ]);
    }

    public function view($id)
    {
        $record = ChatGPTNova4::findOrFail($id);

        return response()->json([
            'record' => $record,
        ]);
    }

    public function delete(NovaRequest $request)
    {
        $delete = ChatGPTNova4::where('id', $request->id)->delete();
        return response()->json($delete);
    }

}
