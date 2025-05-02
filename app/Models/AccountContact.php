<?php

namespace App\Models;

use App\Enums\EnvironmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountContact extends Model
{
    /** @use HasFactory<\Database\Factories\AccountContactFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'erp_id',
        'environment',
        'external_id',
        'name',
        'email',
        'phone',
        'mobile',
        'department',
        'function',
        'environment',
        'created_by_user',
        'updated_by_user',
        'synced_at',
        'synced_by_user',
        'deleted_at',
        'deleted_by_user',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'environment' => EnvironmentEnum::class,
        ];
    }

    /*
     * Account
     */
    public function account() : BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
