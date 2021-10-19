<?php

namespace App\Models\Scholars;

use App\Extenders\Models\BaseModel as Model;
use App\Models\Users\User;

class ScholarDailyRecord extends Model
{
    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray() {
        return [
            'id' => $this->id,
        ];
    }

    /*
	|--------------------------------------------------------------------------
	| @Relationships
	|--------------------------------------------------------------------------
	*/

    /**
     * Returns scholar owner
     *
     * @return array/collection
     */
	public function scholar()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Store/Update resource to storage
     *
     * @param  array $request
     * @param  object $item
     */
     public static function store($request, $item = null, $columns = ['name', 'type', 'slp_required', 'frequency', 'manager_share', 'scholar_share', 'image', 'description'])
    {
        $vars = $request->only($columns);

        if (!$item) {
            $item = static::create($vars);
        } else {
            $item->update($vars);
        }

        return $item;
    }

    /*
	|--------------------------------------------------------------------------
	| @Renders
	|--------------------------------------------------------------------------
	*/

    /**
     * Render show url for specific item
     *
     * @return string/route
     */
	public function renderShowUrl($prefix = 'admin')
    {
        $route = $this->id;
        $name = 'scholar-types.show';

        return route($prefix . ".{$name}", $route);
    }

    /**
     * Render archive url for specific item
     *
     * @return string/route
     */
    public function renderArchiveUrl($prefix = 'admin')
    {
        return route($prefix . '.scholar-types.archive', $this->id);
    }

    /**
     * Render archive url for specific item
     *
     * @return string/route
     */
    public function renderRestoreUrl($prefix = 'admin')
    {
        return route($prefix . '.scholar-types.restore', $this->id);
    }

	/*
	|--------------------------------------------------------------------------
	| @Checkers
	|--------------------------------------------------------------------------
	*/
}
