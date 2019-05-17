<?php
   include_once "database.php";

   $key = $_POST["key"];

   if (isset($_POST)) {
      $queryLoadConta = $connection->prepare("SELECT Key_User, Nome, Username, Email FROM User WHERE Key_User = :KeyUser");
      $queryLoadConta->bindParam(":KeyUser", $key, PDO::PARAM_STR);
      $queryLoadConta->execute();

      if ($queryLoadConta->rowCount() == 1) {
         foreach ($queryLoadConta->fetchAll() as $resultado) {
            setlocale(LC_ALL, 'pt_PT', 'pt_PT.utf-8', 'pt_PT.utf-8', 'portuguese');
            $return_arr = array("KeyUser" => utf8_encode($resultado["Key_User"]),
               "Nome" => utf8_encode($resultado["Nome"]),
               "Username" => utf8_encode($resultado["Username"]),
               "Email" => utf8_encode($resultado["Email"])
            );
         }
      } else {
         echo "Erro";
         exit();
      }

      $queryLoadConta->closeCursor();

      echo json_encode($return_arr);
      exit();
   } else {
      echo "Erro";
      exit();
   }
?>
