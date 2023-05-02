<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id', 
        'title'
    ];

    /**
     * The validations for each attribute.
     *
     * @var string[]
     */
    public static $rules = [
        "title" => "required|string|unique:categories,title|max:250"
    ];

    /**
     * Messages customization for validation rules
     */
    public static $messages = [
        "title" => [
            "unique" => "title.unique"
        ]
    ];
}
