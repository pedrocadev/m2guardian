<?php

namespace App\Filament\Widgets;

use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Leader;
use App\Models\TrainingSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Empresas Ativas', Company::where('status', 'active')->count())
                ->description('Total cadastradas: ' . Company::count())
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Empresas Pro', Company::where('license', 'pro')->where('status', 'active')->count())
                ->description('Demo: ' . Company::where('license', 'demo')->count())
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Líderes', Leader::count())
                ->description('Ativos: ' . Leader::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Treinamentos Completos', TrainingSession::whereNotNull('completed_at')->count())
                ->description('Colaboradores: ' . Collaborator::count())
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
