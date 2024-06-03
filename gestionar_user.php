<?php
require_once('Connection.php');
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("location:index.php");
    exit();
} elseif ($_SESSION['rol'] != 'admin') {
    header("location:componentes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/styles_formularios.css" >
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link rel="stylesheet" href="css/styles.css" type="text/css">
<title>Gestión</title>
<style>
    .contenido {
        margin-top: 5rem !important;
    }
    button {
        background-color: transparent !important;
        border: none !important;
    }
    /* Estilos del input de búsqueda */
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

    .formulario{
        max-width:50%;
        margin: 2rem auto;
    }

    .dropdown-content li>span {
		color: rgb(220,220,220); 
		}

		.dropdown-content li>span {
		background-color: #333; 
		}

		.select-wrapper option:disabled {
		background-color: #333; 
		}

		.dropdown-content li{
		min-height: 0px !important;
		}

        .dropdown-content li>a, .dropdown-content li>span{
            color: aliceblue !important;
        }
    .columnas_opciones td{
        width: fit-content !important;
    }
</style>
</head>
<body>
<?php include 'navbar2.php'; ?>

<?php
$pdo = (new Connection())->getPdo();
try {
    $stmt = $pdo->prepare("SELECT id, nombre, pass, rol, bloqueado FROM usuario");
    $stmt->execute();
    $result = $stmt->fetchAll(); 
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, 'error_log.txt'); 
    echo "<script>alert('Algo ha salido mal. Por favor, inténtelo de nuevo más tarde.');</script>";
}
?>

<div class="container grey darken-3 z-depth-4 contenido">
    <h2>Gestión de usuarios</h2>
    <hr>
    <br>
    <?php
    if(!isset($_REQUEST['id'])){
    ?>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Rol</th>
                <th class="columnas_opciones" style="width:fit !important">Estado</th>
                <th style="width:fit !important">Modificar</th>
                <th style="width:fit !important">Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($result as $user) {
        ?>
            <tr>
                <td><?php echo $user['nombre']; ?></td>
                <td><?php echo $user['rol']; ?></td>
                <td>
                    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" onsubmit="return confirmarModificacion(<?php echo $user['bloqueado']; ?>);">
                        <input type="hidden" name="usuario_id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="estado" value="bloquear">
                        <button type="submit" class="iconos_descarga" name="bloquear_usuario" style="cursor:pointer">
                            <i class="fa-solid <?php echo $user['bloqueado'] ? 'fa-lock' : 'fa-lock-open'; ?> fa-xl"></i>
                        </button>
                    </form>
                </td>
                <td>
                    <a class="iconos_descarga" href="gestionar_user.php?id=<?php echo $user['id']; ?>">
                        <i class="fa-solid fa-pen fa-xl"></i>
                    </a>
                </td>
                <td>
                    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" onsubmit="return confirmarEliminacion();">
                        <input type="hidden" name="usuario_id" value="<?php echo $user['id']; ?>">
                        <input type="hidden" name="estado" value="eliminar">
                        <button type="submit" class="iconos_descarga" name="eliminar_usuario" style="cursor:pointer">
                            <i class="fa-solid fa-xmark fa-xl"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
    <?php
    }
    else{
        $pdo = (new Connection())->getPdo();
        $id=$_REQUEST['id'];
        $stmt = $pdo->prepare("SELECT id, nombre, pass, rol FROM usuario WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch();

        ?>
        <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="formulario" onsubmit="return confirmarEnvio();">
            <!-- Campo oculto para el ID del usuario -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($result['id']) ?>">

            <div class="input-field"> 
                <i class="material-icons prefix">perm_identity</i>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($result['nombre']) ?>" required>
            </div>
            <div class="input-field">
                <i class="material-icons prefix">lock</i>
                <label for="pass">Contraseña</label>
                <input type="text" id="pass" name="pass">
            </div>

            <div class="input-field col s12">
                <select name="rol" required>
                    <?php
                    // Consulta para obtener los valores del enum
                    $stmt = $pdo->prepare("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'usuario' AND COLUMN_NAME = 'rol'");
                    $stmt->execute();
                    $enumString = $stmt->fetchColumn();
                    $enumString = str_replace("enum('", "", $enumString);
                    $enumString = str_replace("')", "", $enumString);
                    $valuesArray = explode("','", $enumString);

                    // Generar las opciones del select
                    foreach ($valuesArray as $value) {
                        $selected = ($value == $result['rol']) ? 'selected' : '';
                        echo "<option value='$value' $selected>$value</option>";
                    }
                    ?>
                </select>
                <label>Selecciona el rol</label>
            </div>
            <br><br>
            <p class="center-align">
                <button class="waves-effect waves-light btn grey darken-3" type="submit" name="submit"><i class="material-icons right">mode_edit</i>Editar</button>
            </p>
        </form>
        <?php
    }
    ?>
</div>

<?php
    if(isset($_POST['bloquear_usuario']) or isset($_POST['eliminar_usuario']) ){
        $usuario_id = $_POST['usuario_id'];
        $estado = $_POST['estado'];
        $pdo = (new Connection())->getPdo();
        if($estado=='eliminar'){
            $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = :id");
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            echo '<script>window.location.href = "gestionar_user.php";</script>';
            exit();
        }
            $stmt = $pdo->prepare("UPDATE usuario SET bloqueado = NOT bloqueado WHERE id = :id");
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
        echo '<script>window.location.href = "gestionar_user.php";</script>';
    }
?>




<?php
if (isset($_POST["submit"])) {
    try {
        // Asume que la clase Connection está definida correctamente y devuelve un PDO.
        $pdo = (new Connection())->getPdo();

        // Sanitizar y obtener los datos del formulario
        $nombre = htmlspecialchars($_POST["nombre"]);
        $pass = password_hash(htmlspecialchars($_POST["pass"]), PASSWORD_BCRYPT); // Encriptar contraseña
        $rol = htmlspecialchars($_POST["rol"]);
        $usuario_id = $_POST["id"];  // Asegúrate de obtener el ID del usuario de alguna manera

        // Preparar la consulta SQL
        if ($pass != '') {
            $stmt = $pdo->prepare("UPDATE usuario SET nombre = :nombre, pass = :pass, rol = :rol WHERE id = :id");
            $stmt->bindParam(':pass', $pass);
        } else {
            $stmt = $pdo->prepare("UPDATE usuario SET nombre = :nombre, rol = :rol WHERE id = :id");
        }
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':id', $usuario_id);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            echo'ha salido bien';
        } else {
            echo "<script>alert('Error: No se actualizó ninguna fila.');</script>";
        }
        
        // Redirigir después de la operación
        echo '<script>window.location.href = "gestionar_user.php";</script>';
    
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
    }
}
?>





<script>
function confirmarModificacion(estado) {
    var mensaje = estado == 1 ? "¿Estás seguro de que deseas desbloquear este usuario?" : "¿Estás seguro de que deseas bloquear este usuario?";
    return confirm(mensaje);
}

function confirmarEliminacion() {
    return confirm("¿Estás seguro de que deseas eliminar este usuario?");
}

function confirmarEnvio() {
    return confirm("¿Estás seguro de que deseas guardar los cambios realizados a este usuario?");
}
</script>
<script src="https://kit.fontawesome.com/2c87fc6ff6.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>  

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/desplegable.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>


	<script>
    document.addEventListener('DOMContentLoaded', function() {
      var elems = document.querySelectorAll('select');
      var instances = M.FormSelect.init(elems);
    });
  </script>
</body>
</html>
