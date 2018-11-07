<?php
// Página para insertar una nueva tarea del usuario logeado
// Valido los datos de la tarea ingresados en el formulario
// Inserto el registro en la tabla de tareas

    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    $titulo="Nueva tarea";
    escribe_cabecera($titulo);
    
    if(isset($_SESSION['id'])){
        $id_usuario=$_SESSION['id'];
    }else{
        $id_usuario=0;
    }
    
    // Si recibo datos del formulario de nueva tarea
    if ($_POST){
        // Valido la tarea ingresada en el formulario
        $errores=array(); // inicializo vector de errores

        if(isset($_POST['titulo']) && !empty($_POST['titulo'])){
            $titulo= htmlspecialchars($_POST['titulo']);
            $long_titulo=strlen($titulo);
            if($long_titulo < 5){
                $errores['titulo']="La longitud del titulo debe ser mayor o igual a 5";
            }
            if($long_titulo > 40){
                $errores['titulo']="La longitud del titulo debe ser menor o igual a 40";
            }
        }else{
            $errores['titulo']="Debe ingresar el titulo de la tarea";
        }
        if(isset($_POST['descripcion']) && !empty($_POST['descripcion'])){
            $descripcion= htmlspecialchars($_POST['descripcion']);
            if(strlen($descripcion) < 5){
                $errores['descripcion']="La longitud de la descripción debe ser mayor o igual a 5";
            }
        }else{
            $errores['descripcion']="Debe ingresar la descripción de la tarea";
        }
        
        // Si no hay errores de validación
        if (count($errores)==0){
            // Abro conexión con la base de datos
            $conn= abrir_conexion();
            // preparo la sentencia para insertar registro en la tabla tareas
            $sql="INSERT INTO tareas(id_usuario,titulo,descripcion,estado,fecha_creado)
                    VALUES(?,?,?,?,?)";
            // preparo los datos a insertar
            $estado=0;
            $fecha_actual=date('Y-m-d H:i:s');
            // preparo la inserción
            $stmt= mysqli_prepare($conn, $sql);
            // ligamos parametros mysqli ?,?,? con variables php
            mysqli_stmt_bind_param($stmt, "issis", $id_usuario, $titulo, $descripcion, $estado, $fecha_actual);
            // ejecutar sentencia
            mysqli_stmt_execute($stmt);
            // Si no hubo error de inserción
            if (mysqli_stmt_errno($stmt)==0){ 
                $_SESSION['mensaje']="Tarea insertada correctamente";
                $_SESSION['tipo_mensaje']="success";
            }else{
                $_SESSION['mensaje']="No se pudo insertar la tarea";
                $_SESSION['tipo_mensaje']="danger";
            }
            //cierro la sentencia preparada
            mysqli_stmt_close($stmt);
            // cierro la conexión con la base de datos
            mysqli_close($conn);
            // va a mostrar el listado de tareas del usuario
            header("Location: tareas.php");
        }
    }
?>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" id="form-tareas">
            <p>
                <label for="titulo">Título</label>
                <br>
                <input type="text" placeholder="Ingrese el titulo" name="titulo"
                       size="50"
                        value="<?php 
                               if (isset($_POST['titulo'])){
                                   echo $_POST['titulo'];
                               }
                               ?>"/>
                <?php 
                if (isset($errores['titulo'])){
                    echo '<span class="text-danger">'.$errores['titulo'].'</span>';
                } ?>
            </p>
            <p>
                <label for="descripcion">Descripción</label>
                <br>
                <textarea name="descripcion" form="form-tareas"
                      placeholder="Ingrese la descripción" rows="4" cols="50"><?php 
                if (isset($_POST['descripcion'])){
                    echo $_POST['descripcion'];
                }
                ?></textarea>
                <?php 
                if (isset($errores['descripcion'])){
                    echo '<span class="text-danger">'.$errores['descripcion'].'</span>';
                } ?>
            </p>
            <input type="submit" name="agregar" value="Agregar tarea"/>
        </form>
        <br>
        <hr>
        <p><a href="tareas.php" class="btn btn-info btn-md">Listado de tareas</a></p>
    <?php
        escribe_pie();