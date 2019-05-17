<?php
   include_once "database.php";

   $user = $_POST["user"];
   $password = $_POST["password"];

   $queryLogin = $connection->prepare("SELECT * FROM User WHERE Username = :user OR Email = :user");

   $queryLogin->bindParam(":user", $user, PDO::PARAM_STR);
   $queryLogin->execute();

   if ($queryLogin->rowCount() == 0) {

      $queryLogin->closeCursor();
      $connection = null;

      echo "Error";
      exit();

   } elseif ($queryLogin->rowCount() == 1) {
      $row = $queryLogin->fetchAll(PDO::FETCH_ASSOC);
      $hashedPasswordCheck = password_verify($password, $row[0]["Password"]);

      if ($hashedPasswordCheck == FALSE) {
         echo "Error";
         exit();
      } elseif ($hashedPasswordCheck == TRUE) {

         session_start();
         $_SESSION["User_Id"] = $row[0]["Key_User"];
         $_SESSION["User_Nome"] = $row[0]["Nome"];
         $_SESSION["User_Username"] = $row[0]["Username"];
         $_SESSION["User_Email"] = $row[0]["Email"];

         $queryLogin->closeCursor();
         $connection = null;

         echo "Login";
         exit();
      }
   } else {

      $queryLogin->closeCursor();
      $connection = null;

      echo "Error";
      exit();
   }
?>
