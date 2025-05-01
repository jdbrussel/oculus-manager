<?php

namespace App\Models;

use App\Enums\EnvironmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\AccountPackage\StatusEnum;
use App\Enums\AccountPackage\TypeEnum;

class AccountPackage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'status',
        'account_id',
        'erp_id',
        'year',
        'edition',
        'environment',
        'external_id',
        'external_name',
        'type',
        'order_datetime_from',
        'order_datetime_until',
        'order_in_production_datetime_from',
        'order_in_production_datetime_until',
        'order_production_ready_datetime',
        'scheduled_fulfilment_datetime',
        'scheduled_delivery_datetime',
        'run_time_datetime_from',
        'run_time_datetime_until',
        'handling_location_id',
        'created_by_user',
        'updated_by_user',
        'synced_at',
        'Synced_by_user'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->edition} - {$this->external_name} ".(!empty($this->country->external_id) ? "({$this->country->external_id})" : "")."";
    }

    protected $casts = [
        'environment' => EnvironmentEnum::class,
        'type' => TypeEnum::class,
        'status' => StatusEnum::class,
        'config' => 'json'
    ];

    public function getStatusAttribute() : StatusEnum
    {
        $statusCase = StatusEnum::getAccountPackageStatus($this);
        if(!empty($statusCase)) {
            return StatusEnum::from($statusCase);
        }
    }

    /*
     * Account
     */

    public function account() : BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /*
     * account_package_items
     */

    public function account_package_items() : HasMany
    {
        return $this->hasMany(AccountPackageItem::class);
    }

    public function account_package_seals() : HasMany
    {
        return $this->hasMany(AccountPackageSeal::class);
    }

    public function account_package_boxes() : HasMany
    {
        return $this->hasMany(AccountPackageBox::class);
    }

}
