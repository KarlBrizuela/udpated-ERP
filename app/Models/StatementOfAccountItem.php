<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfAccountItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_of_account_id',
        'service',
        'description',
        'qty',
        'price'
    ];

    public function statementOfAccount()
    {
        return $this->belongsTo(StatementOfAccount::class);
    }
}
