<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/inicio.css">
   </head>
   <body>
      <?php
         session_start();

         if (!isset($_SESSION["User_Id"])) {
            header("Location: index.php");
         } else {
            include_once "includes/header.php";
      ?>
      <main>
         <div class="div-mensagem">
            <div class="div-campos">
               <h3>Mensagens Totais:</h3>
               <p id="MensagemTotal"></p>
            </div>
            <div class="div-campos">
               <h3>Mensagens Por Responder:</h3>
               <p id="MensagemResponder"></p>
            </div>
            <progress value="50" max="100" id="MensagemResponderProg">1000%</progress>
            <div class="div-campos">
               <h3>Noticias Totais:</h3>
               <p id="NoticiasTotais"></p>
            </div>
         </div>
      </main>
      <script>
         $(document).ready(function() {
            $.ajax({
               type: "POST",
               url: "backend/carregarInicio.php",
               success: function(output) {
                  output = JSON.parse(output);
                  $("#MensagemTotal").html(output.Mensagens);
                  $("#MensagemResponder").html(output.Responder);
                  $("#MensagemResponderProg").val(output.ResponderPerc);
                  $("#MensagemResponderProg").html(output.ResponderPerc + "%");
                  $("#NoticiasTotais").html(output.Noticias);
               }
            });
         });
      </script>
      <?php } ?>
   </body>
</html>
