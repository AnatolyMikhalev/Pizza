<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
//    public function user() {
//        return response()->json(User::get(), 200);
//    }
//    public function index(): \Illuminate\Http\JsonResponse
//    {
//        return response()->json(User::get(), 200);
//    }
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return User::all();
    }
}
