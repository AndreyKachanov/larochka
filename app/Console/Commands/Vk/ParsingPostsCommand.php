<?php

namespace App\Console\Commands\Vk;

use App\Jobs\Vk\ParsingPosts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ParsingPostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:vk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parsing posts of vk.com groups';

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
        if (!Cache::has('parsing_vk_groups')) {
            $this->info('key parsing_vk_groups not exists in cache');
            return false;
        }

        if (empty($arrFromCache = Cache::get('parsing_vk_groups'))) {
            $this->info('empty array parsing_vk_groups');
            return false;
        }

        //ParsingPosts::dispatch(array_key_first($arr), $arr[array_key_first($arr)])->delay(Carbon::now()->addSecond(3));
        //dd($arrFromCache);
        foreach ($arrFromCache as $key => $value) {
            ParsingPosts::dispatch($key, $value);
        }
        return true;
    }
}
