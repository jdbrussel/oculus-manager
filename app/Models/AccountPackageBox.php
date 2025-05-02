<?php

namespace App\Models;

use App\Enums\EnvironmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPackageBox extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'account_package_id',
        'environment',
    ];

    protected function casts(): array
    {
        return [
            'environment' => EnvironmentEnum::class,
        ];
    }

    public function account_package(): BelongsTo
    {
        return $this->belongsTo(AccountPackage::class);
    }

}
