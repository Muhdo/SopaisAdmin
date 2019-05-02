<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
   </head>
   <body>
      <?php
         session_start();
         if (isset($_SESSION["User_Id"])) {
            header("Location: inicio.php");
         }
         else {
            header("Location: login.php");
         }
      ?>
   </body>
</html>
