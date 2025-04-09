
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeSourceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function incomeSources()
    {
        return $this->hasMany(IncomeSource::class);
    }
}
