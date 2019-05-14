<?php
   include_once "database.php";

   $key = utf8_encode($_POST["KeyMensagem"]);
   $estado = $_POST["estado"];

   if (isset($_POST)) {
      $queryUpdate = $connection->prepare("UPDATE Mensagem SET Respondido = :Respondido WHERE Key_Mensagem = :Key");
      $queryUpdate->bindParam(":Key", $key, PDO::PARAM_STR);
      $queryUpdate->bindParam(":Respondido", $estado, PDO::PARAM_STR);

      if ($queryUpdate->execute()) {
         echo "Updated";
      } else {
         echo "ErroUpdate";
      }

      exit();
   } else {
      echo "Error";
      exit();
   }
?>
