<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id', 
        'title',
        'description',
        'cost',
        'promotion',
        'image',
        'category_id'
    ];

    /**
     * The validations for each attribute.
     *
     * @var string[]
     */
    public static $rules = [
        "title" => "required|string|max:250",
        "description" => "required|string|max:1000",
        "cost" => "numeric",
        "promotion" => "numeric",
        "image" => "file|mimes:jpg,png",
        "category_id" => "numeric"
    ];

    /**
     * Whose this entity belongs
     * 
     * @return Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
