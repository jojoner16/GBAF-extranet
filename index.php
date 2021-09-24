<?php 
    session_start();
    // Chargement des fonctions génériques
    require_once 'php/fonction.php';
    $pdo = connect_bdd();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="images/logo.png" />
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <title>GBAF</title>
    </head>
    
    <body>
        <?php

            // REDIRECTION: CONNECTÉ
            if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
            {
                header('Location: user_conected.php');
                exit();
            }

            if (isset($_POST['dataSubmit']) && !empty($_POST['username']) && !empty($_POST['password'])) 
            {
                // Cherche l'utilisateur dans la BDD (voir account)
                $dataAccount = searchUser($bdd, $_POST['username']);
                // Si l'user existe, et que tous les champs sont remplis
                if ($dataAccount) 
                {
                    // Vérification mot de passe (Hashé)
                    $isPasswordCorrect = password_verify($_POST['password'], $dataAccount['password']);
                    // Si le mot de passe correspond
                    if ($isPasswordCorrect) 
                    {
                        // Connexion
                        $_SESSION['nom'] = htmlspecialchars($dataAccount['nom']);
                        $_SESSION['prenom'] = htmlspecialchars($dataAccount['prenom']);
                        $_SESSION['id_user'] = $dataAccount['id_user'];
                        $_SESSION['username'] = htmlspecialchars($dataAccount['username']);

                        header('Location: user_conected.php');
                        exit();
                    }
                    //mot de passe incorrect
                    if (!$isPasswordCorrect) 
                    {
                        $message = MOT_DE_PASSE_INCORRECT;
                    }
                }
                // identifiant n'existe pas
                if (!$dataAccount) 
                {
                    $message = UTILISATEUR_INCONNU;
                }
            }
            // champs non remplis
            if (isset($_POST['dataSubmit']) && empty($_POST['username']) && empty($_POST['password'])) 
            {
                $message = CHAMP_VIDE;
            }



            // NON CONNECTÉ - page de connexion
            if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) 
            {
                require_once('member_area.php');

        ?>

    <main class="inscription-connexion">
        <div class="form_container">
            <fieldset>
                <legend>Se connecter :</legend>

                <span class="message">
                    <?php echo $message; ?>
                </span>
                <form method="post" action="index.php">
                    <p>
                        <label for="pseudo">Identifiant : </label>
                        <input type="text" id="pseudo" name="username" required/>

                        <label for="mp">Mot de passe : </label>
                        <input type="password" id="mp" name="password" required/>

                        <input class="button-envoyer" type="submit" name="dataSubmit" value="Connexion" onclick=" unsetPreviousSession()"/>
                                    
                        <span>Les champs indiqués par une <em>*</em> sont obligatoires</span>    
                    </p>
                </form>
                <a href="/mp.php"> mot de passe oublié ? </a>

                <a href="/inscription.php"> créer un compte </a>
            </fieldset>
        </div>
    </main>

    <?php 
        // require_once('pied_de_page.php');
    }
    ?>
    </body>
</html>