<?php

namespace App\Http\Controllers;

use App\Mail\CollaboratorInviteMail;
use App\Models\Collaborator;
use App\Models\MagicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeaderInviteController extends Controller
{
    public function index()
    {
        $leader = Auth::guard('leader')->user()->load('company.collaborators');
        $collaborators = $leader->company->collaborators()->orderByDesc('invited_at')->get();

        return view('leader.invite', compact('leader', 'collaborators'));
    }

    public function store(Request $request)
    {
        $leader = Auth::guard('leader')->user()->load('company');
        $company = $leader->company;

        $validated = $request->validate([
            'email'      => ['required', 'email', 'max:180'],
            'name'       => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:80'],
            'profile'    => ['nullable', 'in:rh,financeiro,operacao,outro'],
        ]);

        // Verifica se já existe na empresa
        $existing = Collaborator::where('company_id', $company->id)
            ->where('email', $validated['email'])
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Este e-mail já foi convidado para esta empresa.']);
        }

        $collaborator = Collaborator::create([
            'company_id'          => $company->id,
            'invited_by_leader_id' => $leader->id,
            'email'               => $validated['email'],
            'name'                => $validated['name'] ?? null,
            'department'          => $validated['department'] ?? null,
            'profile'             => $validated['profile'] ?? 'outro',
            'invited_at'          => now(),
        ]);

        ['plain_token' => $plainToken] = MagicLink::generateFor(
            $collaborator,
            'collaborator_training',
            expiresDays: 30
        );

        $magicLinkUrl = url('/auth/acesso') . '?t=' . $plainToken;

        try {
            Mail::to($collaborator->email)
                ->send(new CollaboratorInviteMail($collaborator, $leader, $magicLinkUrl));

            return redirect()->route('leader.invite.index')
                ->with('success', "Convite enviado para {$collaborator->email}!");
        } catch (\Exception $e) {
            return redirect()->route('leader.invite.index')
                ->with('warning', "Colaborador cadastrado, mas o e-mail falhou: {$e->getMessage()}");
        }
    }

    public function generateLink(Collaborator $collaborator)
    {
        $leader = Auth::guard('leader')->user();

        if ($collaborator->company_id !== $leader->company_id) {
            abort(403);
        }

        ['plain_token' => $plainToken] = MagicLink::generateFor(
            $collaborator,
            'collaborator_training',
            expiresDays: 30
        );

        return response()->json([
            'url' => url('/auth/acesso') . '?t=' . $plainToken,
        ]);
    }

    public function resend(Collaborator $collaborator)
    {
        $leader = Auth::guard('leader')->user()->load('company');

        // Garante que o colaborador pertence à empresa do líder
        if ($collaborator->company_id !== $leader->company_id) {
            abort(403);
        }

        ['plain_token' => $plainToken] = MagicLink::generateFor(
            $collaborator,
            'collaborator_training',
            expiresDays: 30
        );

        $magicLinkUrl = url('/auth/acesso') . '?t=' . $plainToken;

        try {
            Mail::to($collaborator->email)
                ->send(new CollaboratorInviteMail($collaborator, $leader, $magicLinkUrl));

            return back()->with('success', "Convite reenviado para {$collaborator->email}!");
        } catch (\Exception $e) {
            return back()->with('warning', "Erro ao reenviar: {$e->getMessage()}");
        }
    }
}
