<?php

//ouvrir une session
session_start();
if (!isset($_SESSION['start'])) {
    $_SESSION['start'] = date('YmdHms');
    $_SESSION['log'] = [];
}

//on appel ce qu'on a besoin
require_once "INC/dbConnect.inc.php";
require_once  "INC/mesFonctions.inc.php";
require_once  "INC/config.inc.php";
require_once "ALL/kint/kint.php";
kint::$return=true;

if (!isset($_SESSION['config'])) {
    $iCfg = new Config('INC/config.ini.php');
    $_SESSION['config'] = $iCfg->load();
    $_SESSION['loadTime'] = time();
}

if (isset($_GET['rq'])) {
    if (!empty($_GET['rq'])) {
        $_SESSION['log'][time()] = $_GET['rq'];
        $toSend = []; //sera rempli de la propriété 'display'
        require_once "INC/request.inc.php";
        gereRequete($_GET['rq']);
        die(json_encode($toSend));
    }
    else {
        $_SESSION['log'][time()] = 'reset(F5)';
    }
}

//pour le formulaire
$site= &$_SESSION['config']['SITE'];
$logo =&$_SESSION['config']['LOGO'];

$home = 'Accueil';
$siteName = $site['titre'];
$logoPath = $site['images'].'/'.$logo['logo'];
$logoAlt = 'Logo';
$mainZone = 'Hello';
$mail = ___MATRICULE___ . '@students.ephec.be';
$author = '<a href="mailto:' . $mail . '" title="'. $mail . '">' . $__INFOS__['nom'] . ' ' . $__INFOS__['prenom']. '</a>';

include("INC/layout.html.inc.php");
?>



