<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'user_group_id', 'name', 'start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }

    // In App\Models\Budget

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->budgetItems()->sum('amount');
    }
}