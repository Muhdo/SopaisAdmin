<?php
   $editor = $_POST["editor"];

   if (strlen($editor) < 60) {
      echo "Erro";
   } else {
      echo "Valido";
   }

   exit();
?>
