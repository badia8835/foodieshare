<?php
session_start();
$accounts = __DIR__ . '/public/data/accounts.json';

$hiddenConnectStatus="hidden";
$hiddenUsername="hidden";
$hiddenDisconnectStatus="hidden";
$hiddenFilter="hidden";
//print_r($filename);
$errors = [
    'firstName' => '',
    'lastName' => '',
    'username' => '',
    'password' => '',
    'email' => '',
];

// Essayer toujours !! d'afficher vos variables pour comprendre
// mieux le fonctionnement
//print_r($accounts);

if (file_exists($accounts)) {

    // Si le contenu du fichier est pas vide alors, obtenir le contenu du fichier puis,
    // convertir le format json en un tableau PHP associatif
    // ?? [] : sinon affecter a la variable $ listeRepas un tableau vide []
    // Ceci evite des erreurs dans le code plus tard en initialisant de
    // toute facon la variable $listeAccounts
    $listeAccounts = json_decode(file_get_contents($accounts), true) ?? [];

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Filtrer les donnees du formulaire
    $_POST = filter_input_array(INPUT_POST, [

        'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
        'lastName' => FILTER_SANITIZE_SPECIAL_CHARS,
        'username' => FILTER_SANITIZE_SPECIAL_CHARS,
        'password' => FILTER_SANITIZE_SPECIAL_CHARS,
        'email' => FILTER_SANITIZE_EMAIL,
    ]
    );

    //initialiser les variables filtrer et valider
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!$firstName) {
        $errors['firstName'] = 'Saisir le prenom svp !';
    }

    if (!$lastName) {
        $errors['lastName'] = "Saisir le nom svp  !";

    }

    if (!$username) {
        $errors['username'] = 'Saisir un nom d utilisateur  svp !';
    }

    if (!$password) {
        $errors['password'] = 'Saisir le mot de passe svp !';
    }

    if (!$email) {

        $errors['email'] = 'Saisir un email svp !';
    }

    // empty retourne true si uen variable est vide
    // array_filter($errors, fn($e) => $e !== '') retourne un nouveau tableau associatif
    //qui contient uniquement les elements dont la valeur nest pas une chaine de
    // characteres vide. Cela evite de soumettre le formulaire avec au moins une erreur
    // (possibilites de attaque)
    if (empty(array_filter($errors, fn($e) => $e !== ''))) {


        //verifier si ce compte existe ou non 

        foreach ($listeAccounts as $account) {

            if ( $username == $account['username']) {
                // compte existe successful
                $existAccount= true; 
                break;
            }
         }

         if ($existAccount == true){
            echo '<h1 style="color:#FF0000;">Le nom de l\'utilisateur est deja existe.</h1>';
         }else{
            $listeAccounts = [...$listeAccounts, [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'id' => time(),
            ],
            ];
    
            file_put_contents($accounts, json_encode($listeAccounts));
            header("location: index.php");
         } 

    }
}

?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <?php require_once 'includes/head.php'?>
        <link rel="stylesheet" href="public/css/add-repas.css">
        <title>Créez un compte</title>
    </head>

    <body>
        <div class="container">
            <?php require_once 'includes/header.php'?>
            <div class="content">
                <div class="block p-20 form-container">
                    <h1>Créez un compte</h1>
                    <form action="/addAccount.php" method="post">
                      
                        <!-- firstName -->
                        <div class="form-control">
                            <label for="firstName">Prenom</label>
                            <input type="text" name="firstName" id="firstName" value=<?=$firstName ?? ''?>>
                            <?php if ($errors['firstName']): ?>
                            <p class='text-danger'><?=$errors['firstName']?></p>
                            <?php endif;?>
                        </div>

                        <!-- lastName -->
                        <div class="form-control">
                            <label for="lastName">Nom</label>
                            <input type="text" name="lastName" id="lastName" value=<?=$lastName ?? ''?>>
                            <?php if ($errors['lastName']): ?>
                            <p class='text-danger'><?=$errors['lastName']?></p>
                            <?php endif;?>
                        </div>
                        
                        <!-- username -->
                        <div class="form-control">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" name="username" id="username" value=<?=$username ?? ''?>>
                            <?php if ($errors['username']): ?>
                            <p class='text-danger'><?=$errors['username']?></p>
                            <?php endif;?>
                        </div>

                        <!-- password -->
                        <div class="form-control">
                            <label for="password">Mot de passe</label>
                            <input type="text" name="password" id="password" value=<?=$password ?? ''?>>
                            <?php if ($errors['password']): ?>
                            <p class='text-danger'><?=$errors['password']?></p>
                            <?php endif;?>
                        </div>                        

                        <!-- email -->
                        <div class="form-control">
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" value=<?=$email ?? ''?>>
                            <?php if ($errors['email']): ?>
                            <p class='text-danger'><?=$errors['email']?></p>
                            <?php endif;?>
                        </div>

                        <!-- Boutons -->
                        <div class="form-actions">
          
                            <a href="index.php" class="btn btn-danger">Annuler</a>
                            <button class="btn btn-primary" name="save" type="submit">Sauvegarder</button>
                        </div>
                    </form>
                </div>
                
            </div>
            <?php require_once 'includes/footer.php'?>
        </div>
    </body>
</html>