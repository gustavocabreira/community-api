<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guild\Invite\StoreInviteRequest;
use App\Http\Resources\InviteResource;
use App\Models\Guild;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GuildInviteController extends Controller
{
    public function store(Guild $guild, StoreInviteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $invite = $guild->invites()->create($validated);

        return response()->json(new InviteResource($invite), Response::HTTP_CREATED);
    }
}
