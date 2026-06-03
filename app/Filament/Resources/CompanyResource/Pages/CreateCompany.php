<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\Company;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_admin_id'] = auth('admin')->id();
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $leaderData = [
                'name'       => $data['leader_name'],
                'email'      => $data['leader_email'],
                'phone'      => $data['leader_phone'] ?? null,
                'role_label' => $data['leader_role'] ?? null,
                'status'     => 'pending',
                'is_primary' => true,
            ];

            unset(
                $data['leader_name'],
                $data['leader_email'],
                $data['leader_phone'],
                $data['leader_role'],
            );

            /** @var Company $company */
            $company = static::getModel()::create($data);

            $company->leaders()->create($leaderData);

            return $company;
        });
    }
}
