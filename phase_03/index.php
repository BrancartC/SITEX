<?php

require_once "INC/dbConnect.inc.php";

$home = 'Accueil';
$siteName = 'Phase03 tpsem05';
$logoSrc = "IMG/04.png";
$logoAlt = 'Logo';
$mainZone = 'Bienvenue';
$mail = ___MATRICULE___ . '@students.ephec.be';
$author = '<a href="mailto:' . $mail . '" title="'. $mail . '">' . $__INFOS__['nom'] . ' ' . $__INFOS__['prenom']. '</a>';

if (isset($_GET['rq'])) {
    if (!empty($_GET['rq'])) {
        require_once "INC/request.inc.php";
        $toSend = []; //sera rempli de la propriété 'display'
        gereRequete($_GET['rq']);
        die(json_encode($toSend));
    }
}

include("INC/layout.html.inc.php");
?>



