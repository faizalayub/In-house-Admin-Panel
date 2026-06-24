<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['service' => 'permission-gui-api', 'status' => 'ok']));
