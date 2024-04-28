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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            $company->slug = Str::slug($company->name);
        });

        static::updating(function ($company) {
            $company->slug = Str::slug($company->name);
        });
    }

    protected $dates = ["deleted_at"];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
