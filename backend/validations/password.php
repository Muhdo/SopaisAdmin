<?php
   $password = $_POST["password"];

   if (strlen($password) == 0) {
      echo "Error";
      exit();
   } else {
      echo "Valid";
      exit();
   }
?>
