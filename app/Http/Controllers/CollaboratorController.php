<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaboratorController extends Controller
{
    public function index()
    {
        $collaborator = Auth::guard('collaborator')->user();
        $collaborator->load('company');

        return view('training.index', compact('collaborator'));
    }

    public function show($scenario)
    {
        // Fase 4
        abort(404);
    }

    public function answer(Request $request)
    {
        // Fase 4
        abort(404);
    }

    public function completed()
    {
        $collaborator = Auth::guard('collaborator')->user();
        return view('training.completed', compact('collaborator'));
    }

    public function logout(Request $request)
    {
        Auth::guard('collaborator')->logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
