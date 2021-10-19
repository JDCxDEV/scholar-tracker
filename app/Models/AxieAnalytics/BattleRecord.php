<?php

namespace App\Models\AxieAnalytics;

use App\Extenders\Models\BaseModel as Model;

class BattleRecord extends Model
{

    public function toSearchableArray()
    {
        return [
             'id' => $this->id,
        ];
    }
    protected $casts = [
        'fighters' => 'array'
    ];

    public $timestamps = false;

    /**
     * Store/Update resource to storage
     *
     * @param  array $request
     * @param  object $item
     */
     public static function store($request, $item = null, $columns = ['question', 'answer'])
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
        $name = 'faqs.show';

        return route($prefix . ".{$name}", $route);
    }

    /**
     * Render archive url for specific item
     *
     * @return string/route
     */
    public function renderArchiveUrl($prefix = 'admin')
    {
        return route($prefix . '.faqs.archive', $this->id);
    }

    /**
     * Render archive url for specific item
     *
     * @return string/route
     */
    public function renderRestoreUrl($prefix = 'admin')
    {
        return route($prefix . '.faqs.restore', $this->id);
    }

	/*
	|--------------------------------------------------------------------------
	| @Checkers
	|--------------------------------------------------------------------------
	*/
}
