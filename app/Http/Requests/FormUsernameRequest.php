<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class FormUsernameRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'userName' => 'string',
        ];
    }
}
