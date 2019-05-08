<!DOCTYPE html>
<html lang="pt" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Admin Website</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/noticias.css">

      <script src="node_modules/jquery/dist/jquery.js"></script><!-- jQuery is required -->
      <link  href="node_modules/cropper/dist/cropper.css" rel="stylesheet">
      <script src="node_modules/cropper/dist/cropper.js"></script>

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
         <form class="newsForm" name="form" action="backend/sendNews.php" method="post">
            <p>Titulo Português:<br>
               <input type="text" name="tituloPT">
            </p>
            <p>Titulo Inglês:<br>
               <input type="text" name="tituloEN">
            </p>
            <p>Imagem Cabeçalho:<br>
               <label class="form-filebutton">Carregar Imagem
                  <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/JPEG, image/jpeg2000, image/jpg, image/gif">
               </label>
            </p>
            <p>Notícia Português:<br>
               <textarea id="editor1" name="editorPT"></textarea>
            </p>
            <p>Notícia Inglês:<br>
               <textarea id="editor2" name="editorEN"></textarea>
            </p>
            <input type="submit" name="submit" value="Enviar">
         </form>
      </main>
      <?php } ?>

      <script>
         $(".newsForm").submit(function(e) {
            e.preventDefault();

            var reader = new FileReader();
            var ficheiro = document.querySelector('input[type=file]').files[0];
            reader.readAsDataURL(ficheiro);
            reader.onloadend = function () {
               $.ajax({
                  type: "POST",
                  url: "backend/sendNews.php",
                  data: {
                     tituloPT: form.tituloPT.value,
                     tituloEN: form.tituloEN.value,
                     imagem: reader.result.replace(/^data:image\/(png|jpg);base64,/, ""),
                     editorPT: form.editorPT.value,
                     editorEN: form.editorEN.value
                  },
                  success: function(output) {
                     if (output == "Error") {
                        $(".p-error").removeClass("hidden");
                     } else if (output == "Login") {
                        $(".p-error").addClass("hidden");
                        location.href = "index.php";
                     } else {
                        console.log(output);
                     }
                  }
               });
            }
         });

         tinymce.init({
            selector: '#editor1, #editor2',
            plugins: "link linkchecker searchreplace visualblocks preview fullscreen tinymcespellchecker emoticons table lists advlist help autosave wordcount",
            toolbar: "cut copy paste | undo redo | styleselect forecolor backcolor | bold italic underline strikethrough subscript superscript link | alignleft aligncenter alignright alignjustify | table bullist numlist outdent indent | help restoredraft",
            spellchecker_language: "pt_PT",
            default_link_target: "_blank"
         });
      </script>
   </body>
</html>
