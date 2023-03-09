<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'order_id',
        'count',
    ];

    protected $hidden = [
        'order_id',
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Menu::class,'menu_id');
    }
}
