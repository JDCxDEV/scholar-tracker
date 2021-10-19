<?php

namespace App\Models\Users;

use Algolia\AlgoliaSearch\Http\Guzzle6HttpClient;
use App\Extenders\Models\BaseUser as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\Web\Auth\ResetPassword;
use App\Notifications\Web\Auth\VerifyEmail;
use Password;

use App\Models\Documents\Document;
use App\Models\Scholars\ScholarDailyRecord;
use App\Models\Scholars\ScholarType;
use App\Models\Usages\TimeUsage;
use GuzzleHttp\Client as GuzzleClient;

use Carbon\Carbon;
use Hash;
use Exception;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    const FILLABLE = ['first_name', 'last_name', 'email', 'birthday', 'gender', 'username', 'telephone_number', 'mobile_number', 'user_type', 'calling_code', 'ronin_address', 'scholar_type_id'];

    protected $casts = [
        'birthday' => 'date',
    ];

    protected $appends = [ 'mask_mobile_number' ];

    const USER = 0;
    const ADMIN = 1;

    /**
     * @Mutators
     */
    public function setBirthdayAttribute($value) {
        if (strtotime($value)) {
            $date = Carbon::parse($value);
            $this->attributes['age'] = $date->age;
        }

        $this->attributes['birthday'] = $value;
    }

    /**
     * Overrides default reset password notification
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token, $this->password));
        $this->password = Hash::make($this->password);
        $this->save();
    }

    /* Overrides default verification notification */
    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail);
    }

    public function device_tokens() {
        return $this->morphMany(DeviceToken::class, 'user');
    }

    public function providers() {
        return $this->morphMany(SocialiteProvider::class, 'user');
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }

    public function timeUsage() {
        return $this->hasMany(TimeUsage::class);
    }

    public function scholar_type() {
        return $this->belongsTo(ScholarType::class);
    }

    public function today_slp() {
        return $this->hasMany(ScholarDailyRecord::class)->latest();
    }

    public function daily_record() {
        return $this->hasMany(ScholarDailyRecord::class)->whereDate('date', Carbon::today());
    }

    public function yesterday_slp() {
        return $this->hasMany(ScholarDailyRecord::class)->whereDate('date', Carbon::today()->subDays(1));
    }

    public function overall_slp_record() {
        return $this->hasMany(ScholarDailyRecord::class);
    }

    /* Overrides default forgot password */
    public function broker() {
        return Password::broker('users');
    }

    /**
     * JWT Configurations
     */
    public function getJWTCustomClaims(): array {
        return [
            'guard' => 'web',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray() {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
        ];
    }

    /**
     * @Renders
     */
    public function isIncomplete() {
        return in_array(null, [
            $this->first_name,
            $this->last_name,
            $this->email,
            $this->username,
            $this->gender,
            $this->mobile_number,
            $this->birthday,
        ]);
    }

    /* User Verification Status */
    public function renderStatus($showLabel = true) {
        $result = $showLabel ? 'Unverified' : 'bg-danger';

        if ($this->email_verified_at) {
            $result = $showLabel ? 'Verified' : 'bg-success';
        }

        return $result;
    }

    public function renderShowUrl($prefix = 'admin') {
        if (in_array($prefix, ['web'])) {
            return route($prefix . '.profiles.show');
        }

        return route($prefix . '.users.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        if (in_array($prefix, ['web'])) {
            return;
        }

        return route($prefix . '.users.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        if (in_array($prefix, ['web'])) {
            return;
        }

        return route($prefix . '.users.restore', $this->id);
    }


    /**
     * @Getters
     */
    public static function getTypes() {
        return [
            ['value' => static::USER, 'label' => 'User', 'description' => '', 'class' => 'bg-info'],
            ['value' => static::ADMIN, 'label' => 'Admin', 'description' => '', 'class' => 'bg-secondary'],
        ];
    }

    public function renderType($column = 'label') {
        return static::renderConstants(static::getTypes(), $this->user_type, $column);
    }

    public function getSlpData() {
        $client = new GuzzleClient();
        $wallet = $this->ronin_address ? preg_replace('/[^A-Za-z0-9\-]/', '', $this->ronin_address) : '0xe1dxf29ex7ec04ae4c1xx6e496c072f18d999999';
        $wallet = trim($wallet, " ");

        if($wallet) {
            $address ='https://server.axie.watch/scholar?address=' . $wallet . '&pvp=true';

            while (true) {
                try {
                    $res = $client->get($address);

                    $status = $res->getStatusCode();

                    if($status == 200){

                        $data = json_decode($res->getBody(), true);
                        $scholar = $data['scholar'];

                        if($data['pvp']) {
                            $pvp = $data['pvp'];

                            $last_claim = Carbon::parse($scholar['last_claimed_item_at'])->format('m/d/Y g:i A');
                            $next_claim = Carbon::parse($scholar['last_claimed_item_at'])->addDays(14)->format('m/d/Y g:i A');

                            $ronin_slp = $scholar['blockchain_related']['balance'];
                            $total = $scholar['total'];
                            $ingame_slp = $scholar['total'] - $scholar['claimable_total'];
                            $today = $this->getTodaySlp($ingame_slp);

                            $data = [
                                'ingame_slp' => $ingame_slp,
                                'claimable_slp' => $scholar['claimable_total'],
                                'total_slp' => $total,
                                'ronin_slp' => $ronin_slp,
                                'next_claim' =>  $next_claim,
                                'last_claim' => $last_claim,
                                'today_slp' => $today,
                                'average_slp' => $this->getAverageDailySlp($scholar['total']),
                                'pvp' => $pvp,
                            ];

                            return $data;

                            break;
                        }

                        return false;
                    }

                    return false;

                }catch (Exception $e) {
                    return false;
                }
            }
        }
    }

    public function getPvpData($item) {
        $client = new GuzzleClient();
        $wallet = $item->ronin_address ? preg_replace('/[^A-Za-z0-9\-]/', '', $item->ronin_address) : '0x' ;
        $wallet = trim($wallet, " ");

        if($wallet) {
            $address ='https://axie-proxy.secret-shop.buzz/_basicStats/' . $wallet;
            $res = $client->get($address);
            $data = json_decode($res->getBody(), true);

            $data = $data['stats'];

            $data = [
                'win_total' => $data['win_total'],
                'draw_total' => $data['draw_total'],
                'lose_total' => $data['lose_total'],
                'elo' => $data['elo'],
                'rank' => $data['rank'],
                'name' => $data['name'],
            ];

            return $data;
        }
    }

    public function getScholarType() {

        $scholar_type = $this->scholar_type;


        if(!$scholar_type) {
            return [
                'daily' => 'N/A',
                'monthly' => 'N/A',
                'manager_share' => 'N/A',
                'scholar_share' => 'N/A',
            ];
        }

        switch ($scholar_type->type) {
            case 'percentage':
                return [
                    'daily' => 'N/A',
                    'monthly' => 'N/A',
                    'manager_share' => $scholar_type->manager_share,
                    'scholar_share' => $scholar_type->scholar_share,
                ];
            case 'quota':
                if($scholar_type->frequency == 'daily') {
                    return [
                        'daily' => $scholar_type->slp_required,
                        'monthly' => Carbon::now()->daysInMonth *  $scholar_type->slp_required,
                        'manager_share' => 'N/A',
                        'scholar_share' => 'N/A',
                    ];
                }
                if($scholar_type->frequency == 'monthly') {
                    return [
                        'daily' => 'N/A',
                        'monthly' => $scholar_type->slp_required,
                        'manager_share' => 'N/A',
                        'scholar_share' => 'N/A',
                    ];
                }
            break;
        }

    }

    /* claimable slp / last claim days difference = average */
    public function getAverageDailySlp($slp, $date = null) {

        /* System Average */
        $avg = $this->overall_slp_record->slice(1)->avg('slp');
        return $avg <= 0 ? 0 : $avg;

        // $days =  Carbon::parse(Carbon::now())->diffInDays($date);
        // return  $slp > 0 ? ($slp / $days) : 0;
    }

    public function getYesterDaySlp() {
        $result = $this->today_slp->first()->total_slp ?? 0;

        if($this->overall_slp_record->count() <= 2 && $result == 0) {
            $result = $this->today_slp->first()->total_slp ?? 0;
        }

        return $result;
    }

    public function getTodaySlp($current_slp) {
       $slp = $current_slp - $this->getYesterDaySlp();

       if($slp < 0) {
          $slp = (abs($slp) + $current_slp) - $this->getYesterDaySlp();
       }

        return $slp;
    }


    /**
     * Append mask number full path
     *
     * @return string $result
     */
    public function getMaskMobileNumberAttribute()
    {
        $user_num = $this->mobile_number;
        $lastTwoDigits = substr($user_num, -2, 2);
        $result = '*** *** **'.$lastTwoDigits;
        return $result;
    }
}
