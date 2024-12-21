<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function user (Request $request)
    {
        return response()->json(new UserResource($request->user()));
    }

    public function edit(EditProfileRequest $request)
    {
        $request->user()->update($request->validated());
        return response()->json(new UserResource($request->user()));
    }
}
