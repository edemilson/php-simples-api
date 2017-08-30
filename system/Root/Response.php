<?php

namespace Root;

class Response {

    private $data;

    public function __construct($data=false){

        $this->data = $data;

    }

    public function set_data($data){

        $this->data = $data;

    }

    public function json(){

        if(!$this->data){

            $this->data = array("Sua consulta não retornou registros.");

        }
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

        return json_encode($this->data, true);

    }

}