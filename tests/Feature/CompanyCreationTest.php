<?php

use App\Models\Admin;
use App\Models\Company;
use App\Models\Leader;
use App\Services\CnpjService;

beforeEach(function () {
    $this->admin = Admin::factory()->create(['status' => 'active', 'role' => 'super']);
});

test('CnpjService valida CNPJ com checksum correto', function () {
    expect(CnpjService::validate('33000167000101'))->toBeTrue();  // Petrobras
    expect(CnpjService::validate('11.222.333/0001-81'))->toBeTrue(); // aceita com mascara

    expect(CnpjService::validate('11111111111111'))->toBeFalse(); // todos iguais
    expect(CnpjService::validate('33000167000102'))->toBeFalse(); // Petrobras com DV2 trocado
    expect(CnpjService::validate('123'))->toBeFalse();            // tamanho errado
    expect(CnpjService::validate(''))->toBeFalse();
});

test('empresa nao pode ficar sem lider — bloqueia delete do ultimo', function () {
    $company = Company::factory()->create(['created_by_admin_id' => $this->admin->id]);
    $leader = Leader::factory()->create(['company_id' => $company->id]);

    expect($leader->canBeArchived())->toBeFalse();

    expect(fn () => $leader->delete())
        ->toThrow(\RuntimeException::class, 'precisa ter pelo menos um líder');

    expect(Leader::where('company_id', $company->id)->count())->toBe(1);
});

test('empresa com 2 lideres permite arquivar 1', function () {
    $company = Company::factory()->create(['created_by_admin_id' => $this->admin->id]);
    $leader1 = Leader::factory()->create(['company_id' => $company->id]);
    $leader2 = Leader::factory()->create(['company_id' => $company->id]);

    expect($leader1->canBeArchived())->toBeTrue();

    $leader1->delete();

    expect(Leader::where('company_id', $company->id)->count())->toBe(1);
    expect($leader2->fresh()->canBeArchived())->toBeFalse();
});

test('lider primary nao pode ser arquivado mesmo com outros lideres', function () {
    $company = Company::factory()->create(['created_by_admin_id' => $this->admin->id]);
    $primary = Leader::factory()->create(['company_id' => $company->id, 'is_primary' => true]);
    $secondary = Leader::factory()->create(['company_id' => $company->id, 'is_primary' => false]);

    expect($primary->canBeArchived())->toBeFalse();
    expect($secondary->canBeArchived())->toBeTrue();

    expect(fn () => $primary->delete())
        ->toThrow(\RuntimeException::class, 'Líderes principais não podem ser arquivados');

    expect(Leader::where('company_id', $company->id)->count())->toBe(2);
});

test('lider primary nao pode ter nome nem empresa alterados', function () {
    $company1 = Company::factory()->create(['created_by_admin_id' => $this->admin->id]);
    $company2 = Company::factory()->create(['created_by_admin_id' => $this->admin->id]);
    $primary = Leader::factory()->create([
        'company_id' => $company1->id,
        'is_primary' => true,
        'name'       => 'Nome Original',
    ]);

    expect(fn () => $primary->update(['name' => 'Outro Nome']))
        ->toThrow(\RuntimeException::class, 'Nome do líder principal');

    $primary->refresh(); // limpa state dirty da tentativa bloqueada

    expect(fn () => $primary->update(['company_id' => $company2->id]))
        ->toThrow(\RuntimeException::class, 'Empresa do líder principal');

    $primary->refresh();

    expect($primary->name)->toBe('Nome Original');
    expect($primary->company_id)->toBe($company1->id);

    // Campos editaveis funcionam normalmente
    $primary->update(['phone' => '11999999999', 'role_label' => 'Diretor']);
    expect($primary->fresh()->phone)->toBe('11999999999');
});
