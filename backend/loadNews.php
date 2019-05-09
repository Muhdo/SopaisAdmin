<?php
   include_once "../backend/database.php";

   $key = $_POST["key"];
   $return = array();

   if (isset($_POST)) {
      $queryLoadNoticia = $connection->prepare("SELECT Key_Noticia, TituloPT, TituloEN, Imagem, ConteudoPT, ConteudoEN FROM Noticia WHERE Key_Noticia = :Key_Noticia");
      $queryLoadNoticia->bindParam(":Key_Noticia", $key, PDO::PARAM_STR);
      $queryLoadNoticia->execute();

      if ($queryLoadNoticia->rowCount() == 1) {
         foreach ($queryLoadNoticia->fetchAll() as $resultado) {
            $return_arr = array("keyNoticia" => utf8_encode($resultado["Key_Noticia"]),
               "tituloPT" => utf8_encode($resultado["TituloPT"]),
               "tituloEN" => utf8_encode($resultado["TituloEN"]),
               "imagem" => base64_encode($resultado["Imagem"]),
               "conteudoPT" => utf8_encode($resultado["ConteudoPT"]),
               "conteudoEN" => utf8_encode($resultado["ConteudoEN"])
            );
         }
         $queryLoadNoticia->closeCursor();
      }
   }
   echo json_encode($return_arr);
?>
