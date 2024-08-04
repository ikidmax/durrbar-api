<?php

namespace App\Models\User;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Order;
use App\Models\Review;
use App\Models\Address;
use App\Models\Comment;
use App\Models\Wishlist;
use App\Models\Invoice\Invoice;

use Database\Factories\UserFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use HasFactory;
    use HasUuids;
    use Notifiable;
    use HasApiTokens;
    use HasProfilePhoto;
    use TwoFactorAuthenticatable;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    protected $appends = [
        'photo_url',
        'name'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'photo',
        'first_name',
        'last_name',
        'password',
        'phone',
        'birthday',
        'gender',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Return the full name of the customer.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                "{$this->first_name} {$this->last_name}"
            )
        );
    }

    /**
     * Return the addresses relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Return the comments relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Return the invoises relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function invoises(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'invoiseable');
    }

    /**
     * Return the orders relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    /**
     * Return the orders relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Return the wishlists relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function wishlists(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }
}
