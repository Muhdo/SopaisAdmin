<?php
   include_once "database.php";

   $key = "";
   $a = -1;

   $tituloPT = utf8_decode($_POST["tituloPT"]);
   $tituloEN = utf8_decode($_POST["tituloEN"]);
   $imagem = file_get_contents($_POST["imagem"]);
   $editorPT = utf8_decode($_POST["editorPT"]);
   $editorEN = utf8_decode($_POST["editorEN"]);

   if ($_POST) {
      $queryValidarTituloPT = $connection->prepare("SELECT * FROM Noticia WHERE TituloPT = :TituloPT");

      $queryValidarTituloPT->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
      $queryValidarTituloPT->execute();

      if ($queryValidarTituloPT->rowCount() >= 1 || strlen($tituloPT) <= 5) {
         echo "ErroTituloPT";
      } else {
         $queryValidarTituloEN = $connection->prepare("SELECT * FROM Noticia WHERE TituloEN = :TituloEN");

         $queryValidarTituloEN->bindParam(":TituloEN", $tituloEN, PDO::PARAM_STR);
         $queryValidarTituloEN->execute();

         if ($queryValidarTituloEN->rowCount() >= 1 || strlen($tituloEN) <= 5) {
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
                  $queryInserirNoticia->bindParam(":TituloEN", $tituloEN, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":Imagem", $imagem, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":ConteudoPT", $editorPT, PDO::PARAM_STR);
                  $queryInserirNoticia->bindParam(":ConteudoEN", $editorEN, PDO::PARAM_STR);

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
