<?php

namespace Root;
use Root\ActiveRecord;

class Model {

    private $db;
    public $table = false;    
    public $campo_id = "id";

    public function __construct(){

        if(!$this->table){
            $table = strtolower(get_class($this));
            $table = explode("\\", $table);
            $this->table = $table[1];
        }

        $this->db = new ActiveRecord();

    }

    public function buscar($id=false){

        if($id){
            $this->db->where($this->campo_id, $id);
        }

        $resultado = $this->db->get($this->table);

        return $resultado->result_array();

    }

    public function inserir($data){

        $this->db->insert($this->table, $data);
        return array($this->campo_id => $this->db->lastInsertId());

    }

    public function atualizar($id, $data){

        $this->db->where($this->campo_id, $id);
        $this->db->update($this->table, $data);

        return array($this->campo_id => $id);

    }

    public function deletar($id){

        $this->db->where($this->campo_id, $id);
        $this->db->delete($this->table);

        return array($this->campo_id => $id);

    }

}