<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class OfficeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user'      => UserResource::make($this->whenLoaded('user')),
            'images'    => ImageResource::collection($this->whenLoaded('images')),
            'tags'      => TagResource::collection($this->whenLoaded('tags')),
            'reservation_count' => $this->resource->reservations_count ?? 0,

            $this->merge(Arr::except(parent::toArray($request), [
                'created_at',
                'updated_at',
                'email',
                'email_verified_at'
            ]))
        ];

    }
}

////return parent::toArray($request);
//return Arr::except(parent::toArray($request),[
//    'user_id', 'created_at', 'updated_at',
//    'deleted_at'
//]);
