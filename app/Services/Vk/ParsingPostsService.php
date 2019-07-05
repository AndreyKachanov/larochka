<?php

namespace App\Services\Vk;

use App\Events\PrivateTest;
use App\Events\SendPostToPusherWithoutQueue;
use App\Events\SendPostToPusherWithQueue;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Auth;

class ParsingPostsService
{
    private $client;
    public $accessToken;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->accessToken = config('vk.access_token');
    }

    /**
     * @param string $group
     * @param $countGroups
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkGroup(string $group, $countGroups): bool
    {
        $userId = Auth::id();
        $url = 'https://api.vk.com/method/resolveScreenName';
        $query = [
            'screen_name' => $group,
            'access_token' => $this->accessToken,
            'v' => '5.92'
        ];
        $delay = $countGroups > 3 ? 333.3 : 0;
        $response = $this->getResponseByGuzzleClient2($url, $query, $delay);
        if (!empty($response['response'])) {
            if ($response['response']['type'] === 'group') {
                if (Cache::has('parsing_vk_groups_live')) {
                    $arr = Cache::get('parsing_vk_groups_live');
                    $arr[$userId][] = [
                        'name' => $group,
                        'id' => $response['response']['object_id']
                    ];
                } else {
                    $arr[$userId][] = [
                        'name' => $group,
                        'id' => $response['response']['object_id']
                    ];
                }
                Cache::forever('parsing_vk_groups_live', $arr);
                return true;
            }
        }
        Cache::forget('parsing_vk_groups_live');
        return false;
    }

    /**
     * @param array $vkGroups
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //public function getGroups(array $vkGroups): array
    //{
    //    $arr = [];
    //    foreach ($vkGroups as $group) {
    //        try {
    //            $response = $this->client->request('GET', 'https://api.vk.com/method/resolveScreenName', [
    //                'delay' => (count($vkGroups) > 3) ? '334' : '0',
    //                'query' => [
    //                    'screen_name' => $group['name'],
    //                    'access_token' => $this->accessToken,
    //                    'v' => '5.92'
    //                ]
    //            ])->getBody()->getContents();
    //
    //            $json = json_decode($response, true);
    //            //если запрос прошел удачно, но вк вернул ошибку - останавливаем и выводим
    //            if (isset($json['error'])) {
    //                $error = $json['error'];
    //                $errorMsg = sprintf(
    //                    'Response vk has error. Error code - %d, error msg - %s',
    //                    $error['error_code'],
    //                    $error['error_msg']
    //                );
    //                dd($errorMsg);
    //            }
    //
    //            //если вернулся какой-то ответ
    //            if (!empty($json['response'])) {
    //                //если в ответе есть группы
    //                if ($json['response']['type'] === 'group') {
    //                    $arr[] = [
    //                        'name' => $group['name'],
    //                        'id'   => $json['response']['object_id']
    //                    ];
    //                }
    //            }
    //        } catch (Exception $e) {
    //            $errorMsg = sprintf(
    //                'Error during Guzzle request. %s. Line - %d. File - %s. (ParsingPostsService.php, 60+ line)',
    //                $e->getMessage(),
    //                $e->getLine(),
    //                $e->getFile()
    //            );
    //            dd($errorMsg);
    //        }
    //    }
    //
    //    return $arr;
    //}

    public function setParsingDataFromUser(int $userId, array $groupsFromVk, string $keywords, int $days): void
    {
        $keywords = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $keywords);
        if (Cache::has('parsing_vk_groups')) {
            $arr = Cache::get('parsing_vk_groups');
            $arr[$userId] = [
                'groups'   => $groupsFromVk,
                'keywords' => $keywords,
                'days' => $days
            ];
        } else {
            $arr = [
                $userId => [
                    'groups'   => $groupsFromVk,
                    'keywords' => $keywords,
                    'days' => $days
                ]
            ];
        }
        Cache::forever('parsing_vk_groups', $arr);
    }

    public function setParsingDataFromUser2(int $userId, array $groupsFromVk, string $keywords, int $days): void
    {
        $keywords = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $keywords);

        if (Cache::has('parsing_vk_groups')) {
            $arr = Cache::get('parsing_vk_groups');
            $arr[$userId] = [
                'groups'   => $groupsFromVk,
                'keywords' => $keywords,
                'days' => $days
            ];
        } else {
            $arr = [
                $userId => [
                    'groups'   => $groupsFromVk,
                    'keywords' => $keywords,
                    'days' => $days
                ]
            ];
        }
        Cache::forever('parsing_vk_groups', $arr);
    }

    /**
     * @param int $userId
     * @param array $groupsFromVk
     * @param string $keywords
     * @param int $days
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendDataToPusher(int $userId, array $groupsFromVk, string $keywords, int $days): void
    {
        $arr = [
            'user_id'  => $userId,
            'groups'   => $groupsFromVk,
            'keywords' => $keywords = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $keywords),
            'days'     => $days
        ];

        $posts = $this->getPosts($arr);
        //dd($posts);

        $moreThat = Carbon::now()->subDay($days)->startOfDay()->timestamp;
        $dataForPusher = $posts->where('date', '>=', $moreThat)
            ->sortByDesc('date')
            ->map(function ($item, $key) {
                $item['date'] = Carbon::createFromTimestamp($item['date'])->format('d.m.Y H:i:s');
                return $item;
            });

        $dataForPusher->each(function ($item, $key) use ($userId) {
            broadcast(new SendPostToPusherWithQueue($item, $userId));
        });
    }

    public function stopParsingFromUser(int $userId): void
    {
        if (Cache::has('parsing_vk_groups')) {
            $arrFromCache = Cache::get('parsing_vk_groups');
            unset($arrFromCache[$userId]);
            Cache::forever('parsing_vk_groups', $arrFromCache);
        }
    }

    /**
     * @param array $parsingData
     * @return Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPosts(array $parsingData): Collection
    {
        $url = 'https://api.vk.com/method/wall.search';
        $delay =  333.3;

        $data = [];
        foreach ($parsingData['groups'] as $group) {
            foreach ($parsingData['keywords'] as $keyword) {
                $query = [
                    'owner_id' => '-' .  $group['id'],
                    'query' => $keyword,
                    //'query' => implode(',', $parsingData['keywords']),
                    'access_token' => $this->accessToken,
                    'v' => '5.58',
                    'extended' => 1,
                    'count' => 100
                ];
                $response = $this->getResponseByGuzzleClient2($url, $query, $delay);
                //dump($response);

                if ($response['response']['count'] > 0) {
                    $items = $response['response']['items'];
                    $profiles = $response['response']['profiles'];
                    $groups = $response['response']['groups'];
                    foreach ($items as $item) {
                        //если не спам
                        if (iconv_strlen($item['text']) < 400) {
                            //ищем в массиве профайлов нужного юзера
                            $profile = array_filter($profiles, function ($arr) use ($item) {
                                return $arr['id'] === $item['from_id'];
                            });
                            $profile = array_shift($profile);
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
                            $temp['user_src'] = 'https://vk.com/id' . $profile['id'];
                            $temp['date'] = $item['date'] ?? null;
                            $temp['text'] = $item['text'] ?? null;
                            $temp['photo_50'] = $profile['photo_50'] ?? null;
                            $temp['group_photo_50'] = $responseGroup['photo_50'] ?? null;
                            $temp['group_src'] = 'https://vk.com/' . $responseGroup['screen_name'];
                            $temp['group_name'] = $responseGroup['name'];
                            $data[] = $temp;
                        }
                    }
                }
            }
        }
        return collect($data);
    }

    /**
     * @param string $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //private function getResponseByGuzzleClient(string $request): array
    //{
    //    usleep(300000);
    //    try {
    //        $response = $this->client->get($request)->getBody()->getContents();
    //        $json = json_decode($response, true);
    //        //если запрос прошел удачно, но вк вернул ошибку - останавливаем и выводим
    //        if (isset($json['error'])) {
    //            $error = $json['error'];
    //            $errorMsg = sprintf(
    //                'Response vk has error. Error code - %d, error msg - %s',
    //                $error['error_code'],
    //                $error['error_msg']
    //            );
    //            dd($errorMsg);
    //        }
    //    } catch (RequestException $e) {
    //        $errorMsg = sprintf(
    //            'Error during Guzzle request. %s. Line - %d. File - %s. (ParsingPostsService.php, 60+ line)',
    //            $e->getMessage(),
    //            $e->getLine(),
    //            $e->getFile()
    //        );
    //        dd($errorMsg);
    //    }
    //    return $json;
    //}

    /**
     * @param string $url
     * @param array $query
     * @param $delay
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getResponseByGuzzleClient2(string $url, array $query, $delay): array
    {
        $json = [];
        try {
            $response = $this->client->request('GET', $url, [
                'delay' => $delay,
                'query' => $query
            ])->getBody()->getContents();

            $json = json_decode($response, true);

            if (isset($json['error'])) {
                $error = $json['error'];
                $errorMsg = sprintf(
                    'Response vk has error. Error code - %d, error msg - %s. Class - %s, line - %d, query - %s',
                    $error['error_code'],
                    $error['error_msg'],
                    __CLASS__,
                    __LINE__,
                    $url
                );
                dd($errorMsg);
            }
        } catch (Exception $e) {
            $errorMsg = sprintf(
                'Error during Guzzle request. %s.  Class - %s, line - %d',
                $e->getMessage(),
                __CLASS__,
                __LINE__
            );
            dd($errorMsg);
        }
        return $json;
    }
}
