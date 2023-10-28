<?php
session_start();
$accounts = __DIR__ . '/public/data/accounts.json';
$listeAccounts = [];
$hiddenConnectStatus="hidden";
$hiddenUsername="hidden";
$hiddenDisconnectStatus="hidden";
$hiddenFilter="hidden";
$connexionStatus="connected";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $listeAccounts = json_decode(file_get_contents($accounts), true) ?? [];

     foreach ($listeAccounts as $account) {

        if ($username == $account['username'] && $password == $account['password'] ) {
            // Authentication successful
            $connexionStatus="disconnected";
            $firstName=$account["firstName"]; 
            $lastName=$account["lastName"];  
            $idAccount=$account["id"];     
            break;
        }
     }

     if ($connexionStatus=="connected") {
           // Authentication failed
           echo '<h1 style="color:#FF0000;">Le nom de l\'utilisateur ou le mot de passe n\'est pas valide.</h1>';
    
      }else{
        // Set session variables
        $_SESSION["firstName"] = $firstName;
        $_SESSION["lastName"] = $lastName;       
        $_SESSION["idAccount"] = $idAccount; 
        header("location: index.php");
  
      }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <?php require_once 'includes/head.php'?>
  <link rel="stylesheet" type="text/css" href="public/css/login.css">
</head>
<body>
<?php require_once 'includes/header.php'?>
  <div class="login-container">
    <h1>Connexion</h1>
    <form action="login.php" method="POST">
      <label for="username">Nom d'utilisateur:</label>
      <input type="text" id="username" name="username" required>
      <br>
      <label for="password">Mot de passe:</label>
      <input type="password" id="password" name="password" required>
      <br>
      <input type="submit" value="Login">
    </form>
    <p>Vous n'avez pas de compte? <a href="addAccount.php">Créez un ici</a></p>


  </div>
  <?php require_once 'includes/footer.php'?>
</body>
</html>
