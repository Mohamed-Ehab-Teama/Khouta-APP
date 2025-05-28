<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;


    // Relation To User
    public function user()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }


    protected $guarded = ['id'];

}
