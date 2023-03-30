<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $input = $request->all();
        $accessToken = env('REGISTER_TOKEN');
        if(empty($input['token']) || $input['token'] != $accessToken) {
            $response = [
                'success' => false,
                'message' => 'You are not authorized'
            ];
            return response()->json($response, 404);
        }

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['id'] = $user->id;
        $success['name'] = $user->name;
        $success['token'] = $user->createToken('MyApp')->plainTextToken;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User Register Successfully'
        ];

        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['id'] = $user->id;
            $success['name'] = $user->name;
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            
            $response = [
                'success' => true,
                'data' => $success,
                'message' => 'User Login Successfully'
            ];
            return response()->json($response, 200);
        }
        else{
            $response = [
                'success' => false,
                'message' => 'Invalid Credential'
            ];
            return response()->json($response);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $response = [        
            'success' => true,
            'message' => 'User logged out successfully.'
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::with('organizations', 'folders')->find($id);

        if (!$user) {
            $response = [
                'success' => false,
                'message' => 'User not found'
            ];
            return response()->json($response, 404);
        }

        $response = [
            'success' => true,
            'data' => $user
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        if ($id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            $response = [
                'success' => false,
                'message' => 'User not found'
            ];
            return response()->json($response, 404);
        }
    
        $input = $request->only(['name', 'email', 'password', 'password_hint']);
    
        if (isset($input['password'])) {
            $input['password'] = bcrypt($input['password']);
        }
    
        $user->update($input);
    
        $response = [
            'success' => true,
            'data' => $user,
            'message' => 'User updated successfully.'
        ];
    
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            $response = [
                'success' => false,
                'message' => 'User not found'
            ];
            return response()->json($response, 404);
        }

        $user->delete();

        $response = [
            'success' => true,
            'message' => 'User deleted successfully'
        ];

        return response()->json($response, 200);
    }
}
