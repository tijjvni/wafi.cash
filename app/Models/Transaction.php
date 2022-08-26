<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function creditor()
    {
        return $this->belongsTo(User::class,'to');
    }    

    public function debitor()
    {
        return $this->belongsTo(User::class,'from');
    }
    
}
