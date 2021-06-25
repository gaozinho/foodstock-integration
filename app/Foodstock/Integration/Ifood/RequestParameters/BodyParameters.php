<?php
namespace App\Foodstock\Integration\Ifood\RequestParameters;

use App\Foodstock\Integration\Ifood\Enums\GrantType;

class BodyParameters{

    private $grantType;
    private $clientId;
    private $clientSecret;
    private $authorizationCode;
    private $authorizationCodeVerifier;
    private $refreshToken;

    public function __construct($grantType, $clientId, $clientSecret, $authorizationCode = "", $authorizationCodeVerifier = "", $refreshToken = "")
    {
        $this->grantType = $grantType;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->authorizationCode = $authorizationCode;
        $this->authorizationCodeVerifier = $authorizationCodeVerifier;
        $this->refreshToken = $refreshToken;

        $this->parseParameters();
    }

    public function toArray(){
        return [
            "grantType" => $this->grantType,
            "clientId" => $this->clientId,
            "clientSecret" => $this->clientSecret,
            "authorizationCode" => $this->authorizationCode,
            "authorizationCodeVerifier" => $this->authorizationCodeVerifier,
            "refreshToken" => $this->refreshToken,
        ];
    }

    public function getGrantType(){
        return $this->grantType;
    }

    private function parseParameters(){
        if($this->grantType == GrantType::ClientCredentials){ //CENTRALIZADO
            if($this->grantType == "" || $this->clientId == "" || $this->clientSecret == "") throw new \Exception("Invalid parameters for " . GrantType::ClientCredentials);
        }else if($this->grantType == GrantType::AuthorizationCode){ //DISTRIBUIDO
            if($this->grantType == "" || $this->clientId == "" || $this->clientSecret == "" || $this->authorizationCode == "" || $this->authorizationCodeVerifier == "") throw new \Exception("Invalid parameters for " . GrantType::AuthorizationCode);
        }else if($this->grantType == GrantType::RefreshToken){
            if($this->grantType == "" || $this->clientId == "" || $this->clientSecret == "" || $this->refreshToken == "") throw new \Exception("Invalid parameters for " . GrantType::RefreshToken);
        }        
    }

    public function getAuthorizationCode(){
        return $this->authorizationCode;
    }
}