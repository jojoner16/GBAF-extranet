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
        <link rel="stylesheet" type="text/css" href="styles.css">
        <title>GBAF inscription</title>
    </head>
    
    <body>
        <?php
        require_once 'header-footer/header.php';
        // require_once('core/account.php');

        // REDIRECTION: CONNECTÉ
        // if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
        // {
        //     header('Location: /espace-membre/accueil.php');
        //     exit();
        // }


        // Si on envoi le formulaire
        if (isset($_POST['dataSubmit'])) 
        {
            

            if (!$dataUser && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['question']) && !empty($_POST['reponse'])) 
            {
                // Verification que le mot de passe contient minimum 1 lettre 1 maj et 1 chiffre
                if (preg_match($mpValid, $_POST['password'])) 
                    {
                        // Hashage du mot de passe
                        $passwordHashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $reponseHashed = password_hash($_POST['reponse'], PASSWORD_DEFAULT);
                        // Insère le nouvel Utilisateur dans la BDD
                        $req_add_user = $bdd->prepare('INSERT INTO user (nom, prenom, username, password, question, reponse) VALUES (:nom, :prenom, :username, :password, :question, :reponse)');
                        $req_add_user->execute(array(
                        'nom' => ($_POST['nom']),
                        'prenom' => ($_POST['prenom']),
                        'username' => ($_POST['username']),
                        'password' => $passwordHashed,
                        'question' => ($_POST['question']),
                        'reponse' => $reponseHashed
                        ));

                        $req_add_user->closeCursor();

                        $message = WELCOME;

                        $_SESSION['username'] = htmlspecialchars($_POST['username']);
                        $_SESSION['message'] = $message;

                        header('Location: /index.php');
                        exit();
                    } 
                    // message : mauvais mp
                    if (!preg_match($mpValid, $_POST['password'])) 
                    {
                        $message = PASSWORD_WRONG;
                    }
            }

                // message : un des champs est vide
                if (empty($_POST['nom']) OR empty($_POST['prenom']) OR empty($_POST['username']) OR empty($_POST['password']) OR empty($_POST['question']) OR empty($_POST['reponse'])) 
                {
                    $message = EMPTY_FIELD;
                }

                // message : l'username existe déja
                if ($dataUser) 
                {
                    $message = USERNAME_EXIST;
                }
        }

        // NON CONNECTÉ - page inscription
        if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) 
        {
            //require_once 'header.php';
        }
 
        ?>

                <!-- Page d'inscription HTML -->
    <main class="inscription-connexion">
        <div class="form_container">
            <fieldset>
                <legend>Créer un compte :</legend>

                <!-- message erreur -->
                <!-- <span class="message-erreur">
                    /
                </span> -->

                <!-- Formulaire avec 'value' préenregistrées -->
                <form method="post" action="inscription.php">
                    <p>
                        <label for="pseudo">Identifiant : </label>
                        <input type="text" id="pseudo" name="username" size="20"/>

                        <label for="mp">Mot de passe : </label>
                        <input type="password" id="mp" name="password" size="20"/>

                        <label for="nom">Nom : </label>
                        <input type="text" id="nom" name="nom" size="30"/>

                        <label for="prenom">Prénom : </label>
                        <input type="text" id="prenom" name="prenom" size="30"/>

                        <label for="question">
                            Votre question secrète : 
                        </label>
                        <input type="text" id="question" name="question"/>

                        <label for="reponse">
                            La réponse à votre question : 
                        </label>
                        <input type="text" id="reponse" name="reponse"/>

                        <input class="button-envoyer" type="submit" name="dataSubmit" value="Envoyer"/>

                        <span>
                            Les champs indiqués par une * sont obligatoires
                        </span>
                    </p>
                </form>

                <a href="index.php"> se connecter </a>

                <a href="/mp.php"> mot de passe oublié ? </a>
            </fieldset>
        </div>
    </main>
        <?php require_once 'header-footer/footer.php'?>
    </body>
</html>