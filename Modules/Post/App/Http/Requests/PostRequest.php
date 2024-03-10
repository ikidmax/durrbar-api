<?php

namespace Modules\Post\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'title' => 'required|unique:posts|max:255',
            'publish' => 'required',
            'content',
            'cover_url',
            'author_id',
            'meta_title',
            'total_views',
            'description',
            'total_shares',
            'meta_keywords',
            'total_favorites',
            'meta_description'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
