<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les colonnes remplissables
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'pseudo',
        'phone',
        'email',
        'birthdate',
        'location',
        'delivery_address',
        'photo',
        'password',
    ];

    /**
     * Les colonnes cachées
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les colonnes castées automatiquement
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date',
    ];

    /**
     * ✅ Correct : Retourne l'URL complète de la photo utilisateur
     */
    public function getPhotoAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }
        return asset('default-avatar.png'); // Image par défaut
    }

    /**
     * ✅ Correct : Définir la relation User ➔ Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
