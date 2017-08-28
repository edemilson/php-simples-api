<?php

namespace Root;
use Root\Response;
use Root\OuathController;
use Root\Validation;

class Controller {
    
    private $resource;
    private $response;
    public $resource_name = false;

    public function __construct(){
        
        $ouath = new OuathController();
        $ouath->autorizado();

        if(!$this->resource_name){
            $resource_name = ucfirst(get_class($this));
            $resource_name = explode("\\", $resource_name);
            $resource_name = "Model\\".$resource_name[1];
            $this->resource = new $resource_name();
        }else{
            $resource = ucfirst($this->resource_name);
            $resource = "Model\\".$resource;
            $this->resource = new $resource();
        }

        $this->response = new Response();

    }

    public function anyIndex($id=false){
        
        $retorno = array();

        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'post':
                $retorno = $this->postSalvar();
                break;
            case 'get':
                $retorno = $this->getBuscar($id);
                break;
            case 'put':
                $retorno = $this->putAtualizar($id);
                break;
            case 'delete':
                $retorno = $this->deleteDeletar($id);
                break;
            default:
                $retorno = $this->error_method();
                break;
        }

        return $retorno;

    }

    protected function getBuscar($id=false)
    {   

        $this->response->set_data($this->resource->buscar($id));
        return $this->response->json();

    }

    protected function postSalvar()
    {   

        $data = $this->get_data_request('post');

        $this->response->set_data($this->resource->inserir($data['dados']));
        return $this->response->json();

    }

    protected function putAtualizar($id=false)
    {   
        
        $data = $this->get_data_request('put');

        $this->response->set_data($this->resource->atualizar($id, $data['dados']));
        return $this->response->json();

    }

    protected function deleteDeletar($id=false)
    {   
    
        $this->response->set_data($this->resource->deletar($id));
        return $this->response->json();

    }

    protected function get_data_request($method = false, $validation = true){

        $dados = array();

        switch ($method) {

            case 'post':
                $dados = $_POST;
                break;
            
            default:
                parse_str(file_get_contents('php://input'),  $dados);
                break;
        }
        
        $validacao = new Validation();
        $validacao->setar_descricao_tabela($this->resource->descrever_tabela());
        $validacao->setar_dados_recebidos_por_parametros($dados);
        $data['validacao'] = $validacao->validar_dados();
        $data['dados'] = $dados;

        if($data['validacao']){
            $this->response->set_data(array('Checar os valores do campos, validação nao atendida', $data['validacao']));
            echo $this->response->json();
            die();
        }

        return $data;

    }

    protected function error_method(){

        $this->response->set_data(array());
        return $this->response->json();

    }

}