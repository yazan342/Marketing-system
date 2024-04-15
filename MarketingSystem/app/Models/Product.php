<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected $fillable = ['marketing_page_id', 'name', 'description', 'price', 'image', 'offer_price', 'offer_start_date', 'offer_end_date'];

    public function marketingPage()
    {
        return $this->belongsTo(MarketingPage::class, 'marketing_page_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
