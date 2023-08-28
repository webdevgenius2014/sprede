<?php

namespace App\Http\Controllers\Api\Auth;

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
            'name' => 'required|regex:/^[a-zA-Z\s]*$/',
            'email' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'terms' => 'required|in:1'
        ],[
            'email.required' => 'Email or Phone number is required.',
            'terms.in' => 'Please accept Terms and Conditions.'
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
                    'terms' => 'required|in:1'
                ],
                [
                    'email.required' => 'Email or Phone number is required.',
                    'email.numeric' => 'Phone number must be numeric.',
                    'email.digits' => 'Phone number must be of 10 digits.',
                    'email.unique' => 'Mobile number already taken.',
                    'terms.in' => 'Please accept Terms and Conditions.'
                ]
            );
        }elseif(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                $email = $data['email'];
                // dd("yes");
                $validator = Validator::make($request->all(), 
                [
                    'email' => 'required|string|email:rfc,dns|max:100|unique:users',
                    'name' => 'required|string|between:2,100',
                    'password' => 'required|string|confirmed|min:6',
                    'terms' => 'required|in:1'
                ], 
                [
                    'email.required' => 'Email or Phone number is required.',
                    'terms.in' => 'Please accept Terms and Conditions.'
                ]);
        }else{
            $validator = Validator::make($request->all(), 
            [
                'email' => 'required|string|email:rfc,dns|max:100|unique:users',
                'name' => 'required|string|between:2,100',
                'password' => 'required|string|confirmed|min:6',
                'terms' => 'required|in:1'
            ], 
            [
                'email.required' => 'Email or Phone number is required.',
                'email.email' => 'Please provide correct email address.',
                'terms.in' => 'Please accept Terms and Conditions.'
            ]);
            if($validator->fails()){
                return response()->json($validator->errors(), 400);
            }
        }

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if (!array_key_exists("news_updates", $data)) {
           $data['news_updates'] = 0;
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
                            'message' => 'Please check your login credentials.',
                        ], 401);
        }

        // if (! $token = auth()->login($user)) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
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
        return response()->json([
                                'status'=>200,
                                'message' => 'User successfully signed out',
                                'user' => auth()->user()
                            ]);
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
    
        return response()->json(['status'=>200, 
                                 'message' => 'success',
                                 'data' => auth()->user()
                               ]);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        // dd(auth()->factory()->getTTL() * 60);
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    // public function login_view(){
    //     dd("in login view");
    // }
}
