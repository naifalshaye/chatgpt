<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\NovaRequest;
use Naif\Chatgpt\Http\Controllers\ChatGPTController;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/', function (NovaRequest $request) {
    return inertia('Chatgpt');
});

Route::get('/view-questions-history', function (NovaRequest $request) {
    return inertia('History');
});

Route::get('/view-questions-history/view/{id}', function (NovaRequest $request) {
    return Inertia::render('View',with(['id'=>$request->id]));
});
