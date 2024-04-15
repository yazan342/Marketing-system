<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'type', 'amount', 'transaction_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
