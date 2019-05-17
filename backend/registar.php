<?php
   include_once "database.php";

   session_start();
   $key = "";
   $a = -1;

   $_SESSION['regForm'] = $_POST;
   $nome = $_POST["nome"];
   $username = $_POST["user"];
   $email = $_POST["email"];
   $password = $_POST["password"];
   $repPassword = $_POST["repPassword"];

   if (strlen($nome) < 2 || strlen($nome) > 60 || !preg_match("/^[a-záàâãäåæçèéêëìíîïðñòóôõøùúûüýþÿı ]*$/i", $nome)) {
      echo "ErrorName";
      exit();
   } else {
      if (strlen($username) < 2 || strlen($username) > 60) {
         echo "ErrorUsername";
         exit();
      } else {

         $queryProcurarUsername = $connection->prepare("SELECT * FROM User WHERE Username = :Username");
         $queryProcurarUsername->bindParam(":Username", $username, PDO::PARAM_STR);
         $queryProcurarUsername->execute();
         if ($queryProcurarUsername->rowCount() > 0) {
            $queryProcurarUsername->closeCursor();
            echo "DuplicationUsername";
            exit();
         } else {
            $queryProcurarUsername->closeCursor();
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 256) {
               echo "ErrorEmail";
               exit();
            } else {

               $queryProcurarEmail = $connection->prepare("SELECT * FROM User WHERE Email = :Email");
               $queryProcurarEmail->bindParam(":Email", $email, PDO::PARAM_STR);
               $queryProcurarEmail->execute();
               if ($queryProcurarEmail->rowCount() > 0) {
                  $queryProcurarEmail->closeCursor();
                  echo "DuplicationEmail";
                  exit();
               } else {
                  $queryProcurarEmail->closeCursor();
                  if (strlen($password) == 0) {
                     echo "ErrorPassword";
                     exit();
                  } else {
                     if ($password != $repPassword) {
                        echo "ErrorRepPassword";
                        exit();
                     } else {
                        do {
                           $a += 1;
                           $key = KeyGenerator(16);

                           $queryProcurarkey = $connection->prepare("SELECT * FROM User WHERE Key_User = :Key");
                           $queryProcurarkey->bindParam(":Key", $key, PDO::PARAM_STR);
                           $queryProcurarkey->execute();

                           if ($queryProcurarkey->rowCount() == 0) {
                              $queryProcurarkey->closeCursor();

                              break;
                           }
                        } while (true);

                        $options = [
                           "cost" => 12,
                        ];

                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

                        $queryInserir = $connection->prepare("INSERT INTO User(Key_User, Nome, Username, Email, Password) VALUES (:Key, :Nome, :Username, :Email, :Password)");

                        $queryInserir->bindParam(":Key", $key, PDO::PARAM_STR);
                        $queryInserir->bindParam(":Username", $username, PDO::PARAM_STR);
                        $queryInserir->bindParam(":Nome", $nome, PDO::PARAM_STR);
                        $queryInserir->bindParam(":Email", $email, PDO::PARAM_STR);
                        $queryInserir->bindParam(":Password", $hashedPassword, PDO::PARAM_STR);
                        $queryInserir->execute();

                        $queryInserir->closeCursor();
                        $connection = null;

                        echo "Registar";
                        exit();
                     }
                  }
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
