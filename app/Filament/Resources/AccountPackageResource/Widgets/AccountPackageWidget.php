<?php

namespace App\Filament\Resources\AccountPackageResource\Widgets;


use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use Illuminate\Database\Eloquent\Model;
class AccountPackageWidget extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        return [
            Stat::make(__('Pakket :erp_id', [ 'erp_id' => $this->record->erp_id ]), "{$this->record->edition} {$this->record->year}")
                ->description(
                    __(':from t/m :until' ,
                        [
                            'from' => Carbon::parse($this->record->run_time_datetime_from)->format('d-m'),
                            'until' => Carbon::parse($this->record->run_time_datetime_until)->format('d-m-Y')
                        ]
                    )
                )
                ->descriptionIcon('heroicon-o-calendar-date-range', IconPosition::Before),
            Stat::make(__('Inhoud'),
                    __(":count onderdelen",
                    [
                        'count' => $this->record->account_package_items->count()
                    ])
                )
                ->description(__(':environment', [
                    'environment' => Ucfirst($this->record->environment->value)
                ]))
                ->descriptionIcon('heroicon-o-beaker', IconPosition::Before),
            Stat::make('AccountPackageStatusEnum',
                    $this->record->status->getLabel()
                )
                ->label(__('Status'))
                ->description($this->record->status->nextState($this->record))
                ->descriptionIcon($this->record->status->nextIcon(), IconPosition::Before),
        ];
    }
}
