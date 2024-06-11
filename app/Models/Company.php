<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'street_address',
        'district_address',
        'city',
    ];

    protected $dates = ["deleted_at"];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
