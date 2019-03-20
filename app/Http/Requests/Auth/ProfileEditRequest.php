<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 12.02.19
 * Time: 21:46
 */

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

class ProfileEditRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|regex:/^\d+$/s',
        ];
    }

}