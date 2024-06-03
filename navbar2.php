<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/styles_formularios.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link rel="stylesheet" href="css/styles.css" type="text/css">
<title>Gestión</title>
<style>
    .search-input {
        height: 2rem !important;
        border: none !important;
        padding-left: 8px !important;
        border-radius: 4px !important;
        border-bottom: 2px solid transparent !important;
        transition: border-color 0.3s ease-in-out !important;
    }

    .search-input:focus {
        border-bottom: none;
        outline: none !important;
        box-shadow: none !important;
    }
</style>
</head>
<body>
<nav class="grey darken-3 z-depth-2" style="position: fixed; width: 100%; top: 0;">
    <div class="nav-wrapper">
        <!-- <a href="#!" class="brand-logo center">
            <img src="https://fernauro.es/wp-content/uploads/2021/01/Fernauro_centro_de_estudios1.jpg" alt="logo" class="responsive-img" style="max-width: 10rem;">
        </a> -->
        <ul class="right hide-on-med-and-down">
            <?php if ($_SESSION['rol'] == 'admin') { ?>
                <li><a href="gestionar_user.php"><i class="material-icons">group</i></a></li>
                <li style="border-right: 2px solid #757575;"><a href="crear_users.php"><i class="material-icons">group_add</i></a></li>
            <?php } ?>
            <?php if ($_SESSION['rol'] != 'user' ) { ?>
                <li><a href="formulario.php"><i class="material-icons">vertical_align_top</i></a></li>
                <li><a href="crear_categoria.php"><i class="material-icons">create_new_folder</i></a></li>
            <?php } ?>
            <li><a href="componentes.php"><i class="material-icons">home</i></a></li>
            <li><a href="index.php?l=1"><i class="material-icons">exit_to_app</i></a></li>
        </ul>
    </div>
</nav>
</body>
</html>
