<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class Card extends Model
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
        'cardholder_name',
        'brand',
        'number',
        'exp_month',
        'exp_year',
        'cvv'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'item_id' => 'string',
        'cardholder_name' => 'string',
        'brand' => 'string',
        'number' => 'string',
        'exp_month' => 'string',
        'exp_year' => 'string',
        'cvv' => 'string'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function setNumberAttribute($value)
    {
        $this->attributes['number'] = Crypt::encryptString($value);
        $this->attributes['cvv'] = Crypt::encryptString($value);
    }
    
    public function getNumberAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}
