<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'start',
        'end',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function workers()
    {
        return $this->belongsToMany(User::class, 'shift_workers');
    }

    public function hasUser(User $user)
    {
        return $this->workers()->where(['user_id' => $user->id])->exists();
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, ShiftWorker::class);
    }

    public function open()
    {
        $this->update(['active'=>true]);
        return $this;
    }

    public function close()
    {
        $this->update(['active'=>false]);
        return $this;
    }

    public function amountForAllOrders()
    {
        return $this->orders->reduce(function ($sum, $item) {
            return $sum + $item->getPrice();
        });
    }
}
