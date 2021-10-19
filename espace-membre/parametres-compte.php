<?php
    require_once '../php/fonction.php';
    $pdo = connect_bdd();
    require_once '../php/account.php';

    // REDIRECTION: NON CONNECTÉ
    if (!isset($_SESSION['nom']) && !isset($_SESSION['prenom']) && !isset($_SESSION['id_user'])) 
    {
        header('Location: ../index.php');
        exit();
    }

    // Cherche si l'utilisateur existe dans la BDD 
    $dataAccountOld = searchUser($pdo, $_SESSION['username']);

    // si on envoie le formulaire
    if (isset($_POST['dataSubmit'])) 
    {
        $dataAccount = searchUser($pdo, $_POST['username']);

        // Si l'username n'existe pas
        if (!$dataAccount OR $dataAccountOld['username'] == $_POST['username']) 
        {
            // Verification tous les champs ont été remplis
            if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['question']) && !empty($_POST['reponse']) && !empty($_POST['password']) && !empty($_POST['username'])) 
            {
                // et que le mot de passe est correct
                $isPasswordCorrect = password_verify($_POST['password'], $dataAccountOld['password']);

                if ($isPasswordCorrect) 
                {
                    $reponseHashed = password_hash($_POST['reponse'], PASSWORD_DEFAULT);

                    // Change les infos de la BDD
                    $req_update_infos_user = $pdo->prepare('UPDATE user SET username = :username, nom = :nom,  prenom = :prenom, question = :question, reponse = :reponse WHERE id_user = :id_user');

                    $req_update_infos_user->execute(array(
                        'username' => ($_POST['username']),
                        'nom' => ($_POST['nom']),
                        'prenom' => ($_POST['prenom']),
                        'question' => ($_POST['question']),
                        'reponse' => $reponseHashed,
                        'id_user' => $dataAccountOld['id_user']
                    ));

                    $usernameNew = $_POST['username'];
                    $req_update_infos_user->closeCursor();

                    // Récupère les nouvelles valeurs de SESSION (fonction voir account)
                    $dataAccountNew = searchUser($pdo, $usernameNew);

                    $_SESSION['nom'] = htmlspecialchars($dataAccountNew['nom']);
                    $_SESSION['prenom'] = htmlspecialchars($dataAccountNew['prenom']);
                    $_SESSION['username'] = htmlspecialchars($dataAccountNew['username']);

                    $message = ACCOUNT_UPDATE;
                    header('Refresh: 5; url=parametres-compte.php');
                }
                if (!$isPasswordCorrect) 
                {
                    $message = PASSWORD_WRONG;
                }
            } 
            if (empty($_POST['nom']) OR empty($_POST['prenom']) OR empty($_POST['question']) OR empty($_POST['reponse']) OR empty($_POST['password']) OR empty($_POST['username'])) 
            {
                $_SESSION['message']=  'ERREUR : veuillez remplir tous les champs !';
                header('Location: parametres-compte.php'); exit;
            }
        }
        if ($dataAccount AND $dataAccountOld['username'] != $_POST['username']) 
        {
            $message = USERNAME_EXIST;
        }
    }

    // CONNECTÉ:
    if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['id_user'])) 
    {
        require_once '../header-footer/header.php';
                    
?>
                                                                        <!-- HTML FORMULAIRE INSCRIPTION  -->

    <main class="inscription-connexion">
        <div class="form_container">
            <fieldset>
                <legend>Modifier son Profil :</legend>

                <!-- message erreur -->
                <span class="message-erreur">
                    <?php echo $message; ?>
                </span>
                <!-- Formulaire -->
                <form method="post" action="parametres-compte.php">
                    <p>
                        <label for="pseudo">Identifiant : </label>
                        <input type="text" id="pseudo" name="username" size="20" value="<?php defaultInputValue('username', $dataAccountOld['username']);?>"/>

                        <label for="nom">Nom : </label>
                        <input type="text" id="nom" name="nom" size="30" value="<?php defaultInputValue('nom', $dataAccountOld['nom']);?>"/>

                        <label for="prenom">Prénom : </label>
                        <input type="text" id="prenom" name="prenom" size="30" value="<?php defaultInputValue('prenom', $dataAccountOld['prenom']);?>"/>

                        <label for="question">Votre question secrète : </label>
                        <input type="text" id="question" name="question" value="<?php defaultInputValue('question', $dataAccountOld['question']);?>"/>

                        <label for="reponse">La réponse a votre question : </label>
                        <input type="text"  id="reponse"  name="reponse" value=""/>

                        <label for="mp">Entrez votre mot de passe: </label>
                        <input type="password" id="mp" name="password" size="20"/>

                        <input class="button-envoyer" type="submit" name="dataSubmit" value="Envoyer" onclick="unsetPreviousSession()"/>

                        <span>
                            <em>*</em> Tous les champs doivent être remplis
                        </span>
                    </p>
                </form>

                <a href="deconnexion.php?redirection=mp"> changer son mot de passe </a>

                <a href="accueil.php"> Retour à l'accueil </a>
            </fieldset>
        </div>
    </main>

<?php
    require_once '../header-footer/footer.php';
}
?>