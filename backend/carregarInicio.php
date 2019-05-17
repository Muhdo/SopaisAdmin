<?php
   include_once "database.php";

   $queryContarMensagens = $connection->prepare("SELECT COUNT(Key_Mensagem) AS 'Contagem' FROM Mensagem");
   $queryContarMensagens->execute();

   $resultado = $queryContarMensagens->fetchAll();
   $Mensagens = $resultado[0]["Contagem"];

   $queryContarMensagens->closeCursor();

   $queryContarResponder = $connection->prepare("SELECT COUNT(Key_Mensagem) AS 'Contagem' FROM Mensagem WHERE Respondido = 0");
   $queryContarResponder->execute();

   $resultado = $queryContarResponder->fetchAll();
   $Responder = $resultado[0]["Contagem"];

   $queryContarResponder->closeCursor();

   if ($Responder == 0 || $Mensagens == 0) {
      $ResponderPerc = 0;
   } else {
      $ResponderPerc = $Responder * 100 / $Mensagens;
   }

   $queryContarNoticias = $connection->prepare("SELECT COUNT(Key_Noticia) AS 'Contagem' FROM Noticia");
   $queryContarNoticias->execute();

   $resultado = $queryContarNoticias->fetchAll();
   $Noticias = $resultado[0]["Contagem"];

   $queryContarNoticias->closeCursor();

   $queryContarContas = $connection->prepare("SELECT COUNT(Key_User) AS 'Contagem' FROM User");
   $queryContarContas->execute();

   $resultado = $queryContarContas->fetchAll();
   $Contas = $resultado[0]["Contagem"];

   $queryContarNoticias->closeCursor();

   $return_arr = array("Mensagens" => $Mensagens,
      "Responder" => $Responder,
      "ResponderPerc" => $ResponderPerc,
      "Noticias" => $Noticias,
      "Contas" => $Contas
   );

   echo json_encode($return_arr);
   exit();
?>
