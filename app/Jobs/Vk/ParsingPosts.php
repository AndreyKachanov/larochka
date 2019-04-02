<?php

namespace App\Jobs\Vk;

use App\Events\PrivateTest;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $accessToken = config('vk.access_token');
        //dd($this->parsingData['groups']);
        $keywords = implode(',', $this->parsingData['keywords']);
        $data = [];
        foreach ($this->parsingData['groups'] as $group) {
            $groupId = $group['id'];
            $client = new Client();
            $request = "https://api.vk.com/method/wall.search?owner_id=-$groupId&query=$keywords&access_token=$accessToken&v=5.58&extended=1&count=100";
            try {
                $response = $client->get($request)->getBody()->getContents();
                $json = json_decode($response, true);
                if (isset($json['error'])) {
                    $error = $json['error'];
                    dd('Response vk. error_code -' . $error['error_code'] . ', error_msg - ' . $error['error_msg']);
                }
            } catch (\Exception $e) {
                dd("Error - " . $e->getMessage() . ', line - ' . $e->getLine() . ' File - ' . $e->getFile());
            }
            //dd($json);
            $items = $json['response']['items'];
            if (count($items) > 0) {
                $profiles = $json['response']['profiles'];
                //dd($profiles);
                $groups = $json['response']['groups'];

                foreach ($items as $item) {
                    //ищем в массиве профайлов нужного юзера
                    $profile = array_filter($profiles, function ($arr) use ($item) {
                        return $arr['id'] === $item['from_id'];
                    });

                    //dd($profile);
                    //получаем содержимое найденного ключа (т.е. данные самого первого ключа)
                    //$profile = $profile[array_key_first($profile)];
                    $profile = array_shift($profile);
                    //dd($profile);
                    //dump($profile);

                    $responseGroup = array_filter($groups, function ($arr) use ($item) {
                        return $arr['id'] === -$item['owner_id'];
                    });

                    $responseGroup = array_shift($responseGroup);

                    $temp['group_screen_name'] = $group['name'] ?? null;
                    $temp['first_name'] = $profile['first_name'] ?? null;
                    $temp['last_name'] = $profile['last_name'] ?? null;
                    $temp['screen_name'] = $profile['screen_name'] ?? null;
                    $temp['id'] = $item['id'] ?? null;
                    $temp['group_id'] = $item['from_id'] ?? null;
                    $temp['user_src'] = 'https://vk.com/id' .  $profile['id'];
                    $temp['date'] = $item['date'] ?? null;
                    $temp['text'] = ((iconv_strlen($item['text']) < 1000) ? $item['text'] : '>1000 symbols') ?? null;
                    $temp['photo_50'] = $profile['photo_50'] ?? null;
                    $temp['group_photo_50'] = $responseGroup['photo_50'] ?? null;
                    $temp['group_src'] = 'https://vk.com/' . $responseGroup['screen_name'];
                    $temp['group_name'] = $responseGroup['name'];
                    $data[] = $temp;
                }
            }
        }

        usort($data, function ($a, $b) {
            return ($a['date'] < $b['date']);
        });

        //dd($data);

        //$allowKeys = ['date' => '', 'from_id' => '', 'text' => ''];
        //
        foreach ($data as $one) {
            //$data = array_intersect_key($item, $allowKeys);
            $one['date'] = Carbon::createFromTimestamp($one['date'])->addHour()->format('d.m.Y H:i:s');

            broadcast(new PrivateTest($one, $this->userId));
        }
    }
}
