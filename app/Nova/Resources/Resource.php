<?php

namespace App\Nova\Resources;

use Laravel\Nova\Resource as NovaResource;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

abstract class Resource extends NovaResource
{
    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The default ordering to use when listing this resource.
     *
     * @var array
     */
    public static $defaultOrderings = [];

    /**
     * The relationship counts that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $withCount = [];

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->withCount(static::$withCount);
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Scout\Builder                   $query
     *
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }

    /**
     * Perform any final formatting of the given validation rules.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array  $rules
     *
     * @return array
     */
    protected static function formatRules(NovaRequest $request, array $rules)
    {
        // Initialize the replacements
        $replacements = [
            '{{resourceId}}' => str_replace(['\'', '"', ',', '\\'], '', $request->resourceId ?: 'NULL')
        ];

        // Initialize the replacement values
        $values = [];

        // Add the request parameters as replacement values
        $values['request'] = $request->all();

        // Convert the replacement values into replacement rules
        $replacements = ($replacer = function($replacements, $values, $prefix = null) use (&$replacer) {

            foreach($values as $key => $value) {

                if(is_array($value)) {
                    return $replacer($replacements, $value, !is_null($prefix) ? "{$prefix}.{$key}" : $key);
                } else if(!is_null($prefix)) {
                    $replacements["{{{$prefix}.{$key}}}"] = str_replace(['\'', '"', ',', '\\'], '', $value);
                } else {
                    $replacements["{{{$key}}}"] = str_replace(['\'', '"', ',', '\\'], '', $value);
                }

            }

            return $replacements;

        })($replacements, $values);

        // Remove all empty replacement values
        $replacements = array_filter($replacements);

        // If no replacements were found, just return the rules as-is
        if(empty($replacements)) {
            return $rules;
        }

        // Replace the rules
        return collect($rules)->map(function ($rules) use ($replacements) {
            return collect($rules)->map(function ($rule) use ($replacements) {
                return is_string($rule)
                            ? str_replace(array_keys($replacements), array_values($replacements), $rule)
                            : $rule;
            })->all();
        })->all();
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array                                  $orderings
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        if(empty($orderings)) {
            $orderings = static::$defaultOrderings;
        }

        return parent::applyOrderings($query, $orderings);
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     */
    public function subtitle()
    {
        if(property_exists($this, 'subtitle')) {
            return $this->{static::$subtitle};
        }

        return null;
    }

    /**
     * Returns the select options for this resource.
     *
     * @param  string|null  $key
     *
     * @return array
     */
    public static function selection($key = null)
    {
        // Create a new model instance
        $model = static::newModel();

        // If the title property is available, pluck it using a query
        if(!is_null(static::$title)) {
            return $model->newQuery()->pluck($model->getKeyName(), static::$title)->sortKeys()->all();
        }

        // Otherwise, use the title method
        return $model::all()->keyBy($key)->map(function($model) {
            return (new static($model))->title();
        })->sortKeys()->all();
    }

    /**
     * Returns the default select option for this resource.
     *
     * @return mixed
     */
    public static function defaultSelection()
    {
        // Determine the model class
        $model = static::$model;

        // Make sure a default selection method exists
        if(!method_exists($model, 'getDefaultSelectionValue')) {
            return null;
        }

        // Return the result of the model
        return $model::getDefaultSelectionValue();
    }

    /**
     * Creates and returns a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function newModel()
    {
        $model = static::$model;

        $instance = new $model;

        $instance->setRawAttributes(static::getDefaultAttributes());

        return $instance;
    }

    /**
     * Returns the default attributes for new model instances.
     *
     * @return array
     */
    public static function getDefaultAttributes()
    {
        return static::$defaultAttributes ?? [];
    }

    /**
     * Creates and returns a new download action.
     *
     * @return \Laravel\Nova\Actions\Action
     */
    public static function newDownloadAction()
    {
        // Create a new action
        $action = new DownloadExcel;

        // Ask for the filename
        $action->askForFilename();

        // Ask for writer type
        $action->askForWriterType();

        // Include headers
        $action->withHeadings();

        // Return the action
        return $action;
    }
}
