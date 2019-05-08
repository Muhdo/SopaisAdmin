<?php
   include_once "../database.php";

   $titulo = utf8_decode($_POST["titulo"]);
   $lang = $_POST["lang"];

   if ($lang == "PT") {
      $queryValidarTitulo = $connection->prepare("SELECT * FROM Noticia WHERE TituloPT = :Titulo");
   } elseif ($lang == "EN") {
      $queryValidarTitulo = $connection->prepare("SELECT * FROM Noticia WHERE TituloEN = :Titulo");
   }

   $queryValidarTitulo->bindParam(":Titulo", $titulo, PDO::PARAM_STR);
   $queryValidarTitulo->execute();

   if ($queryValidarTitulo->rowCount() >= 1) {
      echo "Erro";
   } else {
      echo "Valido";
   }
   $connection = null;
   exit();
?>
