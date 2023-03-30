<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Identity extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'item_id',
        'title',
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'phone',
        'email',
        'security_number',
        'license_number'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'item_id' => 'string',
        'title' => 'string',
        'username' => 'string',
        'first_name' => 'string',
        'middle_name' => 'string',
        'last_name' => 'string',
        'address' => 'string',
        'phone' => 'string',
        'email' => 'string',
        'security_number' => 'string',
        'license_number' => 'string',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
