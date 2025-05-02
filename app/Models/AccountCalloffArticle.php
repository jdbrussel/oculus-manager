<?php

namespace App\Models;

use App\Enums\EnvironmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountCalloffArticle extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'erp_id',
        'environment',
        'year',
        'name',
        'external_id',
        'external_name',
        'in_stock',
        'created_by_user',
        'updated_by_user',
        'deleted_by_user',
        'synched_at_user',
        'pre_deleted',
        'deleted_at',
        'synched_at'
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
