<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property array<string> $processes
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    protected $fillable = ['name', 'processes', 'user_id'];

    protected function casts(): array
    {
        return [];
    }

    public function setProcessesAttribute(mixed $value): void
    {
        if (is_array($value)) {
            $this->attributes['processes'] = implode(',', $value);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);

            $this->attributes['processes'] = is_array($decoded)
                ? implode(',', $decoded)
                : $value;
        } else {
            $this->attributes['processes'] = '';
        }
    }

    /** @return array<string> */
    public function getProcessesAttribute(?string $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return explode(',', $value);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
