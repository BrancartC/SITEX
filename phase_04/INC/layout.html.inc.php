<?php
if(count(get_included_files()) == 1) die('pas la permission');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns="" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $home; ?></title>
    <!-- La feuille de styles "base.css" doit être appelée en premier. -->
    <linl rel="stylesheet" type="text/css" href="CSS/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="CSS/base01.css" media="all" />
    <link rel="stylesheet" type="text/css" href="CSS/modele05.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="CSS/index.css" media="screen" />
    <script type="text/javascript" src="/all/jQ/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="JS/jquery-ui.min.js"></script>
    <script type="text/javascript" src="JS/index.js"></script>
</head>

<body>

<div id="global">

    <header id="entete">
        <h1>
            <!-- <img id=logo src="?php echo $logoSrc ?>" alt="?php echo $logoAlt; ?>" /> -->
            <img id="logo" alt="<?= $logoAlt ?>" src="<?= $logoPath ?>" />
            <?php echo $siteName; ?>

        </h1>
        <nav>
            <ul id="menu" class="menu">
                <li><a href="accueil.html">Accueill</a></li>
                <li><a href="userProfil.html">Profil</a></li>
                <li><a href="userInfos.html">Mes infos</a></li>
                <li><a href="config.html">Configuration</a></li>
                <li> SESSION
                  <ul id="sMenu" class="menu"> <!- regarder l'id ou la classe ->
                    <li><a href="displaySession.html">affiche</a></li>
                    <li><a href="clearLog.html">efface log</a></li>
                    <li><a href="resetSession.html">redémarre</a></li>
                  </ul>
                </li>
                <li><a href="connect.html">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <nav id="sous-menu" class="menu">
        <ul>
            <li><a href="tableau.html">JSON 00</a></li>
            <li><a href="sem02.html">TP02</a></li>
            <li><a href="sem03.html">TP03</a></li>
            <li><a href="sem04.html">TP04</a></li>
            <li><a href="TPsem05.html" id="TPsem05">TP05</a></li>
            <li><a href="testDB.html" id="test">test</a></li>
        </ul>
    </nav>

    <main id="contenu">
        <?php echo $mainZone; ?>
    </main>

    <footer id="copyright">
        <span id="auteur"><?php echo $author; ?></span>@2018
        -
        <span>Crédits</span>
        <span id="credits">
            Mise en page &copy; 2008
            <a href="http://www.elephorm.com">Elephorm</a> et
            <a href="http://www.alsacreations.com">Alsacréations</a>
        </span>
    </footer>

</div><!-- #global -->
</body>
</html>
