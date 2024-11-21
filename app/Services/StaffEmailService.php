<?php

namespace app\Services;

use Mail;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;


use app\Models\Client;
use app\Models\StaffEmailVerificationToken;

/**
 * email service class
 */
class StaffEmailService
{
    private $error;

    public function genCode($email)
    {
        try{
            do{
                $token = '';
                for($i=0; $i<6; $i++) {
                    $token .= mt_rand(0, 9);
                }
                //$token = Str::random(4);
                $signature = hash('md5', $token);
                $exists = $this->getToken($email, $token);
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
            $emailExists = $this->emailExists($data['email']);
            if(!$emailExists) return ['success'=>false, 'error' => 'Email does not exist for verification'];
            $emailToken = $this->getToken($data['email'], $data['token']);
            if(!$emailToken) {
                return [
                    'error' => "Invalid token",
                    'success' => false
                ]; 
            }
            if(Carbon::now()->greaterThan($emailToken->expires_at)) {
                return [
                    'error' => "The Token has expired",
                    'success' => false
                ];
            }
            $emailToken->verified = true;
            $emailToken->update();
            // $client = Client::findOrFail($data['email']);
            // $client->activated = 1;
            // $client->email_verified_at = Carbon::now();
            // $client->update();
            // $this->delete_email_tokens($data['email']);
            //$emailToken->delete();
            return [
                "success" => true
            ];
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function delete_email_token($email, $token)
    {
        $tokenObj = $this->getToken($email, $token);
        if($tokenObj) {
            $token->delete();
        }
    }

    public function clearToken($token)
    {
        $verifyToken = StaffEmailVerificationToken::findOrFail($token->id);
        //delete password verify token
        
        if($verifyToken) $verifyToken->delete();
    }

    public function delete_email_tokens($email)
    {
        $tokens = StaffEmailVerificationToken::where('email', $email)->get();
        if($tokens->count()) {
            foreach($tokens as $token) {
                $token->delete();
            }
        }
    }

    private function clearExpiredTokens()
    {
        $tokens = EmailVerificationToken::all();
        if($tokens->count()) {
            foreach($tokens as $token) {
                if(Carbon::now()->greaterThan($token->expires_at)) {
                    $token->delete();
                }
            }
        }
    }

    public function getToken($email, $token)
    {
        //$this->clearExpiredTokens();
        return StaffEmailVerificationToken::where('email', $email)->where('token_signature', hash('md5', $token))->first();
    }

    public function emailExists($email)
    {
        return StaffEmailVerificationToken::where('email', $email)->first();
    }

    public function saveEmailVerificationToken($email)
    {
        // delete any tokens generated for this email before if it exists
        $this->delete_email_tokens($email);
        
        $signatureToken = $this->genCode($email);
        try{
            $emailVerificationToken = new StaffEmailVerificationToken;
            $emailVerificationToken->email = $email;
            $emailVerificationToken->token_signature = $signatureToken['signature'];
            $emailVerificationToken->expires_at = Carbon::now()->addMinutes(30);
            $emailVerificationToken->save();
            return $signatureToken['token'];
        }catch(\Exception $e){
            throw $e;
        }
    }
}