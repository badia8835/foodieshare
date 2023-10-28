<?php

session_start();

$filename = __DIR__ . '/public/data/listeRepas.json';
$dollarcanadian = "CAD";
$ratingscorre = "Note: ";

/// account processs 
$accounts = __DIR__ . '/public/data/accounts.json';

if (file_exists($accounts)) {
  $listeAccounts = json_decode(file_get_contents($accounts), true) ?? [];

  if (isset($_SESSION["firstName"]) && isset($_SESSION["lastName"])) {
    $hiddenConnectStatus = "";
    $hiddenUsername = "";
    $hiddenDisconnectStatus = "hidden";
    $hiddenFilter = "hidden";
  } else {
    $hiddenConnectStatus = "hidden";
    $hiddenDisconnectStatus = "";
    $hiddenUsername = "hidden";
    $hiddenFilter = "";
  }
} else {
  $error_message = "Un problem technique est survenue.";
  echo $error_message;
}

// Read the comments from the file
//$comments = file("comments.txt", FILE_IGNORE_NEW_LINES);

$listeRepas = [];
$categorie = [];

$GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
// Récupération des valeurs
$selectedCat = $_GET['cat'] ?? '';
$searchQuery = $_GET['search'] ?? '';

// Récupération des valeurs des champs de filtrage
$priceFilter = $_GET['price'] ?? null;
$locationFilter = $_GET['location'] ?? null;

// Fonction de filtrage des repas
function filter_repas($repas, $priceFilter, $locationFilter)
{
  if ($priceFilter && $repas['price'] > $priceFilter) {
    return false;
  }

  // Convertit à la fois la localisation de l'utilisateur et la localisation du repas en minuscules avant de les comparer
  if ($locationFilter && strtolower($repas['location']) !== strtolower($locationFilter)) {
    return false;
  }

  return true;
}

// Application du filtrage
$listeRepas = array_filter($listeRepas, function ($repas) use ($priceFilter, $locationFilter, $searchQuery) {
  return search_repas($searchQuery, $repas) && filter_repas($repas, $priceFilter, $locationFilter);
});


function count_categories($accumulateur, $valeur_courante)
{

  if (isset($accumulateur[$valeur_courante])) {
    $accumulateur[$valeur_courante]++;
  } else {
    $accumulateur[$valeur_courante] = 1;
  }
  return $accumulateur;
}

function classifier_repas($acc, $repas)
{

  if (isset($acc[$repas['categorie']])) {
    $acc[$repas['categorie']] = [...$acc[$repas['categorie']], $repas];
  } else {
    $acc[$repas['categorie']] = [$repas];
  }
  return $acc;
}

function search_repas($query, $repas)
{
  return strpos(strtolower($repas['nom']), strtolower($query)) !== false ||
    strpos(strtolower($repas['description']), strtolower($query)) !== false;
}



if (file_exists($filename)) {

  $listeRepas = json_decode(file_get_contents($filename), true) ?? [];

  // Application du filtrage
  $listeRepas = array_filter($listeRepas, function ($repas) use ($priceFilter, $locationFilter, $searchQuery) {
    return search_repas($searchQuery, $repas) && filter_repas($repas, $priceFilter, $locationFilter);
  });

  if ($searchQuery && count($listeRepas) === 0) {
    $error_message = "Aucun résultat ne correspond à votre recherche.";
    echo $error_message;
  }

  $cattmp = array_map(fn ($a) => $a['categorie'], $listeRepas);

  $categories = array_reduce($cattmp, 'count_categories', []);

  $listeRepasParCategorie  = array_reduce($listeRepas, 'classifier_repas', []);
} else {

  $error_message = "Un problem technique est survenue.";
  echo $error_message;
}

function chercheFullName($accountId)
{
  $accounts = __DIR__ . '/public/data/accounts.json';
  $listeAccounts = json_decode(file_get_contents($accounts), true) ?? [];
  $accountIndex = array_search($accountId, array_column($listeAccounts, 'id'));
  $firstName = $listeAccounts[$accountIndex]['firstName'];
  $lastName = $listeAccounts[$accountIndex]['lastName'];
  $fullName = $firstName . ' ' . $lastName;
  return $fullName;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="public/css/index.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FoodieShare</title>
  <style>
  </style>

</head>

<body>
  <div class="container">

    <?php require_once 'includes/header.php' ?>

    <div class="content">
      <div class="newsfeed-container">
        <div class="categorie-container">
          <ul>
            <li class=<?= $selectedCat ? '' :  'cat-active' ?>>
              <a href="/"> Tous les repas <span class="small"> (<?= count($listeRepas) ?>) </span></a>
            </li>
            <?php foreach ($categories as $catName => $catNum) : ?>
              <li class=<?= $selectedCat === $catName  ? "cat-active" : '' ?>>
                <a href="/?cat=<?= $catName ?>"><?= $catName ?> <span class="small">(<?= $catNum ?>) </span></a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <div class="newsfeed-content">
        <!-- en cas la categorie est non selectionne -->
        <?php if (!$selectedCat) : ?>
          <?php foreach ($categories as $cat => $num) : ?>
            <h2><?= $cat ?></h2>
            <div class="listeRepas-container">
              <?php foreach ($listeRepasParCategorie[$cat] as $a) : ?>

                <?php
                // $opinions = $a['opinion'];
                $length = 0;
                if (isset($a['opinions']) && is_array($a['opinions'])) {
                  // The $opinions array exists
                  // Place your code here to work with the $opinions array
                  $opinions = $a['opinions'];
                  foreach ($opinions as $opinion) {
                    $length = $length + 1;
                  }
                }
                ?>

                <a href="/show-repas.php?id=<?= $a['id'] ?>" class="repas block">
                  <!-- image -->
                  <div class="overflow">
                    <div class="img-container" style="background-image: url(<?= $a['image'] ?>)">

                    </div>
                    <a href="downloadPhoto.php?url=<?= $a['image'] ?>&mealName=<?= $a['nom'] ?>">Télécharger la photo ici</a>

                  </div>
                  <!-- repas details -->
                  <div>
                    <ul>
                      <li><?= $a['nom'] ?></li>
                      <li>
                        <p class="repas-content"><?= $a['description'] ?></p>
                      </li>
                      <li><?= $a['price'] ?> <?= $dollarcanadian ?></li>
                      <li><?= $a['location'] ?></li>

                    </ul>

                  </div>
                  <div>

                    <!-- </div> -->
                </a>
                <!-- afficher commentaire et note -->
                <div>

                  <button class="collapsible"><?= $length ?> commentaires</button>
                  <div class="contentComments">
                    <ul>
                      <?php
                      if ($length != 0) {
                        foreach ($opinions as $opinion) {
                          $accountId = $opinion['accountId'];
                          $comment = $opinion['comment'];
                          $rating = $opinion['rating'];
                          // chercher le nom d'utilisateur 
                          $fullName = chercheFullName($accountId);

                          echo "<li>" . htmlspecialchars($fullName) . " : " . htmlspecialchars($comment) . "</li>";
                          echo "<li> note: " . htmlspecialchars($rating) . "</li>";
                        }
                      }

                      ?>
                    </ul>

                    <!-- HTML form to submit comments -->
                    <form <?= $hiddenConnectStatus ?> action="code.php?id=<?= $a['id'] ?>" method="POST">
                      <p>Donnez une note:</p>
                      <div class="rating">

                          <input type="radio" name="rating5<?= $a['id'] ?>" id="star5<?= $a['id'] ?>" value="5">
                          <label for="star5<?= $a['id'] ?>"></label>
                          <input type="radio" name="ratin4<?= $a['id'] ?>" id="star4<?= $a['id'] ?>" value="4">
                          <label for="star4<?= $a['id'] ?>"></label>
                          <input type="radio" name="rating3<?= $a['id'] ?>" id="star3<?= $a['id'] ?>" value="3">
                          <label for="star3<?= $a['id'] ?>"></label>
                          <input type="radio" name="rating2<?= $a['id'] ?>" id="star2<?= $a['id'] ?>" value="2">
                          <label for="star2<?= $a['id'] ?>"></label>
                          <input type="radio" name="rating1<?= $a['id'] ?>" id="star1<?= $a['id'] ?>" value="1">
                          <label for="star1<?= $a['id'] ?>"></label>
                      </div>

                      <label for="comment">Ajouter un commentaire:</label><br>
                      <textarea name="comment<?= $a['id'] ?>" rows="4" cols="50"></textarea><br>
                      <input type="submit" name="add-comments-button"></input>
                    </form>
                  </div>
                </div>



              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
          <!-- en cas la categorie est selectionne -->
        <?php else : ?>
          <h2><?= $selectedCat ?> </h2>
          <div class="listeRepas-container">
            <?php foreach ($listeRepasParCategorie[$selectedCat] as $a) : ?>
              <?php
              // $opinions = $a['opinion'];
              $length = 0;
              if (isset($a['opinions']) && is_array($a['opinions'])) {
                // The $opinions array exists
                // Place your code here to work with the $opinions array
                $opinions = $a['opinions'];
                foreach ($opinions as $opinion) {
                  $length = $length + 1;
                }
              }

              ?>
              <a href="/show-repas.php?id=<?= $a['id'] ?>" class="repas block">
                <!-- <div class="repas block"> -->
                <!-- image -->
                <div class="overflow">
                  <div class="img-container" style="background-image:url(<?= $a['image'] ?>)">
                  </div>
                  <a href="downloadPhoto.php?url=<?= $a['image'] ?>&mealName=<?= $a['nom'] ?>">Télécharger ici</a>
                </div>
                <!-- nom -->
                <ul>
                  <li><?= $a['nom'] ?></li>
                  <li>
                    <p class="repas-content"><?= $a['description'] ?></p>
                  </li>
                  <li><?= $a['price'] ?> <?= $dollarcanadian ?></li>
                  <li><?= $a['location'] ?></li>

                </ul>
                <!-- </div> -->
              </a>
              <!-- afficher commentaire et note -->
              <div>
                <button class="collapsible"><?= $length ?> commentaires</button>
                <div class="contentComments">
                  <ul>
                    <?php
                    if ($length != 0) {
                      foreach ($opinions as $opinion) {
                        $accountId = $opinion['accountId'];
                        $comment = $opinion['comment'];
                        $rating = $opinion['rating'];
                        // chercher le nom d'utilisateur 
                        $fullName = chercheFullName($accountId);

                        echo "<li>" . htmlspecialchars($fullName) . " : " . htmlspecialchars($comment) . "</li>";
                        echo "<li> note: " . htmlspecialchars($rating) . "</li>";
                      }
                    }

                    ?>
                  </ul>

                  <!-- HTML form to submit comments -->
                  <form <?= $hiddenConnectStatus ?> action="code.php?id=<?= $a['id'] ?>" method="POST">
                    <p>Donnez une note:</p>
                    <div class="rating">
                      <input type="radio" name="rating5<?= $a['id'] ?>" id="star5" value="5">
                      <label for="star5"></label>
                      <input type="radio" name="ratin4<?= $a['id'] ?>" id="star4" value="4">
                      <label for="star4"></label>
                      <input type="radio" name="rating3<?= $a['id'] ?>" id="star3" value="3">
                      <label for="star3"></label>
                      <input type="radio" name="rating2<?= $a['id'] ?>" id="star2" value="2">
                      <label for="star2"></label>
                      <input type="radio" name="rating1<?= $a['id'] ?>" id="star1" value="1">
                      <label for="star1"></label>
                    </div>

                    <label for="comment">Ajouter un commentaire:</label><br>
                    <textarea name="comment<?= $a['id'] ?>" rows="4" cols="50"></textarea><br>
                    <input type="submit" name="add-comments-button"></input>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>
      </div>

    </div>
  </div>
  <?php require_once 'includes/footer.php' ?>

  <script>
    var coll = document.getElementsByClassName("collapsible");
    var i;

    for (i = 0; i < coll.length; i++) {
      coll[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var content = this.nextElementSibling;
        if (content.style.maxHeight) {
          content.style.maxHeight = null;
        } else {
          content.style.maxHeight = content.scrollHeight + "px";
        }
      });
    }
  </script>

</body>

</html>