<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(BookCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BookCategory::class, 'parent_id');
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }

    public function subCategoryBooks()
    {
        return $this->hasMany(Book::class, 'sub_category_id');
    }
}
