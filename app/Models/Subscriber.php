<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
    ];

    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class);
    }
}
