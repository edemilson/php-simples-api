<?php

namespace Root;

class OuathController {

    private $settings;
    private $settings_oauth;

    public function __construct(){

        if (file_exists(__DIR__ . "/../../config/database.ini")) {
            $this->settings = parse_ini_file("./config/database.ini", true);
        }
        else die('not find database.ini');

        if (file_exists(__DIR__ . "/../../config/oauth2.ini")) {
            $this->settings_oauth = parse_ini_file("./config/oauth2.ini", true);
        }
        else die('not find oauth2.ini');

        $dsn = "mysql:dbname=".$this->settings['mysql']['database']['database'].";host=".$this->settings['mysql']['database']['host'];

        \OAuth2\Autoloader::register();
        $storage = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $this->settings['mysql']['database']['user'], 'password' => $this->settings['mysql']['database']['password']));
        $this->server = new \OAuth2\Server($storage);

        if($this->settings_oauth['config']['ouath']['grant_type'] == 'client_credentials'){

            //VER MAIS SOBRE CLIENT CREDENTIALS https://bshaffer.github.io/oauth2-server-php-docs/overview/grant-types/
            $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));

        }else if ($this->settings_oauth['config']['ouath']['grant_type'] == 'password'){

            //VER MAIS SOBRE USER CREDENTIALS https://bshaffer.github.io/oauth2-server-php-docs/overview/grant-types/
            $this->server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
            $this->server->addGrantType(new \OAuth2\GrantType\RefreshToken($storage, ['always_issue_new_refresh_token' => true]));

        }
        else die('grant_type not defined');

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