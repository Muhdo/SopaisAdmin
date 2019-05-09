<?php
   include_once "database.php";

   $key = $_POST["key"];

   if (isset($_POST)) {
      $queryDelete = $connection->prepare("DELETE FROM Noticia WHERE Key_Noticia = :Key");
      $queryDelete->bindParam(":Key", $key, PDO::PARAM_STR);

      $queryDelete->execute();

      echo "Deleted";
      exit();
   }
?>
