<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_of_person',
        'table_id',
        'status_order_id',
        'shift_worker_id'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusOrder::class, 'status_order_id');
    }

    public function workShift()
    {
        return $this->hasOneThrough(WorkShift::class, ShiftWorker::class,
            'id',
            'id',
            'shift_worker_id',
            'work_shift_id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, ShiftWorker::class,
            'id',
            'id',
            'shift_worker_id',
            'user_id');
    }

    public function positions()
    {
        return $this->hasMany(OrderMenu::class);
    }

    public function getPrice()
    {
        return $this->positions->reduce(function ($price, $item) {
                return $price + $item->count * $item->product->price;
            }) ?? 0;
    }

    public function getAllowedsTransitions(User $user)
    {
        if ($user->hasRole(['waiter'])) {
            $alloweds = [
                'taken' => ['canceled'],
                'ready' => ['paid-up']
            ];
        }

        if ($user->hasRole(['cook'])) {
            $alloweds = [
                'taken' => ['preparing'],
                'preparing' => ['ready']
            ];
        }

        return collect($alloweds[$this->status->code] ?? []);
    }

    public function changeStatus($status)
    {
        $this->update([
            'status_order_id' => StatusOrder::where(['code' => $status])->first()->id
        ]);
    }
}
