<?php
   include_once "../database.php";

   session_start();
   $email = $_POST["email"];

   if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 256) {
      echo "Error";
      exit();
   } else {

      $queryProcurarEmail = $connection->prepare("SELECT * FROM USer WHERE Email = :Email");
      $queryProcurarEmail->bindParam(":Email", $email, PDO::PARAM_STR);
      $queryProcurarEmail->execute();
      if ($queryProcurarEmail->rowCount() == 1) {
         $queryProcurarEmail->closeCursor();
         $connection = null;
         
         echo "Duplication";
         exit();
      } elseif ($queryProcurarEmail->rowCount() == 0) {
         $queryProcurarEmail->closeCursor();
         $connection = null;

         echo "Valid";
         exit();
      }
   }
?>
