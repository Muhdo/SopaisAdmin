<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/login.css">

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   </head>
   <body>
      <?php
         session_start();

         if (isset($_SESSION["User_Id"])) {
            header("Location: index.php");
         } else {
      ?>
      <form class="div-form" name="login" method="POST">
         <p>Utilizador ou Email:</p>
         <input type="text" name="user">
         <p>Password:</p>
         <input type="password" name="password">
         <p class="p-error hidden">Utilizador ou Password Incorreto</p>
         <input type="submit" name="submit" value="Enviar">
      </form>
      <script>
      $(".div-form").submit(function(e) {
         e.preventDefault();

         $.ajax({
            type: "POST",
            url: "backend/login.php",
            data: {
               user: login.user.value,
               password: login.password.value,
            },
            success: function(output) {
               if (output == "Error") {
                  $(".p-error").removeClass("hidden");
               } else if (output == "Login") {
                  $(".p-error").addClass("hidden");
                  location.href = "index.php";
               }
            }
         });
      });
      </script>
      <?php } ?>
   </body>
</html>
