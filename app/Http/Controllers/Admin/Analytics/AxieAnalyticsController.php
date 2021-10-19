<?php

namespace App\Http\Controllers\Admin\Analytics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Scholars\ScholarDailyRecord;
use Carbon\Carbon;

use App\Models\Users\User;
use App\Models\AxieAnalytics\BattleRecord;
use GuzzleHttp\Client as GuzzleClient;
class AxieAnalyticsController extends Controller
{

    public function fetchUserGraphLine(Request $request, $id) {

        $days = $request->days;
        $last_month = $request->last_month;
        $type = $request->data_type ? 'slp' : 'mmr';

        $days_arr = [];
        $record = [];

        if($last_month || !$days ) {
            $lastDayofPreviousMonth = Carbon::now()->subMonth()->endOfMonth();
            $days = $lastDayofPreviousMonth->daysInMonth;
        }

        /* Set the date range and get all the data on every date */
        for ($i = 0; $i < $days; $i++) {
            if($last_month) {
                $date = Carbon::parse($lastDayofPreviousMonth)->subDay($i)->format('Y-m-d');
                $display_date = Carbon::parse($lastDayofPreviousMonth)->subDay($i)->format('m/d');
            }else {
                $date = Carbon::now('UTC')->subDay($i)->format('Y-m-d');
                $display_date = Carbon::now('UTC')->subDay($i)->format('m/d');
            }

            $days_arr[] = $display_date;
            $record[] = ScholarDailyRecord::where('user_id', $id)->where('date', $date)->sum($type) ?? 0;
        }

        /* Get Average SLP/MMR */
        $start = Carbon::parse(reset($days_arr))->format('Y-m-d');
        $end = Carbon::parse(end($days_arr))->format('Y-m-d');
        $average = ScholarDailyRecord::where('user_id', $id)
                ->where('date', '>=', $end)
                ->where('date', '<=', $start)
                ->average($type);

        /* Get Today Record */
        $user = User::find($id)->getSlpData();
        $today = $type == 'slp' ? $user['today_slp'] : @$user['pvp']['elo'];

        $record = array_reverse($record);
        $record[array_key_last($record)] = $today;

        return response()->json([
            'days' => array_reverse($days_arr),
            'record' => $record,
            'total' => array_sum($record),
            'average' => $average ?? 0,
            'status' => 'success'
        ]);
    }

    public function fetchBattleLogs($id) {

        $client = new GuzzleClient();
        $wallet = $id;
        $address = 'https://game-api.skymavis.com/game-api/clients/' . $wallet . '/battles?offset=0&limit=100&battle_type=0';

        $res = $client->get($address);
        $data = json_decode($res->getBody(), true);

        $battles = [];
        $win = 0;
        $lose = 0;
        $draw = 0;
        $winrate = 0;

        foreach ($data['items'] as $key => $battle) {
            if($battle['battle_type'] == 0) {

                $battles[] = $battle;

                $result = $battle['winner'];

                if($result == 2) {
                    $draw += 1;
                }

                if($result == 0 && $battle['first_client_id'] == $id) {
                    $win += 1;
                }

                if($result == 1 && $battle['first_client_id'] != $id) {
                    $win += 1;
                }

               if($result == 0 && $battle['first_client_id'] != $id) {
                    $lose += 1;
                }

                if($result == 1 && $battle['first_client_id'] == $id) {
                    $lose += 1;
                }
            }
        }

        $matches = $win + $lose + $draw;

        if($matches) {
            $winrate = ($win / $matches) * 100;
        }
        $users = User::where('ronin_address', $id)
            ->where('battle_logs_updated_at', '>', Carbon::now()->subHours(3)->toDateTimeString())
            ->update(
                [
                    'win' => $win,
                    'lose' => $lose,
                    'draw' => $draw,
                    'win_rate' => $winrate,
                    'battle_logs_updated_at' => Carbon::now(),
                ]
            );

        return response()->json([
            'data' => $battles,
            'win' => $win,
            'lose' => $lose,
            'draw' => $draw,
            'winrate' => $winrate,
            'status' => 'success'
        ]);
    }

    public function fetchLine(Request $request) {

        $days = $request->days;
        $last_month = $request->last_month;

        $days_arr = [];
        $slp_record = [];

        if($last_month) {
            $lastDayofPreviousMonth = Carbon::now()->subMonth()->endOfMonth();
            $days = $lastDayofPreviousMonth->daysInMonth;
        }

        for ($i = 0; $i < $days; $i++) {
            if($last_month) {
                $date = Carbon::parse($lastDayofPreviousMonth)->subDay($i)->format('Y-m-d');
                $display_date = Carbon::parse($lastDayofPreviousMonth)->subDay($i)->format('m/d');
            }else {
                $date = Carbon::now('UTC')->subDay($i)->format('Y-m-d');
                $display_date = Carbon::now('UTC')->subDay($i)->format('m/d');
            }
            $days_arr[] = $display_date;
            $slp_record[] = ScholarDailyRecord::where('date', $date)->sum('slp');
        }

        $scholars = User::whereNotNull('ronin_address')->get();
        $today_slp = 0;

        if($scholars->count()) {

            foreach ($scholars as $key => $scholar) {
                $data = $scholar->getSlpData($scholar);
                $today_slp += $data['today_slp'];
            }
        }

        $slp_record = array_reverse($slp_record);
        $slp_record[array_key_last($slp_record)] = $today_slp;

        return response()->json([
            'days' => array_reverse($days_arr),
            'slp_record' => $slp_record,
            'status' => 'success'
        ]);
    }

    public function fetchPie(Request $request) {

        $days = $request->days;
        $last_month = $request->last_month;

        $scholars = [];

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->subDay($days)->format('Y-m-d');

        if($last_month) {
            $startDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
            $endDate = Carbon::parse($startDate)->subDay(30)->format('Y-m-d');
        }

        $scholar_list = User::whereHas('overall_slp_record')->get();

        foreach ($scholar_list as $key => $scholar) {
            $name = $scholar->renderName();
            $slp = ScholarDailyRecord::where('user_id', $scholar->id)
                ->whereBetween('date', [$endDate, $startDate])
                ->sum('slp');

            $scholars[] = ['name' => $name, 'value' => $slp];
        }

        return response()->json([
            'scholars' => $scholars,
            'status' => 'success'
        ]);
    }

    public function fetchUserBattleLogs($id) {
        $offset = 0;
        $limit = 100;

        $client = new GuzzleClient();
        $wallet = $id;
        $offset_player = 0;
        while($offset_player < 1000) {
            $address = 'https://game-api.skymavis.com/game-api/leaderboard?client_id=0xc4a9b090e90b78aa7cb769cb5a354ad73a974725&offset='. $offset_player .'&limit=100';
            $res = $client->get($address);
            $players = json_decode($res->getBody(), true);
            $insert = [];

            foreach ($players['items'] as $key => $player) {;
                while(true) {
                    $client = new GuzzleClient();
                    $wallet = $id;
                    $address = 'https://game-api.skymavis.com/game-api/clients/' . $player['client_id'] . '/battles?offset='. $offset .'&limit='. $limit .'&battle_type=0';
                    $res = $client->get($address);
                    $data = json_decode($res->getBody(), true);
                    if(count($data['items'])) {
                        foreach ($data['items'] as $key => $battle) {
                            if($battle['battle_type'] == 0) {
                                $battle['created_at'] = Carbon::parse($battle['created_at']);
                                echo $battle['name'] . '\r\n';
                                BattleRecord::updateOrCreate(['id'=> $battle['id']],$battle);
                                $insert[] = $battle;
                            }
                        }
                    }else {
                        break;
                    }
                    $offset += 100;
                }
            }

            $offset_player += 100;
        }
    }
}
