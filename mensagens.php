<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/mensagens.css">

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
         <table id="Lista">
            <?php
               include_once "backend/database.php";

               $contagem;
               $ultimoPost = 0;

               $queryVerificar = $connection->prepare("SELECT COUNT(key_Mensagem) AS 'Contagem' FROM Mensagem");
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
                     <th>Data</th>
                  </tr>';

                  $queryLoadMensagens = $connection->prepare("SELECT Key_Mensagem, Nome, Email, Data, Respondido FROM Mensagem ORDER BY Respondido, Data DESC LIMIT 30");
                  $queryLoadMensagens->execute();

                  if ($queryLoadMensagens->rowCount() >= 1) {
                     foreach ($queryLoadMensagens->fetchAll() as $resultado) {
                        $ultimoPost++;
                        setlocale(LC_ALL, 'pt_PT', 'pt_PT.utf-8', 'pt_PT.utf-8', 'portuguese');
                        if ($resultado["Respondido"] == 0) {
                           echo '<tr class="responder" id="'.$resultado["Key_Mensagem"].'" cont="'.$ultimoPost.'">
                              <td>'.utf8_encode($resultado["Nome"]).'</td>
                              <td>'.utf8_encode($resultado["Email"]).'</td>
                              <td>'.ucfirst(utf8_encode(strftime("%d-%m-%Y %H:%M", strtotime($resultado["Data"])))).'</td>
                           </tr>';
                        } else {
                           echo '<tr class="respondido" id="'.$resultado["Key_Mensagem"].'" cont="'.$ultimoPost.'">
                           <td>'.utf8_encode($resultado["Nome"]).'</td>
                           <td>'.utf8_encode($resultado["Email"]).'</td>
                           <td>'.ucfirst(utf8_encode(strftime("%d-%m-%Y %H:%M", strtotime($resultado["Data"])))).'</td>
                           </tr>';
                        }
                     }
                  }
                  $queryLoadMensagens->closeCursor();
               } else {
                  echo "Ainda não existe nenhuma mensagem.";
               }
            ?>
         </table>
         <div class="div-menu">
            <a onclick="prevMessage($('tr:nth-child(2)').attr('cont'));">
               <img class="img-arrow" src="img/arrow-bwd.png">
            </a>
            <div class="div-numeros">
            <?php
               for ($i=0; $i < ceil($contagem / 30); $i++) {
                  if (ceil($ultimoPost / 30) == $i + 1) {
                     echo '<a class="pageSelected">'.($i + 1).'</a>';
                  } else {
                     echo '<a onclick="irPara($(this).html());">'.($i + 1).'</a>';
                  }
               }
            ?>
            </div>
            <a onclick="nextMessage($('tr:last').attr('cont'));">
               <img class="img-arrow" src="img/arrow-fwd.png">
            </a>
         </div>
         <div class="div-mensagem">
            <div class="div-campos">
               <h2>Nome:</h2>
               <p id="Nome"></p>
            </div>
            <div class="div-campos">
               <h2>Email:</h2>
               <a href="" target="_blank" id="Email"></a>
            </div>
            <div class="div-campos">
               <h2>Data:</h2>
               <p id="Data"></p>
            </div>
            <div class="div-mensagem">
               <h2>Mensagem:</h2>
               <p id="Mensagem"></p>
            </div>

            <div class="div-butao">
            </div>
         </div>
      </main>
      <?php } ?>
      <script>
         $(document).on('click','tr',function(){
            var key = $(this).attr("id");
            if (key != "heading") {
               $.ajax({
                  type: "POST",
                  url: "backend/carregarInfoMensagem.php",
                  data: {
                     key: key
                  },
                  success: function(output) {
                     output = JSON.parse(output);
                     $("#Nome").html(output.Nome);
                     $("#Email").html(output.Email);
                     $("#Email").attr("href", "mailto:" + output.Email + "?Subject=Resposta%20%C3%A0%20mensagem%20na%20Sopais")
                     $("#Data").html(output.Data);
                     $("#Mensagem").html(output.Mensagem);

                     $(".div-butao button").remove();

                     if (output.Respondido == 0) {
                        $('.div-butao').append('<button type="button" name="btn-estado" onClick="mudarEstado(\'' + key + '\', 1);">Marcar como respondido</button>');
                     } else {
                        $('.div-butao').append('<button type="button" name="btn-estado" onClick="mudarEstado(\'' + key + '\', 0);">Marcar como por responder</button>');
                     }
                  }
               });
            }
         });

         function mudarEstado(chave, estado) {
            $.ajax({
               type: "POST",
               url: "backend/mudarEstadoMensagem.php",
               data: {
                  KeyMensagem: chave,
                  estado: estado
               },
               success: function(output) {
                  $(".div-butao button").remove();
                  if (estado == 0) {
                     $('.div-butao').append('<button type="button" name="btn-estado" onClick="mudarEstado(\'' + chave + '\', 1);">Marcar como respondido</button>');
                     $("#" + chave).removeClass("respondido").addClass("responder");
                  } else if (estado == 1) {
                     $("#" + chave).removeClass("responder").addClass("respondido");
                     $('.div-butao').append('<button type="button" name="btn-estado" onClick="mudarEstado(\'' + chave + '\', 0);">Marcar como por responder</button>');
                  }
               }
            });
         }

         function irPara(pagina) {
            var indice = 30 * (pagina - 1);

            $.ajax({
               type: "POST",
               url: "backend/irParaMensagem.php",
               data: {
                  indice: indice
               },
               success: function(output) {
                  output = JSON.parse(output);
                  var el = document.getElementById("Lista");
                  $("tr").not(':first').remove();

                  for (i in output)
                  {
                     indice++;
                     if (output[i].Respondido == 0) {
                        $('table tbody').append('<tr class="responder" id="' + output[i].KeyMensagem + '" cont="' + indice + '"><td>' + output[i].Nome + '</td><td>' + output[i].Email + '</td><td>' + output[i].Data + '</td></tr>');
                     } else if (output[i].Respondido == 1) {
                        $('table tbody').append('<tr class="respondido" id="' + output[i].KeyMensagem + '" cont="' + indice + '"><td>' + output[i].Nome + '</td><td>' + output[i].Email + '</td><td>' + output[i].Data + '</td></tr>');
                     }
                  }

                  $(".div-numeros").children().remove();
                  var contagem = <?php echo $contagem; ?>;
                  for (var i = 0; i < Math.ceil(contagem / 30); i++) {
                     if (Math.ceil(indice / 30) == i + 1) {
                        $('.div-numeros').append('<a class="pageSelected">' + (i + 1) + '</a>');
                     } else {
                        $('.div-numeros').append('<a onclick="irPara($(this).html());">' + (i + 1) + '</a>');
                     }
                  }
               }
            });
         }

         function prevMessage(indice) {
            $.ajax({
               type: "POST",
               url: "backend/loadMessages.php",
               data: {
                  indice: indice,
                  func: "Prev"
               },
               success: function(output) {
                  output = JSON.parse(output);
                  var el = document.getElementById("Lista");
                  $("tr").not(':first').remove();

                  indice -= 31;
                  for (i in output)
                  {
                     indice++;
                     if (output[i].Respondido == 0) {
                        $('table tbody').append('<tr class="responder" id="' + output[i].KeyMensagem + '" cont="' + indice + '"><td>' + output[i].Nome + '</td><td>' + output[i].Email + '</td><td>' + output[i].Data + '</td></tr>');
                     } else if (output[i].Respondido == 1) {
                        $('table tbody').append('<tr class="respondido" id="' + output[i].KeyMensagem + '" cont="' + indice + '"><td>' + output[i].Nome + '</td><td>' + output[i].Email + '</td><td>' + output[i].Data + '</td></tr>');
                     }
                  }

                  $(".div-numeros").children().remove();
                  var contagem = <?php echo $contagem; ?>;
                  for (var i = 0; i < Math.ceil(contagem / 30); i++) {
                     if (Math.ceil(indice / 30) == i + 1) {
                        $('.div-numeros').append('<a class="pageSelected">' + (i + 1) + '</a>');
                     } else {
                        $('.div-numeros').append('<a onclick="irPara($(this).html());">' + (i + 1) + '</a>');
                     }
                  }
               }
            });
         }

         function nextMessage(indice) {
            $.ajax({
               type: "POST",
               url: "backend/loadMessages.php",
               data: {
                  indice: indice,
                  func: "Next"
               },
               success: function(output) {
                  output = JSON.parse(output);
                  var el = document.getElementById("Lista");
                  $("tr").not(':first').remove();
                  for (i in output)
                  {
                     indice++;
                     if (output[i].Respondido == 0) {
                        $('table tbody').append('<tr class="responder" id="' + output[i].KeyMensagem + '" cont="' + indice + '"><td>' + output[i].Nome + '</td><td>' + output[i].Email + '</td><td>' + output[i].Data + '</td></tr>');
                     } else if (output[i].Respondido == 1) {
                        $('table tbody').append('<tr class="respondido" id="' + output[i].KeyMensagem + '" cont="' + indice + '"><td>' + output[i].Nome + '</td><td>' + output[i].Email + '</td><td>' + output[i].Data + '</td></tr>');
                     }
                  }
                  $(".div-numeros").children().remove();
                  var contagem = <?php echo $contagem; ?>;
                  for (var i = 0; i < Math.ceil(contagem / 30); i++) {
                     if (Math.ceil(indice / 30) == i + 1) {
                        $('.div-numeros').append('<a class="pageSelected">' + (i + 1) + '</a>');
                     } else {
                        $('.div-numeros').append('<a onclick="irPara($(this).html());">' + (i + 1) + '</a>');
                     }
                  }
               }
            });
         }
      </script>
   </body>
</html>
