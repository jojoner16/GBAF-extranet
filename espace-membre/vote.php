<?php
    session_start();
    require_once '../php/fonction.php';
    $pdo = connect_bdd();

    // REDIRECTION: NON CONNECTÉ ou PAS DE VOTE
    if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user']) && !isset($_GET['vote']) && !isset($_GET['id_acteur'])) 
    {
        header('Location: ../index.php');
        exit();
    }

    // CONNECTÉ
    if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user']) && isset($_GET['vote']) && isset($_GET['id_acteur'])) 
    {
        //Cherche le vote de l'utilisateur sur la page acteur
        $req_vote_user = $pdo->prepare('SELECT vote FROM vote WHERE acteur_id_acteur = ? AND user_id_user = ?');
        $req_vote_user->execute(array($_GET['id_acteur'], $_SESSION['id_user']));
        $userVote = $req_vote_user->fetch();
        $req_vote_user->closeCursor();

        // Si il a pas voté
        if (!$userVote) 
        {
                // on Ajoute son vote
                $req_insert_vote = $pdo->prepare('INSERT INTO vote (user_id_user, acteur_id_acteur, vote) VALUES (:id_user, :id_acteur, :vote)');

                $req_insert_vote->execute(array(
                    'id_user' => ($_SESSION['id_user']),
                    'id_acteur' => ($_GET['id_acteur']),
                    'vote' => ($_GET['vote'])
                ));
                $req_insert_vote->closeCursor();
                header('Location: partenaire.php?id_acteur=' . $_GET['id_acteur']);
                exit();
        } 
        // Si il a voté
        if ($userVote && $_GET['vote'] != $userVote['vote'])
        {
            $_SESSION['message']= 'Vous avez déjà voté !';
            header('Location: partenaire.php?id_acteur=' . $_GET['id_acteur']);
            exit();
        }

        // REDIRECTION: vote identique
        if ($userVote && $_GET['vote'] == $userVote['vote']) 
        {
            header('Location: partenaire.php?id_acteur=' . $_GET['id_acteur']);
            exit();
        }
    }