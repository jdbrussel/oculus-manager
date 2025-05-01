<?php

namespace App\Models;

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
        'created_by_user',
        'updated_by_user'
    ];

    protected function casts(): array
    {
        return [
            'config' => 'json'
        ];
    }

    /*
     * Accounts
     */

    public function accounts() : BelongsToMany {
        return $this->belongsToMany(Account::class);
    }
}
