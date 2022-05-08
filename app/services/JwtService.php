<?php
namespace app\services;

use DateTime;
use Exception;

class JwtService{
    //type AND algorithm
    private $header;
    //the user data
    //account,roles,expire
    private $payload;

    private $secureKey;

    public function __construct()
    {
        $this->header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        $this->payload = [
            'account'=>'',
            'roles'=>[],
            'exp'=>0,
        ];
        $this->secureKey = $_ENV['SECURE_KEY'];
    }
    public function Jwt_user_encode($account,$roles){
        // process header
        $header = json_encode($this->header);
        $header = base64_encode($header);

        // process payload
        $payload = $this->payload;
        $payload['account'] = $account;
        // return $roles;
        foreach($roles as $role){
            $payload['roles'][] = $role['Id'];
        }
        $payload['exp'] = strtotime("+ 1 day");
        $payload = base64_encode(json_encode($payload));

        // process signature
        $signature = hash_hmac('sha256',$header.'.'.$payload,$this->secureKey);

        return $header.'.'.$payload.'.'.$signature;
    }
    public function Jwt_user_decode($token){
        if(isset($token)){
            $jwt = explode('.',$token);
            if(count($jwt) == 3){
                $header = $jwt[0];
                $payload = $jwt[1];
                $signature = $jwt[2];
                if(hash_hmac('sha256',$header.'.'.$payload,$this->secureKey) == $signature){
                    $payload = base64_decode($payload);
                    return json_decode($payload,true);
                }else{
                    return 'error';
                }
            }
        }
        return 'error';
    }

}
