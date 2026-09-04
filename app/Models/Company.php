<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';
    protected $primaryKey = 'company_id';

    protected $fillable = [
        'company_name',
        'parent_id',
        'account_number',
        'mobile',
        'main_email',
        'shipping_address',
        'is_inactive'
    ];

    protected $casts = [
        'is_inactive' => 'boolean',
    ];

    /**
     * Get the parent company/branch.
     */
    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id', 'company_id');
    }

    /**
     * Get the child branches.
     */
    public function branches()
    {
        return $this->hasMany(Company::class, 'parent_id', 'company_id');
    }
}
