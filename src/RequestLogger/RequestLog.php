<?php

namespace Landrok\Laravel\RequestLogger;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getTable()
    {
        return config('requestlogger.tablename', parent::getTable());
    }

    /**
     * Relation to user
     */
    public function user()
    {
        return $this->belongsTo(
            config('requestlogger.user_model')
        );
    }
}
