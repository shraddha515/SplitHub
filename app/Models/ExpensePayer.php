<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpensePayer extends Model
{
    protected $fillable = ['expense_id', 'user_id', 'paid_amount'];

    protected function casts(): array
    {
        return ['paid_amount' => 'decimal:2'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
