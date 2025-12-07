<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('articles', ArticleController::class);
});
