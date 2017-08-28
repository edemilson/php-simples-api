<?php

namespace Root;
use Valitron\Validator;

class Validation {

    protected $descricao_tabela;
    protected $descricao_tabela_formatado;
    protected $dados_recebidos_por_parametro;
    protected $validacoes_mapeadas;

    public function __construct(){

    }

    public function setar_descricao_tabela($data){
        $this->descricao_tabela = $data;
    }

    public function setar_dados_recebidos_por_parametros($data){
        $this->dados_recebidos_por_parametro = $data;
    }

    public function validar_dados(){
        $this->filtrar_retorno_da_descricao_tabela();
        return $this->mapear_validacoes();
    }

    protected function filtrar_retorno_da_descricao_tabela(){

        $r = array();

        foreach($this->descricao_tabela as $registro){

            preg_match('/^(?<type>[[:word:]]*+)(?:\((?<attribute>[^\)]*+)\))?+/', $registro['Type'], $output_array);
            if(array_key_exists("attribute", $output_array)){
                $output_array['attribute'] = explode(",", str_replace("'", "", $output_array['attribute']));
            }else{
                $output_array['attribute'] = array();
            }

            $output_array['field'] = $registro['Field'];
            $output_array['is_null'] = ($registro['Null'] == "YES") ? true : false;

            if($registro['Comment']){
                $output_array['validacao_extra'] = explode(',', $registro['Comment']);
            }

            $r[] = $output_array;

        }

        $this->descricao_tabela_formatado = $r;

    }

    protected function mapear_validacoes(){

        $r = array();
        $v = new \Valitron\Validator($this->dados_recebidos_por_parametro);

        foreach($this->descricao_tabela_formatado as $campo_a_validar){

            if(array_key_exists($campo_a_validar['field'], $this->dados_recebidos_por_parametro)){
                switch ($campo_a_validar['type']) {
                    case 'varchar':
                    case 'text':
                        if($campo_a_validar['attribute']){
                            $v->rule('lengthMax', $campo_a_validar['field'], $campo_a_validar['attribute'][0]);
                        }
                        if(!$campo_a_validar['is_null']){
                            $v->rule('required', $campo_a_validar['field']);
                        }
                        break;

                    case 'int':
                    case 'tinyint':
                        $v->rule('integer', $campo_a_validar['field']);
                        if($campo_a_validar['attribute']){
                            $v->rule('lengthMax', $campo_a_validar['field'], $campo_a_validar['attribute'][0]);
                        }
                        if(!$campo_a_validar['is_null']){
                            $v->rule('required', $campo_a_validar['field']);
                        }
                        break;

                    case 'float':
                        $v->rule('numeric', $campo_a_validar['field']);
                        if(!$campo_a_validar['is_null']){
                            $v->rule('required', $campo_a_validar['field']);
                        }
                        break;

                    case 'date':
                    case 'datetime':
                        $v->rule('date', $campo_a_validar['field']);
                        if(!$campo_a_validar['is_null']){
                            $v->rule('required', $campo_a_validar['field']);
                        }
                        break;
                    
                    default:
                        # code...
                        break;
                }

                if(isset($campo_a_validar['validacao_extra'])){
                    foreach($campo_a_validar['validacao_extra'] as $regra){
                        $v->rule($regra, $campo_a_validar['field']);
                    }
                }

            }

        }

        $retorno = array();

        if(!$v->validate()) {
            $retorno = $v->errors();
        }

        return $retorno;

    }

}