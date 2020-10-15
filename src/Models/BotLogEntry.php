<?php

declare(strict_types=1);

namespace Ravisdo\Bot\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BotLogEntry
 *
 * @property int $id
 * @property string $message
 * @property string|null $context
 * @property int $level
 * @property string $level_name
 * @property string $channel
 * @property string $record_datetime
 * @property string|null $extra
 * @property string $formatted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BotLogEntry newModelQuery()
 * @method static Builder|BotLogEntry newQuery()
 * @method static Builder|BotLogEntry query()
 * @method static Builder|BotLogEntry whereChannel($value)
 * @method static Builder|BotLogEntry whereContext($value)
 * @method static Builder|BotLogEntry whereCreatedAt($value)
 * @method static Builder|BotLogEntry whereExtra($value)
 * @method static Builder|BotLogEntry whereFormatted($value)
 * @method static Builder|BotLogEntry whereId($value)
 * @method static Builder|BotLogEntry whereLevel($value)
 * @method static Builder|BotLogEntry whereLevelName($value)
 * @method static Builder|BotLogEntry whereMessage($value)
 * @method static Builder|BotLogEntry whereRecordDatetime($value)
 * @method static Builder|BotLogEntry whereUpdatedAt($value)
 */
class BotLogEntry extends Model
{

    protected $fillable = [
        'message',
        'context',
        'level',
        'level_name',
        'channel',
        'record_datetime',
        'extra',
        'formatted',
    ];

    protected $casts = [
        'context' => 'array',
    ];
}
