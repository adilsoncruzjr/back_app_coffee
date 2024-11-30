<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Register a new user.
     * Método para cadastrar um novo usuário.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso.',
            'user' => $user,
        ], 201);
    }

    /**
     * Fazer login e retornar um token de autenticação.
     */
    public function login(Request $request)
    {

        Log::info('Login attempt', ['email' => $request->email]);


        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);


        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Invalid login attempt', ['email' => $request->email]);
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 401);
        }


        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;


        Log::info('User logged in successfully', ['user_id' => $user->id, 'token' => $token]);

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user_id' => $user->id,
        ]);
    }

    public function getUserById($id)
    {

        $user = User::find($id);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }


        return response()->json([
            'user' => $user
        ], 200);
    }

    public function updateUser(Request $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }


        $validatedData = $request->validate([
            'name' => 'nullable|string|min:3|max:255',
            'cpf' => 'nullable|string|size:11',
            'contact_phone' => 'nullable|string|min:10|max:15',
            'address' => 'nullable|string|min:5',
        ]);


        if (isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }

        if (isset($validatedData['cpf'])) {
            $user->cpf = $validatedData['cpf'];
        }

        if (isset($validatedData['contact_phone'])) {
            $user->contact_phone = $validatedData['contact_phone'];
        }

        if (isset($validatedData['address'])) {
            $user->address = $validatedData['address'];
        }


        $user->save();

        return response()->json([
            'message' => 'User information updated successfully.',
            'user' => $user
        ], 200);
    }

    /**
     * Obter os dados do usuário autenticado.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }


    public function getOrdersId($id)

    {

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }


        $ordersId = $user->orders_id ? json_decode($user->orders_id, true) : [];

        return response()->json([
            'orders_id' => $ordersId
        ], 200);
    }


    public function addOrderId(Request $request, $id)
    {

        $request->validate([
            'order_id' => 'required|string'
        ]);


        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }


        $ordersId = $user->orders_id ? json_decode($user->orders_id, true) : [];


        $ordersId[] = $request->order_id;


        $user->orders_id = json_encode($ordersId);
        $user->save();

        return response()->json([
            'message' => 'Order ID added successfully.',
            'orders_id' => $ordersId
        ], 200);
    }


    public function logout(Request $request)
    {
        try {

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while logging out.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function syncOrdersForAllUsers()
    {

        $users = User::all();

        foreach ($users as $user) {

            $orders = $user->orders()->pluck('id')->toArray();


            $user->orders_id = json_encode($orders);
            $user->save();
        }

        return response()->json([
            'message' => 'Orders synced successfully for all users.'
        ], 200);
    }
}
