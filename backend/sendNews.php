<?php
   include_once "database.php";

   $key = "";
   $a = -1;

   $tituloPT = utf8_decode($_POST["tituloPT"]);
   $tituloEN = utf8_decode($_POST["tituloEN"]);
   $imagem = $_POST["imagem"];
   $editorPT = utf8_decode($_POST["editorPT"]);
   $editorEN = utf8_decode($_POST["editorEN"]);
   $func = $_POST["func"];

   if ($_POST) {
      if ($func == "Guardar") {
         $queryValidarTituloPT = $connection->prepare("SELECT * FROM Noticia WHERE TituloPT = :TituloPT");

         $queryValidarTituloPT->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
         $queryValidarTituloPT->execute();

         if ($queryValidarTituloPT->rowCount() >= 1 || strlen($tituloPT) <= 5) {
            echo "ErroTituloPT";
            exit();
         } else {
            $queryValidarTituloEN = $connection->prepare("SELECT * FROM Noticia WHERE TituloEN = :TituloEN");

            $queryValidarTituloEN->bindParam(":TituloEN", $tituloEN, PDO::PARAM_STR);
            $queryValidarTituloEN->execute();

            if ($queryValidarTituloEN->rowCount() >= 1 || strlen($tituloEN) <= 5) {
               echo "ErroTituloEN";
               exit();
            } else {
               if (strlen($editorPT) < 60) {
                  echo "ErroConteudoPT";
                  exit();
               } else {
                  if (strlen($editorEN) < 60) {
                     echo "ErroConteudoEN";
                     exit();
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

                     echo "Valid";
                     exit();
                  }
               }
            }
         }

         exit();
      } else {
         $queryValidarTituloPT = $connection->prepare("SELECT * FROM Noticia WHERE TituloPT = :TituloPT");

         $queryValidarTituloPT->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
         $queryValidarTituloPT->execute();

         if ($queryValidarTituloPT->rowCount() >= 1 || strlen($tituloPT) <= 5) {
            $resultado = $queryValidarTituloPT->fetchAll();

            if ($resultado[0]["Key_Noticia"] !== $func) {
               echo "ErroTituloPT";
               exit();
            }
         }

         $queryValidarTituloEN = $connection->prepare("SELECT * FROM Noticia WHERE TituloEN = :TituloEN");

         $queryValidarTituloEN->bindParam(":TituloEN", $tituloEN, PDO::PARAM_STR);
         $queryValidarTituloEN->execute();

         if ($queryValidarTituloEN->rowCount() >= 1 || strlen($tituloEN) <= 5) {
            $resultado = $queryValidarTituloEN->fetchAll();

            if ($resultado[0]["Key_Noticia"] != $func) {
               echo "ErroTituloEN";
               exit();
            }
         }

         if (strlen($editorPT) < 60) {
            echo "ErroConteudoPT";
            exit();
         } else {
            if (strlen($editorEN) < 60) {
               echo "ErroConteudoEN";
               exit();
            } else {
               if ($imagem == "NoImage") {
                  $queryAtualizarNoticia = $connection->prepare("UPDATE Noticia SET TituloPT = :TituloPT, TituloEN = :TituloEN, ConteudoPT = :ConteudoPT, ConteudoEN = :ConteudoEN WHERE Key_Noticia = :Key_Noticia");
                  $queryAtualizarNoticia->bindParam(":Key_Noticia", $func, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":TituloEN", $tituloEN, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":ConteudoPT", $editorPT, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":ConteudoEN", $editorEN, PDO::PARAM_STR);
               } else {
                  $imagem = file_get_contents($imagem);
                  $queryAtualizarNoticia = $connection->prepare("UPDATE Noticia SET TituloPT = :TituloPT, TituloEN = :TituloEN, Imagem = :Imagem, ConteudoPT = :ConteudoPT, ConteudoEN = :ConteudoEN WHERE Key_Noticia = :Key_Noticia");
                  $queryAtualizarNoticia->bindParam(":Key_Noticia", $func, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":TituloPT", $tituloPT, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":TituloEN", $tituloEN, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":Imagem", $imagem, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":ConteudoPT", $editorPT, PDO::PARAM_STR);
                  $queryAtualizarNoticia->bindParam(":ConteudoEN", $editorEN, PDO::PARAM_STR);

                  $queryAtualizarNoticia->execute();
               }

               echo "Updated";
               exit();
            }
         }
      }
      echo "Erro";
      exit();
   } else {
      echo "Incorrect Method";
      exit();
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
