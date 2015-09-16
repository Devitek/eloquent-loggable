<?php

namespace Devitek\Laravel\Eloquent\Loggable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LogEntry extends Model
{
    /**
     * Use timestamps ?
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'ext_log_entries';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = ['action', 'logged_at', 'object_id', 'object_class', 'version', 'reason', 'data', 'user_id'];

    /**
     * Date fields
     *
     * @var array
     */
    protected $dates = ['logged_at'];

    /**
     * The watched model
     *
     * @return MorphTo
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * The user who save this revision
     *
     * @return BelongsTo
     */
    public function user()
    {
        $className = config('auth.model');

        return $this->belongsTo($className);
    }
}