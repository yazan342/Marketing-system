<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingPage extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id', 'name', 'description', 'type', 'language',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function Members()
    {
        return $this->hasMany(PageMember::class);
    }

    public function privacySettings()
    {
        return $this->hasMany(PagePrivacySetting::class);
    }

    public function isUserBlocked($userId)
    {
        return $this->blockedUsers()->where('user_id', $userId)->exists();
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'page_privacy_settings', 'marketing_page_id', 'user_id');
    }

    public function scopeWhereNotBlockedForUser($query, $userId)
    {
        return $query->whereDoesntHave('blockedUsers', function ($subQuery) use ($userId) {
            $subQuery->where('user_id', $userId);
        });
    }
}
