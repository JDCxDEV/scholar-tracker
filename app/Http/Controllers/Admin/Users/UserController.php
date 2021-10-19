<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Users\UserStoreRequest;

use Excel;
use Carbon\Carbon;

use App\Http\Controllers\Admin\Users\UserFetchController;
use App\Exports\Users\UserExport;

use App\Models\Users\User;
use Exception;
use GuzzleHttp\Client as GuzzleClient;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.users.index', [

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create', [
            //
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $item = User::store($request, null, User::FILLABLE);

        $message = "You have successfully created {$item->renderName()}";
        $redirect = $item->renderShowUrl();

        return response()->json([
            'message' => $message,
            'redirect' => $redirect,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = User::withTrashed()->findOrFail($id);

        return view('admin.users.show', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserStoreRequest $request, $id)
    {
        $item = User::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->renderName()}";

        $item = User::store($request, $item, User::FILLABLE);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = User::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->renderName()}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Admin  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = User::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->renderName()}",
        ]);
    }

    public function export(Request $request)
    {
        $controller = new UserFetchController;

        $request = $request->merge(['ids_only' => 1]);

        $data = $controller->fetch($request);
        $ids = $data->getData()->items;

        $message = 'Exporting data, please wait...';

        if (!count($ids)) {
            throw ValidationException::withMessages([
                'items' => 'No users found.',
            ]);
        }

        if (!$request->ajax()) {
            $items = User::whereIn('id', $ids)->get();
            return Excel::download(new UserExport($items), 'Users_' . Carbon::now()->toDateTimeString() . '.xls');
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        }
    }

    public function getTotalAxies()
    {
        $scholars = User::whereNotNull('ronin_address')->get();

        $total = 0;
        $axie_ids = [];

        if($scholars->count()) {
            foreach ($scholars as $key => $scholar) {
                $client = new GuzzleClient();
                $wallet = $scholar->ronin_address ? preg_replace('/[^A-Za-z0-9\-]/', '', $scholar->ronin_address) : '0x';
                $wallet = trim($wallet, " ");
                $client = new GuzzleClient();
                try {
                    $res = $client->get('https://api.lunaciaproxy.cloud/_axies/' . $wallet);
                    $status = $res->getStatusCode();

                    if($status == 200){
                        $res = json_decode($res->getBody(), true);
                        $total += $res['available_axies']['total'];

                        if(count($res['available_axies']['results'])) {
                            foreach($res['available_axies']['results'] as $key => $axie) {
                                $axie_ids[] = @$axie['id'];
                            }
                        }
                    }
                }catch (Exception $e) {
                    echo $e;
                }
            }
        }
        return response()->json(
            [
                'success' => true,
                'axie_ids' => $axie_ids,
                'total' => $total,
            ]
        );
    }

    public function getCardsData()
    {
        $scholars = User::whereNotNull('ronin_address')->get();
        $today_slp = 0;
        $total_slp = 0;

        if($scholars->count()) {

            foreach ($scholars as $key => $scholar) {
                $data = $scholar->getSlpData($scholar);
                $today_slp += $data['today_slp'];
                $total_slp += $data['total_slp'];
            }
        }

        return response()->json(
            [
                'scholars' => $scholars->count(),
                'today_slp' => $today_slp,
                'total_slp' => $total_slp,
                'success' => true,
            ]
        );
    }

    public function getScholars()
    {
        $scholars = User::whereNotNull('ronin_address')->get();

        return response()->json(
            [
                'scholars' => $scholars,
                'total_scholar' => $scholars->count(),
                'success' => true,
            ]
        );
    }

    public function updateScholarBattleLogs()
    {
        $scholars = User::whereNotNull('ronin_address')->where('battle_logs_updated_at', '<', Carbon::now()->subHours(3)->toDateTimeString())->get();

        foreach ($scholars as $key => $scholar) {
            $id = $scholar->ronin_address;
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
                ->update(
                    [
                        'win' => $win,
                        'lose' => $lose,
                        'draw' => $draw,
                        'win_rate' => $winrate,
                        'battle_logs_updated_at' => Carbon::now(),
                    ]
                );
        }

        return response()->json(
            [
                'scholars' => $scholars->count(),
                'success' => true,
            ]
        );
    }

}
