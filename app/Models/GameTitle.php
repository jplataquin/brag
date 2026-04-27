<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTitle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status'];

    public function templates()
    {
        return $this->hasMany(Template::class);
    }
}
