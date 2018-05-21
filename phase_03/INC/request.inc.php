<?php

if (count(get_included_files()) == 1) die("bye");

require_once 'mesFonctions.inc.php';

function display($txt) { //vide 'display' pour le remplir de $txt apres
    global $toSend;
    if (!isset($toSend['display'])) $toSend['display'] = ""; //verif que display existe deja
    $toSend['display'] .= $txt;
}

function error($txt) {
    global $toSend;
    if (!isset($toSend['error'])) $toSend['error'] = "";
    $toSend['error'] .= $txt;
}

function debug($txt) {
    global $toSend;
    if (!isset($toSend['debug'])) $toSend['debug'] = "";
    $toSend['debug'] .= $txt;
}

function toSend($txt, $action='display'){
    global $toSend;
    if (!isset($toSend[$action])) $toSend[$action]="";
    $toSend[$action] .= $txt;
}

function chargeTemplate($name = 'yololo'){
    $name = 'INC/template.'.strtolower($name).'.inc.php';
    //$name = 'INC/template.'.$name.'.inc.php';
    return file_exists($name) ? implode("\n", file($name)) : false;
}

function gereSubmit(){
    //debug(monPrint_r($_FILES));
    //debug(monPrint_r($_REQUEST));
    if (!isset($_POST['senderId'])) $_REQUEST['senderId'] = '';
    switch ($_POST['senderId']) {
        case 'formTP05':
            require_once '/RES/appelAjax.php';
            toSend('#tp05result div', 'destination');
            toSend('#tp05result p', 'cacher');
            //toSend(monPrint_r(RES_appelAjax('coursGroup')), 'debug');
            sendMakeTable(RES_appelAjax('coursGroup'));
            break;
        default:
            error('<dl><dt>Error in <b>' . __FUNCTION__ . '()</b></dt><dt>'. monPrint_r(["_REQUEST" => $_REQUEST, "_FILES" => $_FILES]) .'</dt></dl>');
            break;
    }
}

function TPsem05(){
    require_once '/RES/appelAjax.php';
    include_once '/RES/appelAjax.php';
    toSend(RES_appelAjax('allGroups'), 'data');
    toSend(chargeTemplate('tpsem05'),'formTP05');
    debug(monPrint_r($_FILES));
}

function callResAjax($rq){
    require_once '/RES/appelAjax.php';
    global $toSend;
    $toSend = json_decode(RES_appelAjax($rq, 'action'));
}

function sendMakeTable($tab){
    global $toSend;
    if (!isset($toSend['makeTable'])) $toSend['makeTable'] = "";
    $toSend['makeTable'] = $tab;
}

function gereRequete($rq) { //va remplir 'display' pour sem04 et sem03

    require_once '/RES/appelAjax.php';
    global $toSend;

    switch ($rq) {
        case 'sem04':
            display('Cette fois je te reconnais (' . $rq . ')');
            break;

        case 'sem03':
            display('Requête « ' . $rq . ' » : le TP03 est disponnible sur le serveur !');
            break;

        case 'TPsem05':
            TPsem05();
            break;

        case 'formSubmit':
            gereSubmit();
            break;

        default:
            callResAjax($rq);
    }

}


//echo gereRequete('plup');
