<?php

if (count(get_included_files()) == 1) die("You are not the access");

require_once 'mesFonctions.inc.php';
require_once 'db.inc.php';

function callResAjax($rq) {
    require_once '/RES/appelAjax.php';
    global $toSend;
    $toSend = array_merge($toSend,(Array) json_decode(RES_appelAjax($rq, 'action')));
}

function chargeTemplate($name = 'yololo') {
    $name = 'INC/template.' . strtolower($name) . '.inc.php';
    return file_exists($name) ? implode("\n", file($name)) : false;
}

function tpSem05() {
    require_once '/RES/appelAjax.php';
    toSend(chargeTemplate('tpsem05'), 'formTP05');
    toSend(RES_appelAjax('allGroups'),'data');
}

function klogin(){
    require_once '/RES/appelAjax.php';
    toSend(chargeTemplate('login'),'formLogin');
    //$res = chargeTemplate('login');
    //if($res)
    //  {toSend($res,'loginAction');}
    //else
    //   {error('ceci est une erreur de chargement');}
}

function kLogout() {
    toSend('Au revoir <b>' . $_SESSION['user']['pseudo'] . '</b> !', 'logout');
    unset($_SESSION['user']);
    //toSend(creeMenu(), 'newMenu');
}


function authentication($user) {
    $iDB = new Db();
    $profil = $iDB->call('userProfil', [$user['id']]);
    $isActiv = false;

    kint(d($user,$profil,$isActiv));

    if ($isActiv) {
        toSend('Vous devez activer votre compte (Cfr. email envoyé)', 'peutPas');
        return -1;
    }
    $_SESSION['user'] = $user;
    $_SESSION['user']['profil'] = $profil;


    toSend(json_encode($_SESSION['user']),'userConnu');
    creeDroits();
    //-return kint(d($_SESSION['user']));
    //toSend(creeMenu(),'newMenu');
}

function peutPas($rq){
    if ($rq == 'formSubmit' && isset($_POST['senderId'])){
        $rq = $_POST['senderId'];
    }
    if (!in_array($rq, $_SESSION['droits'])) {
        toSend('Droits insuffisants', 'peutPas');
        return true;
    }
     return false;
}

function gereSubmit() {
    if (!isset($_POST['senderId'])) $_REQUEST['senderId'] = '';
    switch ($_POST['senderId']) {
        case 'formTP05':
            require_once '/RES/appelAjax.php';
            toSend('#tp05result div', 'destination');
            toSend('#tp05result p', 'cacher');
            //toSend(monPrint_r(RES_appelAjax('coursGroup')), 'debug');
            sendMakeTable(RES_appelAjax('coursGroup'));
            break;

        case 'sauveConfig':
            $iCfg = new Config('INC/config.ini.php');
            $iCfg->load();
            debug($iCfg->save('test.ini.php'));
            if ($iCfg->getSaveError() == 0) {
                $_SESSION['config'] = $iCfg->getConfig();
                $_SESSION['loadTime'] = time();
                toSend(json_encode(['titre' => $_SESSION['config']['SITE']['titre'], 'logoPath' => $_SESSION['config']['SITE']['images'] . '/' . $_SESSION['config']['LOGO']['logo'] . '?' . rand(0, 100)]), 'layout');
            }
            break;

        case 'formLogin':
            $iDB = new Db();
            $user = $iDB->call('whoIs', array_values($_POST['login']));
            kint(d($user,isAuthenticated(),isActiv()));
            if ($user)
                if (isset($user['__ERR__'])) error($user['__ERR__']);
                //else if(isActiv()) toSend('vous devez activer votre compte');
                else authentication($user[0],$iDB);
            else debug('Pas le bon pseudo et/ou MDP !');
            break;

        default:
            error('<dl><dt>Error in <b>' . __FUNCTION__ . '()</b></dt><dt>'. monPrint_r(["_REQUEST" => $_REQUEST, "_FILES" => $_FILES]) .'</dt></dl>');
            break;
    }

}

function sendMakeTable($tab) {
    global $toSend;
    if (!isset($toSend['makeTable'])) $toSend['makeTable'] = "";
    $toSend['makeTable'] = $tab;
}

function gereRequete($rq) {
    if(peutPas($rq))return -1;
    //kint( d( $rq ) );
    switch ($rq) {
        case 'sem04':
            toSend('Cette fois je te reconnais (' . $rq . ')', 'display');
            break;
        case 'sem03':
            toSend('Requête « ' . $rq . ' » : le TP03 est disponnible sur le serveur !', 'display');
            break;
        case 'TPsem05':
            tpSem05();
            break;
        case 'formSubmit':
            gereSubmit();
            break;
        case 'displaySession':
            debug(d($_SESSION['start']));
            debug(d($_SESSION['log']));
            break;
        case 'clearLog':
            $_SESSION['log'] = [];
            $_SESSION['log'][time()] = $rq;
            debug(d($_SESSION['start']));
            debug(d($_SESSION['log']));
            break;
        case 'resetSession':
            session_unset();
            $_SESSION['start'] = date('YmdHms');
            $_SESSION['log'][time()] = $rq;
            debug(d($_SESSION['start']));
            debug(d($_SESSION['log']));
            break;
        case 'config':
            //$iConfig = new config("autreConfig.ini.php");
            //debug('le nom du fichier est:'. $iConfig->getFilename());
            //debug(d($iConfig->isFileExist()));
            //kint(d($iConfig->getConfig()));
            $iConfig = new config("INC/config.ini.php");
            $iConfig->load();
            //kint(d($cfg));
            toSend($iConfig->getForm(), 'formConfig');
            break;
        case 'gestLog':
            $f = 'kLog' . (isAuthenticated() ?  'out' : 'in');
            //$f = 'kLog' . (isset($_SESSION['user']) ?  'out' : 'in');
            $f();
            break;
        case 'testDB':
            $iDB = new Db();
            debug($iDB->getException());
            //kint(d($iDB->call('mc_allGroups')));
            //kint(d($iDB->call('mc_group', ['2TL'])));
            //kint(d($iDB->call('mc_coursesGroup', ['2TL'])));
            //kint(d($iDB->call('whoIs', ['ano', 'anonyme'])));
            //kint(d($iDB->call('userProfil', [8])));
            //kint(d($iDB->call_v1()));
            break;
        default:
            callResAjax($rq);
            //kint('requête inconnue ('.$rq.') transférée à callResAjax()');
            break;

    }
}
// echo gereRequete('yoloooo');



