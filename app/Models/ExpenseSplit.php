<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseSplit extends Model
{
    protected $fillable = ['expense_id', 'user_id', 'share_amount', 'percentage'];

    protected function casts(): array
    {
        return [
            'share_amount' => 'decimal:2',
            'percentage' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
