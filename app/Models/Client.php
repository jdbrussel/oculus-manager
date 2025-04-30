<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'erp_id',
        'created_by_user',
        'updated_by_user'
    ];

    /*
     * Users
     */

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }

    public function created_user() : belongsTo {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    /*
     * Accounts
     */

    public function accounts(): HasMany {
        return $this->HasMany(Account::class);
    }



}
