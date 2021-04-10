<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller{
    public $loginAfterSignup=true;

    public function register(Request $request){
        $user = new User();
        $user->nombre=$request->nombre;
        $user->correo_telefono=$request->correo_telefono;
        $user->usuario=$request->usuario;
        $user->password=bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignup){
            return $this->login($request);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $user,
        ], 200);
    }

    public function login(Request $request) {
        $input = $request->only('usuario', 'password');
        $jwt_token = null;
        
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'status' => 'Credenciales Invalidas',
                'message' => 'Correo o contrasenia no validos.',
            ], 401);
        }
        
        return response()->json([
            'status' => 'ok',
            'token' => $jwt_token,
        ]);
    }

    public function logout(Request $request) {
        $this->validate($request, [
            'token' => 'required'
        ]);
        
        try {
            JWTAuth::invalidate($request->token);
        
            return response()->json([
                'status' => 'ok',
                'message' => 'Cierre de sesión exitoso.'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'status' => 'unknown_error',
                'message' => 'Al usuario no se le pudo cerrar la sesión.'
            ], 500);
        }
    }

    public function getAuthUser(Request $request){
        $this->validate($request, [
            'token' => 'required',
        ]);

        $user = JWTAuth::authenticate($request->token);
        return response()->json(['user' => $user]);
    }

    protected function jsonResponse($data, $code = 200){
        return response()->json($data, $code,[
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ], JSON_UNESCAPED_UNICODE);
    }
}
