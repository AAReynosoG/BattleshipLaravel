<?php

namespace App\Http\Controllers;

use App\Mail\CodeVerification;
use App\Mail\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function login(Request $reques){
        $validator = Validator::make($reques->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'password.required' => 'Password is required'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 400);
        }

        $user = User::where('email', $reques->email)->first();

        if(!$user){
            return response()->json([
                'success' => false,
                'msg' => 'Usuario no encontrado'
            ], 404);
        }

        if(! $user || !Hash::check($reques->password, $user->password)){
            return response()->json([
                'success' => false,
                'msg' => 'Contraseña incorrecta'
            ], 401);
        }


        if($user->is_verified == false){
            return response()->json([
                'success' => false,
                'msg' => 'Correo no verificado'
            ], 403);
        }

        if($reques->has('codigo')) {

            if (is_null($user->{'2fa_code'}) || is_null($user->{'2fa_code_at'})) {
                return response()->json(['msg' => 'Aun no generas un código de verificación.', 'data' => $user], 405);
            }

            $minutosParaExpiracion = 5;

            $codigoValido = Carbon::now()->diffInMinutes($user->{'2fa_code_at'}) <= $minutosParaExpiracion;

            if (!$codigoValido) {
                return response()->json(['msg' => 'Codigo expirado.', 'data' => $user], 405);
            }

            if($this->verifyCode($reques->codigo, $user)){
                $token = $user->createToken('Accesstoken')->plainTextToken;
                $user->{'2fa_code'} = null;
                $user->{'2fa_code_at'} = null;
                return response()->json([
                    'msg' => 'Se ha logeado correctamente',
                    'data' => $user,
                    'jwt' => $token,
                ]);

            } else {
                return response()->json(['msg' => 'Codigo incorrecto.', 'data' => $user], 405);
            }

        } else {
            $this->getCode($user->id);
            return response()->json(['msg' => 'Código 2FA enviado. Por favor, verifícalo.', 'data' => $user], 201);
        }
    }

    function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|min:6|max:255|unique:users',
            'password' => 'required|min:6|max:255'
        ], [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre es muy corto',
            'name.max' => 'El nombre es muy largo',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email es invalido',
            'email.min' => 'El email es muy corto',
            'email.max' => 'El email es muy largo',
            'email.unique' => 'El email ya esta en uso',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña es muy corta',
            'password.max' => 'La contraseña es muy larga'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        Mail::to($user->email)->send(new EmailVerification($user));

        return response()->json([
            'status' => 'success',
            'message' => 'Register success'
        ], 200);
    }

    function getCode($userId){
        if(!$userId){
            return response()->json(['mensaje' => 'parametro no valido'], 404);
        }

        $user = User::find($userId);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $codigo = random_int(100000, 999999);
        $user->{'2fa_code'} = encrypt($codigo);
        $user->{'2fa_code_at'} = Carbon::now();
        $user->save();

        $this->sendVerifyCodeEmail($user->email, $codigo);

    }


    function sendVerifyCodeEmail($email, $codigo){

        try {
            Mail::to($email)->send((new CodeVerification($codigo))->build());
        }catch (\Exception $e){
            return response()->json(["success" => false, "message" => "Ha ocurrido un error interno."], 500);
        }

    }

    function verifyCode($codigo_ingresado, $user) {

        if (hash_equals((string)decrypt($user->{'2fa_code'}), (string)$codigo_ingresado)) {
            $user->{'2fa_code'} = null;
            $user->{'2fa_code_at'} = null;
            $user->save();
            return true;
        }

        return false;
    }

    public function logout(){

        $user = Auth::user();

        if (!$user) {
            return response()->json(['msg' => 'Usuario no encontrado'], 404);
        };

        $userFind = User::find($user->id);

        $user->currentAccessToken()->delete();

        return response()->json(['status' => true]);

    }
}
