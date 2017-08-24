<?php

$resource = getopt("r:c:");

error_reporting(0);
ini_set('display_errors', false);

$loader = require 'vendor/autoload.php';
$loader->add('Root', __DIR__.'/system/');

use Root\ActiveRecord;
$db = new ActiveRecord();

if(!file_exists(__DIR__.'/application/Controller/'.$resource['r'].'.php')){

    try {
        $db->query("SHOW TABLES LIKE " . strtolower($resource['r']))->num_rows();
    } catch (\Exception $e) {
        
        $campos[] = "`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
        foreach(explode(",", $resource['c']) as $field){

            $arrayCampo = explode(":", $field);
            $campos[] = "`". $arrayCampo[2] ."` ". $arrayCampo[0] . "(".$arrayCampo[1].")" . " DEFAULT NULL";

        }

        $string_criar_table = "CREATE TABLE `" . strtolower($resource['r']) . "` (";
        $string_criar_table .= implode(",", $campos);
        $string_criar_table .= ")ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1";

        $result = $db->simple_query($string_criar_table);
    }

    $content = "<?php

    namespace Controller;
    use Root\Controller;

    class ".$resource['r']." extends Controller{


    }";

    $fp = fopen(__DIR__.'/application/Controller/'.$resource['r'].'.php',"wb");
    fwrite($fp,$content);
    fclose($fp);

    $content = "<?php

    namespace Model;
    use Root\Model;

    class ".$resource['r']." extends Model{


    }";

    $fp = fopen(__DIR__.'/application/Model/'.$resource['r'].'.php',"wb");
    fwrite($fp,$content);
    fclose($fp);

    echo "Criado com sucesso";

}else{

    echo "Arquivo já existe";

}