<?php 
    //session_start();
    // Chargement des fonctions génériques
    require_once 'php/fonction.php';
    $pdo = connect_bdd();
?>

<!DOCTYPE html>

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="images/logo1.png" />
        <link rel="stylesheet" type="text/css" href="css/connexion.css">
        <title>GBAF connexion</title>
    </head>
    <body>
        <div class="contain_all">
            <header>
				<!-- Logo GBAF -->
				<div class="header-logo">
					<img id="logo" src="images/logo1.png" alt="Logo GBAF" />
                </div>
            </header>
        </div>
    
        <?php
            require_once 'php/account.php';

            // REDIRECTION: CONNECTÉ
            if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
            {
                header('Location: espace-membre/accueil.php');
                exit();
            }
            if (isset($_POST['dataSubmit']) && !empty($_POST['username']) && !empty($_POST['password'])) 
            {
                // Cherche l'utilisateur dans la BDD (voir account)
                $dataAccount = searchUser($pdo, $_POST['username']);
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

                        header('Location: espace-membre/accueil.php');
                        exit();
                    }
                    //mot de passe incorrect
                    if (!$isPasswordCorrect) 
                    {
                        $message = PASSWORD_WRONG;
                    }
                }
                // identifiant n'existe pas
                if (!$dataAccount) 
                {
                    $message = USERNAME_UNKNOWN;
                }
            }
            // champs non remplis
            if (isset($_POST['dataSubmit']) && empty($_POST['username']) && empty($_POST['password'])) 
            {
                $_SESSION['message']=  'ERREUR : veuillez remplir tous les champs !';
                header('Location: index.php'); exit;
            }
        ?>

                        <!-- index html -->
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
                                <input type="text" id="pseudo" name="username"/>

                                <label for="mp">Mot de passe : </label>
                                <input type="password" id="mp" name="password"/>

                                <input class="button-envoyer" type="submit" name="dataSubmit" value="Connexion"/>
                                            
                                <span>Les champs indiqués par une <em>*</em> sont obligatoires</span>    
                            </p>
                        </form>
                    <a href="mp.php"> Mot de passe oublié ? </a>
                    <a href="inscription.php"> Créer un compte </a>
                </fieldset>
            </div>
        </main>
<?php 
    require_once 'header-footer/footer.php';
?>
 