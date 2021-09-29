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
        <title>GBAF connexion</title>
    </head>
    
    <body>
        <?php
            require_once 'header-footer/header.php';
            // Si l'action "déconnexion" est passé en URL, on détruit la session
			if(isset($_GET['action']) && $_GET['action'] === 'deconnexion')
			{
				$_SESSION['message']= 'Vous venez de vous déconnecter.';
				header('Refresh: 2; url=connexion.php');
				session_destroy();

				// Suppression des cookies de connexion automatique
				setcookie('username', '');
				setcookie('pasword', '');
			}

            if(isset($_POST['username']) && isset($_POST['password'])) 
            {   
                $require= $pdo->prepare('SELECT username, password FROM user WHERE id_user');

                $username= htmlspecialchars($_POST['username']);
                $password= htmlspecialchars($_POST['password']);

                $require->execute(array(
                    ':username'=> $username,
                    ':password'=> $password
                ));
                $user = $require->fetch(PDO::FETCH_ASSOC);
                   
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = htmlspecialchars($POST['username']);
                $_SESSION['nom'] = htmlspecialchars($dataAccount['nom']);
                $_SESSION['prenom'] = htmlspecialchars($dataAccount['prenom']);
                header('Location: espace-membre/accueil.php');   
            }            
        ?>

                    <!-- index html -->
    <main class="inscription-connexion">
        <div class="form_container">
            <fieldset>
                <legend>Se connecter :</legend>
                    <form method="post" action="index.php">
                        <p>
                            <label for="pseudo">Identifiant : </label>
                            <input type="text" id="pseudo" name="username" required/>

                            <label for="mp">Mot de passe : </label>
                            <input type="password" id="mp" name="password" required/>

                            <input class="button-envoyer" type="submit" name="dataSubmit" value="Connexion"/>
                                        
                            <span>Les champs indiqués par une <em>*</em> sont obligatoires</span>    
                        </p>
                    </form>
                <a href="mp.php"> mot de passe oublié ? </a>
                <a href="inscription.php"> créer un compte </a>
            </fieldset>
        </div>
    </main>
        <?php require_once 'header-footer/footer.php'?>
    </body>
</html>