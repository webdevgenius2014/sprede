<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;
use Hash;
use App\Http\Requests\Api\RegistrationRequest;
use App\Http\Requests\Api\LoginRequest;
use JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct() {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'terms' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $mobile = null;
        $email = null;

        if( is_numeric($data['email']) ){
            $mobile = $data['email'];
            $validator = Validator::make($request->all(), 
                [
                    'email' => 'required|digits:10|numeric|unique:users,mobile',
                    'name' => 'required|string|between:2,100',
                    'password' => 'required|string|confirmed|min:6',
                    'terms' => 'required'
                ],
                [
                    'email.required' => 'Email or Phone number is required.',
                    'email.numeric' => 'Phone number must be numeric.',
                    'email.digits' => 'Phone number must be of 10 digits.',
                    'email.unique' => 'Mobile number already taken.'
                ]
            );
        }else{
            if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                $email = $data['email'];
                // dd("yes");
                $validator = Validator::make($request->all(), 
                [
                    'email' => 'required|string|email|max:100|unique:users',
                    'name' => 'required|string|between:2,100',
                    'password' => 'required|string|confirmed|min:6',
                    'terms' => 'required'
                ], 
                [
                    'email.required' => 'Email or Phone number is required.'
                ]);
            }else{
                $validator = Validator::make($request->all(), 
                [
                    'email' => 'required|string|email|max:100|unique:users',
                    'name' => 'required|string|between:2,100',
                    'password' => 'required|string|confirmed|min:6',
                    'terms' => 'required'
                ], 
                [
                    'email.required' => 'Email or Phone number is required.'
                ]);
            }
        }

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if (!array_key_exists("news_updates", $data)) {
           $data['news_updates'] = '0';
        }

        $user = User::create([
                    'name' => $data['name'],
                    'email' => $email,
                    'mobile' => $mobile,
                    'password' => Hash::make($data['password']),
                    'terms' => $data['terms'],
                    'news_updates' => $data['news_updates']
                ]);

        $token = Auth::login($user);
        return $this->createNewToken($token);
        // $credentials = $request->only('email', 'password');
        // $token = auth()->attempt($credentials);
        // return response()->json([
        //     'status' => 200,
        //     'message' => 'success',
        //     'data' => $this->createNewToken($token)
        // ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        $data = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ],
        [
            'email.required' => 'Email or Mobile number is required.'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $password = $data['password'];
        if( is_numeric($data['email']) ){
            $mobile = $data['email'];
            $validator = Validator::make($request->all(), 
                [
                    'email' => 'required|digits:10|numeric'
                ],
                [
                    'email.required' => 'Phone number is required',
                    'email.numeric' => 'Phone number must be numeric',
                    'email.digits' => 'Phone number must be of 10 digits'
                ]
            );
            $user = User::where('mobile', $mobile)->first();

        }else{
            if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){

                $email = $data['email'];
                $user = User::where('email', $email)->first();

            }else{
                $validator = Validator::make($request->all(), 
                [
                    'email' => 'required|string|email|max:100|unique:users',
                ],
                [
                    'email.required' => 'Email or Phone number is required'
                ]);
            }
        }

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // if (! $token = auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        if($user != null){
            if(Hash::check($password, $user->password)){
                $token = auth()->login($user);
                if($token){
                    return $this->createNewToken($token);
                }
            }else{
                return response()->json([
                                        'status' => 400,
                                        'message' => 'Please check your password.',
                                    ], 401);
            }
            
        }else{
            return response()->json([
                            'error' => 'Unauthorized',
                            'status' => 400,
                            'message' => 'failed',
                        ], 401);
        }

        if (! $token = auth()->login($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // if (! $token = auth()->attempt($validator->validated())) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        return $this->createNewToken($token);
    }
    

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request) {

        // if(auth()->user()){
        //     // dd("yes");
        //     if($request->bearerToken()){
        //         return response()->json(['status'=>200, 
        //             'message' => 'success',
        //             'data' => auth()->user()
        //         ]);
        //     }else{
        //         return response()->json(['status' => 400,
        //                                 'message' => 'failed',
        //                             ]);
        //     }
        // }else{
        //     dd("no");
        //     // dd(auth()->check());
        //     return response()->json(['status' => 400,
        //                              'message' => 'failed',
        //                              'data' => 'Please Login to access'
        //                             ]);
        // }

        if(auth()->user() && $request->bearerToken()){

            return response()->json(['status'=>200, 
                                     'message' => 'success',
                                     'data' => auth()->user()
                                    ]);
        }else{
            return response()->json(['status' => 400,
                                     'message' => 'failed',
                                     'data' => 'Please Login to access'
                                    ]);
        } 
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
