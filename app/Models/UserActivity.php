<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'data',
    ];
    
    protected $casts = [
        'data' => 'array',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public static function log(string $activityType, string $description, ?array $data = null, ?User $user = null)
    {
        $user = $user ?: auth()->user();
        
        if (!$user) {
            return null;
        }
        
        return static::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => $data,
        ]);
    }
}
