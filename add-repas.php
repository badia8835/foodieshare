<?php
session_start();
$filename = __DIR__ . '/public/data/listeRepas.json';

if (isset($_SESSION["firstName"]) && isset($_SESSION["lastName"])) {
    $hiddenConnectStatus="hidden";
    $hiddenUsername="";
    $hiddenDisconnectStatus="hidden";
    $hiddenFilter="hidden";
  } else {
    header("location: index.php");
    echo '<h1 style="color:#FF0000;">vous douvez connecter afin d\'ajouter une repas.</h1>';
  }

//print_r($filename);
$errors = [
    'nom' => '',
    'image' => '',
    'price' => '',
    'location' => '',
    'categorie' => '',
    'description' => '',
];

// Essayer toujours !! d'afficher vos variables pour comprendre
// mieux le fonctionnement
//print_r($filename);

if (file_exists($filename)) {

    // Si le contenu du fichier est pas vide alors, obtenir le contenu du fichier puis,
    // convertir le format json en un tableau PHP associatif
    // ?? [] : sinon affecter a la variable $ listeRepas un tableau vide []
    // Ceci evite des erreurs dans le code plus tard en initialisant de
    // toute facon la variable $listeRepas
    $listeRepas = json_decode(file_get_contents($filename), true) ?? [];

}

$selectedCat = $_GET['cat'] ?? '';

if (!empty($selectedCat)) {
    $listeRepas = array_filter($listeRepas, function($repas) use ($selectedCat) {
        return $repas['categorie'] === $selectedCat;
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Filtrer les donnees du formulaire
    $_POST = filter_input_array(INPUT_POST, [

        'nom' => FILTER_SANITIZE_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'price' => FILTER_SANITIZE_SPECIAL_CHARS,
        'location' => FILTER_SANITIZE_SPECIAL_CHARS,
        'categorie' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'description' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    ]
    );

    //initialiser les variables filtrer et valider
    $nom = $_POST['nom'] ?? '';
    $image = $_POST['image'] ?? '';
    $price = $_POST['price'] ?? '';
    $location = $_POST['location'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $description = $_POST['description'] ?? '';

    if (!$nom) {
        $errors['nom'] = 'Saisir le nom svp !';
    }

    if (!$image) {
        $errors['image'] = "Entrer l'URL de l'image svp !";

    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {

        $errors['image'] = "Entrer une URL valide de l'image svp ! ";
    }

    if (!$price) {
        $errors['price'] = 'Saisir le prix svp !';
    }

    if (!$location) {
        $errors['nom'] = 'Saisir le une localisation svp !';
    }

    if (!$categorie) {

        $errors['categorie'] = 'Saisir la catégorie svp !';
    }

    if (!$description) {

        $errors['description'] = 'Saisir le description svp !';
    }

    // empty retourne true si uen variable est vide
    // array_filter($errors, fn($e) => $e !== '') retourne un nouveau tableau associatif
    //qui contient uniquement les elements dont la valeur nest pas une chaine de
    // characteres vide. Cela evite de soumettre le formulaire avec au moins une erreur
    // (possibilites de attaque)
    if (empty(array_filter($errors, fn($e) => $e !== ''))) {

        $listeRepas = [...$listeRepas, [
            'nom' => $nom,
            'image' => $image,
            'price' => $price,
            'location' => $location,
            'categorie' => $categorie,
            'description' => $description,
            'id' => time(),
        ],
        ];

        file_put_contents($filename, json_encode($listeRepas));
        header('location: /');
    }
}

?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <?php require_once 'includes/head.php'?>
        <link rel="stylesheet" href="public/css/add-repas.css">
        <title>Ajouter un repas</title>
    </head>

    <body>
        <div class="container">
            <?php require_once 'includes/header.php'?>
            <div class="content">
                <div class="block p-20 form-container">
                    <h1>Ajouter un repas</h1>
                    <form action="/add-repas.php" method="post">
                        
                        <!-- nom -->
                        <div class="form-control">
                            <label for="nom">Nom de plat</label>
                            <input type="text" name="nom" id="nom" value=<?=$nom ?? ''?>>
                            <?php if ($errors['nom']): ?>
                            <p class='text-danger'><?=$errors['nom']?></p>
                            <?php endif;?>
                        </div>

                        <!-- Image -->
                        <div class="form-control">
                            <label for="image">Image</label>
                            <input type="text" name="image" id="image" value=<?=$image ?? ''?>>
                            <?php if ($errors['image']): ?>
                            <p class='text-danger'><?=$errors['image']?></p>
                            <?php endif;?>
                        </div>

                        <!-- nom -->
                        <div class="form-control">
                            <label for="price">Prix</label>
                            <input type="text" name="price" id="price" value=<?=$price ?? ''?>>
                            <?php if ($errors['price']): ?>
                            <p class='text-danger'><?=$errors['price']?></p>
                            <?php endif;?>
                        </div>

                        <!-- nom -->
                        <div class="form-control">
                            <label for="location">Localisation</label>
                            <input type="text" name="location" id="location" value=<?=$location ?? ''?>>
                            <?php if ($errors['location']): ?>
                            <p class='text-danger'><?=$errors['location']?></p>
                            <?php endif;?>
                        </div>                        

                        <!-- categorie -->
                        <div class="form-control">
                            <label for="categorie">Catégorie</label>
                            <select name="categorie" id="categorie">
                                <option value="">Non choisie</option>
                                <option value="cuisine quebecoise">Cuisine québécoise</option>
                                <option value="cuisine maghrebine">Cuisine maghrébine</option>
                                <option value="cuisine italienne">Cuisine italienne</option>
                                <option value="cuisine asiatique">Cuisine asiatique</option>
                                <option value="cuisine mexicaine">Cuisine mexicaine</option>
                            </select>
                            <?php if ($errors['categorie']): ?>
                            <p class='text-danger'><?=$errors['categorie']?></p>
                            <?php endif;?>
                        </div>
                        

                        <!-- description -->
                        <div class="form-control">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" value=<?=$description ?? ''?>></textarea>
                            <?php if ($errors['description']): ?>
                            <p class='text-danger'><?=$errors['description']?></p>
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