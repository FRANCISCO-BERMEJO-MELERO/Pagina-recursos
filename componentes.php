<?php
    session_start();
	if(!isset($_SESSION["id_usuario"])){
		header("location:index.php");
	}
	require_once('Connection.php');
	?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles_formularios.css" >

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    
    <link rel="stylesheet" href="css/styles.css"> 
    <title>Recursos</title>

    <style>
        button{
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
            border-bottom: none; /* Cambia este color al que prefieras */
            outline: none !important; /* Remueve el outline predeterminado */
            box-shadow: none!important; /* Remueve el box-shadow predeterminado */
        }


    </style>

</head>
<body>
<?php include 'navbar.php'; ?>









    <br><br><br>
    <?php
    $pdo = (new Connection())->getPdo();
    $stmt = $pdo->prepare("SELECT id_categoria, icono FROM categoria ");
    $stmt->execute();

    $result = $stmt->fetchAll()
        ?>
    <!--/////////////////Contenido/////////////////////////-->
    <div class="contenedor" style="margin:0 auto">
        <div  class="grey darken-3 scroll_lateral z-depth-4" > 
                <ul class="collection">
                    <?php
                    foreach ($result as $row) {
                    if($row['id_categoria']==0){
                        echo'
                        <li><a href="componentes.php?v='.$row['id_categoria'].'"><i class="fa-solid '.$row['icono'].' fa-2xl"></i></a></li>
                        <hr>
                        ';
                    }
                    else{echo'<li><a href="componentes.php?v='.$row['id_categoria'].'"><i class="fa-brands '.$row['icono'].' fa-2xl "></i></a></li>';}}
                    ?>
                    <!-- <li><a href="componentes.php?v=0"><i class="fa-solid fa-clock-rotate-left fa-2xl"></i></a></li>
                    <hr>
                    <li><a href="componentes.php?v=1"><i class="fa-brands <?= $logo ?> fa-html5 fa-2xl "></i></a></li>
                    <li><a href="componentes.php?v=2"><i class="fa-brands fa-css3 fa-2xl"></i></a></li>
                    <li><a href="componentes.php?v=3"><i class="fa-brands fa-python fa-2xl"></i></a></li>
                    <li><button><a href="#python"><i class="fa-brands fa-js fa-2xl"></i></a></button></li> -->
                    <!-- <li><a href="#!"><i class="fa-brands fa-java fa-2xl"></i></a></li>
                    <li><a href="#angular"><i class="fa-brands fa-angular fa-2xl"></i></a></li> -->
                </ul>
        </div>
        <div class="container grey darken-3 z-depth-4 contenido" >



<?php
$pdo = (new Connection())->getPdo();
////////////////////////////////////////////////////////////    TER AR ESTA PARTE
if (isset($_GET['q'])) {
    $search_query = "%" . $_GET['q'] . "%";
    $stmt = $pdo->prepare("SELECT c.titulo, c.descripcion, u.nombre, c.archivo, c.dia, c.id_componente  FROM componentes c JOIN usuario u ON u.id = c.id_usuario WHERE c.titulo LIKE ? OR c.descripcion LIKE ?");
    $stmt->execute([$search_query, $search_query]);
    ?>
    <h2>Resultados de búsqueda</h2>
    <hr>
    <?php
            $contador = 0;
            while ($result = $stmt->fetch()) {
                if ($contador % 3 == 0) {
                    if ($contador != 0) {
                        echo '</div>'; 
                    }
                    echo '<div class="grey darken-3 contenedor_tarjetas">'; 
                }
            ?>        
            <?php
            if($result['archivo']!=''){
            ?>
            <div class="tarjetas grey darken-3 col s4">
                <span>
                    <h5><?php echo $result['titulo']; ?></h5>
                    <hr>
                    <p><?php echo $result['descripcion']; ?></p>
                    <br>
                    <h5 style="font-size:0.8rem !important; color:#757575;">Creado por: <?= $result['nombre']?></h5>
                    <h5 style="font-size:0.8rem !important; color:#757575;">Fecha: <?= $result['dia']?></h5>
                    <br>
                    <?php
                    $archivos_prueba=$result['archivo'];
                    if($archivos_prueba!=NULL ||$archivos_prueba!=''){
                        $archivos=explode(';',$archivos_prueba);
                        $num_archivos=count($archivos);
                        // for ($i=0; $i < $num_archivos ; $i++) { 
                        //     $nom_archivo=$archivos[$i];
                        //     // echo"<a class='grey darken-3 waves-effect waves-light btn' style='margin-top: 1rem' href='recursos/$nom_archivo' download >Descargar</a>";
                        //     echo"<a class='grey darken-3 waves-effect waves-light btn' style='margin-top: 1rem' href='componentes.php?d=3'>Descargar</a>";
                        // }
                        ?>
                        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                        <button class="waves-effect  btn grey darken-3" type="submit" name="submit" value ="<?=$result['id_componente']?>">Descargar <i class="fa-solid fa-download"></i></button>
                        </form>
                        <?php
                    }
                    ?>
                </span>
            </div>
            <?php
            $contador++;

            }
            ?>
            <?php
            }
            if ($contador % 3 != 0) {
                    echo '</div>';
                }
            ?>
        </div>
    <?php
}else{
    if(!isset($_REQUEST['d'])){
        
        if(isset($_REQUEST['v'])){
        $v = $_REQUEST['v'];
        if($v!=0){
            $stmt = $pdo->prepare("SELECT  c.titulo,c.descripcion,u.nombre,c.archivo,c.dia,c.id_componente FROM componentes c, usuario u WHERE c.categoria=:v and u.id=c.id_usuario ");
            $stmt->bindParam(":v", $v);
        }
        else{
            $stmt = $pdo->prepare("SELECT  c.titulo,c.descripcion,u.nombre,c.archivo,c.dia,c.id_componente FROM componentes c, usuario u WHERE  u.id=c.id_usuario and c.archivo!=''  ORDER BY id_componente DESC LIMIT 6 ");
        }
        $stmt->execute();

        $stmt2 = $pdo->prepare("SELECT  titulo,icono FROM categoria WHERE id_categoria=:v ");

        $stmt2->bindParam(":v", $v);
        $stmt2->execute();
        $result2 = $stmt2->fetch();
        
        ?>
        <h2><?=$result2['titulo']?> <i class="<?php echo ($v != 0) ? 'fa-brands' : 'fa-solid'; ?> <?=$result2['icono']?>"></i></h2>
        <hr>
            <?php
            $contador = 0;
            while ($result = $stmt->fetch()) {
                if ($contador % 3 == 0) {
                    if ($contador != 0) {
                        echo '</div>'; 
                    }
                    echo '<div class="grey darken-3 contenedor_tarjetas">'; 
                }
            ?>        
            <?php
            if($result['archivo']!=''){
            ?>
            <div class="tarjetas grey darken-3 col s4">
                <span>
                    <h5><?php echo $result['titulo']; ?></h5>
                    <hr>
                    <p><?php echo $result['descripcion']; ?></p>
                    <br>
                    <h5 style="font-size:0.8rem !important; color:#757575;">Creado por: <?= $result['nombre']?></h5>
                    <h5 style="font-size:0.8rem !important; color:#757575;">Fecha: <?= $result['dia']?></h5>
                    <br>
                    <?php
                    $archivos_prueba=$result['archivo'];
                    if($archivos_prueba!=NULL ||$archivos_prueba!=''){
                        $archivos=explode(';',$archivos_prueba);
                        $num_archivos=count($archivos);
                        // for ($i=0; $i < $num_archivos ; $i++) { 
                        //     $nom_archivo=$archivos[$i];
                        //     // echo"<a class='grey darken-3 waves-effect waves-light btn' style='margin-top: 1rem' href='recursos/$nom_archivo' download >Descargar</a>";
                        //     echo"<a class='grey darken-3 waves-effect waves-light btn' style='margin-top: 1rem' href='componentes.php?d=3'>Descargar</a>";
                        // }
                        ?>
                        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                        <button class="waves-effect  btn grey darken-3" type="submit" name="submit" value ="<?=$result['id_componente']?>">Descargar <i class="fa-solid fa-download"></i></button>
                        </form>
                        <?php
                    }
                    ?>
                </span>
            </div>
            <?php
            }
            ?>
            <?php
                $contador++;
            }
            if ($contador % 3 != 0) {
                echo '</div>';
            }
            ?>
        </div>
        <?php
        }
        else{

        $v = 0;
        $stmt = $pdo->prepare("SELECT  c.id_componente,c.titulo,c.descripcion,u.nombre,c.archivo,c.dia FROM componentes c, usuario u WHERE  u.id=c.id_usuario and c.archivo!=''  ORDER BY id_componente DESC LIMIT 6  ");
        $stmt->execute();
        
        ?>
        <h2>Más Recientes  <i class="fa-solid fa-clock-rotate-left "></i></h2>
        <hr>
        <?php
            $contador = 0;
            while ($result = $stmt->fetch()) {
                if ($contador % 3 == 0) {
                    if ($contador != 0) {
                        echo '</div>'; 
                    }
                    echo '<div class="grey darken-3 contenedor_tarjetas">'; 
                }
            ?>
            <?php
            if($result['archivo']!=''){
            ?>
            <div class="tarjetas grey darken-3 col s4">
                <span>
                    <h5><?php echo $result['titulo']; ?></h5>
                    <hr>
                    <p><?php echo $result['descripcion']; ?></p>
                    <br>
                    <h5 style="font-size:0.8rem !important; color:#757575;">Creado por: <?= $result['nombre']?></h5>
                    <h5 style="font-size:0.8rem !important; color:#757575;">Fecha: <?= $result['dia']?></h5>
                    <br>
                    <?php
                    $archivos_prueba=$result['archivo'];
                    if($archivos_prueba!=NULL ||$archivos_prueba!=''){
                        $archivos=explode(';',$archivos_prueba);
                        $num_archivos=count($archivos);
                        // for ($i=0; $i < $num_archivos ; $i++) { 
                        //     $nom_archivo=$archivos[$i];
                            ?>
                            <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                                <button class="waves-effect  btn grey darken-3" type="submit" name="submit" value ="<?=$result['id_componente']?>">Descargar  <i class="fa-solid fa-download"></i></button>
                            </form>
                            <!-- <a class='grey darken-3 waves-effect waves-light btn' style='margin-top: 1rem'href='recursos/$nom_archivo' download >Descargar</a>"; -->
                            <?php
                        // }
                    }
                    ?>
                </span>
            </div>
            <?php
            }
            ?>
            <?php
                $contador++;
            }
            if ($contador % 3 != 0) {
                echo '</div>';
            }
            ?>  
        </div>
        <?php
        }
    }
        else{
        echo'<h2>Descargar <i class="fa-solid fa-download"></i></h2>';
        echo'<hr>';
        $stmt = $pdo->prepare("SELECT  c.titulo,c.descripcion,u.nombre,c.archivo,c.dia FROM componentes c, usuario u WHERE c.id_componente=:id and u.id=c.id_usuario ");
        $stmt->bindParam(":id", $_REQUEST['d']);
        $stmt->execute();
        $result = $stmt->fetch();
        $archivos_prueba=$result['archivo'];
        if($archivos_prueba!=NULL ||$archivos_prueba!=''){
            $archivos=explode(';',$archivos_prueba);
            $num_archivos=count($archivos);
            echo'<table>';
            echo'<tr>
                <th>Nombre</th>
                <th>Visualizar</th>
                <th>Descargar</th>';
                if($_SESSION['rol']=='admin'){
                    echo'<th>Eliminar</th>';
                }
                echo'</tr>';

            for ($i=0; $i < $num_archivos ; $i++) { 
                
                    $nom_archivo=$archivos[$i];
                    echo'<tr>';
                    echo'<td>'.$nom_archivo.'</td>';
                    echo'<td><a href="recursos/'.$nom_archivo.'" class="iconos_descarga" target="_blank"><i class="fa-solid fa-eye fa-xl"></i></a></td>';
                    echo'<td><a href="recursos/'.$nom_archivo.'" download class="iconos_descarga"><i class="fa-solid fa-download fa-xl"></i></a></td>';
                    // echo"<a class='grey darken-3 waves-effect waves-light btn' style='margin-top: 1rem'href='recursos/$nom_archivo' download >Descargar</a>"; 
                    if ($_SESSION['rol'] == 'admin') {
                        echo '<td>
                                <form method="post" action="' . $_SERVER['PHP_SELF'] . '?d=' . $_GET['d'] . '" onsubmit="return confirmarEliminacion();">
                                    <input type="hidden" name="archivo_a_eliminar" value="' . $nom_archivo . '">
                                    <button type="submit" class="iconos_descarga" name="eliminar_archivo"><i class="fa-solid fa-xmark fa-xl" ></i></button>
                                </form>
                            </td>';
                    }
                    echo'</tr>';
            }
            echo'</table>';
        }
        else{
            echo"<h5>No hay archivos disponibles</h5>";
        }
    }
}


?>
        </div>
    </div>

<?php

if(isset($_POST['submit'])){
    $id=$_POST['submit'];
    echo '<script>window.location.href = "componentes.php?d=' . $id . '";</script>';
    header("location:componentes.php?d=".$id);
}

?>


<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_archivo'])) {
    $archivo_a_eliminar = $_POST['archivo_a_eliminar'];

    // Verificar que 'd' esté definido y sea un número
    if (isset($_GET['d'])) {
        $id_componente = intval($_GET['d']);

        // Obtener el registro actual de la base de datos
        $sql_select = "SELECT archivo FROM componentes WHERE id_componente = ?";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->bindParam(1, $id_componente, PDO::PARAM_INT);
        $stmt_select->execute();
        $resultado = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $registro_actual = $resultado['archivo'];

            // Convertir el registro a un array
            $archivos = explode(';', $registro_actual);

            // Eliminar el archivo del array
            $archivos = array_filter($archivos, function($archivo) use ($archivo_a_eliminar) {
                return $archivo !== $archivo_a_eliminar;
            });

            // Convertir el array de nuevo a una cadena
            $nuevo_registro = implode(';', $archivos);

            // Actualizar el registro en la base de datos
            $sql_update = "UPDATE componentes SET archivo = ? WHERE id_componente = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->bindParam(1, $nuevo_registro, PDO::PARAM_STR);
            $stmt_update->bindParam(2, $id_componente, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                // Redirigir o recargar la página para reflejar los cambios
                echo '<script>window.location.href = "componentes.php?d='.$id_componente.'";</script>';

                exit;
            } else {
                echo "Error al actualizar el registro: " . $stmt_update->errorInfo()[2];
            }
        } else {
            echo "No se encontró el registro.";
        }
    } else {
        echo "ID de componente no válido.";
    }
}
?>






<script src="https://kit.fontawesome.com/2c87fc6ff6.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.sidenav');
        var instances = M.Sidenav.init(elems);
    });

    $(document).ready(function(){
        $('.sidenav').sidenav();
    });
</script>

<script>
function confirmarEliminacion() {
    return confirm("¿Estás seguro de que deseas eliminar este archivo?");
}
</script>


</body>
</html>