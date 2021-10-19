<?php

namespace App\Http\Controllers\Admin\Scholars\DailyRecords;

use App\Extenders\Controllers\FetchController as Controller;
use App\Models\Scholars\ScholarDailyRecord;
use Carbon\Carbon;

class ScholarDailyRecordFetchController extends Controller
{
    /**
     * Set object class of fetched data
     *
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new ScholarDailyRecord;
    }

    /**
     * Custom filtering of query
     *
     * @param Illuminate\Support\Facades\DB $query
     * @return Illuminate\Support\Facades\DB $query
     */
    public function filterQuery($query)
    {
        /**
         * Queries
         *
         */
        if ($this->request->filled('scholar_id')) {
            $query = $query->where('user_id', $this->request->input('scholar_id'));
        }

        return $query;
    }

    /**
     * Custom formatting of data
     *
     * @param Illuminate\Support\Collection $items
     * @return array $result
     */
    public function formatData($items)
    {
        $result = [];

        foreach($items as $item) {
            $data = $this->formatItem($item);
            $data = array_merge($data, [
                'id' => $item->id,
                'date' => Carbon::parse($item->date)->format('m/d/Y'),
                'slp' => $item->slp,
                'mmr' => $item->mmr,
                'created_at' => $item->renderDate(),
                'deleted_at' => $item->deleted_at,
            ]);

            array_push($result, $data);
        }

        return $result;
    }

    /**
     * Build array data
     * @return array
     */
    protected function formatItem($item)
    {
        return [

        ];
    }

    public function fetchView($id = null)
    {
        $item = new ScholarDailyRecord;
        $images = [];

        if ($id) {
            $item = ScholarDailyRecord::withTrashed()->findOrFail($id);
            $item = $this->formatView($item);
        }

        return response()->json([
            'item' => $item,
        ]);
    }

    protected function formatView($item)
    {
        // $item->archiveUrl = $item->renderArchiveUrl();
        // $item->restoreUrl = $item->renderRestoreUrl();

        return $item;
    }
}
