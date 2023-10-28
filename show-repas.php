<?php
session_start();
$filename = __DIR__ . '/public/data/listeRepas.json';
$dollarcanadian = "CAD";



if (isset($_SESSION["firstName"]) && isset($_SESSION["lastName"])) {
    $hiddenConnectStatus = "hidden";
    $hiddenUsername = "";
    $hiddenDisconnectStatus = "hidden";
    $hiddenFilter = "hidden";
} else {
    $hiddenConnectStatus = "hidden";
    $hiddenDisconnectStatus = "";
    $hiddenUsername = "hidden";
    $hiddenFilter = "hidden";
}

$listeRepas = [];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';
$ratings = $_GET['ratings'] ?? '';
$comments = $_GET['comments'] ?? '';
$length = $_GET['length'] ?? '';

if (!$id) {
    header('location: /');
} else {
    if (file_exists($filename)) {
        $listeRepas = json_decode(file_get_contents($filename), true) ?? [];
        $repasIndex = array_search($id, array_column($listeRepas, 'id'));
        $repas = $listeRepas[$repasIndex];
    }
    // ajouter le cas où le fichier n'existe pas
}

$accounts = __DIR__ . '/public/data/accounts.json';

if (file_exists($accounts)) {
    $listeAccounts = json_decode(file_get_contents($accounts), true) ?? [];
    $hiddenTags = "hidden";
    $connexionStatus = "connexion";
} else {
    $error_message = "Un problème technique est survenu.";
    echo $error_message;

    // Vérifiez si l'utilisateur est connecté
if(!isset($_SESSION['username'])){
  echo 'Vous devez être connecté pour télécharger des fichiers.';
  exit();
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php'; ?>
    <link rel="stylesheet" href="public/css/show-repas.css">
    <title>Repas</title>
    <style>
        /* Set the map container size */
        #map-container {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php'; ?>
        <div class="newsfeed-content">
            <div class="repas-container">
                <?php
                $length = 0;
                if (isset($repas['opinion']) && is_array($repas['opinion'])) {
                    $opinions = $repas['opinion'];
                    $ratings = $opinions['rating'];
                    $comments = $opinions['comments'];
                    foreach ($ratings as $rating) {
                        $length++;
                    }
                }
                ?>
                <div class="overflow">
                    <div class="img-container" style="background-image: url(<?= $repas['image'] ?>)"></div>
                    
                </div>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <h3>Ajoutez votre photo :</h3>
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Envoyer" name="submit">
                </form>
                <div>
                    <ul>
                        <li><?= $repas['nom'] ?></li>
                        <li><?= $repas['price'] ?> <?= $dollarcanadian ?></li>
                        <li><?= $repas['location'] ?></li>
                    </ul>
                    <p class="repas-content"><?= $repas['description'] ?></p>
                </div>
                <div></div>
            </div>
        </div>
        <?php require_once 'includes/footer.php'; ?>
    </div>
</body>

</html>
