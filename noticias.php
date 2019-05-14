<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/noticias.css">

      <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=75m26byuv004g3ef1g0nt44veoaej2ja385dzy5fynrjt9jm"></script>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <link  href="node_modules/cropperjs/dist/cropper.css" rel="stylesheet">
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
         <nav class="nav-noticias">
            <?php
               include_once "backend/database.php";

               $contagem;
               $ultimoPost = 0;

               $queryVerificar = $connection->prepare("SELECT COUNT(key_Noticia) AS 'Contagem' FROM Noticia");
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
                  $queryLoadNoticias = $connection->prepare("SELECT Key_Noticia, TituloPT, Imagem, DataPublicacao FROM Noticia ORDER BY DataPublicacao DESC");
                  $queryLoadNoticias->execute();

                  if ($queryLoadNoticias->rowCount() >= 1) {
                     foreach ($queryLoadNoticias->fetchAll() as $resultado) {
                        $ultimoPost++;

                        if (strlen(utf8_encode($resultado["TituloPT"])) > 60) {
                           $titulo = mb_substr(utf8_encode($resultado["TituloPT"]), 0, 60)."<code>...</code>";
                        } else {
                           $titulo = utf8_encode($resultado["TituloPT"]);
                        }

                        setlocale(LC_ALL, 'pt_PT', 'pt_PT.utf-8', 'pt_PT.utf-8', 'portuguese');
                        echo '<div class="div-content post artigo" id="'.$resultado["Key_Noticia"].'" cont="'.$ultimoPost.'">
                        <div class="image-container">
                        <img id="imagem" src="data:image/jpeg;base64,'.base64_encode($resultado["Imagem"]).'">
                        </div>
                        <h4 id="Data">'.ucfirst(utf8_encode(strftime("%d %B, %Y &agrave;s %H:%M", strtotime($resultado["DataPublicacao"])))).'</h4>
                        <h3 id="titulo">'.$titulo.'</h3>
                        </div>';
                     }
                  }
                  $queryLoadNoticias->closeCursor();
               } else {
                  echo "Ainda não existe nenhuma notícia.";
               }
            ?>
         </nav>
         <div class="div-menu">
            <button type="button" name="NovaNoticia" onclick="novaNoticia();">Nova Notícia</button>
         </div>
         <form class="newsForm" name="form" action="backend/sendNews.php" method="post">
            <p>Titulo Português:<br>
               <input type="text" name="tituloPT" id="tituloPT">
            </p>
            <p>Titulo Inglês:<br>
               <input type="text" name="tituloEN" id="tituloEN">
            </p>
            <p>Imagem Cabeçalho:<br>
               <label class="form-filebutton" id="filebutton">Carregar Imagem
                  <input type="file" id="imagem" name="Imagem" accept="image/*">
               </label>
               <img class="img-preview hidden" id="img-view">
               <div class="div-preview hidden">
                  <img class="img-preview" id="img-preview">
                  <div class="div-buttons">
                     <div class="div-buttons-group">
                        <button class="btn-selected" type="button" id="btn-move"><img class="img-button" src="img/arrows.png"></button>
                        <button type="button" id="btn-crop"><img class="img-button" src="img/crop.png"></button>
                     </div>
                     <div class="div-buttons-group">
                        <button type="button" id="btn-rotLft"><img class="img-button" src="img/rotate-left.png"></button>
                        <button type="button" id="btn-rotRht"><img class="img-button" src="img/rotate-right.png"></button>
                     </div>
                     <div class="div-buttons-group">
                        <button type="button" id="btn-zoomIn"><img class="img-button" src="img/zoom-in.png"></button>
                        <button type="button" id="btn-zoomOut"><img class="img-button" src="img/zoom-out.png"></button>
                     </div>
                     <div class="div-buttons-group">
                        <button type="button" id="btn-reset"><img class="img-button" src="img/reset.png"></button>
                     </div>
                  </div>
               </div>
            </p>
            <p>Notícia Português:<br>
               <textarea id="editor1" name="editorPT"></textarea>
            </p>
            <p>Notícia Inglês:<br>
               <textarea id="editor2" name="editorEN"></textarea>
            </p>
            <input type="submit" name="submit" value="Enviar">
         </form>
         <button class="btn-delete" type="button" onclick="abrirApagar();">Apagar</button>
      </main>
      <?php } ?>

      <script src="node_modules/cropperjs/dist/cropper.js"></script>
      <script src="node_modules/jquery-cropper/dist/jquery-cropper.js"></script>
      <script>
         function abrirApagar() {
            if (butao != "Guardar") {
               var popup = prompt("Tem a certeza que quer apagar a noticia \"" + $("#tituloPT").val() + "\"? \n Escreva o nome da noticia para apagar!");

               if (popup == $("#tituloPT").val()) {
                  $.ajax({
                     type: "POST",
                     url: "backend/deleteNews.php",
                     data: {
                        key: butao
                     },
                     success: function(output) {
                        if (output == "Deleted") {
                           location.reload();
                        }
                     }
                  });
               } else if (popup === null) {
                  alert("A notícia NÃO foi apagada!");
               } else {
                  alert("O titulo introduzido não corresponde!");
               }
            }
         }

         function novaNoticia() {
            butao = "Guardar";
            $("#tituloPT").val("");
            $("#tituloEN").val("");

            $(".div-preview").addClass("hidden");
            $("#img-view").addClass("hidden");
            $("input:file").val("");
            $("#img-preview").cropper("destroy");
            $("#img-preview").attr("src", "");
            $(tinymce.get('editor1').getBody()).html("");
            $(tinymce.get('editor2').getBody()).html("");
         }

         var butao = "Guardar";

         $(document).on('click','.artigo',function(){
               id = $(this).attr("id");
               $.ajax({
                  type: "POST",
                  url: "backend/loadNews.php",
                  data: { key: id },
                  success: function(output) {
                     output = JSON.parse(output);
                     butao = output.keyNoticia;
                     $("#tituloPT").val(output.tituloPT);
                     $("#tituloEN").val(output.tituloEN);

                     $(".div-preview").addClass("hidden");
                     $("#img-view").removeClass("hidden");
                     $("input:file").val("");
                     $("#img-preview").cropper("destroy");
                     $("#img-view").attr("src", "data:image/jpeg;base64," + output.imagem);

                     $(tinymce.get('editor1').getBody()).html(output.conteudoPT);
                     $(tinymce.get('editor2').getBody()).html(output.conteudoEN);
                  }
               });
            });

         $(function() {
            var image = $("#img-preview");
            $("input:file").change(function() {
               StyleValid("filebutton");
               $(".div-preview").removeClass("hidden");

               var oFReader = new FileReader();

               oFReader.readAsDataURL(this.files[0]);
               oFReader.onload = function (oFREvent) {
                  $("#img-view").addClass("hidden");
                  image.cropper("destroy");
                  image.attr("src", this.result);
                  image.cropper({
                     aspectRatio: 16 / 9,
                     viewMode: 0,
                     toggleDragModeOnDblclick: false,
                     dragMode: "move",
                     crop: function(e) {}
                  });
               };
            });

            $("#btn-move").click(function() {
               $("#btn-crop").removeClass("btn-selected");
               $("#btn-move").addClass("btn-selected");
               $("#img-preview").cropper("setDragMode", "move");
            })

            $("#btn-crop").click(function() {
               $("#btn-move").removeClass("btn-selected");
               $("#btn-crop").addClass("btn-selected");
               $("#img-preview").cropper("setDragMode", "crop");
            })

            $("#btn-rotLft").click(function() {
               $("#img-preview").cropper("rotate", -5);
            })

            $("#btn-rotRht").click(function() {
               $("#img-preview").cropper("rotate", 5);
            })

            $("#btn-zoomIn").click(function() {
               $("#img-preview").cropper("zoom", 0.1);
            })

            $("#btn-zoomOut").click(function() {
               $("#img-preview").cropper("zoom", -0.1);
            })

            $("#btn-reset").click(function() {
               $("#img-preview").cropper("reset");
            })
         });

         function StyleErro(input) {
            $("#" + input).addClass("erro");
         }

         function StyleValid(input) {
            $("#" + input).removeClass("erro");
         }

         var Timer;
         var Intervalo = 500;

         $("#tituloPT").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/titulo.php",
               data: {
                  titulo: $("#tituloPT").val(),
                  lang: "PT"
               },
               success: function(output) {
                  if (output == "Erro") {
                     StyleErro("tituloPT");
                  } else if (output == "Valido") {
                     StyleValid("tituloPT");
                  }
               }
            });
         });
         $("#tituloPT").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/titulo.php",
                  data: {
                     titulo: $("#tituloPT").val(),
                     lang: "PT"
                  },
                  success: function(output) {
                     if (output == "Erro") {
                        StyleErro("tituloPT");
                     } else if (output == "Valido") {
                        StyleValid("tituloPT");
                     }
                  }
               });
            }, Intervalo);
         });
         $("#tituloPT").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#tituloEN").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/titulo.php",
               data: {
                  titulo: $("#tituloEN").val(),
                  lang: "EN"
               },
               success: function(output) {
                  if (output == "Erro") {
                     StyleErro("tituloEN");
                  } else if (output == "Valido") {
                     StyleValid("tituloEN");
                  }
               }
            });
         });
         $("#tituloEN").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/titulo.php",
                  data: {
                     titulo: $("#tituloEN").val(),
                     lang: "EN"
                  },
                  success: function(output) {
                     if (output == "Erro") {
                        StyleErro("tituloEN");
                     } else if (output == "Valido") {
                        StyleValid("tituloEN");
                     }
                  }
               });
            }, Intervalo);
         });
         $("#tituloEN").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#editorPT").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/conteudo.php",
               data: {
                  editor: $("#editorPT").val()
               },
               success: function(output) {
                  if (output == "Erro") {
                     StyleErro("editor1");
                  } else if (output == "Valido") {
                     StyleValid("editor1");
                  }
               }
            });
         });
         $("#editorPT").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/conteudo.php",
                  data: {
                     editor: $("#editorPT").val()
                  },
                  success: function(output) {
                     if (output == "Erro") {
                        StyleErro("editor1");
                     } else if (output == "Valido") {
                        StyleValid("editor1");
                     }
                  }
               });
            }, Intervalo);
         });
         $("#editorPT").on("keydown", function() {
            clearTimeout(Timer);
         });

         $("#editorEN").on("blur", function() {
            $.ajax({
               type: "POST",
               url: "backend/validations/conteudo.php",
               data: {
                  editor: $("#editorEN").val()
               },
               success: function(output) {
                  if (output == "Erro") {
                     StyleErro("editor2");
                  } else if (output == "Valido") {
                     StyleValid("editor2");
                  }
               }
            });
         });
         $("#editorEN").on("keyup", function() {
            clearTimeout(Timer);
            Timer = setTimeout(function() {
               $.ajax({
                  type: "POST",
                  url: "backend/validations/conteudo.php",
                  data: {
                     editor: $("#editorEN").val()
                  },
                  success: function(output) {
                     if (output == "Erro") {
                        StyleErro("editor2");
                     } else if (output == "Valido") {
                        StyleValid("editor2");
                     }
                  }
               });
            }, Intervalo);
         });
         $("#editorEN").on("keydown", function() {
            clearTimeout(Timer);
         });

         $(".newsForm").submit(function(e) {
            e.preventDefault();

            var imagem;

            if (butao != "Guardar") {
               try {
                  imagem = $("#img-preview").cropper("getCroppedCanvas", {fillColor: '#fff', width: 1000}).toDataURL("image/jpeg", 1);
               } catch (e) {
                  imagem = "NoImage";
               }
            } else if (butao == "Guardar" || butao == "") {
               imagem = $("#img-preview").cropper("getCroppedCanvas", {fillColor: '#fff', width: 1000}).toDataURL("image/jpeg", 1);
            }

            $.ajax({
               type: "POST",
               url: "backend/sendNews.php",
               data: {
                  tituloPT: form.tituloPT.value,
                  tituloEN: form.tituloEN.value,
                  imagem: imagem,
                  editorPT: $(tinymce.get('editor1').getBody()).html(),
                  editorEN: $(tinymce.get('editor2').getBody()).html(),
                  func: butao
               },
               success: function(output) {
                  if (output == "ErroTituloPT") {
                     StyleErro("tituloPT");
                  } else if (output == "ErroTituloEN") {
                     StyleErro("tituloEN");
                  } else if (output == "ErroConteudoPT") {
                     StyleErro("editor1");
                  } else if (output == "ErroConteudoEN") {
                     StyleErro("editor2");
                  } else if (output == "Valid" || output == "Updated") {
                     location.reload();
                  }
               }
            });
         });

         tinymce.init({
            selector: '#editor1',
            plugins: "link linkchecker searchreplace visualblocks preview fullscreen tinymcespellchecker emoticons table lists advlist help autosave wordcount",
            toolbar: "cut copy paste | undo redo | styleselect forecolor backcolor | bold italic underline strikethrough subscript superscript link | alignleft aligncenter alignright alignjustify | table bullist numlist outdent indent | help restoredraft",
            spellchecker_language: "pt_PT",
            default_link_target: "_blank"
         });

         tinymce.init({
            selector: '#editor2',
            plugins: "link linkchecker searchreplace visualblocks preview fullscreen tinymcespellchecker emoticons table lists advlist help autosave wordcount",
            toolbar: "cut copy paste | undo redo | styleselect forecolor backcolor | bold italic underline strikethrough subscript superscript link | alignleft aligncenter alignright alignjustify | table bullist numlist outdent indent | help restoredraft",
            spellchecker_language: "en",
            default_link_target: "_blank"
         });
      </script>
   </body>
</html>
