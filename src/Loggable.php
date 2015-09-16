<?php

namespace Devitek\Laravel\Eloquent\Loggable;

use DB;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Loggable
{
    /**
     * @var string
     */
    private static $actionCreate = 'CREATE';

    /**
     * @var string
     */
    private static $actionUpdate = 'UPDATE';

    /**
     * @var string
     */
    private static $actionRemove = 'REMOVE';

    /**
     * @var bool
     */
    protected $updating;

    /**
     * @var string
     */
    protected $logMessage;

    /**
     * Boot the loggable trait for a model.
     *
     * @return void
     */
    public static function bootLoggable()
    {
        static::saving(function (Model $model) {
            $model->prepareNewVersion();
        });

        static::saved(function (Model $model) {
            $model->createVersion();
        });

        static::deleted(function (Model $model) {
            $model->createVersion(false);
        });
    }

    /**
     * Prepare a new version
     *
     * @see Loggable::bootLoggable
     */
    public function prepareNewVersion()
    {
        $this->updating = $this->exists ? true : false;

        if (isset($this->reason)) {
            $reasonField      = $this->reason;
            $this->logMessage = $this->$reasonField;

            unset($this->$reasonField);
        }
    }

    /**
     * Create a new version
     *
     * @param void|bool $createOrUpdate
     *
     * @see Loggable::bootLoggable
     */
    public function createVersion($createOrUpdate = true)
    {
        if (empty($this->versioned)) {
            return;
        }

        $changes = [];

        foreach ($this->getDirty() as $field => $value) {
            if (in_array($field, $this->versioned)) {
                $changes[$field] = $value;
            }
        }

        $logEntry = new LogEntry();

        $logEntry->action        = (! $createOrUpdate ? static::$actionRemove : ($this->updating ? static::$actionUpdate : static::$actionCreate));
        $logEntry->logged_at     = new DateTime();
        $logEntry->loggable_id   = $this->getKey();
        $logEntry->loggable_type = get_class($this);
        $logEntry->version       = $this->getNewVersionNumber();
        $logEntry->reason        = $this->logMessage;
        $logEntry->data          = json_encode($changes);
        $logEntry->user_id       = auth()->id();

        $logEntry->save();
    }

    /**
     * Get all entries
     *
     * @return MorphMany
     */
    public function logEntries()
    {
        return $this->morphMany(LogEntry::class, 'loggable')->orderBy('version', 'DESC');
    }

    /**
     * Get the next vesion number
     *
     * @return int
     */
    protected function getNewVersionNumber()
    {
        $newVersion = DB::table('ext_log_entries')
            ->where('loggable_id', '=', $this->getKey())
            ->where('loggable_type', '=', get_class($this))
            ->max('version');

        return $newVersion + 1;
    }

    /**
     * Revert to a specific revision (default : 1)
     *
     * @param int $version
     *
     * @return $this
     */
    public function revert($version = 1)
    {
        $logEntries = (new LogEntry())->query()
            ->where('loggable_id', '=', $this->getKey())
            ->where('loggable_type', '=', get_class($this))
            ->where('version', '<=', $this->logEntries->first()->version)
            ->where('version', '>=', $version)
            ->orderBy('version', 'ASC')
            ->get();

        if ($logEntries->count() > 0) {
            while (($log = $logEntries->pop())) {
                if ($data = json_decode($log->data)) {
                    foreach ($data as $field => $value) {
                        $this->$field = $value;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get the current version (default : 1)
     *
     * @return int
     */
    public function getVersionAttribute()
    {
        return 1;
    }
}