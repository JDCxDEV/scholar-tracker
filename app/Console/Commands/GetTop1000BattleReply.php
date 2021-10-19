<?php

namespace App\Console\Commands;

use App\Models\Scholars\ScholarDailyRecord;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;
use App\Models\AxieAnalytics\BattleRecord;

class GetTop1000BattleReply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:battle-record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get 1000 player battle reply.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $client = new GuzzleClient();
        $offset_player = 0;
        while($offset_player < 1000) {
            $address = 'https://game-api.skymavis.com/game-api/leaderboard?client_id=0xc4a9b090e90b78aa7cb769cb5a354ad73a974725&offset='. $offset_player .'&limit=100';
            $res = $client->get($address);
            $players = json_decode($res->getBody(), true);
            $insert = [];

            foreach ($players['items'] as $key => $player) {
                $offset = 0;
                $limit = 100;

                while(true) {
                    $client = new GuzzleClient();
                    $address = 'https://game-api.skymavis.com/game-api/clients/' . $player['client_id'] . '/battles?offset='. $offset .'&limit='. $limit .'&battle_type=0';
                    $res = $client->get($address);
                    $data = json_decode($res->getBody(), true);
                    if(count($data['items'])) {
                        $insert = [];
                        foreach ($data['items'] as $key => $battle) {
                            $battle['created_at'] = Carbon::parse($battle['created_at']);
                            BattleRecord::updateOrCreate(['id'=> $battle['id']],$battle);
                            $insert[] = $battle;

                        }
                        echo $player['name'] . ' - ' . count($insert) . PHP_EOL;
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
