<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StrategicPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'post_id'      => $this->post_id,
            'post_date'    => $this->post_date?->toISOString(),
            'post_modified'=> $this->post_modified?->toISOString(),
            'status'       => $this->status,
            'link'         => $this->link,
            'title'        => $this->title,
            'excerpt'      => $this->excerpt,
            'content_text' => $this->content_text,
            'categories'   => $this->categories,
            'slug'         => $this->slug,

            'images' => [
                'featured'  => $this->image_url,
                'content_1' => $this->content_image_1,
                'content_2' => $this->content_image_2,
            ],

            'links' => [
                'execution_report'   => $this->execution_report,
                'association_website'=> $this->association_website,
            ],

            'drive' => [
                'image' => [
                    'file_id'       => $this->image_drive_file_id,
                    'link'          => $this->image_drive_link,
                    'file_name'     => $this->image_file_name,
                    'upload_status' => $this->image_upload_status,
                ],
                'content_image_1' => [
                    'file_id'       => $this->content_image_1_drive_file_id,
                    'link'          => $this->content_image_1_drive_link,
                    'file_name'     => $this->content_image_1_file_name,
                    'upload_status' => $this->content_image_1_upload_status,
                ],
                'content_image_2' => [
                    'file_id'       => $this->content_image_2_drive_file_id,
                    'link'          => $this->content_image_2_drive_link,
                    'file_name'     => $this->content_image_2_file_name,
                    'upload_status' => $this->content_image_2_upload_status,
                ],
            ],

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 