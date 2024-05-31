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
            if (!Config::get('chatgpt-nova4.chatgpt_api_key')) {
                throw new Exception('ChatGPT API Key not found!', '404');
            } else {
                $response = Http::withoutVerifying()
                    ->withOptions([
                        'proxy' => Config::get('chatgpt-nova4.chatgpt_api_key'),
                    ])
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . Config::get('chatgpt-nova4.chatgpt_api_key'),
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'user','content' => $request->question],
                        ],
                        "max_tokens" => (int)Config::get('chatgpt-nova4.max_tokens'),
                    ]);

                if (isset($response->json()['choices'][0]['message']['content'])) {

                    $answer = $response->json()['choices'][0]['message']['content'];
                    $total_tokens = $response->json()['usage']['total_tokens'];

                    ChatGPTNova4::create([
                        'question' => $request->question,
                        'answer' => $answer,
                        'total_tokens' => (int)$total_tokens,
                    ]);

                    return response()->json([
                        'answer' => $answer,
                        'total_tokens' => $total_tokens
                    ]);
                } else if (isset($response->json()['error'])) {
                    return response()->json([
                        'api_response_error' => $response->json()['error']['message'],
                    ]);
                }
            }
        } catch (\Exception $e) {
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
