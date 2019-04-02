<?php

namespace App\Services\Vk;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ParsingPostsService
{
    private $client;
    private $accessToken;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->accessToken = config('vk.access_token');
    }

    public function setParsingDataFromUser($userId, $groupsFromVk, $keywords): void
    {
        //Cache::forget('parsing_vk_groups');
        //dd();

        //$keywords = explode(',', $keywords);
        $keywords = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $keywords);

        if (Cache::has('parsing_vk_groups')) {
            $arr = Cache::get('parsing_vk_groups');
            $arr[$userId] = [
                'groups' => $groupsFromVk,
                'keywords' => $keywords
            ];
        } else {
            $arr = [
                $userId => [
                    'groups' => $groupsFromVk,
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

    public function getGroups(array $vkGroups): array
    {
        $arr = [];
        foreach ($vkGroups as $group) {
            $groupName = $group['name'];
            $client = new Client();
            $request = "https://api.vk.com/method/resolveScreenName?screen_name=$groupName&access_token=$this->accessToken&v=5.92";

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

            if (!empty($json['response'])) {
                if ($json['response']['type'] == 'group') {
                    $arr[] = [
                        'name' => $groupName,
                        'id' => $json['response']['object_id']
                    ];
                }
            }
        }
        return $arr;
    }
}
