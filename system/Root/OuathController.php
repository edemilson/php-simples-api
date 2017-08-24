<?php

namespace Root;

class OuathController {

    private $settings;

    public function __construct(){

        if (file_exists(__DIR__ . "/../../config/database.ini")) {
            $this->settings = parse_ini_file("./config/database.ini", true);
        }
        else die('not find config.php');

        $dsn = "mysql:dbname=".$this->settings['mysql']['database']['database'].";host=".$this->settings['mysql']['database']['host'];

        \OAuth2\Autoloader::register();
        $storage = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $this->settings['mysql']['database']['user'], 'password' => $this->settings['mysql']['database']['password']));
        $this->server = new \OAuth2\Server($storage);
        $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

    }

    public function token(){

        return $this->server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();

    }

    public function autorizado(){

        if (!$this->server->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
            die;
        }

    }

}