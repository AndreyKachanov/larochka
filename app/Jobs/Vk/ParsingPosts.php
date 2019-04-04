<?php

namespace App\Jobs\Vk;

use App\Events\PrivateTest;
use App\Services\Vk\ParsingPostsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;

class ParsingPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $parsingData;

    public function __construct($userId, $parsingData)
    {
        $this->userId = $userId;
        $this->parsingData = $parsingData;
    }

    public function handle(ParsingPostsService $service): void
    {
        $dataForPusher = $service
            ->getPosts($this->parsingData)
            ->sortByDesc('date')
            ->map(function ($item, $key) {
                $item['date'] = Carbon::createFromTimestamp($item['date'])->addHour()->format('d.m.Y H:i:s');
                return $item;
            });

        $dataForPusher->each(function ($item, $key) {
            broadcast(new PrivateTest($item, $this->userId));
        });
    }
}
