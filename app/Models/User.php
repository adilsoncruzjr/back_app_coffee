<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',         // ID único do usuário
        'name',            // Nome do usuário
        'email',           // Email do usuário
        'password',        // Senha do usuário
        'contact_phone',   // Telefone de contato
        'address',         // Endereço do usuário
        'orders_id',       // ID relacionado a pedidos
        'id_cart',         // ID relacionado ao carrinho
        'remember_token',  // Token para manter login persistente
        'cpf', // CPF do usuário
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->user_id) {
                $user->user_id = (string) Str::uuid(); // Gerar UUID para user_id
            }
        });
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
}
