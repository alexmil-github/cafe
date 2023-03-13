<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserListResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Return_;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $user = User::where([
            'login' => $request->login,
            'password' => $request->password,
            'status' => 'working'

//            ['login', '=', $request->login],
//            ['password', '=', $request->password],

        ])->first();

        if ($user) {
            $user->api_token = Str::random(18);
            $user->save();
            return [
                'data' => [
                    'user_token' => $user->api_token
                ]
            ];

        } else {
            return [
                'error' => [
                    'code' => 401,
                    'message' => 'Authentication failed'
                ]
            ];
        }


    }

    public function logout()
    {
        $user = Auth::user();
        $user->api_token = null;
        $user->save();

        return [
            'data' => [
                'message' => 'logout'
            ]
        ];
    }

    public function index()
    {
//        $users = User::all();
//        $result = [];
//
//        foreach ($users as $key => $user) {
//            $result[$key] = [
//                'id' => $user->id,
//                'name' => $user->name,
//                'login' => $user->login,
//                'status' => $user->status,
//                'group' => Role::find($user->role_id)->name,
//            ];
//        }
//        return ['data' => $result];

        return UserListResource::collection(User::all());

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'string',
            'patronymic' => 'string',
            'login' => 'required|string|unique:users',
            'password' => 'required|string',
            'photo_file' => 'image|mimes:jpg,jpeg,png',
            'role_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails())
            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ]
            ], 422);

        $user = User::create([
                'photo_file' => $request->photo_file ? $request->photo_file->store('public/photos') : null,
            ] + $request->all()
        );

        return response()->json([
            'data' => [
                'id' => $user->id,
                'status' => 'created'
            ]
        ])->setStatusCode(201, 'Created');

    }
}
