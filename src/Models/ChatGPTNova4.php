<?php

namespace Naif\Chatgpt\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class ChatGPTNova4 extends Eloquent
{
    protected $table = 'chatgpt_nova4';
    protected $fillable = [
        'question',
        'answer',
        'total_tokens',
    ];
}
