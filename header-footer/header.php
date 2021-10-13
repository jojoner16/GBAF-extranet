<!DOCTYPE html>
<html lang="fr">
	<head>
        <meta charset="utf-8" />
		<link rel="stylesheet" href="../styles.css" />
        <link rel="icon" type="image/jpg" href="../images/logo.png" />
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;900&display=swap"	rel="stylesheet"/>
        <!-- <title>GBAF extranet</title> -->
	</head>

	<body>
        <div class="contain_all">
            <header>
				<!-- Logo GBAF -->
				<div class="header-logo">
					<img id="logo" src="../images/logo.png" alt="Logo GBAF" />
                </div>
                
                <?php // affiche le profil uniquement si un utilsateur est connecté
                if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
                {
                ?>

                <!-- Profil -->
                <ul class="header-profil">
                    <!-- Nom Prenom -->
                    <li class="profil_nom">
                        <p> <?php echo htmlspecialchars($_SESSION['nom']); ?> </p>
                        <p> <?php echo htmlspecialchars($_SESSION['prenom']); ?> </p>
                    </li>

                    <!-- bouton Déconnexion -->
                    <li>
                        <a href="../espace-membre/deconnexion.php?redirection=exit">Se déconnecter</a>
                    </li>

                    <!-- bouton Modifier son Profil -->
                    <li>
                        <a href="../espace-membre/parametres-compte.php">Paramètres du compte</a>
                    </li>
                </ul>
                <?php
                }
                ?>
            </header>
        </div>