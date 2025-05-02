<?php

namespace App\Models;

use App\Enums\EnvironmentEnum;
use App\Enums\ModulesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'config',
        'environment',
        'created_by_user',
        'updated_by_user'
    ];

    protected function casts(): array
    {
        return [
            'environment' => EnvironmentEnum::class,
            'config' => 'json'
        ];
    }

    /*
     * Accounts
     */

    public function accounts() : BelongsToMany {
        return $this->belongsToMany(Account::class);
    }


    public function getConfig(string $field, string $fall_back = '') : string
    {
        if(!$field) {
            return '';
        }
        $fall_back_value = $fall_back ?? '';
        $config = $this->config;
        return $config[$field] ?? $fall_back_value;
    }
}
