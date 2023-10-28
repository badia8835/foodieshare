<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION["firstName"]) || !isset($_SESSION["lastName"])) {
    echo 'Vous devez être connecté pour télécharger des fichiers.';
    exit();
  }
  

$target_dir = "uploads/";
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));

// Vérifier si le fichier est une véritable image ou une image factice
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
      echo "Le fichier est une image - " . $check["mime"] . ".";
      $uploadOk = 1;
    } else {
      echo "Le fichier n'est pas une image.";
      $uploadOk = 0;
    }
  }
  
  // Vérifier le type MIME du fichier
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime = finfo_file($finfo, $_FILES['fileToUpload']['tmp_name']);
  if (strpos($mime, 'image') === false) {
    echo "Le fichier doit être une image.";
    $uploadOk = 0;
  }
  
  // Vérifier si le fichier existe déjà
  if (file_exists($target_file)) {
    echo "Désolé, le fichier existe déjà.";
    $uploadOk = 0;
  }
  
  // Vérifier la taille du fichier
  if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Désolé, votre fichier est trop volumineux.";
    $uploadOk = 0;
  }
  
  // Autoriser certains formats de fichiers
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    echo "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
    $uploadOk = 0;
  }
  
  // Renommer le fichier pour une meilleure sécurité
  $newFileName = uniqid("img_", true) . '.' . $imageFileType;
  $target_file = $target_dir . $newFileName;
  
  // Vérifier si $uploadOk est défini à 0 par une erreur
  if ($uploadOk == 0) {
    echo "Désolé, votre fichier n'a pas été téléchargé.";
  // si tout est correct, essayez de télécharger le fichier
  } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      echo "Le fichier ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " a été téléchargé.";
    } else {
      echo "Désolé, il y a eu une erreur lors du téléchargement de votre fichier.";
    }
  }
  