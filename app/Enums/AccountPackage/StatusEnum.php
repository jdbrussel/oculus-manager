<?php

namespace App\Enums\AccountPackage;

use App\Models\AccountPackage;
use Carbon\Carbon;
use Filament\Support\Contracts\HasLabel;

enum StatusEnum : string implements HasLabel
{
    case ORDER_PENDING = 'pending';
    case ORDER_OPEN = 'open';
    case ORDER_PRE_PRODUCTION = 'pre_production';
    case ORDER_IN_PRODUCTION = 'production';
    case ORDER_PRODUCED = 'produced';
    case ORDER_IN_FULFILMENT = 'fulfilment';
    case ORDER_DELIVERED = 'delivered';
    case ORDER_CLOSED = 'closed';

    public function getLabel(): string
    {
        return match($this) {
            self::ORDER_PENDING => __('Order Pending'),
            self::ORDER_CLOSED => __('Order gesloten'),
            self::ORDER_OPEN => __('Order open'),
            self::ORDER_PRE_PRODUCTION => __('Klaar voor productie'),
            self::ORDER_IN_PRODUCTION => __('In productie'),
            self::ORDER_PRODUCED => __('Geproduceerd'),
            self::ORDER_IN_FULFILMENT => __('In fulfilment'),
            self::ORDER_DELIVERED => __('Geleverd'),
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::ORDER_PENDING => 'heroicon-o-archive-box',
            self::ORDER_OPEN => 'heroicon-o-archive-box-arrow-down',
            self::ORDER_PRE_PRODUCTION => 'heroicon-o-printer',
            self::ORDER_IN_PRODUCTION => 'heroicon-o-printer',
            self::ORDER_PRODUCED => 'heroicon-o-printer',
            self::ORDER_IN_FULFILMENT => 'heroicon-o-arrow-up-on-square-stack',
            self::ORDER_DELIVERED => 'heroicon-o-building-storefront',
            self::ORDER_CLOSED => 'heroicon-o-lock-closed',
        };
    }

    public static function getAccountPackageStatus($account_package = false) : string
    {
        $status = 'closed';
        $now = Carbon::now();

        if(
            $now >= Carbon::parse($account_package->order_datetime_from)->toDateTime()
                && $now <= Carbon::parse($account_package->order_datetime_until)->toDateTime()
        ) {
            $status = 'open';
        }
        else if(
            $now >= Carbon::parse($account_package->order_datetime_until)->toDateTime()
                && $now <= Carbon::parse($account_package->order_in_production_datetime_from)->toDateTime()
        ) {
            $status = 'pre_production';
        }
        else if(
            $now >= Carbon::parse($account_package->order_in_production_datetime_from)->toDateTime()
                && $now <= Carbon::parse($account_package->order_in_production_datetime_until)->toDateTime()
        ) {
            $status = 'production';
        }
        else if(
            $now >= Carbon::parse($account_package->order_production_ready_datetime)->toDateTime()
                && $now < Carbon::parse($account_package->scheduled_fulfilment_datetime)->toDateTime()
        ) {
            $status = 'produced';
        }
        else if(
            $now >= Carbon::parse($account_package->scheduled_fulfilment_datetime)->toDateTime()
                && $now < Carbon::parse($account_package->scheduled_delivery_datetime)->toDateTime()
        ) {
            $status = 'fulfilment';
        }
        else if(
            $now >= Carbon::parse($account_package->scheduled_delivery_datetime)->toDateTime()
        ) {
            $status = 'delivered';
        }
        else if (
            $now < Carbon::parse($account_package->order_datetime_from)->toDateTime()
        ) {
            $status = 'pending';
        }

        return $status;
    }

    public function nextState($record = []): string
    {
        return match($this) {
            self::ORDER_PENDING => __('Order open: :datetime uur', [
                'datetime' => Carbon::parse($record['order_datetime_from'])->format('d-m H:i')
            ]),
            self::ORDER_OPEN => __('Order sluit: :datetime uur', [
                'datetime' => Carbon::parse($record['order_datetime_until'])->format('d-m H:i')
            ]),
            self::ORDER_PRE_PRODUCTION => __('Productie start: :datetime uur', [
                'datetime' => Carbon::parse($record['order_in_production_datetime_from'])->format('d-m H:i')
            ]),
            self::ORDER_IN_PRODUCTION => __('Productie klaar: :datetime uur', [
                    'datetime' => Carbon::parse($record['order_production_ready_datetime'])->format('d-m H:i')
            ]),
            self::ORDER_PRODUCED => __('Fulfilment start: :datetime uur', [
                'datetime' => Carbon::parse($record['scheduled_fulfilment_datetime'])->format('d-m H:i')
            ]),
            self::ORDER_IN_FULFILMENT => __('Levering gepland op :datetime', [
                'date' => Carbon::parse($record['scheduled_delivery_datetime'])->format('d-m H:i')
            ]),
            self::ORDER_DELIVERED => __('Geleverd op :day :date', [
                'day' => Carbon::parse($record['scheduled_delivery_datetime'])->format('l'),
                'date' => Carbon::parse($record['scheduled_delivery_datetime'])->format('d-m-Y')
            ]),
            self::ORDER_CLOSED => __('Order gesloten'),
        };
    }


    public function nextIcon(): string
    {

        return match($this) {
            self::ORDER_PENDING => 'heroicon-o-archive-box-arrow-down',
            self::ORDER_OPEN => 'heroicon-o-lock-closed',
            self::ORDER_PRE_PRODUCTION => 'heroicon-o-printer',
            self::ORDER_IN_PRODUCTION => 'heroicon-o-arrow-up-on-square-stack',
            self::ORDER_PRODUCED => 'heroicon-o-arrow-up-on-square-stack',
            self::ORDER_IN_FULFILMENT => 'heroicon-o-building-storefront',
            self::ORDER_DELIVERED => 'heroicon-o-building-storefront',
            self::ORDER_CLOSED => 'heroicon-o-archive-box-x-mark',
        };
    }




    public function isSynchable() : array
    {
        return match($this) {
            self::ORDER_PENDING => ['error' => false],
            self::ORDER_OPEN => ['error' => false],
            self::ORDER_PRE_PRODUCTION => ['error' => false],
            self::ORDER_IN_PRODUCTION => ['error' => false],
            self::ORDER_PRODUCED => ['error' => false],
            self::ORDER_IN_FULFILMENT => ['error' => __('Order in fulfilment')],
            self::ORDER_DELIVERED => ['error' => __('Order reeds geleverd')],
            self::ORDER_CLOSED => ['error' => __('Order reeds gesloten')],
        };
    }


}
