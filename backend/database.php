<?php
   try {
      $connection = new PDO("mysql:host=localhost;dbname=website", "root", "");
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }
   catch(PDOException $e)
   {
      echo "Sem Conexão! Não é possivel comunicar com o servidor!";
   }
?>
