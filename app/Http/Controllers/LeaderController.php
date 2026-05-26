<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderController extends Controller
{
    public function dashboard()
    {
        $leader = Auth::guard('leader')->user();
        $company = $leader->load('company.collaborators');

        $collaborators = $company->company->collaborators;
        $completed = $collaborators->whereNotNull('completed_at');
        $pending = $collaborators->whereNull('completed_at');

        $completionRate = $collaborators->count() > 0
            ? round(($completed->count() / $collaborators->count()) * 100)
            : 0;

        $avgScore = $completed->avg('score') ?? 0;

        return view('leader.dashboard', compact(
            'leader',
            'collaborators',
            'completed',
            'pending',
            'completionRate',
            'avgScore'
        ));
    }

    public function logout(Request $request)
    {
        Auth::guard('leader')->logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
