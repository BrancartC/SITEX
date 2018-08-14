<?php

require_once 'INC/config.inc.php';

class Db
{

    private $db = [];
    private $pdoException = null;
    private $iPdo = null;

    public function __construct()
    {
        $iCfg = new Config('config.ini.php');
        $config = $iCfg->load();
        $this->db = $config['DB'];
        try {
            $this->iPdo = new PDO('mysql:host=' . $this->getServer() . ';dbname=' . $this->db['dbname'], $this->db['user'], $this->db['pswd']);
            //$this->iPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            $this->pdoException = $e;
        }
    }

    public function getException() {
        return 'PDOException : ' . ($this->pdoException ? $this->pdoException->getMessage() : 'aucune !');
    }

    private function getServer() {
        return in_array($_SERVER['SERVER_NAME'], ['193.190.65.92', 'devweb.ephec.be']) ? 'localhost' : '193.190.65.92';
    }

    public function call_v1() {
        try {
            $sth = $this->iPdo->prepare('call mc_allGroups()');
            $sth->execute();
            return $sth->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->pdoException = $e;
            return ['__ERR__' => $this->pdoException];
        }
    }

    public function call($name, $param = []) {
        $p = [];
        $argCount = 0;
        switch ($name) {
            //ordonné en ordre décroissant obligatoirement
            // 2 params
            case 'whoIs':
                $argCount = 2;
                break;
            // 1 param
            case 'userProfil':
            case 'mc_group':
            case 'mc_coursesGroup':
                $argCount = 1;
                break;
            // 0 param
            case 'mc_allGroups':
                $argCount = 0;
                break;
            default: return ['__ERR__' => 'call impossible à ' . $name];

        }

        // Et puis seulement on fait la requête
        //if (!$this->pdoException) {
        try {
            $appel = 'call ' . $name . '('. implode(',', array_fill(0, $argCount, '?')) .')';
            //echo $appel;
            $sth = $this->iPdo->prepare($appel);
            $sth->execute($param);
            return $sth->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->pdoException = $e;
            return ['__ERR__' => $this->pdoException];
        }
        //} else {
        //  return ['__ERR__' => $this->pdoException];
        //}


    }
}