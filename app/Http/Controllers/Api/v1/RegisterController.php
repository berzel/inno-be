<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisterController extends Controller
{
    /**
     * Register a new user
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::create($request->validated());
            Auth::login($user);

            return response()->json([
                'token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'user' => new UserResource($user)
            ], HttpResponse::HTTP_CREATED);
        });
    }
}
