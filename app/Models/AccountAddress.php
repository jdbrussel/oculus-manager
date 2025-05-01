<?php

namespace App\Models;

use App\Enums\CountriesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Self_;

class AccountAddress extends Model
{
    /** @use HasFactory<\Database\Factories\AccountAddressFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'account_id',
        'erp_id',
        'external_id',
        'address_type',
        'formula',
        'name',
        'street',
        'house_number',
        'house_number_addition',
        'postal_code',
        'city',
        'region',
        'country',
        'rayon',
        'dc_day_id',
        'dc_week_id',
        'dc_theme_id',
        'created_by_user',
        'updated_by_user',
        'synced_at',
        'Synced_by_user'
    ];

    protected $casts = [
        'country' => CountriesEnum::class,
    ];

    /*
     * Account
     */

    public function account() : BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /*
     * DCs
     */

    public function dc_day() : HasOne
    {
        return $this->hasOne(self::class, 'erp_id', 'dc_day_id');
    }
    public function dc_week() : HasOne
    {
        return $this->hasOne(self::class, 'erp_id', 'dc_week_id');
    }
    public function dc_theme() : HasOne
    {
        return $this->hasOne(self::class, 'erp_id', 'dc_theme_id');
    }


}
