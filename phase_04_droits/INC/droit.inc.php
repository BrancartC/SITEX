<?php
/**
 * Created by PhpStorm.
 * User: Adrien
 * Date: 14/05/18
 * Time: 09:35
 */
if (count(get_included_files()) == 1) die("--access denied--");
/* ---------- GESTION DES STATUTS ---------- */
/**
 * Vérifie qu'un utilisateur est connecté.
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['user']);
}
/**
 * Vérifie si l'utilisateur à un profil administrateur
 * @return bool
 */
function isAdmin() {
    return isAuthenticated() && in_array('admin', $_SESSION['user']['lesProfils']);
}
/**
 * Vérifie si l'utilisateur à un profil sous-administrateur
 * @return bool
 */
function isSousAdmin() {
    return isAuthenticated() && in_array('sAdmin', $_SESSION['user']['lesProfils']);
}
/**
 * Vérifie si l'utilisateur est à un statut de réactivation
 * @return bool
 */
function isReactiv() {
    return isAuthenticated() && in_array('réac', $_SESSION['user']['lesStatuts']);
}
/**
 * Vérifie si l'utilisateur est à un status de mot de passe perdu
 * @return bool
 */
function isMdpp() {
    return isAuthenticated() && in_array('mdpp', $_SESSION['user']['lesStatuts']);
}
/**
 * Vérifie si l'utilisateur peut éditer les informations du site (Fonction non utilisée)
 * @return bool
 */
function isEdit() {
    return isAdmin() || !isReactiv();
}
/* ---------- GESTION DES DROITS ---------- */
/**
 * Crée les droits de l'utilisateur en fonction de son profils et de ses status et stocke ces droits en session
 * @return int
 */
function creeDroits() {
    $_SESSION['droitsDeBase'] = ['index', 'gestLog', 'pasvorto', 'formSubmit', 'formLogin'];
    if (isset($_SESSION['user']['droits'])) {
        $_SESSION['droits'] = &$_SESSION['user']['droits'];
    } else {
        $_SESSION['droits'] = &$_SESSION['droitsDeBase'];
    }
    if ( ! isAuthenticated()) return -1;
    $listeDesStatuts = [];
    $listeDesProfils = [];
    foreach ($_SESSION['user']['profile'] as $profil) {
        if ($profil['pEstStatus']) array_push($listeDesStatuts, $profil['pAbrev']);
        else array_push($listeDesProfils, $profil['pAbrev']);
    }
    $_SESSION['user']['lesStatuts'] = $listeDesStatuts;
    $_SESSION['user']['lesProfils'] = $listeDesProfils;
    $listeDesDroits = [];
    switch($listeDesProfils[0]) {
        case 'admin': $listeDesDroits = array_merge($listeDesDroits, ['modifConfig', 'displaySession', 'clearLog', 'resetSession']);
        case 'sAdmin': $listeDesDroits = array_merge($listeDesDroits, ['testDB', 'config']);
        case 'modo': $listeDesDroits = array_merge($listeDesDroits, ['tableau', 'moderation']);
        case 'memb': $listeDesDroits = array_merge($listeDesDroits, ['sem02', 'sem03', 'sem04', 'TPsem05', 'formTP05', 'userProfil']);
        case 'ano': $listeDesDroits = array_merge($listeDesDroits, $_SESSION['droitsDeBase']);
    }
    $_SESSION['user']['droits'] = $listeDesDroits;
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
}
/* ---------- GENERATION DES MENUS ---------- */
/**
 * Génère dynamiquement les menus à afficher en fonction du profil et des status de l'utilisateur
 * @return string
 */
function creeMenu() {
    $gestLog = 'Connexion';
    $profil = 'ano';
    if (isAuthenticated()) {
        $gestLog = 'Déconnexion';
        $profil = $_SESSION['user']['lesProfils'][0];
    }
    $menu = [];
    array_unshift($menu, <<<MENU
            <li><a href="gestLog.html">$gestLog</a></li>
MENU
    );
    switch ($profil) {
        case 'admin':
        case 'sAdmin':
            array_unshift($menu, <<<MENU
                <li>Admin
                    <ul class="menu sMenu">
                        <li><a href="testDB.html" id="test">test</a></li>
                        <li><a href="config.html">Configuration</a></li>
                        <li>Session
                            <ul class="menu sMenu">
                                <li><a href="displaySession.html">affiche</a></li>
                                <li><a href="clearLog.html">efface log</a></li>
                                <li><a href="resetSession.html">redémarre</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
MENU
            );
        case 'modo':
            array_unshift($menu, <<<MENU
                <li>Modo
                    <ul class="menu sMenu">
                        <li><a href="tableau.html">JSON 00</a></li>
                         <li><a href="moderation.html">Modération</a></li>
                    </ul>
                </li>          
MENU
            );
        case 'memb':
            array_unshift($menu, <<<MENU
                <li>Membre
                    <ul class="menu sMenu">
                        <li><a href="userProfil.html">Profil</a></li>
                        <li>TP
                            <ul class="menu sMenu">
                                <li><a href="sem02.html">TP02</a></li>
                                <li><a href="sem03.html">TP03</a></li>
                                <li><a href="sem04.html">TP04</a></li>
                                <li><a href="TPsem05.html">TP05</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
MENU
            );
        case 'ano':
            array_unshift($menu, <<<MENU
                <li><a href="index.html">Accueil</a></li>
MENU
            );
    }
    return implode("\n", $menu);
}