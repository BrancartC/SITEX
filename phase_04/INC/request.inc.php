<?php

if (count(get_included_files()) == 1) die("You are not the access");

require_once 'mesFonctions.inc.php';
require_once 'db.inc.php';

function display($txt) {
    global $toSend;
    if (!isset($toSend['display'])) $toSend['display'] = "";
    $toSend['display'] .= $txt;
}

function error($txt) {
    global $toSend;
    if (!isset($toSend['error'])) $toSend['error'] = "";
    $toSend['error'] .= $txt;
}

function kint($txt) {
    global $toSend;
    if (!isset($toSend['kint'])) $toSend['kint'] = '';
    $toSend['kint'] .= $txt;
}

function debug($txt) {
    global $toSend;
    if (!isset($toSend['debug'])) $toSend['debug'] = "";
    $toSend['debug'] .= $txt;
}

function toSend($txt, $action = 'display') {
    global $toSend;
    if (!isset($toSend[$action])) $toSend[$action] = "";
    $toSend[$action] .= $txt;
}

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
    toSend(chargeTemplate('login'),'loginConnect');
    //$res = chargeTemplate('login');
    //if($res)
    //  {toSend($res,'loginAction');}
    //else
    //   {error('ceci est une erreur de chargement');}
}

function authentication($user) {
    $iDB = new Db();
    $profil = $iDB->call('userProfil', [$user['uid']]);
    $isActiv = false;
    foreach ($profil as $p) if ($p['pAbrev'] == 'acti') $isActiv = true;
    if ($isActiv) {
        toSend('Vous devez activer votre compte (Cfr. email envoyé)', 'peutPas');
        return -1;
    }

    $_SESSION['user'] = $user;
    $_SESSION['user']['profile'] = $profil;
    toSend(json_encode($_SESSION['user']), 'userConnu');
    creeDroits();

    if (isReactiv()) {
        toSend('Vous n\'avez pas encore validé votre nouveau mail (Cfr. mail de confirmation envoyé à la nouvelle adresse mail)', 'peutPas');
        toSend('<div id="enReact">Vous devez valider votre nouveau mail (Cfr. mail de confirmation)</div>', 'estRéac');
    }

    if (isMdpp()) {
        toSend('Vous aviez demandé un changement de mot de passe mais manifestement vous avez retrouvé votre mot de passe. Nous annulons votre demande', 'peutPas');
    }

    toSend(creeMenu(), 'newMenu');

    //return kint(d($_SESSION['user']));
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

        case 'loginConnect':
            $iDB = new Db();
            $user = $iDB->call('whoIs', array_values($_POST['login']));
            if ($user) if (isset($user['__ERR__'])) error($user['__ERR__']);
            else authentication($user[0]);
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
        case 'connect':
            klogin();
        case 'testDB':
            $iDB = new Db();
            debug($iDB->getException());
            //kint(d($iDB->call('mc_allGroups')));
            //kint(d($iDB->call('mc_group', ['2TL'])));
            //kint(d($iDB->call('mc_coursesGroup', ['2TL'])));
            kint(d($iDB->call('whoIs', ['ano', 'anonyme'])));
            //kint(d($iDB->call('userProfil', [8])));
            //kint(d($iDB->call_v1()));
            break;
        default:
            callResAjax($rq);
            //kint('requête inconnue ('.$rq.') transférée à callResAjax()');
            break;

    }
}
// echo gereRequete('yolo');



