<?php

namespace App\Services\Vk;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Exception;

class ParsingPostsService
{
    private $client;
    public $accessToken;

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

    public function getPosts(array $parsingData): Collection
    {
        $data = [];
        foreach ($parsingData['groups'] as $group) {
            $request = sprintf(
                'https://api.vk.com/method/wall.search?owner_id=-%d&query=%s&access_token=%s&v=5.58&extended=1&count=100',
                $group['id'],
                implode(',', $parsingData['keywords']),
                $this->accessToken
            );
            $json = $this->getResponseByGuzzleClient($request);
            if ($json['response']['count'] > 0) {
                $items = $json['response']['items'];
                $profiles = $json['response']['profiles'];
                $groups = $json['response']['groups'];
                foreach ($items as $item) {
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
                    $temp['text'] = (iconv_strlen($item['text']) < 1000
                            ? $item['text']
                            : 'post > 1000 symbols')
                        ?? null;
                    $temp['photo_50'] = $profile['photo_50'] ?? null;
                    $temp['group_photo_50'] = $responseGroup['photo_50'] ?? null;
                    $temp['group_src'] = 'https://vk.com/' . $responseGroup['screen_name'];
                    $temp['group_name'] = $responseGroup['name'];
                    $data[] = $temp;
                }
            }
        }
        return collect($data);
    }

    private function getResponseByGuzzleClient(string $request): array
    {
        try {
            $response = $this->client->get($request)->getBody()->getContents();
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
