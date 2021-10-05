<?php
namespace App\Foodstock\Integration\Neemo\RequestParameters;


class PoolingParameters{

    private $token_account; //Token de acesso da aplicação
    private $limit; //Limite de registro que serão retornados. (máx: 50)
    private $page; //Página da consulta.
    private $modified; //Data de modificação do Pedido. (YYYY-mm-dd HH:MM:SS). Retorna os pedidos que foram criados ou alterados a partir da data informada
    private $status; //Status do pedido. 0 = Novo Pedido, 1 = Confirmado, 2 = Entregue, 3 = Cancelado (restaurante), 4 = Enviado, 5 = Cancelado Automaticamente (sistema), 6 = Cancelado, com Pagamento Estornado (restaurante), 7 = Cancelado Automaticamente, com Pagamento Estornado (sistema)
    private $sort; //Ordenação de listagem, ex.: [campo] [asc/desc]
    private $created_at; //Data de criação do pedido. (YYYY-mm-dd HH:MM:SS). Retorna os pedidos que foram criados a partir da data informada

    public function __construct($token_account, $status = 0, $limit = 50, $page = 1, $modified = null, $sort = "asc", $created_at = null)
    {
        $this->token_account = $token_account;
        $this->limit = intval($limit) > 50 ? 50 : intval($limit);
        $this->page = intval($page);
        $this->modified = $modified;
        $this->status = intval($status);
        $this->sort = $sort;
        $this->created_at = $created_at;

        $this->parseParameters();
    }

    public function toArray(){
        return [
            "token_account" => $this->token_account,
            "limit" => $this->limit,
            "page" => $this->page,
            "modified" => $this->modified,
            "status" => $this->status,
            //"sort" => $this->sort,
            "created_at" => $this->created_at,
        ];
    }

    public function getToken(){
        return $this->token;
    }

    private function parseParameters(){
        if($this->token_account == "") throw new \Exception("Invalid parameters.");     
    }

    public function formParameters(){
        $params = $this->toArray();
        if($params["modified"] == null) unset($params["modified"]);
        if($params["created_at"] == null) unset($params["created_at"]);
        return ["form_params" => $params];
    }
}