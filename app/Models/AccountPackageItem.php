<?php

namespace App\Models;

use App\Enums\AccountPackageItem\TypeEnum;
use App\Enums\EnvironmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPackageItem extends Model
{
    /** @use HasFactory<\Database\Factories\AccountPackageItemFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_package_id',
        'erp_id',
        'year',
        'name',
        'environment',
        'external_id',
        'external_name',
        'type',
        'num_versions',
        'num_per_version',
        'quantity',
        'quantity_reserved',
        'quantity_stock',
        'quantity_production',
        'supplier',
        'is_value_product',
        'allocation',
        'created_by_user',
        'updated_by_user',
        'synced_at',
        'Synced_by_user'
    ];

    protected $casts = [
        'environment' => EnvironmentEnum::class,
        'allocation' => 'json',
        'type' => TypeEnum::class,
    ];

    public function getExternalIdAttribute($value) {
        if(strpos($value,'_') > -1) {
           $values = explode('_', $value);
        }
        $values = array_splice($values, 1);
        return implode('-', $values);
    }

    /*
     * AccountPackage
     */

    public function accountPackage() : BelongsTo
    {
        return $this->belongsTo(AccountPackage::class);
    }
}
