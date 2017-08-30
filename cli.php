<?php
 
$resource = getopt("r:c:u:a:m:");
//$file = file(__DIR__.'/application/Model/'.$resource['m'].'.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);var_dump($file);return;
error_reporting(0);
ini_set('display_errors', false);

$loader = require 'vendor/autoload.php';
$loader->add('Root', __DIR__.'/system/');

use Root\ActiveRecord;
$db = new ActiveRecord();

if(!file_exists(__DIR__.'/application/Controller/'.$resource['r'].'.php')){

    try {
        $db->query("SHOW TABLES LIKE " . strtolower($resource['r']))->num_rows();
        /*if($resource['u']){
            $db->query("SHOW TABLES LIKE " . strtolower($resource['u']))->num_rows();    
        }
        if($resource['um']){
            $db->query("SHOW TABLES LIKE " . strtolower($resource['um']))->num_rows();
        }
        if($resource['mm']){
            $db->query("SHOW TABLES LIKE " . strtolower($resource['mm']))->num_rows();    
        }*/
    } catch (\Exception $e) {
        
        $sql_relacionamento = "";

        $campos[] = "`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
        foreach(explode(",", $resource['c']) as $field){

            $arrayCampo = explode(":", $field);
            $campos[] = "`". $arrayCampo[2] ."` ". $arrayCampo[0] . "(".$arrayCampo[1].")" . " DEFAULT NULL";

        }

        $string_criar_table = "CREATE TABLE `" . strtolower($resource['r']) . "` (";
        $string_criar_table .= implode(",", $campos);
        
        if($resource['u']){
            $string_criar_table .= ',`' . strtolower($resource['u']) . "_id` INT(11) NOT NULL, ";
            $string_criar_table .= "FOREIGN KEY (`".strtolower($resource['u'])."_id`) REFERENCES `".strtolower($resource['u'])."`(`id`) ON DELETE CASCADE ON UPDATE CASCADE , UNIQUE (`".strtolower($resource['u'])."_id`)";

            $sql_relacionamento = 'protected $relacionamento_uu = "'.strtolower($resource['r']).'";';

        }

        if($resource['a']){
            $string_criar_table .= ",`" . strtolower($resource['a']) . "_id` INT(11) NOT NULL, ";
            $string_criar_table .= "FOREIGN KEY (`".strtolower($resource['a'])."_id`) REFERENCES `".strtolower($resource['a'])."`(`id`) ON DELETE CASCADE ON UPDATE CASCADE";

            $sql_relacionamento = 'protected $relacionamento_um = "'.strtolower($resource['r']).'";';

        }

        $string_criar_table .= ")ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci";

        $result = $db->simple_query($string_criar_table);

        if($resource['m']){
            
            $tabela_de_relacionamento = "CREATE TABLE ".strtolower($resource['r'])."2".strtolower($resource['m'])." (
                ".strtolower($resource['r'])."_id INT(11) NOT NULL,
                ".strtolower($resource['m'])."_id INT(11) NOT NULL,
                PRIMARY KEY (".strtolower($resource['r'])."_id, ".strtolower($resource['m'])."_id),
                FOREIGN KEY (".strtolower($resource['r'])."_id) REFERENCES ".strtolower($resource['r'])." (id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (".strtolower($resource['m'])."_id) REFERENCES ".strtolower($resource['m'])." (id)
                ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci";
            
            $result = $db->simple_query($tabela_de_relacionamento);

            $sql_relacionamento = 'protected $relacionamento_mm = "'.strtolower($resource['r']).'";';

        }

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

    if($resource['u']){

        $file = file(__DIR__.'/application/Controller/'.$resource['u'].'.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $content = "";

        foreach($file as $key => $line){

            if(trim($line) == "class ".$resource['u']." extends Controller{"){
                $content .= $line . "\n";
                $content .= $sql_relacionamento . "\n";
            }else{
                $content .= $line . "\n";
            }

        }

        $fp = fopen(__DIR__.'/application/Controller/'.$resource['u'].'.php',"wb");
        fwrite($fp,$content);
        fclose($fp);

    }

    if($resource['a']){

        $file = file(__DIR__.'/application/Controller/'.$resource['a'].'.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $content = "";

        foreach($file as $key => $line){

            if(trim($line) == "class ".$resource['a']." extends Controller{"){
                $content .= $line . "\n";
                $content .= $sql_relacionamento . "\n";
            }else{
                $content .= $line . "\n";
            }
            
        }

        $fp = fopen(__DIR__.'/application/Controller/'.$resource['a'].'.php',"wb");
        fwrite($fp,$content);
        fclose($fp);

    }

    if($resource['m']){

        $file = file(__DIR__.'/application/Controller/'.$resource['m'].'.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $content = "";

        foreach($file as $key => $line){

            if(trim($line) == "class ".$resource['m']." extends Controller{"){
                $content .= $line . "\n";
                $content .= $sql_relacionamento . "\n";
            }else{
                $content .= $line . "\n";
            }
            
        }

        $fp = fopen(__DIR__.'/application/Controller/'.$resource['m'].'.php',"wb");
        fwrite($fp,$content);
        fclose($fp);

    }

    echo "Criado com sucesso" . PHP_EOL;

}else{

    echo "Arquivo já existe" . PHP_EOL;

}