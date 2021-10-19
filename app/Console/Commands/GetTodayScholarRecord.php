<?php

namespace App\Console\Commands;

use App\Models\Scholars\ScholarDailyRecord;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;

class GetTodayScholarRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:daily-record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all the daily records of the scholars';

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
        while (true) {
            try {
                $users = User::whereNotNull('ronin_address')->doesnthave('daily_record')->get();
                echo 'total users :' . $users->count() . PHP_EOL;
                if($users->count()) {
                    foreach ($users as $key => $user) {

                        echo 'call api for - ' . $user->renderName() . PHP_EOL ;
                        $data = $user->getSlpData($user);
                        $pvp = @$data['pvp'];

                        $item = [
                            'user_id' => $user->id,
                            'ronin_address' => $user->ronin_wallet,
                            'total_slp' => $data['ingame_slp'],
                            'rank' => @$pvp['rank'] ?? 0,
                            'mmr' => @$pvp['elo'] ?? 0,
                            'total_matches' => 0,
                            'slp' => $data['today_slp'],
                            'date' => Carbon::now()->subDay(1)
                        ];

                        /* Save the scholar daily record */
                        ScholarDailyRecord::create($item);
                    }
                }

                break;
            }
            catch (Exception $e) {
                echo $e;
            }
            // sleep 200ms to give the MySQL server time to come back up
            usleep(200000);
        }
    }
}
