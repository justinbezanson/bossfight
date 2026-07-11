<?php

namespace App\Models;

use Database\Factories\LogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $log_date
 * @property int $kid_id
 * @property int|null $game_id
 * @property int $user_id
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Kid $kid
 * @property-read Game|null $game
 * @property-read User $user
 */
class Log extends Model
{
    /** @use HasFactory<LogFactory> */
    use HasFactory;

    protected $fillable = ['log_date', 'kid_id', 'game_id', 'user_id', 'message'];

    protected function casts(): array
    {
        return [
            'log_date' => 'datetime',
        ];
    }

    /** @return BelongsTo<Kid, $this> */
    public function kid(): BelongsTo
    {
        return $this->belongsTo(Kid::class);
    }

    /** @return BelongsTo<Game, $this> */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
