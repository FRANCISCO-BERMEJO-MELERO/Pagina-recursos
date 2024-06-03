<?php
require_once('Connection.php');
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("location:index.php");
    exit();
} else {
    if ($_SESSION['rol'] == 'user') {
        header("location:componentes.php");
        exit();
    }
}
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="css/styles_formularios.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://kit.fontawesome.com/2c87fc6ff6.js" crossorigin="anonymous"></script>

    <style>
        .dropdown-content li > span {
            color: rgb(220, 220, 220);
        }

        .dropdown-content li > span {
            background-color: #333;
        }

        .select-wrapper option:disabled {
            background-color: #333;
        }

        .dropdown-content li {
            min-height: 0px !important;
        }
    </style>
</head>
<body>

<?php include 'navbar2.php'; ?>


<br><br><br>

<section class="container" style="margin-top: 5rem">
    <div class="row">
        <h3 class="center-align">AGREGAR CATEGORÍA</h3>
        <hr>
        <br>
        <article class="col s6 offset-s3">
            <form method="post" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>">
                <div class="input-field">
                    <i class="material-icons prefix">perm_identity</i>
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="input-field">
                    <i class="material-icons prefix">camera_alt</i>
                    <label for="icono">Icono (Font Awesome)</label>
                    <input type="text" name="icono" required>
                </div>


                <br></br>
                <p class="center-align">
                    <button class="waves-effect waves-light btn grey darken-3" type="submit" name="submit"><i class="material-icons right">add</i>crear</button>
                </p>
            </form>
        </article>
    </div>
</section>

<?php
$pdo = (new Connection())->getPdo();
if (isset($_POST["submit"])) {
    try {
        $nombre = htmlspecialchars($_POST["nombre"]);
        $icono = htmlspecialchars($_POST["icono"]);
        $stmt = $pdo->prepare("INSERT INTO categoria (titulo, icono) VALUES (:nombre, :icono)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':icono', $icono);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo 'Se ha crado correctamente';
        } else {
            echo "Error";
        }

        echo '<script>window.location.href = "componentes.php";</script>';
        exit();
    } catch (PDOException $e) {
        echo "Error consulta: " . $e->getMessage();
    }
}
?>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/desplegable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>


</body>
</html>
