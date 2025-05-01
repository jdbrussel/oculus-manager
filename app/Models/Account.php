<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\Account\ErpStatusEnum;
use App\Enums\EnvironmentEnum;

class Account extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'slug',
        'erp_id',
        'environment',
        'erp_status',
        'calloff_article_import_config',
        'created_by_user',
        'updated_by_user',
        'deleted_by_user',
        'synched_at',
        'synched_at_user',
    ];

    protected $casts = [
        'environment' => EnvironmentEnum::class,
        'erp_status' => ErpStatusEnum::class,
        'config' => 'json',
    ];

    /*
     * Client
     */

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class);
    }

    /*
     * Users
     */

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }

    public function created_user() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    public function updated_user() : BelongsTo {
        return $this->belongsTo(User::class, 'updated_by_user');
    }


    /*
     * Modules
     */

    public function modules(): BelongsToMany {
        return  $this->belongsToMany(Module::class);
    }

    /*
     * AccountContacts
     */

    public function account_contacts(): HasMany {
        return $this->hasMany(AccountContact::class);
    }

    /*
     * Addresses
     */

    public function account_addresses(): HasMany {
        return $this->hasMany(AccountAddress::class);
    }

    /*
     * Packages
     */

    public function account_packages(): HasMany {
        return $this->hasMany(AccountPackage::class);
    }

//    public function account_package_seals(): HasManyThrough {
//        return $this->HasManyThrough(AccountPackageSeal::class, AccountPackage::class);
//    }


    /*
     * Addresses
     */

    public function calloff_articles(): HasMany {
        return $this->hasMany(AccountCalloffArticle::class);
    }


}
