<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'google_id',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
                    ->withTimestamps();
    }

    // ==================== HELPER METHODS ====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function hasInWishlist(Product $product): bool
    {
        return $this->wishlists()
                    ->where('product_id', $product->id)
                    ->exists();
    }

    public function getAvatarUrlAttribute(): string
{
    // Prioritas 1: Avatar yang di-upload (file fisik ada di server)
    // Kita harus cek Storage::exists() agar tidak broken image jika file-nya terhapus manual.
    if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
        return asset('storage/' . $this->avatar);
    }

    // Prioritas 2: Avatar dari Google (URL eksternal dimulai dengan http)
    // Biasanya ini terjadi saat user login via Socialite (Google Sign-In).
    if (str_starts_with($this->avatar ?? '', 'http')) {
        return $this->avatar;
    }

    // Prioritas 3: Gravatar (Layanan sedunia untuk avatar berdasarkan email)
    // Gravatar menggunakan MD5 hash dari email lowercase.
    // Jika user belum punya gravatar, tampilkan 'mp' (Mystery Person).
    // &s=200 artinya size gambar 200x200px.
    $hash = md5(strtolower(trim($this->email)));
    return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
}

/**
 * Get initials from name for avatar fallback.
 * Contoh: "Agung Wahyudi" -> "AW"
 * Berguna jika kita ingin membuat UI avatar berupa inisial huruf teks.
 */
public function getInitialsAttribute(): string
{
    $words = explode(' ', $this->name);
    $initials = '';

    foreach ($words as $word) {
        // Ambil huruf pertama tiap kata dan kapitalkan
        $initials .= strtoupper(substr($word, 0, 1));
    }

    // Ambil maksimal 2 huruf pertama saja
    return substr($initials, 0, 2);
}
}
