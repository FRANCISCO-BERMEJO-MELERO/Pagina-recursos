<?php
require_once('Connection.php');
    session_start();
	if(!isset($_SESSION["id_usuario"]) ){header("location:index.php");}
	else{if($_SESSION['rol']=='user'){header("location:componentes.php");}}
?>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Formulario de Contacto</title>
    <link rel="stylesheet" href="css/styles_formularios.css" >
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<link rel="stylesheet" href="sass/components/_variables.scss">
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
		<h3 class="center-align">INSERTAR MÓDULO</h3>
		<hr>
		<br>
			<article class="col s6 offset-s3">
				<form method="post" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>">
					<div class="input-field">
						<i class="material-icons prefix">perm_identity</i>
						<label for="nombre">Título</label>
						<input type="text" name="nombre"  required>
					</div>

					<div class="input-field">
						<i class="material-icons prefix">mode_edit</i>
						<label for="mensaje">Descripción</label>
						<input type="text" name="descripcion"  required>
					</div>

					<div class="input-field col s12">
                    <select name="categoria">
                        <?php
                        $pdo = (new Connection())->getPdo();
                        $stmt = $pdo->prepare("SELECT titulo, id_categoria FROM categoria");
                        $stmt->execute();
						$result = $stmt->fetchAll();
                        echo '<option value="0"></option>';
                        foreach ($result as $value) {
							if($value['id_categoria']!=0){
								echo '<option value="'.$value["id_categoria"].'">'.$value["titulo"].'</option>';
							}
                        }
                        ?>
                    </select>
                    <label>Selecciona la categoría</label>
                </div>

				<br><br><br>

                    <div class="input-field texto">
                        <span class="material-icons prefix">archive</span>						
                        <label for="file"></label>
						<input type="file" multiple name="file[]" >
					</div>
					<br></br>
					<p class="center-align">
						<button class="waves-effect  btn grey darken-3" type="submit" name="submit"><i class="material-icons right">send</i>enviar</button>
					</p>

				</form>

			</article>
		</div>
	</section>

	<?php
		if(isset($_POST["submit"])){
			try {
				$pdo = (new Connection())->getPdo();

				$nombre=htmlspecialchars($_POST["nombre"]);
				$dia = date("Y-m-d");
				$hora = date("H-i-s");
				$descripcion = htmlspecialchars($_POST["descripcion"]);
				$nombre_archivo = $_FILES['file']['name'];
				$categoria = htmlspecialchars($_POST["categoria"]);


				$id = $_SESSION['id_usuario'];
				
				$nombres_archivos = '';
				if (isset($_FILES['file'])) {
					$files = $_FILES['file'];
					foreach ($files['name'] as $key => $name) {
						$tmp_name = $files['tmp_name'][$key];
						$nombre_final = "$dia-$hora-$name";
						if (is_uploaded_file($tmp_name)) {
							move_uploaded_file($tmp_name, 'recursos/' . $nombre_final);
							$nombres_archivos .= "$nombre_final;";
						} else {
							echo "<script>alert('Error en la subida')</script>";
						}
					}
					$nombres_archivos = substr($nombres_archivos, 0, -1);
				}



				$stmt = $pdo->prepare("INSERT INTO componentes (titulo,descripcion,dia,hora,id_usuario,archivo, categoria) VALUES (:titulo,:descripcion,CURDATE(),CURTIME(),:id,:nombre_final,:categoria)");
				$stmt->bindParam(':titulo', $nombre);
				$stmt->bindParam(':descripcion', $descripcion);
				$stmt->bindParam(':id', $id);
				$stmt->bindParam(':nombre_final', $nombres_archivos);
				$stmt->bindParam(':categoria', $categoria);
				$stmt->execute();




				if ($stmt->rowCount() < 0 ) {
					echo"Error";
				}
			
                echo '<script>window.location.href = "componentes.php";</script>';
			
			} catch (PDOException $e) {
				header('Content-Type: application/json');
				echo"Error consulta". $e->getMessage();
				// echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
			}
		}
	?>


	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="js/desplegable.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>
	<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems);
    });
</script>
</body>
</html>