<?php
namespace App\Foodstock\Bridge\Ifood;

use App\Models\Broker;
use App\Models\IfoodBroker;

use App\Foodstock\Bridge\Ifood\BaseHandler;
use App\Foodstock\Integration\Ifood\Authentication\Token;
use App\Foodstock\Integration\Ifood\RequestParameters\BodyParameters;
use App\Foodstock\Integration\Ifood\Enums\GrantType;

class RefreshTokenHandler{

    protected IfoodBroker $ifoodBroker;

    public function __construct(IfoodBroker $ifoodBroker){
        $this->ifoodBroker = $ifoodBroker;
    }

    public function handle()
    {
        if(strtotime($this->ifoodBroker->expiresIn) <= time()){ //Token expirado
            
            $broker = Broker::findOrFail(1); //TODO - Criar ENUM ou ENV
            
            //Refresh token
            $token = new Token(new BodyParameters(GrantType::RefreshToken, $broker->client_distributed_id, $broker->client_distributed_secret, "", "", $this->ifoodBroker->refreshToken));
            $json = $token->request();

            $this->ifoodBroker->accessToken = $json->accessToken;
            $this->ifoodBroker->refreshToken = $json->refreshToken;
            $this->ifoodBroker->expiresIn = date("Y-m-d H:i:s", (time() + $json->expiresIn - 120));

            $this->ifoodBroker->save();
        }

        return $this->ifoodBroker;
    }
   
}
