<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'patronymic',
        'login',
        'password',
        'photo_file',
        'role_id',
        'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'role',
        'role_id',
        'password',
        'patronymic',
        'surname',
        'photo_file',
        'api_token',
        'created_at',
        'updated_at',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roles)
    {
        return collect($roles)->contains($this->role->code);
    }

    public function shiftWorkers()
    {
        return $this->hasMany(ShiftWorker::class);
    }

    public function getShiftWorker($work_shift_id)
    {
        return $this->shiftWorkers()->where(['work_shift_id' => $work_shift_id])->first();
    }

    public function generateToken()
    {
        $this->update([
            'api_token' => Hash::make(Str::random())
        ]);
        return $this->api_token;
    }

    public function logout()
    {
        $this->update([
            'api_token' => null
        ]);
    }

}
