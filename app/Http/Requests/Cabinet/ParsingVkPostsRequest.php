<?php

namespace App\Http\Requests\Cabinet;

use App\Services\Vk\ParsingPostsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ParsingVkPostsRequest extends FormRequest
{
    private $service;

    public function __construct(
        ParsingPostsService $service,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        $this->service = $service;
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        //dd($this->request->all());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        Cache::forget('parsing_vk_groups_live');
        $countGroups = count($this->request->get('groups'));

        return [
            //общий массив
            'keywords' => [
                'required',
                'string',
                'min:2',
                'max:300',
                'regex:/^[\/\a-zA-Zа-яА-ЯёЁ0-9, ]*$/u'
            ],
            'days' => [
                'required',
                'numeric',
                'min:0',
                'max:1000'
            ],
            'groups'     => 'required|array|min:1',
            //каждый элемент массива
            'groups.*.*' => [
                'required',
                'string',
                'distinct',
                'min:2',
                'max:100',
                function ($attribute, $value, $fail) use ($countGroups) {
                    if (!$this->service->checkGroup($value, $countGroups)) {
                        $fail('Vk group ' . $value . ' does not exist.');
                    }
                },
            ],
        ];
    }

    public function attributes()
    {
        $groupsKeyName = 'groups';
        $groupsRequest = $this->request->get($groupsKeyName);
        $attributesForGroups = $groupsRequest ? $this->setAttributesForGroups($groupsRequest, $groupsKeyName) : [];

        $attributes = [
            'groups' => 'Vk group name'
        ];

        $attributes = array_merge($attributesForGroups, $attributes);
        return $attributes;
    }

    public function messages()
    {
        return [
            'groups.required' => 'The :attribute field is required.',
            'groups.min' => 'The :attribute must be at least :min.',
        ];
    }

    private function setAttributesForGroups(array $groupsRequest, string $groupsKeyName): array
    {
        foreach ($groupsRequest as $key => $value) {
            $arr[$groupsKeyName . '.' . $key . '.name'] = 'Vk group name';
        }
        return $arr;
    }
}
