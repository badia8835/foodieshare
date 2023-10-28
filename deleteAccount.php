<?php

session_start();

$accounts = __DIR__ . '/public/data/accounts.json';
$listeAccounts = [];
$listeAccounts = json_decode(file_get_contents($accounts), true) ?? [];

$accountId = $_SESSION["idAccount"];
$accountIndex = array_search($accountId, array_column($listeAccounts, 'id'));

unset($listeAccounts[$accountIndex]);

// Re-index the array
$listeAccounts = array_values($listeAccounts);

// Save the modified data back to the file
file_put_contents($accounts, json_encode($listeAccounts));


session_unset();

// destroy the session
session_destroy();
header("location: index.php");
