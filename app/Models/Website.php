<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Website extends Model
{
    use HasFactory;

    public const CACHED_SUBSCRIBER_KEY = 'website_subscribers_';

    protected $fillable = [
        'name',
        'endpoint',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class);
    }

    public function getSubscribersCachedAttribute()
    {

        $cached = Cache::rememberForever(self::CACHED_SUBSCRIBER_KEY . $this->id, function () {
            return $this->subscribers()->get()->toArray();
        });
        // deserialize array to subscriber object
        return Subscriber::hydrate($cached);
    }

    public static function dropCachedSubscribers(
        $websiteId
    ) {
        Cache::forget(self::CACHED_SUBSCRIBER_KEY . $websiteId);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
