<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class DebugController extends Controller
{
    public function index()
    {
        $debugTexts = \App\Models\DebugText::all();
        return response()->json($debugTexts);
    }
}
