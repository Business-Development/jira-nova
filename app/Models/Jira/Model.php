<?php

namespace App\Models\Jira;

use App\Models\Model as Eloquent;

abstract class Model extends Eloquent
{
    //////////////////
    //* Attributes *//
    //////////////////
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'jira';

    /**
     * The default avatar size for this model.
     *
     * @var string
     */
    protected $avatarSize = '32x32';

    /**
     * The attribute name of the avatar urls.
     *
     * @var string
     */
    protected $avatarKey = 'avatar_urls';

    /**
     * The internally cached record maps.
     *
     * @var array
     */
    protected static $recordMap = [];

    ///////////////////////
    //* Magic Accessors *//
    ///////////////////////
    /**
     * Returns the url for the default avatar size.
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute()
    {
        // Determine the avatar urls
        $urls = $this->getAttribute($this->avatarKey);

        // Make sure the urls are an associative array
        if(!empty($urls) || !is_array($urls)) {
            return null;
        }

        // Return the default sized entry
        return $urls[$this->avatarSize];
    }

    ///////////////
    //* Casting *//
    ///////////////
    /**
     * Returns the casts mapping.
     *
     * @return array
     */
    public function getCasts()
    {
        return array_merge(['avatar_urls' => 'json'], parent::getCasts());
    }

    //////////////////
    //* Record Map *//
    //////////////////
    /**
     * Loads the specified record map if it has not already been loaded.
     *
     * @param  string  $model
     *
     * @return void
     */
    protected function loadRecordMapIfNotLoaded($model)
    {
        // If the record map has already been loaded, stop here
        if(array_key_exists($model, static::$recordMap)) {
            return;
        }

        // Load the record map
        $model::loadRecordMap();
    }

    /**
     * Loads the record map for this model.
     *
     * @return void
     */
    public static function loadRecordMap()
    {
        static::$recordMap[static::class] = static::getRecordMap();
    }

    /**
     * Returns the record map for this model.
     *
     * @return array
     */
    public static function getRecordMap()
    {
        return static::all()->keyBy((new static)->getRecordMapKeyName());
    }

    /**
     * Returns the record map key name for this model.
     *
     * @return \Closure|string
     */
    public function getRecordMapKeyName()
    {
        return $this->getJiraCacheKeyName();
    }

    /**
     * Returns the specified record from the record map.
     *
     * @param  string  $model
     * @param  mixed   $key
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function getRecordFromMap($model, $key)
    {
        return static::$recordMap[$model][$key] ?? null;
    }

    ///////////////
    //* Caching *//
    ///////////////
    /**
     * Creates and returns new model from the specified jira record.
     *
     * @param  \stdClass  $record
     *
     * @return static
     */
    public static function createFromJira($record)
    {
        // Create a new model
        $model = new static;

        // Update the user model jira
        return $model->updateFromJira($record);
    }

    /**
     * Updates this model using the specified jira record.
     *
     * @param  \stdClass  $record
     *
     * @return $this
     */
    public abstract function updateFromJira($record);

    /**
     * Returns the value of the jira cache key.
     *
     * @return string
     */
    public function getJiraCacheKey()
    {
        return $this->getAttribute($this->getJiraCacheKeyName());
    }

    /**
     * Sets the value of the jira cache key.
     *
     * @param  string  $value
     *
     * @return $this
     */
    public function setJiraCacheKey($value)
    {
        return $this->setAttribute($this->getJiraCacheKeyName(), $value);
    }

    /**
     * Returns the attribute name of the jira cache key.
     *
     * @return string
     */
    public function getJiraCacheKeyName()
    {
        return 'cache_key';
    }

    /**
     * Returns the value of the jira cache value.
     *
     * @return string
     */
    public function getJiraCacheValue()
    {
        return $this->getAttribute($this->getJiraCacheValueName());
    }

    /**
     * Sets the value of the jira cache value.
     *
     * @param  string  $value
     *
     * @return $this
     */
    public function setJiraCacheValue($value)
    {
        return $this->setAttribute($this->getJiraCacheValueName(), $value);
    }

    /**
     * Returns the attribute name of the jira cache value.
     *
     * @return string
     */
    public function getJiraCacheValueName()
    {
        return 'cache_value';
    }

    /**
     * Returns the jira cache key from the specified api record.
     *
     * @return string
     */
    public static function getJiraCacheKeyFromApi($record)
    {
        return $record->self;
    }

    /**
     * Returns the jira cache value from the specified api record.
     *
     * @return string
     */
    public static function getJiraCacheValueFromApi($record)
    {
        return md5(json_encode($record));
    }

    /**
     * Returns the paginated records using the specified connection.
     *
     * @param  \App\Support\Jira\Api\Connection  $connection
     *
     * @return array
     */
    public abstract static function getPaginatedJiraRecords($connection);
}