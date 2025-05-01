<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'created_by_user',
        'updated_by_user',
        'synched_at',
        'synched_at_user',
        'is_super_admin',
        'is_client_admin',
        'pre_delete'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function created_user() : belongsTo {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    public function getTenants(Panel $panel): array | Collection
    {
        if(auth()->user()->is_super_admin) {
            return Client::all();
        }
        return $this->clients;
    }

    public function client(): BelongsToMany {
        return $this->belongsToMany(Client::class)->where('is_active', true);
    }
    public function clients() : BelongsToMany {
        return $this->belongsToMany(Client::class)->where('is_active', true);
    }
    public function accounts() : BelongsToMany {
        $accounts = $this->belongsToMany(Account::class);
        if(Filament::getTenant()) {
            $accounts->where('accounts.client_id', '=', Filament::getTenant()->id);
        }
        return $accounts;
    }
    public function canAccessTenant(Model $tenant): bool
    {
        if(auth()->user()->is_super_admin) {
            return true;
        }
       return $this->clients->contains($tenant);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if(auth()->user()->is_super_admin) {
            return true;
        }

        if($panel->getId() === 'client' && auth()->user()->is_client_admin) {
            return true;
        }
        return false;
    }

}
