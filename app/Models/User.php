<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Mail\MagicLoginLink;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use Impersonate;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password', 'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function loginTokens() {
      return $this->hasMany(LoginToken::class);
    }


    public function sendLoginLink($teamId) {
        $plaintext = Str::random(32);
        $expirationDate = now()->addMinutes(15);
        $this->loginTokens()->create([
          'token' => hash('sha256', $plaintext),
          'expires_at' => $expirationDate,
        ]);

        $url = URL::temporarySignedRoute(
          'verify-login',
          $expirationDate,
          [ 'token' => $plaintext, 'team' => $teamId]
        );

        Mail::to($this->email)->queue(new MagicLoginLink($this->name, $url));
    }
}
