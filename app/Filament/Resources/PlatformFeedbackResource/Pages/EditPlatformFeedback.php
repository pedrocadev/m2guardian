<?php

namespace App\Filament\Resources\PlatformFeedbackResource\Pages;

use App\Filament\Resources\PlatformFeedbackResource;
use Filament\Resources\Pages\EditRecord;

class EditPlatformFeedback extends EditRecord
{
    protected static string $resource = PlatformFeedbackResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
