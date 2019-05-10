<?php
   include_once "database.php";

   $key = $_POST["key"];

   if (isset($_POST)) {
      $queryLoadMensagem = $connection->prepare("SELECT Key_Mensagem, Nome, Email, Data, Mensagem, Respondido FROM Mensagem WHERE Key_Mensagem = :KeyMensagem");
      $queryLoadMensagem->bindParam(":KeyMensagem", $key, PDO::PARAM_STR);
      $queryLoadMensagem->execute();

      if ($queryLoadMensagem->rowCount() == 1) {
         foreach ($queryLoadMensagem->fetchAll() as $resultado) {
            setlocale(LC_ALL, 'pt_PT', 'pt_PT.utf-8', 'pt_PT.utf-8', 'portuguese');
            $return_arr = array("KeyMensagem" => utf8_encode($resultado["Key_Mensagem"]),
               "Nome" => utf8_encode($resultado["Nome"]),
               "Email" => utf8_encode($resultado["Email"]),
               "Data" => ucfirst(utf8_encode(strftime("%d-%m-%Y %H:%M", strtotime($resultado["Data"])))),
               "Mensagem" => $resultado["Mensagem"],
               "Respondido" => $resultado["Respondido"]
            );
         }
      } else {
         echo "Erro";
         exit();
      }

      $queryLoadMensagem->closeCursor();

      echo json_encode($return_arr);
      exit();
   } else {
      echo "Erro";
      exit();
   }
?>
