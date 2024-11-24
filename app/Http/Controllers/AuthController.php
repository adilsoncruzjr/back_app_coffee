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

        // Criar usuário
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
    // Log para registrar os dados da requisição
    Log::info('Login attempt', ['email' => $request->email]);

    // Validação dos dados do login
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Tentar autenticar o usuário
    if (!Auth::attempt($request->only('email', 'password'))) {
        Log::warning('Invalid login attempt', ['email' => $request->email]);
        return response()->json([
            'message' => 'Invalid login credentials.',
        ], 401);
    }

    // Usuário autenticado, gerando o token
    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;

    // Log para registrar sucesso de login e o token gerado
    Log::info('User logged in successfully', ['user_id' => $user->id, 'token' => $token]);

    return response()->json([
        'message' => 'Login successful.',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user_id' => $user->id,  // Adicionando o user_id na resposta
    ]);
}

    public function getUserById($id)
    {
        // Tentando encontrar o usuário pelo ID
        $user = User::find($id);

        // Verificando se o usuário foi encontrado
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Retornando os dados do usuário
        return response()->json([
            'user' => $user
        ], 200);
    }

    public function updateUser(Request $request, $id)
{
    // Encontrando o usuário pelo ID
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    // Validando os campos enviados
    $validatedData = $request->validate([
        'name' => 'nullable|string|min:3|max:255', // Validação do nome
        'cpf' => 'nullable|string|size:11', // Validação do CPF (11 caracteres)
        'contact_phone' => 'nullable|string|min:10|max:15', // Validação do telefone
        'address' => 'nullable|string|min:5', // Validação do endereço
    ]);

    // Atualizando os campos, se fornecidos
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

    // Salvando as alterações no banco de dados
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

    // Método para obter os valores de orders_id
    public function getOrdersId($id)
    {
        // Encontrando o usuário pelo ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Decodificando o JSON da coluna orders_id
        $ordersId = $user->orders_id ? json_decode($user->orders_id, true) : [];

        return response()->json([
            'orders_id' => $ordersId
        ], 200);
    }

    // Método para adicionar um novo valor em orders_id
    public function addOrderId(Request $request, $id)
    {
        // Validando o valor recebido
        $request->validate([
            'order_id' => 'required|string' // Valida se o novo ID de pedido foi enviado
        ]);

        // Encontrando o usuário pelo ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Decodificando o JSON existente ou inicializando como um array vazio
        $ordersId = $user->orders_id ? json_decode($user->orders_id, true) : [];

        // Adicionando o novo valor à lista
        $ordersId[] = $request->order_id;

        // Atualizando a coluna orders_id com o novo JSON
        $user->orders_id = json_encode($ordersId);
        $user->save();

        return response()->json([
            'message' => 'Order ID added successfully.',
            'orders_id' => $ordersId
        ], 200);
    }

    // Método para logout
    public function logout(Request $request)
    {
        try {
            // Revoga o token atual do usuário autenticado
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
    // Obter todos os usuários
    $users = User::all();

    foreach ($users as $user) {
        // Obter as ordens do usuário
        $orders = $user->orders()->pluck('id')->toArray();

        // Atualizar a coluna orders_id com a lista de IDs das ordens
        $user->orders_id = json_encode($orders);
        $user->save();
    }

    return response()->json([
        'message' => 'Orders synced successfully for all users.'
    ], 200);
}


}
