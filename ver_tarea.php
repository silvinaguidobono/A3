<?php
// Página que muestra los datos de la tarea del usuario logeado
// Puedo ir a editar la tarea si quiero modificarla

    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    escribe_cabecera();
    
    if (isset($_GET['id_tarea'])){
        $id_tarea = $_GET['id_tarea'];
    }else{
        echo "No recibo identificación de tarea";
        exit;
    }
    // Abro conexión con la base de datos
    $conn= abrir_conexion();
    // rescato la tarea a mostrar
    $sql="SELECT id_usuario,titulo,descripcion,estado,fecha_creado,fecha_act FROM tareas WHERE id=?";
    // preparo la consulta
    $stmt=mysqli_prepare($conn, $sql);
    // indico los datos a reemplazar con su tipo
    mysqli_stmt_bind_param($stmt, "i", $id_tarea);
    // ejecuto la consulta
    mysqli_stmt_execute($stmt);
    // asoscio los nombres de campos a nombres de variables
    mysqli_stmt_bind_result($stmt,$id_usuario,$titulo,$descripcion,$estado,$fecha_creado,$fecha_act);
    // capturo los resultados y los guardo en un array
    $row=array();
    while (mysqli_stmt_fetch($stmt)){
            // genera un array con un elemento que es el registro de la tabla 
            // que cumple la condición, en formato array asociativo
            $row[]=array(
                'id_usuario'=>$id_usuario,
                'titulo'=>$titulo,
                'descripcion'=>$descripcion, 
                'estado'=>$estado,
                'fecha_creado'=>$fecha_creado,
                'fecha_act'=>$fecha_act
            );    
        
    }
    // cierro la conexión con la base de datos
    mysqli_close($conn);
    
    // Si no encuentro la tarea en la base de datos
    if (count($row)==0){  
        $_SESSION['mensaje']="Tarea no encontrada";
        $_SESSION['tipo_mensaje']="danger";
        header("Location: tareas.php");
    }else{
        // controlo que la tarea sea del usuario logeado
        if ($id_usuario<>$_SESSION['id']){
            $_SESSION['mensaje']="La tarea no pertenece al usuario logeado";
            $_SESSION['tipo_mensaje']="danger";
            header("Location: tareas.php");
        }
    }
?>
	<div class="well">

	    <h2><?= $row['0']['titulo']; ?></h2>
	        <br>
	        <dl>
	            <dt>Descripción</dt>
	            <dd>
	                <?= $row['0']['descripcion']; ?>
	                &nbsp;
	            </dd>
	            <br>

	            <dt>Estado</dt>
	            <dd>
	                <?php
                        if ($row['0']['estado']==0){
                            echo "Pendiente";
                        }elseif($row['0']['estado']==1){
                            echo "Finalizada";
                        }
                        ?>
	                &nbsp;
	            </dd>
	            <br>

	            <dt>Fecha de creación</dt>
	            <dd>
	                <?= $row['0']['fecha_creado']; ?>
	                &nbsp;
	            </dd>
	            <br>
                    <?php if(!is_null($row['0']['fecha_act'])){ ?>
	            <dt>Fecha de modificación</dt>
	            <dd>
	                <?= $row['0']['fecha_act']; ?>
	                &nbsp;
	            </dd>
	            <br>
                    <?php } ?>
	        </dl>
                <br>
                <hr>
                <p>
                <a href="editar_tarea.php?id_tarea=<?= $id_tarea; ?>" class="btn btn-primary btn-md">
                    Editar tarea
                </a>
                </p>
                <p><a href="tareas.php" class="btn btn-info btn-md">Listado de tareas</a></p>
        </div>
    <?php
        escribe_pie();