<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guild\StoreGuildRequest;
use App\Http\Resources\GuildResource;
use App\Models\Guild;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GuildController extends Controller
{
    public function store(StoreGuildRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $guild = Guild::query()->create($validated);

        return response()->json(new GuildResource($guild), Response::HTTP_CREATED);
    }
}
