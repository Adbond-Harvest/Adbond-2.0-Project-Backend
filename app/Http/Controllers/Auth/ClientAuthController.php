<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Mail\EmailVerification;

use App\Services\ClientService;
use App\Services\EmailService;

use App\Http\Resources\ClientBriefResource;

use App\Http\Requests\Client\Register;
use App\Http\Requests\Client\VerifyEmail;
use Illuminate\Http\Request;

use App\Helpers;
use App\Http\Requests\Login;
use App\Http\Requests\Client\ValidateEmailToken;
use App\Utilities;

class ClientAuthController extends Controller
{
    private $clientService;
    private $emailService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->clientService = new ClientService;
        $this->emailService = new EmailService;
        // $this->emailService = new EmailService;
        // $this->authService = new AuthService;
    }

    //Send email verification before registration
    public function sendVerificationMail(VerifyEmail $request)
    {
        try{
            $client = $this->clientService->getClientByEmail($request->email);
            if($client) return Utilities::error402('This email is already registered, please login');

            $emailToken = $this->emailService->saveEmailVerificationToken($request->email);
            $mail = Mail::to($request->email)->send(new EmailVerification($emailToken));
            // if (Mail::failures()) {
            //     return response()->json(['status' => 'fail', 'message' => 'Failed to send email.']);
            // }
            return Utilities::okay('Verification mail sent successfully');
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }

    //Verify the token sent to the email
    public function verifyEmailToken(ValidateEmailToken $request)
    {
        try{
            $data = $request->validated();
            $emailToken = $this->emailService->emailExists($data['email']);
            if($emailToken && $emailToken->verified) return Utilities::error402("Your email has been verified already, Go ahead and login");
            $response = $this->emailService->validateEmailToken($data);
            if($response['success']) {
                return Utilities::okay("Validation Successful");
            }else{
                return response()->json([
                    'statusCode' => 402,
                    'message' => $response['error']
                ], 402);
            }
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }

    /**
     * Register a Client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Register $request) {
        try{
            $post = $request->all();
            // if(isset($post['referal_code'])) {
            //     $user = $this->userService->getUserByRefererCode($post['referal_code']);
            //     if($user) $post['referer_id'] = $user->id;
            // }
            $emailVerification = $this->emailService->emailExists($post['email']);
            if(!$emailVerification || !$emailVerification->verified) return Utilities::error402('Email has not been verified');
            $post['emailVerifiedAt'] = $emailVerification->updated_at;
            $client = $this->clientService->save($post);
            $client?->referer;
            $this->emailService->delete_email_tokens($post['email']);

            // $this->_save_and_send_email_verification_token($client);
            return response()->json([
                'statusCode' => 200,
                'data' => new ClientBriefResource($client)
            ], 200);
        }catch(\Exception $e){
            if(isset($client)) {
                $this->clientService->delete($client);
            }
            Log::stack(['project'])->info($e->getMessage().' in '.$e->getFile().' at Line '.$e->getLine());
            return response()->json([
                'statusCode' => 500,
                'message' => 'An error occured while trying to perform this operation, Please try again later or contact support'
            ], 500);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Login $request){
        $credentials = $request->only('email', 'password');
        if (! $token = Auth::guard('client')->attempt($credentials)) {
            return response()->json([
                'statusCode' => 402,
                'message' => 'Wrong Email or Password'
            ], 402);
        }
        Auth::guard('client')->user()?->referer;
        $user = new ClientBriefResource(Auth::guard('client')->user());
        return response()->json([
            'statusCode' => 200,
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'token_expires_in' => Auth::factory()->getTTL(), 
                'client' => $user
            ]
        ], 200);
    }


    /**
     * Saves and sends email verification token
     *
     * @param Client
     * @return null
     */
    private function _save_and_send_email_verification_token($email)
    {
        try{
            $emailToken = $this->emailService->saveEmailVerificationToken($email);
            //dd($emailToken);
            Mail::to($email)->send(new EmailVerification($emailToken));
        }catch(\Exception $e){
            //$this->emailService->delete_email_token($emailToken);
            Log::stack(['project'])->info($e->getMessage().' in '.$e->getFile().' at Line '.$e->getLine());
        }
    }
}