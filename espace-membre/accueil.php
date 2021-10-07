<?php
    session_start();
    require_once '../php/fonction.php';
    $pdo = connect_bdd();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="images/logo.png" />
        <link rel="stylesheet" type="text/css" href="/styles.css">
        <title>GBAF accueil</title>
    </head>
    
    <body>
        <?php

            // REDIRECTION: NON CONNECTÉ
            if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) 
            {
                header('Location: index.php');
                exit();
            }

            // fonction recherche les infos des acteurs dans la bdd
            function searchActeurs($pdo)
            {
                $req_data_acteur = $pdo->prepare('SELECT *, SUBSTR(description, 1, 135) AS firstLineDescription FROM acteur');
                $req_data_acteur->execute();

                while ($dataPartenaires = $req_data_acteur->fetch()) 
                {
                    echo '<li class="acteur_seul">';
                    echo '<img src="../images/'.$dataPartenaires['logo'].'">';
                    echo '<h3>' . $dataPartenaires['acteur'] . '</h3>';
                    echo '<div class="acteur-seul_description"><p>' . $dataPartenaires['firstLineDescription'] . ' (...)</p></div>';
                    echo '<a href="../espace-membre/partenaire.php?id_acteur=' . $dataPartenaires['id_acteur'] . ' ">Lire la suite</a> ';
                    echo '</li>';
                }
                $req_data_acteur->closeCursor();
            }
    // CONNECTÉ:
if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
{

    require_once '../header-footer/header.php';
    
        ?>
        
                  <!-- Page accueil -->
        <main>
            
            <!-- Section Présentation GBAF -->
            <section class="GBAF">
                <h1>GBAF (Groupement Banque Assurance Français)</h1>

                <div class="text">
                    <p>
                        <strong>
                            Le Groupement Banque Assurance Français (GBAF)
                        </strong>
                        est une fédération représentant les 6 grands groupes
                        français :
                    </p>
                    <ul>
                        <li>BNP Paribas</li>
                        <li>BPCE</li>
                        <li>Crédit Agricole</li>
                        <li>Crédit Mutuel-CIC</li>
                        <li>Société Générale</li>
                        <li>La Banque Postale</li>
                    </ul>
                    <p>
                        Même s’il existe une forte concurrence entre ces
                        entités, elles vont toutes travailler de la même façon
                        pour gérer près de 80 millions de comptes sur le
                        territoire national. <br/>Le GBAF est le représentant
                        de la profession bancaire et des assureurs sur tous  les
                        axes de la réglementation financière française. Sa
                        mission est de promouvoir l'activité bancaire à
                        l’échelle nationale. C’est aussi un interlocuteur
                        privilégié des pouvoirs publics.
                    </p>
                    <br/>
                    <p>
                        Les produits et services bancaires sont nombreux et très
                        variés. Afin de renseigner au mieux les clients, les
                        salariés des 340 agences des banques et assurances en
                        France (agents, chargés de clientèle, conseillers
                        financiers, etc.) recherchent sur Internet des
                        informations portant sur des produits bancaires et des
                        financeurs, entre autres. <br/>Aujourd’hui, il n’existe
                        pas de base de données pour chercher ces informations
                        de manière fiable et rapide ou pour donner son avis sur
                        les partenaires et acteurs du secteur bancaire, tels que
                        les associations ou les financeurs solidaires. Pour
                        remédier à cela, le GBAF souhaite proposer aux salariés
                        des grands groupes français un point d’entrée unique,
                        répertoriant un grand nombre d’informations sur les
                        partenaires et acteurs du groupe ainsi que sur les
                        produits et services  bancaires et financiers.
                        <br/>Chaque salarié pourra ainsi poster un commentaire
                        et donner son avis.
                    </p>
                </div>

                <div class="GBAF-illustration">
                    <img src="../images/logo.png" alt="Logo GBAF" />
                </div>
            </section>

            <!-- Section présentation Acteurs / Partenaires -->
            <section class="acteurs">
                <h2>Acteurs et partenaires du système bancaire français</h2>

                <div class="text1">
                    <p>
                        À l’échelle nationale, ces réseaux gèrent, dans un contexte de forte concurrence, plus de 80 % des quelque 73 millions de comptes courants. Ces réseaux sont pour la plupart fortement internationalisés.
                    </p>
                    <br/>
                    <p>
                        Les grands groupes français sont présents dans tous les métiers de la banque :
                    </p>
                    <ul>
                        <li>banque de détail </li>
                        <li>banque de financement et d’investissement (BFI).</li>
                    </ul>
                    <br/>
                    <p>
                        L’exercice des activités bancaires, des services d’investissement et des services de paiement est réservé aux entreprises bénéficiant d’un agrément et soumis à une surveillance particulière par l’Autorité de Contrôle Prudentiel et de Résolution (ACPR). Globalement au 1er janvier 2012, on comptait 655 établissements de crédit agréés en France par l’Autorité de Contrôle Prudentiel et de Résolution.
                    </p>
                </div>

                <nav class="acteurs_list">
                        <ul>
                            <!-- <li> acteur_seul-->
                            <?php searchActeurs($pdo); ?>
                        </ul>  
                </nav>
            </section>
        </main>
   
    <?php 
        require_once '../header-footer/footer.php';
}
    ?>
  