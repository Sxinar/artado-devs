<?php

$host = "sql104.infinityfree.com";
$dbname = "if0_36661912_newdev";
$username = "if0_36661912";
$password = "QAz1BbPjZ9E26O";

try {
  $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}

?>