<?php
   include_once "database.php";

   $key = "";
   $a = -1;

   $tituloPT = $_POST["tituloPT"];
   $tituloEN = $_POST["tituloEN"];
   $imagem = $_POST["imagem"];
   $editorPT = $_POST["editorPT"];
   $editorEN = $_POST["editorEN"];

   if ($_POST) {
      $queryValidarTituloPT = $connection->prepare("SELECT * FROM Noticia WHERE TituloPT = :TituloPT");

      $queryValidarTituloPT->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
      $queryValidarTituloPT->execute();

      if ($queryValidarTituloPT->rowCount() >= 1) {
         echo "ErroTituloPT";
      } else {
         $queryValidarTituloEN = $connection->prepare("SELECT * FROM Noticia WHERE tituloEN = :tituloEN");

         $queryValidarTituloEN->bindParam(":tituloEN", $tituloEN, PDO::PARAM_STR);
         $queryValidarTituloEN->execute();

         if ($queryValidarTituloEN->rowCount() >= 1) {
            echo "ErroTituloEN";
         } else {
            if (strlen($editorPT) < 60) {
               echo "ErroConteudoPT";
            } else {
               if (strlen($editorEN) < 60) {
                  echo "ErroConteudoEN";
               } else {
                  do {
                     $a += 1;
                     $key = KeyGenerator(16);

                     $queryProcurarkey = $connection->prepare("SELECT * FROM Noticia WHERE Key_Noticia = :Key");
                     $queryProcurarkey->bindParam(":Key", $key, PDO::PARAM_STR);
                     $queryProcurarkey->execute();

                     if ($queryProcurarkey->rowCount() == 0) {
                        $queryProcurarkey->closeCursor();

                        break;
                     }
                  } while (true);

                  $queryInserirNoticia = $connection->prepare("INSERT INTO Noticia(Key_Noticia, TituloPT, TituloEN, Imagem, ConteudoPT, ConteudoEN) VALUES (:Key_Noticia, :TituloPT, :TituloEN, :Imagem, :ConteudoPT, :ConteudoEN)");

                  $queryInserirNoticia->bindParam(":Key_Noticia", $key, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":TituloEN", $TituloEN, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":Imagem", $Imagem, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":ConteudoPT", $ConteudoPT, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":ConteudoEN", $ConteudoEN, PDO::PARAM_STR);
                  $queryInserirNoticia->execute();
               }
            }
         }
      }
   }

   function KeyGenerator($len){
      $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 .'0123456789+/');
      shuffle($seed);
      $rand = '';
      foreach (array_rand($seed, $len) as $k) $rand .= $seed[$k];

      return $rand;
   }

   $connection = null;
   exit();
?>
