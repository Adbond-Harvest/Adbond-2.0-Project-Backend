<?php

namespace app\Services;

use Mail;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;


use app\Models\Customer;
use app\Models\PasswordResetToken;
use app\Models\ClientPasswordResetToken;
use app\Enums\PasswordTypes;

/**
 * password service class
 */
class PasswordService
{
    private $error;

    public function genCode($email, $type)
    {
        try{
            do{
                $token = '';
                for($i=0; $i<6; $i++) {
                    $token .= mt_rand(0, 9);
                }
                //$token = Str::random(4);
                $signature = hash('md5', $token);
                $exists = $this->getToken($email, $token, $type);
            } while ($exists);
            return ['token'=>$token, 'signature'=>$signature];
            //return $token;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * Validate the Alphanumeric code sent by the user
     *
     * @param array data of password reset token
     * 
     * @return Array
     *
     */
    public function validateEmailToken($data)
    {
        try{
            $emailExists = $this->emailExists($data['email'], $data['type']);
            if(!$emailExists) return ['success'=>false, 'error' => 'Email does not exist for token reset'];
            $passwordToken = $this->getToken($data['email'], $data['token'], $data['type']);
            if(!$passwordToken) {
                return [
                    'error' => "Invalid token",
                    'success' => false
                ]; 
            }
            if($passwordToken->verified) {
                return [
                    'error' => "This token has been used and verified before",
                    'success' => false
                ];
            }
            if(Carbon::now()->greaterThan($passwordToken->expires_at)) {
                return [
                    'error' => "The Token has expired",
                    'success' => false
                ];
            }
            $passwordToken->verified = true;
            $passwordToken->update();
            // $customer = Customer::findOrFail($data['email']);
            // $customer->activated = 1;
            // $customer->email_verified_at = Carbon::now();
            // $customer->update();
            // $this->delete_email_tokens($data['email']);
            //$emailToken->delete();
            return [
                "success" => true
            ];
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function delete_email_token($email, $token, $type)
    {
        $tokenObj = $this->getToken($email, $token, $type);
        if($tokenObj) {
            $token->delete();
        }
    }

    public function clearToken($token, $type)
    {
        $verifyToken = ($type == PasswordTypes::USER->value) ?  PasswordResetToken::findOrFail($token->id) : ClientPasswordResetToken::findOrFail($token->id);
        //delete password verify token
        
        if($verifyToken) $verifyToken->delete();
    }

    public function delete_email_tokens($email, $type)
    {
        $tokens = ($type == PasswordTypes::USER->value) ?  PasswordResetToken::where('email', $email)->get() : ClientPasswordResetToken::where('email', $email)->get();
        if($tokens->count()) {
            foreach($tokens as $token) {
                $token->delete();
            }
        }
    }

    private function clearExpiredTokens($type)
    {
        $tokens = ($type == PasswordTypes::USER->value) ?  PasswordResetToken::all() : ClientPasswordResetToken::all();
        if($tokens->count()) {
            foreach($tokens as $token) {
                if(Carbon::now()->greaterThan($token->expires_at)) {
                    $token->delete();
                }
            }
        }
    }

    public function getToken($email, $token, $type)
    {
        //$this->clearExpiredTokens();
        $model = ($type == PasswordTypes::USER->value) ?  PasswordResetToken::where('email', $email) : ClientPasswordResetToken::where('email', $email);
        return $model->where('token_signature', hash('md5', $token))->orderBy("created_at", "DESC")->first();
    }

    public function emailExists($email, $type)
    {
        return ($type == PasswordTypes::USER->value) ?  PasswordResetToken::where('email', $email)->orderBy("id", "DESC")->first() : ClientPasswordResetToken::where('email', $email)->orderBy("id", "DESC")->first();
    }

    public function savePasswordResetToken($email, $type)
    {
        // delete any tokens generated for this email before if it exists
        // $this->delete_email_tokens($email, $type);
        
        $signatureToken = $this->genCode($email, $type);
        try{
            $model = ($type == PasswordTypes::USER->value) ?  new PasswordResetToken : new ClientPasswordResetToken;
            $model->email = $email;
            $model->token_signature = $signatureToken['signature'];
            $model->expires_at = Carbon::now()->addMinutes(30);
            $model->save();
            return $signatureToken['token'];
        }catch(\Exception $e){
            throw $e;
        }
    }
}