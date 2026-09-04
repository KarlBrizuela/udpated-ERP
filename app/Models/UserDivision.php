<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDivision extends Model
{
    use HasFactory;

    protected $table = 'division_user';

    protected $fillable = [
        'user_id',
        'division',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
