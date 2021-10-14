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
            <title>GBAF partenaire</title>
        </head>
        
<?php
    // REDIRECTION: NON CONNECTÉ
    if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) 
    {
        header('Location: index.php');
        exit();
    }

    $idActeur = htmlspecialchars($_GET['id_acteur']);

    // A. Cherche les infos de l'Acteur
    $req_data_acteur = $pdo->prepare('SELECT * FROM acteur WHERE id_acteur = ?');
    $req_data_acteur->execute(array($idActeur));
    $dataActeur = $req_data_acteur->fetch();
    $req_data_acteur->closeCursor();

    // B. Ajoute un nouveau commentaire unique

    // Cherche si l'utilisateur a déjà commenté
    $req_already_comment = $pdo->prepare('SELECT * FROM post WHERE user_id_user = ? AND acteur_id_acteur = ?');
    $req_already_comment->execute(array($_SESSION['id_user'], $idActeur));
    $userHasComment = $req_already_comment->fetch();
    $req_already_comment->closeCursor();

    // si il n'a pas déjà commenté
    if (!$userHasComment) 
    {
        $formComment = '';

        if (isset($_POST['newCommentPosted']) and !empty($_POST['post'])) 
        {
            $req_insert_comment = $pdo->prepare('INSERT into post (user_id_user, acteur_id_acteur, date_add, post) VALUES (:user_id_user, :acteur_id_acteur, NOW(), :post)');

            $req_insert_comment->execute(array(
                'user_id_user' => $_SESSION['id_user'],
                'acteur_id_acteur' => $dataActeur['id_acteur'],
                'post' => ($_POST['post'])
            ));
            $req_insert_comment->closeCursor();
        }
    }
    if ($userHasComment) 
    {
        $formComment = 'Vous avez déjà commenté !';
    }

    // C. Compte le nombre de commentaire sur l'acteur
    $req_nbr_comments = $pdo->prepare('SELECT COUNT(*) as nbrComments FROM post WHERE acteur_id_acteur = ?');
    $req_nbr_comments->execute(array($idActeur));
    $commentsPosted = $req_nbr_comments->fetch();
    $nbrcommentsPosted = $commentsPosted['nbrComments'];
    $req_nbr_comments->closeCursor();

    // D. Fonction Compte le nombre de 'like' et 'Dislike' sur l'acteur
    function nbrLikeDislike($idActeur, $voteValue, $pdo)
    {
        $req_nbr_like_dislike = $pdo->prepare('SELECT COUNT(vote) as nombre FROM vote WHERE acteur_id_acteur = ? AND vote = ?');

        $req_nbr_like_dislike->bindValue(1, $idActeur, PDO::PARAM_INT);
        $req_nbr_like_dislike->bindValue(2, $voteValue, PDO::PARAM_STR);

        $req_nbr_like_dislike->execute();
        $likeOrDislike = $req_nbr_like_dislike->fetch();
        $req_nbr_like_dislike->closeCursor();

        if (isset($likeOrDislike['nombre'])) 
        {
            echo $likeOrDislike['nombre'];
        }
    }

    // E. Cherche si l'user a like ou a dislike
    $req_vote_user = $pdo->prepare('SELECT vote FROM vote WHERE acteur_id_acteur = ? AND user_id_user = ?');
    $req_vote_user->execute(array($_GET['id_acteur'], $_SESSION['id_user']));
    $userVote = $req_vote_user->fetch();
    $req_vote_user->closeCursor();

    // F. Fonction qui affiche tous les commentaires sur l'acteur
    function listCommentaires($pdo, $idActeur)
    {
        $req_comment = $pdo->prepare('SELECT  p.post as comment, DATE_FORMAT(p.date_add, "%d/%m/%Y") as commentDate, DATE_FORMAT(p.date_add, "%d/%m/%Y %T") as commentDateOrder, a.prenom as autorName FROM post p INNER JOIN user a ON user_id_user = a.id_user WHERE acteur_id_acteur = ? ORDER by commentDateOrder DESC');

        $req_comment->bindValue(1, $idActeur, PDO::PARAM_INT);
        $req_comment->execute();

        while ($dataComment = $req_comment->fetch()) 
        {
            echo '<li>';
            echo '<p>' . htmlspecialchars($dataComment['autorName'])  . '</p>';
            echo '<p>' . $dataComment['commentDate']  . '</p>';
            echo '<p>' . htmlspecialchars($dataComment['comment'])  . '</p>';
            echo '</li>';
        }
        $req_comment->closeCursor();
    }

    // CONNECTÉ:
    if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
    {

        require_once '../header-footer/header.php';
?>    
                                                    <!-- HTML page Partenaire  -->
    
    <main>
        <!-- A. Section infos de l'acteur -->
        <section class="partenaire">
            <?php
                echo '<img src="../images/'.$dataActeur['logo'].'">';
                echo '<h2>' . $dataActeur['acteur'] . '</h2>';
                echo '<div class="text"><p>' . $dataActeur['description'] . '</p></div>'
            ?>
        </section>
            
        <!-- Section commentaires -->
        <section class="commentaires">

            <!-- message erreur -->
            <div class="message-erreur-vote">
                <?php 
                    if (isset($userVote['vote']))
                    {
                        $_SESSION['message']= 'Vous avez déjà voté !';
                        echo $_SESSION['message'];
                    }
                ?>
            </div>

            <div class="commentaires_dynamic">
                <!-- C. Nombre de commentaires -->
                <h4> 
                    <?php echo $nbrcommentsPosted; ?> 
                    commentaire(s) 
                </h4>
                
                <!-- Ajouter un nouveau commentaire -->
                <div class="new_commentaire">
                    <label class="open_popup" for="popup_button">Nouveau commentaire</label>
                    <input type="checkbox" id="popup_button"/>
                    <!-- B. fenêtre pop-up du formulaire -->
                    <form class="new_commentaire_formulaire" method="post" action="#">
                        <p>
                            <label class="close_popup" for="popup_button"></label>
                            <label for="post">Ajoutez un nouveau commentaire sur <strong><em> <?php echo $dataActeur['acteur']; ?> </em></strong>: </label>
                            <textarea id="post" name="post">
                                <?php echo $formComment; ?>
                            </textarea>
                            <input type="submit" value="Envoyer" name="newCommentPosted"/>
                        </p>
                    </form>
                </div>

                <!-- Likes / Dislikes -->
                <div class="commentaires_vote">
                    <!-- Ajoute un like (vote) -->
                    <a class="vote_like" href="<?php echo 'vote.php?id_acteur=' . $dataActeur['id_acteur'] . '&vote=like'; ?>">
                        <!-- D. Nombre de like -->
                        <p>
                            <?php nbrLikeDislike($dataActeur['id_acteur'], 'like', $pdo); ?>
                        </p>

                        <!-- E. icone like -->
                        <img src="<?php echo '../images/like.png'; ?>" alt="like"/>
                    </a>

                    <!-- Ajoute un dislike (vote) -->
                    <a class="vote_dislike" href="<?php echo 'vote.php?id_acteur=' . $dataActeur['id_acteur'] . '&vote=dislike'; ?>">
                        <!-- E. icone dislike -->
                        <img  src="<?php echo '../images/dislike.png'; ?>" alt="dislike"/>

                        <!-- D. Nombre de dislike -->
                        <p> 
                            <?php nbrLikeDislike($dataActeur['id_acteur'], 'dislike', $pdo); ?> 
                        </p>
                    </a>
                </div>
            </div>

            <!-- F. Liste de tous les commentaires -->
            <ul class="commentaires-list">
                <!--<li> -->
                <?php listCommentaires($pdo, $idActeur); ?>
            </ul>
            <div class="bouton">
                <button class="retour-accueil">
                    <a class="r-accueil" href="accueil.php">Retour à la page d'accueil</a>
                </button>
            </div>
        </section>
    </main>
    
<?php
    require_once '../header-footer/footer.php';
    }
?>