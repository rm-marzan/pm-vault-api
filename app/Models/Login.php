<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Login extends Model
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
        'username',
        'password',
        'urls'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'item_id' => 'string',
        'username' => 'string',
        'password' => 'string',
        'urls' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
