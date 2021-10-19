<?php

namespace App\Http\Controllers\Admin\Users;

use App\Extenders\Controllers\FetchController as Controller;
use App\Models\Scholars\ScholarType;
use App\Models\Users\User;
use Carbon\Carbon;

class UserFetchController extends Controller
{
    /**
     * Set object class of fetched data
     *
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new User;
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
        if ($this->request->filled('email_verified_at')) {
            if ($this->request->input('email_verified_at') == 1) {
                $query = $query->whereNotNull('email_verified_at');
            } else {
                $query = $query->whereNull('email_verified_at');
            }
        }

        if ($this->request->filled('scholar_type')) {
            $query = $query->where('scholar_type_id', $this->request->input('scholar_type'));
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
            array_push($result, $data);
        }

        return $result;
    }

    protected function formatItem($item)
    {
        $data = $item->getSlpData($item);
        $pvp = $data['pvp'];

        return [
            'id' => $item->id,
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'ronin_address' => $item->ronin_address,

            /* User SLP */
            'ingame_slp' => $data['ingame_slp'],
            'total_slp' => $data['total_slp'],
            'next_claim' =>  $data['next_claim'],
            'last_claim' => $data['last_claim'],
            'today_slp' => $data['today_slp'],
            'average_slp' => $data['average_slp'],
            'claimable_slp' => $data['claimable_slp'],

            /* User PVP */
            'win_total' => @$pvp['win_total'],
            'draw_total' => @$pvp['draw_total'],
            'lose_total' =>  @$pvp['lose_total'],
            'elo' => @$pvp['elo'],
            'rank' => @$pvp['rank'],
            'name' => @$pvp['name'],
            'win_rate' => $item->win_rate,

            'created_at' => $item->renderDate(),
            'showUrl' => $item->renderShowUrl(),
            'deleted_at' => $item->deleted_at,
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
        ];
    }

    public function fetchView($id = null) {
        $item = new User;
        $types = ScholarType::all();

        if ($id) {
            $item = User::withTrashed()->findOrFail($id);
            $item = $this->formatView($item);
        }

        return response()->json([
            'item' => $item,
            'types' => $types
        ]);
    }

    protected function formatView($item)
    {
        $data = $item->getSlpData($item);
        $pvp = $data['pvp'];


        /* User slp data */
        $item->ingame_slp = $data['ingame_slp'];
        $item->total_slp = $data['total_slp'];
        $item->ronin_slp = $data['ronin_slp'];
        $item->next_claim = $data['next_claim'];
        $item->last_claim = $data['last_claim'];
        $item->computed_today_slp = $data['today_slp'];
        $item->average_slp = $data['average_slp'];

         /* User PVP */
        $item->win_total = @$pvp['win_total'];
        $item->draw_total = @$pvp['draw_total'];
        $item->lose_total = @$pvp['lose_total'];
        $item->elo = @$pvp['elo'];
        $item->rank = @$pvp['rank'];
        $item->name = @$pvp['name'];

        /* User Type */
        $requirment = $item->getScholarType();

        $item->daily = $requirment['daily'];
        $item->monthly = $requirment['monthly'];
        $item->manager_share = $requirment['manager_share'];
        $item->scholar_share = $requirment['scholar_share'];

        $item->overall_farm_slp = $item->overall_slp_record->sum('slp');

        $item->archiveUrl = $item->renderArchiveUrl();
        $item->restoreUrl = $item->renderRestoreUrl();
        $item->renderImage = $item->renderImagePath();


        return $item;
    }
}
