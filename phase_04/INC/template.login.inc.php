<?php
if (count(get_included_files()) == 1) header("Location: ../");
?><section id="login">
    <form action="formSubmit.html" method="post" id="formLogin">
        <fieldset>
            <legend>Connexion</legend>
            <label for="pseudo">Pseudo </label><input id="pseudo" type="text" name="login[pseudo]" required><br>
            <label for="mdp">Mot de passe </label><input type="password" id="mdp" name="login[mdp]" required><a href="pasvorto.html" id="logMdPP">(perdu ou oubié ?)</a><br>
            <input type="submit" value="connexion">
        </fieldset>
    </form>
    <link href="CSS/template.login.css" rel="stylesheet">
</section>