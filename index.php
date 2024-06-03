<?php
session_start();
if(isset($_REQUEST["l"])){
    unset($_SESSION);
    session_destroy();
}
else{
    if(isset($_SESSION["id_usuario"])){
        header("location:componentes.php");
    }
}
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="css/styles_formularios.css" >
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="css/estilos_login.css">
    <script src="https://kit.fontawesome.com/2c87fc6ff6.js" crossorigin="anonymous"></script>
    
    <?php
    require_once('Connection.php');
    ?>
</head>
<body>
    <nav class="grey darken-3 z-depth-2" style="position: fixed;">
        <div class="nav-wrapper">
            <!-- <a href="#!" class="brand-logo center"><img src="https://fernauro.es/wp-content/uploads/2021/01/Fernauro_centro_de_estudios1.jpg" alt="logo" class="responsive-img" style="max-width: 10rem;;"/> </a> -->
        </div>
    </nav>

    <br><br><br>

    <section class="container grey darken-3 z-depth-2">
        <div class="row">
            <h3 class="center-align">Iniciar sesión</h3>
            <hr>
            <br>
            <article class="col s6 offset-s3">
                <form method="post" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="input-field">
                        <i class="material-icons prefix">perm_identity</i>
                        <label for="nombre">Usuario</label>
                        <input type="text" name="nombre" required>
                    </div>

                    <div class="input-field">
                        <i class="material-icons prefix">https</i>
                        <label for="mensaje">Contraseña</label>
                        <input type="password" name="pass" required>
                    </div>
                    <br></br>
                    <p class="center-align">
                        <button class="waves-effect btn grey darken-3" type="submit" name="submit"><i class="material-icons right">send</i>enviar</button>
                    </p>
                </form>
            </article>
        </div>
    </section>

    <?php
    if(isset($_POST["submit"])){
        try {
            $pdo = (new Connection())->getPdo();

            $nombre = htmlspecialchars($_POST["nombre"]);
            $pass = htmlspecialchars($_POST["pass"]);

            // Consulta para obtener el hash de la contraseña
            $stmt = $pdo->prepare("SELECT id, pass, rol, bloqueado FROM usuario WHERE nombre = :nombre");
            $stmt->bindParam(":nombre", $nombre);
            $stmt->execute();

            $result = $stmt->fetch();

            if ($result && password_verify($pass, $result['pass']) && $result['bloqueado']==0) {
                // Contraseña verificada, iniciar sesión
                $_SESSION["id_usuario"] = $result["id"]; 
                $_SESSION["rol"] = $result["rol"]; 
                header("location:componentes.php");
                exit();
            } else {
                // Nombre o contraseña incorrectos
                echo "<script type='text/javascript'>alert('El usuario o contraseña no son correctos. Inténtalo otra vez');</script>";
            }

        } catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('El usuario o contraseña no son correctos. Inténtalo otra vez.');</script>";
        }
    }
    ?>

    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="js/desplegable.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>
</body>
</html>
