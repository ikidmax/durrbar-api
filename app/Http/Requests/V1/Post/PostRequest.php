<?php

namespace App\Http\Requests\V1\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // 'id'   => 'required',
            'description' => 'required|string',
            'duration' => 'required|string',
            'content' => 'required|string',
            'title' => 'required|string',
            'publish' => 'required|string',
            'metaTitle' => 'nullable|string',
            'metaKeywords' => 'nullable|string',
            'metaDescription' => 'nullable|string',
            // 'tags' => 'array',
            // 'tags.*' => 'string|exists:tags,id', // Validate each tag ID exists
            // 'coverUrl' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust as needed
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cover'              => $this->coverUrl,
            'author_id'          => $this->authorId,
            'meta_title'         => $this->metaTitle,
            'total_views'        => $this->totalViews,
            'total_shares'       => $this->totalShares,
            'meta_keywords'      => $this->metaKeywords,
            'total_favorites'    => $this->totalFavorites,
            'meta_description'   => $this->metaDescription
        ]);
    }
}
