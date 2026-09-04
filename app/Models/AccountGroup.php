<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'account_group_id');
    }
}
