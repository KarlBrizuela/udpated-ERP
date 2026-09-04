<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'account_group_id',
        'is_active',
        'parent_id',
        'is_postable',
        'normal_balance',
        'display_order',
    ];

    public function journalEntryItems()
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function accountGroup()
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }
}
