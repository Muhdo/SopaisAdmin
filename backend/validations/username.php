<?php
   include_once "../database.php";

   session_start();
   $username = $_POST["username"];

   if (strlen($username) < 2 || strlen($username) > 60) {
      echo "Error";
      exit();
   } else {
      $queryProcurarUsername = $connection->prepare("SELECT * FROM User WHERE Username = :Username");
      $queryProcurarUsername->bindParam(":Username", $username, PDO::PARAM_STR);
      $queryProcurarUsername->execute();
      if ($queryProcurarUsername->rowCount() == 1) {
         $queryProcurarUsername->closeCursor();
         $connection = null;

         echo "Duplication";
         exit();
      } elseif ($queryProcurarUsername->rowCount() == 0) {
         $queryProcurarUsername->closeCursor();
         $connection = null;

         echo "Valid";
         exit();
      }
   }
?>
