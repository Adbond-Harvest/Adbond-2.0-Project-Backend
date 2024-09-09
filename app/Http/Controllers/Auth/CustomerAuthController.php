<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Mail\EmailVerification;

use App\Services\CustomerService;
use App\Services\EmailService;

use App\Http\Resources\CustomerBriefResource;

use App\Http\Requests\Customer\Register;
use App\Http\Requests\Customer\VerifyEmail;
use Illuminate\Http\Request;

use App\Helpers;
use App\Http\Requests\Customer\Login;
use App\Http\Requests\Customer\ValidateEmailToken;
use App\Utilities;

class CustomerAuthController extends Controller
{
    private $customerService;
    private $emailService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->customerService = new CustomerService;
        $this->emailService = new EmailService;
        // $this->emailService = new EmailService;
        // $this->authService = new AuthService;
    }

    //Send email verification before registration
    public function sendVerificationMail(VerifyEmail $request)
    {
        try{
            $customer = $this->customerService->getCustomerByEmail($request->email);
            if($customer) return Utilities::error402('This email is already registered, please login');

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
     * Register a Customer.
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
            if(!$emailVerification || $emailVerification->verified==1) return Utilities::error402('Email has not been verified');
            $post['emailVerifiedAt'] = $emailVerification->updated_at;
            $customer = $this->customerService->save($post);
            $customer?->referer;
            $this->emailService->delete_email_tokens($post['email']);

            // $this->_save_and_send_email_verification_token($customer);
            return response()->json([
                'statusCode' => 200,
                'data' => new CustomerBriefResource($customer)
            ], 200);
        }catch(\Exception $e){
            if(isset($customer)) {
                $this->customerService->delete($customer);
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
        if (! $token = Auth::guard('customer')->attempt($credentials)) {
            return response()->json([
                'statusCode' => 402,
                'message' => 'Wrong Username or Password'
            ], 402);
        }
        Auth::guard('customer')->user()?->referer;
        $user = new CustomerBriefResource(Auth::guard('customer')->user());
        return response()->json([
            'statusCode' => 200,
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'token_expires_in' => Auth::guard('customer')->factory()->getTTL(), 
                'customer' => $user
            ]
        ], 200);
    }


    /**
     * Saves and sends email verification token
     *
     * @param Customer
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
