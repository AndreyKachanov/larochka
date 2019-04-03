<?php

namespace App\Services\Vk;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Exception;

class ParsingPostsService
{
    private $client;
    private $accessToken;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->accessToken = config('vk.access_token');
    }

    public function checkGroup(string $group): bool
    {
        $request = sprintf(
            'https://api.vk.com/method/resolveScreenName?screen_name=%s&access_token=%s&v=5.92',
            $group,
            $this->accessToken
        );

        $json = $this->getResponseByGuzzleClient($request);
        //если вернулся какой-то ответ
        if (!empty($json['response'])) {
            return $json['response']['type'] === 'group';
        }

        return false;
    }

    public function getGroups(array $vkGroups): array
    {
        $arr = [];
        foreach ($vkGroups as $group) {
            $request = sprintf(
                'https://api.vk.com/method/resolveScreenName?screen_name=%s&access_token=%s&v=5.92',
                $group['name'],
                $this->accessToken
            );

            $json = $this->getResponseByGuzzleClient($request);

            //если вернулся какой-то ответ
            if (!empty($json['response'])) {
                //если в ответе есть группы
                if ($json['response']['type'] === 'group') {
                    $arr[] = [
                        'name' => $group['name'],
                        'id'   => $json['response']['object_id']
                    ];
                }
            }
        }
        return $arr;
    }

    public function setParsingDataFromUser($userId, $groupsFromVk, $keywords): void
    {
        //Cache::forget('parsing_vk_groups');
        //dd();
        $keywords = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $keywords);

        if (Cache::has('parsing_vk_groups')) {
            $arr = Cache::get('parsing_vk_groups');
            $arr[$userId] = [
                'groups'   => $groupsFromVk,
                'keywords' => $keywords
            ];
        } else {
            $arr = [
                $userId => [
                    'groups'   => $groupsFromVk,
                    'keywords' => $keywords
                ]
            ];
        }
        Cache::forever('parsing_vk_groups', $arr);
    }

    public function stopParsingFromUser($userId): void
    {
        if (Cache::has('parsing_vk_groups')) {
            $arrFromCache = Cache::get('parsing_vk_groups');
            unset($arrFromCache[$userId]);
            Cache::forever('parsing_vk_groups', $arrFromCache);
        }
    }

    private function getResponseByGuzzleClient(string $request): array
    {
        try {
            $response = $this->client->get($request)->getBody()->getContents();
            //$response = $this->client->get($request)->getBody()->getContents();
            $json = json_decode($response, true);
            //если запрос прошел удачно, но вк вернул ошибку - останавливаем и выводим
            if (isset($json['error'])) {
                $error = $json['error'];
                $errorMsg = sprintf(
                    'Response vk has error. Error code - %d, error msg - %s',
                    $error['error_code'],
                    $error['error_msg']
                );
                dd($errorMsg);
            }
        } catch (Exception $e) {
            $errorMsg = sprintf(
                'Error during Guzzle request. %s. Line - %d. File - %s. (ParsingPostsService.php, 60+ line)',
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            );
            dd($errorMsg);
        }
        return $json;
    }
}

