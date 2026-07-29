<?php

namespace App\Http\Controllers;

use App\Mail\CollaboratorInviteMail;
use App\Models\Collaborator;
use App\Models\MagicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        $magicLinkUrl = MagicLink::generateUrlFor($collaborator, 'collaborator_training', expiresDays: 30);

        try {
            Mail::to($collaborator->email)
                ->send(new CollaboratorInviteMail($collaborator, $leader, $magicLinkUrl));

            return redirect()->route('leader.invite.index')
                ->with('success', "Convite enviado para {$collaborator->email}!");
        } catch (\Exception $e) {
            Log::error('Falha ao enviar convite de colaborador', [
                'collaborator_id' => $collaborator->id,
                'leader_id'       => $leader->id,
                'error'           => $e->getMessage(),
            ]);
            return redirect()->route('leader.invite.index')
                ->with('warning', 'Colaborador cadastrado, mas o envio do e-mail falhou. Use o botão "Copiar Link" para enviar o convite manualmente ou fale com o suporte M2 (suporte@m2cloud.com.br).');
        }
    }

    public function generateLink(Collaborator $collaborator)
    {
        $leader = Auth::guard('leader')->user();

        if ($collaborator->company_id !== $leader->company_id) {
            abort(403);
        }

        return response()->json([
            'url' => MagicLink::generateUrlFor($collaborator, 'collaborator_training', expiresDays: 30),
        ]);
    }

    public function resend(Collaborator $collaborator)
    {
        $leader = Auth::guard('leader')->user()->load('company');

        if ($collaborator->company_id !== $leader->company_id) {
            abort(403);
        }

        $magicLinkUrl = MagicLink::generateUrlFor($collaborator, 'collaborator_training', expiresDays: 30);

        try {
            Mail::to($collaborator->email)
                ->send(new CollaboratorInviteMail($collaborator, $leader, $magicLinkUrl));

            return back()->with('success', "Convite reenviado para {$collaborator->email}!");
        } catch (\Exception $e) {
            Log::error('Falha ao reenviar convite', [
                'collaborator_id' => $collaborator->id,
                'leader_id'       => $leader->id,
                'error'           => $e->getMessage(),
            ]);
            return back()->with('warning', 'Não foi possível reenviar o e-mail. Use "Copiar Link" para enviar manualmente ou fale com o suporte M2.');
        }
    }

    public function updateEmail(Request $request, Collaborator $collaborator)
    {
        $leader = Auth::guard('leader')->user();

        if ($collaborator->company_id !== $leader->company_id) {
            abort(403);
        }

        if ($collaborator->completed_at) {
            return back()->with('warning', 'Não é possível alterar o e-mail: colaborador já concluiu o treinamento.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:180'],
        ]);

        $duplicate = Collaborator::where('company_id', $leader->company_id)
            ->where('email', $validated['email'])
            ->where('id', '!=', $collaborator->id)
            ->exists();

        if ($duplicate) {
            return back()->with('warning', 'Este e-mail já está em uso por outro colaborador desta empresa.');
        }

        $oldEmail = $collaborator->email;
        $collaborator->update(['email' => $validated['email']]);

        return back()->with('success', "E-mail atualizado de {$oldEmail} para {$validated['email']}.");
    }
}
