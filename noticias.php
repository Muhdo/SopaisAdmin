<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/noticias.css">

      <script src="node_modules/jquery/dist/jquery.js"></script>
      <script src="node_modules/cropperjs/dist/cropper.js"></script>
      <link href="node_modules/cropperjs/dist/cropper.css" rel="stylesheet">
      <script src="node_modules/jquery-cropper/dist/jquery-cropper.js"></script>

      <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=75m26byuv004g3ef1g0nt44veoaej2ja385dzy5fynrjt9jm"></script>
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
            <button type="button" name="NovaNoticia">Nova Notícia</button>
         </div>
         <form action="backend/sendNews.php" method="post">
            <p>Titulo Português:<br>
               <input type="text" name="tituloPT">
            </p>
            <p>Titulo Inglês:<br>
               <input type="text" name="tituloEN">
            </p>
            <p>Imagem Cabeçalho:<br>
               <label class="form-filebutton">Carregar Imagem
                  <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/JPEG, image/jpeg2000, image/jpg">
               </label>
            </p>
            <p>Notícia Português:<br>
               <textarea id="editor1" name="editorPT"></textarea>
            </p>
            <p>Notícia Inglês:<br>
               <textarea id="editor2" name="editorEN"></textarea>
            </p>
         </form>
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
            <div class="div-buttons">
               <button class="btn-send" type="button" id="btn-submit">Enviar</button>
            </div>
         </div>
      </main>
      <?php } ?>

      <script>
      $(function() {
         var image = $("#img-preview");
         $("input:file").change(function() {
            $(".div-preview").removeClass("hidden");

            var oFReader = new FileReader();

            oFReader.readAsDataURL(this.files[0]);
            oFReader.onload = function (oFREvent) {
               image.cropper("destroy");
               image.attr("src", this.result);
               image.cropper({
                  aspectRatio: 1 / 1,
                  viewMode: 1,
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

         tinymce.init({
            selector: '#editor1, #editor2',
            plugins: "link linkchecker searchreplace visualblocks preview fullscreen tinymcespellchecker emoticons table lists advlist help autosave save wordcount",
            toolbar: "cut copy paste | undo redo | styleselect forecolor backcolor | bold italic underline strikethrough subscript superscript link | alignleft aligncenter alignright alignjustify | table bullist numlist outdent indent | help restoredraft save",
            spellchecker_language: "pt_PT",
            default_link_target: "_blank"
         });
      </script>
   </body>
</html>
