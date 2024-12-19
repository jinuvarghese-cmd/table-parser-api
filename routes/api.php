<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableParserController;

// Define the route for the TableParserController to handle POST requests
Route::post('/parse-tables', [TableParserController::class, 'parse']);
