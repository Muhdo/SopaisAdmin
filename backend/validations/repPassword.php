<?php
   $password = $_POST["password"];
   $repPassword = $_POST["repPassword"];

   if ($password != $repPassword) {
      echo "Error";
      exit();
   } else {
      echo "Valid";
      exit();
   }
?>
