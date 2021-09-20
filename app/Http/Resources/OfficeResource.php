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
            'user' => UserResource::make($this->user),
            'images' => UserResource::collection($this->images),
            'tags' => UserResource::collection($this->tags),

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
