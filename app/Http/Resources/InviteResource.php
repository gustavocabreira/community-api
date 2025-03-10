<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'expires_at' => $this->expires_at->format('Y-m-d H:i:s'),
            'guild_id' => $this->guild_id,
            'guild' => $this->whenLoaded('guild'),
        ];
    }
}
