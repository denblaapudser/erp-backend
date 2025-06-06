<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
