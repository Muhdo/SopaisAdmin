<head>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
   <link rel="stylesheet" href="css/header.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<header>
   <div class="div-heading">
      <a href="index.php">
         <img class="logo" src="img/Logo.png">
      </a>
      <p class="name"><b>SOPAIS -</b> Componentes Metálicos Lda.</p>
   </div>
   <nav>
      <a class="item" href="inicio.php">Inicio</a>
      <a class="item" href="noticias.php">Noticias</a>
      <a class="item" href="mensagens.php">Mensagens</a>
   </nav>
   <div class="div-footing">
      <div class="user">
         <p>Utilizador:</p>
         <p class="name"><?php echo $_SESSION["User_Nome"]; ?></p>
      </div>
      <a href="backend/logout.php">
         <img class="logout" src="img/logout.png">
      </a>
   </div>
</header>
