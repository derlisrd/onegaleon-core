<?php

namespace App\Http\Controllers\OneGaleonApp;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function login(Request $req)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), [], 400);
            }
            $credentials = $req->only('email', 'password');
            $token = JWTAuth::attempt($credentials);
            if ($token) {
                $refreshToken = JWTAuth::claims(['is_refresh' => true])->attempt($credentials);
                return $this->sendResponse([
                    'tokenRaw' => $token,
                    'token' => 'Bearer ' . $token,
                    'refreshTokenRaw' => $refreshToken,
                    'refreshToken' => 'Bearer ' . $refreshToken,
                ], 'Ingresado');
            }
            return response()->json([
                'success' => false,
                'message' => 'Error de credenciales.',
                'results' => null
            ], 401);
        } catch (\Throwable $th) {
            throw $th;
            Log::error($th);
        }
    }

    public function register(Request $req)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }
            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => bcrypt($req->password)
            ]);
            $token = JWTAuth::fromUser($user);
            $refreshToken = JWTAuth::claims(['is_refresh' => true])->fromUser($user);
            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente',
                'results' => [
                    'tokenRaw' => $token,
                    'token' => 'Bearer ' . $token,
                    'refreshTokenRaw' => $refreshToken,
                    'refreshToken' => 'Bearer ' . $refreshToken,
                ]
            ], 201);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function me()
    {
        try {
            $token = JWTAuth::check(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'token' => $token,
                'message' => 'valid'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['success' => false, 'message' => 'Error'], 400);
        }
    }

    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 400);
        }

        return response()->json(compact('user'));
    }

    // User logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['success' => true, 'message' => 'Successfully logged out']);
    }

    public function refreshToken(Request $req)
    {
        $refreshToken = str_replace('Bearer ', '', $req->header('Authorization'));

        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            if (!$payload->get('is_refresh')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token.',
                    'results' => null
                ], 401);
            }

            $newToken = JWTAuth::refresh($refreshToken);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully.',
                'results' => [
                    'tokenRaw' => $newToken,
                    'token' => 'Bearer ' . $newToken
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not refresh token.',
                'results' => null
            ], 401);
        }
    }
}
