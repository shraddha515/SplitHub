<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = ['group_id', 'user_id', 'role', 'joined_at'];

    protected function casts(): array
    {
        return ['joined_at' => 'datetime'];
    }
}
