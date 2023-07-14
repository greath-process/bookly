<?php


namespace App\Http\Requests\Book;


use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'author' => 'required|string',
            'image' => 'required|string',
            'year' => 'required|string',
            'volume_id' => 'required|string',
            'isbn' => 'numeric',
        ];
    }
}
