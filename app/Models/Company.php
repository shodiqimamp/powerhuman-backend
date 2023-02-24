<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Stmt\Return_;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function team()
    {
        return $this->hasMany(Team::class);
    }

    public function role()
    {
        return $this->hasMany(Role::class);
    }
}
