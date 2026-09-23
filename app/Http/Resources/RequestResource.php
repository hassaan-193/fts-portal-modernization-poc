<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date_time' => $this->date_time,
            'user' => $request->user()->first(['id','name','email']),
            'name' => $this->name,
            'note' => $this->note,
            'comments' => $this->comments,
            'status' => $this->status,
            'files' => $this->media_files(),
            'created_at' => $this->created_at,
        ];
    }
}
