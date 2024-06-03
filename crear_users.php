<?php
require_once('Connection.php');
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("location:index.php");
    exit();
} else {
    if ($_SESSION['rol'] != 'admin') {
        header("location:componentes.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="css/styles_formularios.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
        .dropdown-content li>a, .dropdown-content li>span{
            color: aliceblue !important;
        }
    </style>
</head>
<body>

<?php include 'navbar2.php'; ?>

<br><br><br>

<section class="container" style="margin-top: 5rem">
    <div class="row">
        <h3 class="center-align">AGREGAR USUARIOS</h3>
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
                    <i class="material-icons prefix">mode_edit</i>
                    <label for="pass">Contraseña</label>
                    <input type="password" name="pass" required>
                </div>

                <div class="input-field col s12">
                    <select name="rol" required>
                        <?php
                        $pdo = (new Connection())->getPdo();
                        $stmt = $pdo->prepare("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'usuario' AND COLUMN_NAME = 'rol'");
                        $stmt->execute();
                        $enumString = $stmt->fetchColumn();
                        $enumString = str_replace("enum('", "", $enumString);
                        $enumString = str_replace("')", "", $enumString);
                        $valuesArray = explode("','", $enumString);

                        echo '<option value="" disabled selected>Selecciona el rol</option>';
                        foreach ($valuesArray as $value) {
                            echo "<option value='$value'>$value</option>";
                        }
                        ?>
                    </select>
                    <label>Selecciona el rol</label>
                </div>

                <br></br><br><br><br><br>
                <p class="center-align">
                    <button class="waves-effect waves-light btn grey darken-3" type="submit" name="submit">
                        <i class="material-icons right">add</i>crear
                    </button>
                </p>
            </form>
        </article>
    </div>
</section>

<?php
if (isset($_POST["submit"])) {
    try {
        $pdo = (new Connection())->getPdo();
        
        $nombre = htmlspecialchars($_POST["nombre"]);
        $pass = password_hash(htmlspecialchars($_POST["pass"]), PASSWORD_BCRYPT); // Encriptar contraseña
        $rol = htmlspecialchars($_POST["rol"]);

        $stmt = $pdo->prepare("INSERT INTO usuario (nombre, pass, rol) VALUES (:nombre, :pass, :rol)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':pass', $pass);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo '<script>window.location.href = "componentes.php";</script>';
            exit();
        } else {
            echo "<script>alert('Error en el registro');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error consulta: " . $e->getMessage() . "');</script>";
    }
}
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>



</body>
</html>
