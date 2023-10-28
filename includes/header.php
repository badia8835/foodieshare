<header>
  <?php



  $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
  $selectedCat = $_GET['cat'] ?? '';
  $accountUsername = $_GET['username'] ?? '';
  $accountPassword = $_GET['password'] ?? '';

  ?>

  <div>
    <a href='/' class="logo">FoodieShare</a>
  </div>

  <div>
    <div class="containerDiv">
      <div class="item" <?= $hiddenConnectStatus ?>>
        <a href='/add-repas.php'>Ajouter un repas</a>
      </div>
      <div class="item" <?= $hiddenUsername ?>>
        <a href='/manageConnexion.php' name=disconnected >Déconnexion</a>
      </div>
      <div class="item" <?= $hiddenUsername ?>>
        <a href='/updateAccount.php'><?= $_SESSION["firstName"] ?> <?= $_SESSION["lastName"] ?></a>
      </div>
      <div class="item" <?= $hiddenConnectStatus ?>>
        <form action="/index.php" method="GET" class="search-form">
          <input type="hidden" name="cat" value="<?= $selectedCat ?>">
          <input type="text" name="search" placeholder="Rechercher un plat...">
          <button type="submit">Rechercher</button>
        </form>
      </div>
      <div class="item"<?= $hiddenConnectStatus ?>>
        <form action="/" method="GET">
          <input type="text" name="price" placeholder="Prix max">
          <input type="text" name="location" placeholder="Localisation">
          <button type="submit">Filtrer</button>
        </form>
      </div>
    </div>
    <div class="containerDiv">
      <div class="item" <?= $hiddenDisconnectStatus ?>>
        <a href='/login.php'>Connexion</a>
      </div>
      <div class="item" <?= $hiddenFilter ?>>
        <form action="/index.php" method="GET" class="search-form">
          <input type="hidden" name="cat" value="<?= $selectedCat ?>">
          <input type="text" name="search" placeholder="Rechercher un plat...">
          <button type="submit">Rechercher</button>
        </form>
      </div>
      <div class="item"<?= $hiddenFilter ?>>
        <form action="/" method="GET">
          <input type="text" name="price" placeholder="Prix max">
          <input type="text" name="location" placeholder="Localisation">
          <button type="submit">Filtrer</button>
        </form>
      </div>
    </div>

  </div>
</header>