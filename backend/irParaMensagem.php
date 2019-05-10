<?php
   include_once "database.php";

   $indice = $_POST["indice"];
   $return = array();

   if (isset($_POST)) {
      $queryLoadMensagens = $connection->prepare("SELECT Key_Mensagem, Nome, Email, Data, Respondido FROM Mensagem ORDER BY Respondido, Data DESC LIMIT ".($indice + 1).",30");
      $queryLoadMensagens->execute();

      if ($queryLoadMensagens->rowCount() >= 1) {
         foreach ($queryLoadMensagens->fetchAll() as $resultado) {
            $indice++;
            setlocale(LC_ALL, 'pt_PT', 'pt_PT.utf-8', 'pt_PT.utf-8', 'portuguese');
            $return_arr[] = array("KeyMensagem" => utf8_encode($resultado["Key_Mensagem"]),
               "Nome" => utf8_encode($resultado["Nome"]),
               "Email" => utf8_encode($resultado["Email"]),
               "Data" => ucfirst(utf8_encode(strftime("%d-%m-%Y %H:%M", strtotime($resultado["Data"])))),
               "Respondido" => $resultado["Respondido"]
            );
         }
      }
      $queryLoadMensagens->closeCursor();

      echo json_encode($return_arr);
      exit();
   } else {
      echo "Erro";
      exit();
   }
?>
