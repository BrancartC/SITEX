<?php
/**
 * Created by PhpStorm.
 * User: Clemb
 * Date: 13-08-18
 * Time: 15:18
 */

//----------Est identifié ---------------

function isAuthenticated(){
    return isset($_SESSION['user']);
}

//-----------Gestion des statuts -----------
function isAdmin(){
    return isAuthenticated() && in_array('admin',$_SESSION['user']['lesProfils']);
}

function isSousAdmin(){
    return isAuthenticated() && in_array('sAdmin', $_SESSION['user']['lesProfils']);
}
function isActiv(){
   return isAuthenticated() && in_array('acti', $_SESSION['user']['lesStatuts']);
}
function isReactiv(){
    return isAuthenticated() && in_array('réact', $_SESSION['user']['lesStatuts']);
}
function isMdpp(){
    return isAuthenticated() && in_array('mdpp', $_SESSION['user']['lesStatuts']);
}
function isEdit(){
    return isAdmin() || !isReactiv();
}

//-------------Gestion des droits --------------

function creeDroits(){
    $_SESSION['droitsDeBase'] = ['index','gestLog','pasvorto','formLogin','formSubmit'];
    if(isset($_SESSION['user']['droits'])){
        $_SESSION['droits'] = &$_SESSION['user']['droits'];
    } else {
        $_SESSION['droits'] = &$_SESSION['droitsDeBase'];
    }

    //if(!isset($_SESSION['user'])) return -1;
    if ( ! isAuthenticated()) return -1;

    $listeDesStatuts = ['test'];
    $listeDesProfils = [];
    foreach ($_SESSION['user']['profil'] as $aProfil){
        if($aProfil['pEstStatut']) array_push($listeDesStatuts, $aProfil['pAbrev']);
        else array_push($listeDesProfils, $aProfil['pAbrev']);
    }
    $_SESSION['user']['lesStatuts'] = $listeDesStatuts;
    $_SESSION['user']['lesProfils'] = $listeDesProfils;

    $listeDesDroits = [];
    switch($listeDesProfils[0]){
        case 'admin': $listeDesDroits = array_merge($listeDesDroits, ['modifConfig', 'displaySession', 'clearLog', 'resetSession']);
        case 'sAdmin': $listeDesDroits = array_merge($listeDesDroits, ['testDB', 'config']);
        case 'modo': $listeDesDroits = array_merge($listeDesDroits, ['tableau', 'moderation']);
        case 'memb': $listeDesDroits = array_merge($listeDesDroits, ['sem02', 'sem03', 'sem04', 'TPsem05', 'formTP05', 'userProfil']);
        case 'ano': $listeDesDroits = array_merge($listeDesDroits, $_SESSION['droitsDeBase']);
    }
    $_SESSION['user']['droits'] = $listeDesDroits;
    kint(d($_SESSION['user']));

    //------------Droits Perdu----------------------

    if (!isReactiv() || isAdmin()) return -2;
    $perdu = [
        'memb' => ['formTP05'],
        'modo' => ['moderation'],
        'sAdmin' => ['config']
    ];
    $listeDesDroitsPerdus = [];
    foreach ($listeDesProfils as $profil) {
        if (isset($perdu[$profil])) $listeDesDroitsPerdus = array_merge($listeDesDroitsPerdus, $perdu[$profil]);
    }
    if (!empty($listeDesDroitsPerdus)) {
        $_SESSION['user']['droitsPerdus'] = $listeDesDroitsPerdus;
        $_SESSION['user']['droits'] = array_diff($_SESSION['user']['droits'], $_SESSION['user']['droitsPerdus']);
    }

    kint(d($_SESSION['user']));
}
