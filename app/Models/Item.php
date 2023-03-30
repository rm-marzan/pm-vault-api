<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory, SoftDeletes;

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
        'user_id',
        'organization_id',
        'folder_id',
        'name',
        'type',
        'notes',
        'favorite'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'string',
        'folder_id' => 'string',
        'organization_id' => 'string',
        'name' => 'string',
        'type' => 'integer',
        'favorite' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function login()
    {
        return $this->hasOne(Login::class);
    }

    public function card()
    {
        return $this->hasOne(Card::class);
    }

    public function identity()
    {
        return $this->hasOne(identity::class);
    }
}
