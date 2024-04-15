<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagePrivacySetting extends Model
{
    use HasFactory;

    protected $fillable = ['marketing_page_id', 'user_id'];

    public function marketingPage()
    {
        return $this->belongsTo(MarketingPage::class, 'marketing_page_id');
    }

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
