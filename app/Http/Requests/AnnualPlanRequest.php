<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnualPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('annual_plan');
        $required = $this->isMethod('POST') ? 'required' : 'sometimes|required';

        return [
            'post_id'      => "{$required}|integer|unique:annual_plans,post_id,{$id}",
            'title'        => "{$required}|string|max:255",
            'slug'         => "{$required}|string|max:255",
            'excerpt'      => 'nullable|string',
            'content_text' => 'nullable|string',
            'link'         => 'nullable|url|max:500',
            'status'       => 'nullable|in:publish,draft,pending',
            'category_id'  => 'nullable|integer',
            'published_at' => 'nullable|date',

            'image_url'           => 'nullable|url|max:500',
            'image_file_name'     => 'nullable|string|max:255',
            'image_drive_file_id' => 'nullable|string|max:255',
            'image_drive_link'    => 'nullable|url|max:500',
            'image_upload_status' => 'nullable|in:pending,uploaded,failed',

            'content_image_1_url'              => 'nullable|url|max:500',
            'content_image_1_file_name'        => 'nullable|string|max:255',
            'content_image_1_drive_file_id'    => 'nullable|string|max:255',
            'content_image_1_drive_link'       => 'nullable|url|max:500',
            'content_image_1_upload_status'    => 'nullable|in:pending,uploaded,failed',
        ];
    }
}