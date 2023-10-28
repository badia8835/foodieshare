<?php
session_start();
$accountId=$_SESSION["idAccount"];

if (isset($_POST['add-comments-button'])) {
    $filename = __DIR__ . '/public/data/listeRepas.json';
    $listeRepas = [];
    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
    $id = $_GET['id'] ?? '';
    $comment = $_POST["comment$id"];
    $rating = $_POST["rating$id"];
    $cmp = 0;
    for ($i = 1; $i < 6; $i++) {
        if (($_POST["rating$i$id"]) == $i) {
            $cmp = $cmp + 1;
        }
    }

    if (!$id) {
        header('location: /');
    } else {

        if (file_exists($filename)) {
            // ajouter commentaire  
            $listeRepas = json_decode(file_get_contents($filename), true) ?? [];
            $repasIndex = array_search($id, array_column($listeRepas, 'id'));

            if ($repasIndex !== false) {
                $listeRepas[$repasIndex]['opinions'][] = array(
                    'comment' => $comment,
                    'accountId' => $accountId,
                    'rating' => $cmp
                );
            
                file_put_contents($filename, json_encode($listeRepas));
            }
           // $listeRepas[$repasIndex]['opinion'][$accountId]['commentsArea'] = $comment;
          //  $listeRepas[$repasIndex]['opinion'][$accountId]['accountId'] = $accountId;
          ///  $listeRepas[$repasIndex]['opinion']['rating'][] = $cmp;

          //  file_put_contents($filename, json_encode($listeRepas));
            header("location: index.php");
        } else {

            $error_message = "Un erreur est survenue, verifier que la database nest pas vide.";
            echo $error_message;
        }
    }
}





