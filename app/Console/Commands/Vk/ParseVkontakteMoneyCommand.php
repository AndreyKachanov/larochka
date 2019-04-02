<?php

namespace App\Console\Commands\Vk;

use App\Jobs\CurrencyEchange;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ParseVkontakteMoneyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:money';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse money from Vkontakte';

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
    public function handle(): bool
    {
        if (Cache::has('parsing_currency_exchange')) {
            $arr = Cache::get('parsing_currency_exchange');
            //dd($arr[array_key_first($arr)]);
            if (count($arr) > 0) {
                //CurrencyEchange::dispatch(array_key_first($arr), $arr[array_key_first($arr)])->delay(Carbon::now()->addSecond(3));
                foreach ($arr as $key => $value) {
                    CurrencyEchange::dispatch($key, $value);
                }
                return true;
            }
        } else {
            $this->info('empty parsing_currency_exchange key in cache');
            return false;
        }
    }
}
