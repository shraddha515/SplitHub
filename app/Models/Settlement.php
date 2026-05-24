<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'paid_by', 'paid_to', 'amount', 'settled_at', 'notes'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'paid_to');
    }
}
