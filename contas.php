<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/contas.css">
   </head>
   <body>
      <?php
         session_start();

         if (!isset($_SESSION["User_Id"])) {
            echo "<script type='text/javascript'>window.top.location='index.php';</script>";
         } else {
            include_once "includes/header.php";
      ?>
      <main>
         <table id="Lista">
            <?php
               include_once "backend/database.php";

               $contagem;
               $ultimoPost = 0;

               $queryVerificar = $connection->prepare("SELECT COUNT(Key_User) AS 'Contagem' FROM User");
               $queryVerificar->execute();

               if ($queryVerificar->rowCount() == 0) {
                  echo "Erro de coneção com o servidor!";

                  $queryVerificar->closeCursor();
               } else {
                  $resultado = $queryVerificar->fetch(PDO::FETCH_OBJ);
                  $contagem = $resultado->Contagem;

                  $queryVerificar->closeCursor();
               }

               if ($contagem >= 1) {
                  echo '<tr id="heading">
                     <th>Nome</th>
                     <th>Endereço Email</th>
                     <th>Username</th>
                  </tr>';

                  $queryLoadUsers = $connection->prepare("SELECT Key_User, Nome, Username, Email FROM User");
                  $queryLoadUsers->execute();

                  if ($queryLoadUsers->rowCount() >= 1) {
                     foreach ($queryLoadUsers->fetchAll() as $resultado) {
                        $ultimoPost++;
                        setlocale(LC_ALL, 'pt_PT', 'pt_PT.utf-8', 'pt_PT.utf-8', 'portuguese');
                        echo '<tr  id="'.$resultado["Key_User"].'" cont="'.$ultimoPost.'">
                           <td>'.utf8_encode($resultado["Nome"]).'</td>
                           <td>'.utf8_encode($resultado["Email"]).'</td>
                           <td>'.utf8_encode($resultado["Username"]).'</td>
                        </tr>';
                     }
                  }
                  $queryLoadUsers->closeCursor();
               } else {
                  echo "Ainda não existe nenhum utilizador.";
               }
            ?>
         </table>
         <button class="btn-delete" type="button" onclick="abrirApagar();">Apagar</button>
         <div class="div-form">
            <form class="div-form" name="registar" method="POST">
               <p>Nome:</p>
               <input type="text" name="nome" id="Nome">
               <p>Username:</p>
               <input type="text" name="user" id="User">
               <p>Email:</p>
               <input type="email" name="email" id="Email">
               <p>Password:</p>
               <input type="password" name="password" id="Password">
               <p>Repetir Password:</p>
               <input type="password" name="repPassword" id="RepPassword">
               <p class="p-error hidden">Duplicado</p>
               <input type="submit" name="submit" value="Registar">
            </form>
         </div>
      </main>
      <script>
         function Styling(input, action) {
            if (action == "Error") {
               document.getElementById(input).classList.add("form-input-erro");
            } else if (action == "Valid") {
               document.getElementById(input).classList.remove("form-input-erro");
            }
         }

         var sessao = "<?php echo $_SESSION["User_Id"]; ?>";
         var selectedUser = "";

         function abrirApagar() {
            if (selectedUser == "") {

            } else if (selectedUser != sessao) {
               var popup = prompt("Tem a certeza que quer apagar a conta \"" + $("#Nome").val() + "\"? \n Escreva o nome da conta para apagar!");

               if (popup == $("#Nome").val()) {
                  $.ajax({
                     type: "POST",
                     url: "backend/deleteConta.php",
                     data: {
                        key: selectedUser
                     },
                     success: function(output) {
                        if (output == "Deleted") {
                           location.reload();
                        }
                     }
                  });
               } else if (popup === null) {
                  alert("A conta NÃO foi apagada!");
               } else {
                  alert("O nome introduzido não corresponde!");
               }
            } else if (selectedUser == sessao) {
               alert("Não é possivel apagar a conta que está ligada!");
            }
         }

         $(document).on('click','tr',function(){
            var key = $(this).attr("id");
            if (key != "heading") {
               $.ajax({
                  type: "POST",
                  url: "backend/carregarInfoConta.php",
                  data: {
                     key: key
                  },
                  success: function(output) {
                     output = JSON.parse(output);
                     $("#Nome").val(output.Nome);
                     $("#User").val(output.Username);
                     $("#Email").val(output.Email);

                     selectedUser = output.KeyUser;
                  }
               });
            }
         });

         $(".div-form").submit(function(e) {
            e.preventDefault();

            $.ajax({
               type: "POST",
               url: "backend/registar.php",
               data: {
                  nome: registar.nome.value,
                  user: registar.user.value,
                  email: registar.email.value,
                  password: registar.password.value,
                  repPassword: registar.repPassword.value,
               },
               success: function(output) {
                  console.log(output);
                  if (output == "ErrorName") {
                     Styling("Nome", "Error");
                  } if (output == "ErrorUsername" || output == "DuplicationUsername") {
                     Styling("User", "Error");
                  } if (output == "ErrorEmail" || output == "DuplicationEmail") {
                     Styling("Email", "Error");
                  } if (output == "ErrorPassword") {
                     Styling("Password", "Error");
                  } if (output == "ErrorRepPassword") {
                     Styling("RepPassword", "Error");
                  } else if (output == "Registar") {
                     location.reload();
                  }
               }
            });
         });

         var Timer;
         var Intervalo = 500;

         $("#Nome").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/nome.php",
               data: {
                  nome: registar.nome.value
               },
               success: function(output) {
                  Styling("Nome", output);
               }
            });
         });
         $("#Nome").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/nome.php",
                  data: {
                     nome: registar.nome.value
                  },
                  success: function(output) {
                     Styling("Nome", output);
                  }
               });
            }, Intervalo);
         });
         $("#Nome").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#User").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/username.php",
               data: {
                  username: registar.user.value
               },
               success: function(output) {
                  if (output == "Duplication") {
                     Styling("User", "Error");
                  } else {
                     Styling("User", output);
                  }
               }
            });
         });
         $("#User").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/username.php",
                  data: {
                     username: registar.user.value
                  },
                  success: function(output) {
                     if (output == "Duplication") {
                        Styling("User", "Error");
                     } else {
                        Styling("User", output);
                     }
                  }
               });
            }, Intervalo);
         });
         $("#User").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#Email").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/email.php",
               data: {
                  email: registar.email.value
               },
               success: function(output) {
                  if (output == "Duplication") {
                     Styling("Email", "Error");
                  } else {
                     Styling("Email", output);
                  }
               }
            });
         });
         $("#Email").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/email.php",
                  data: {
                     email: registar.email.value
                  },
                  success: function(output) {
                     if (output == "Duplication") {
                        Styling("Email", "Error");
                     } else {
                        Styling("Email", output);
                     }
                  }
               });
            }, Intervalo);
         });
         $("#Email").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#Password").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/password.php",
               data: {
                  password: registar.password.value
               },
               success: function(output) {
                  Styling("Password", output);
               }
            });

            $.ajax({
               type: "POST",
               url: "backend/validations/repPassword.php",
               data: {
                  password: registar.password.value,
                  repPassword: registar.repPassword.value
               },
               success: function(output) {
                  Styling("RepPassword", output);
               }
            });
         });
         $("#password").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/password.php",
                  data: {
                     password: registar.password.value
                  },
                  success: function(output) {
                     Styling("Password", output);
                  }
               });

               $.ajax({
                  type: "POST",
                  url: "backend/validations/repPassword.php",
                  data: {
                     password: registar.password.value,
                     repPassword: registar.repPassword.value
                  },
                  success: function(output) {
                     Styling("RepPassword", output);
                  }
               });
            }, Intervalo);
         });
         $("#password").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#RepPassword").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/password.php",
               data: {
                  password: registar.password.value
               },
               success: function(output) {
                  Styling("Password", output);
               }
            });

            $.ajax({
               type: "POST",
               url: "backend/validations/repPassword.php",
               data: {
                  password: registar.password.value,
                  repPassword: registar.repPassword.value
               },
               success: function(output) {
                  Styling("RepPassword", output);
               }
            });
         });
         $("#RepPassword").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/password.php",
                  data: {
                     password: registar.password.value
                  },
                  success: function(output) {
                     Styling("Password", output);
                  }
               });

               $.ajax({
                  type: "POST",
                  url: "backend/validations/repPassword.php",
                  data: {
                     password: registar.password.value,
                     repPassword: registar.repPassword.value
                  },
                  success: function(output) {
                     Styling("RepPassword", output);
                  }
               });
            }, Intervalo);
         });
         $("#RepPassword").on("keydown", function() {
            clearTimeout(Timer);
         });
      </script>
      <?php } ?>
   </body>
</html>
