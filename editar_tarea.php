<?php
// Página para editar la tarea del usuario
// En el formulario muestro los datos de la tarea guardados en la base de datos
// Valido los datos modificados
// Actualizo la tarea en la base de datos con los nuevos datos

    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    $titulo="Editar tarea";
    escribe_cabecera($titulo);
 
    if(!$_POST){ 
        // muestro formulario con tarea a modificar rescatada de base de datos
        if (isset($_GET['id_tarea'])){
            $id_tarea = $_GET['id_tarea'];
        }else{
            echo "No recibo identificación de la tarea";
            exit;
            //$_SESSION['mensaje']="No recibo identificación de la tarea";
            //$_SESSION['tipo_mensaje']="danger";
            //header("Location: tareas.php");
        }
        // Abro conexión con la base de datos
        $conn= abrir_conexion();
        // Rescato los datos a mostrar en el formulario de la base de datos
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
            if ($estado==1) {
                $_SESSION['mensaje']="No puede editar una tarea finalizada";
                $_SESSION['tipo_mensaje']="danger";
                header("Location: tareas.php");
            }
        }
        
    }else{ // el formulario ha sido modificado
        // validar los datos ingresados en el formulario
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
        if(isset($_POST['estado']) && !empty($_POST['estado'])){
            $estado= $_POST['estado'];
            if ($estado <> "Pendiente" && $estado <> "Finalizada"){
                $errores['estado']="Debe ingresar un estado válido";
            }
        }else{
            $errores['estado']="Debe ingresar el estado de la tarea";
        }
        if(isset($_POST['id_tarea']) && !empty($_POST['id_tarea'])){
            $id_tarea= $_POST['id_tarea'];
        }else{
            $_SESSION['mensaje']="No recibo identificación de la tarea";
            $_SESSION['tipo_mensaje']="danger";
            header("Location: tareas.php");
        }
        
        if (empty($errores)){ // Si no hay errores de validación
            // Abro conexión con la base de datos
            $conn= abrir_conexion();
            
            $fecha_actual=date('Y-m-d H:i:s');
            if ($estado=="Pendiente"){
                $estado_act=0;
            }elseif($estado=="Finalizada"){
                $estado_act=1;
            }
            
            // modificar registro en la tabla tareas
            $sql="UPDATE tareas SET titulo=?,descripcion=?,estado=?,fecha_act=? WHERE id=?";
            $stmt= mysqli_prepare($conn, $sql);
            // ligamos parametros mysqli ?,?,? con variables php
            mysqli_stmt_bind_param($stmt, "ssisi", $titulo, $descripcion, $estado_act, $fecha_actual, $id_tarea);
            // ejecutar sentencia
            mysqli_stmt_execute($stmt);
            
            // Si no hubo error en el update
            if (mysqli_stmt_errno($stmt)==0){ 
                $_SESSION['mensaje']="Tarea modificada correctamente";
                $_SESSION['tipo_mensaje']="success";
            }else{
                $_SESSION['mensaje']="No se pudo modificar la tarea";
                $_SESSION['tipo_mensaje']="danger";
                //echo "No se pudo modificar la tarea".mysqli_error($conn)."<br>";
            }
            
            // cierro la conexión con la base de datos
            mysqli_close($conn);
        
            header("Location: tareas.php");
        }
    }
    
?>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" id="form-tareas">
            <p>
                <label for="titulo">Título</label>
                <br>
                <input type="text" placeholder="Ingrese el titulo" name="titulo"
                       value="<?php 
                       if($_GET){
                           echo $row['0']['titulo'];
                       }
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
                       if($_GET){
                           echo $row['0']['descripcion'];
                       }
                       if (isset($_POST['descripcion'])){
                           echo $_POST['descripcion'];
                       }
                       ?></textarea>
                <?php 
                if (isset($errores['descripcion'])){
                    echo '<span class="text-danger">'.$errores['descripcion'].'</span>';
                } ?>
            </p>
            <p>
                <label for="estado">Estado</label>
                <br>
                <input type="radio" name="estado" 
                        <?php 
                        if($_GET){
                            if ($row['0']['estado']==0){
                                echo "checked";
                            }
                        }
                        if (isset($_POST['estado']) && $_POST['estado']=="Pendiente"){
                            echo "checked";
                        } 
                       ?>
                       value="Pendiente"/> Pendiente
                <br>
                <input type="radio" name="estado" 
                        <?php 
                        if($_GET){
                            if ($row['0']['estado']==1){
                                echo "checked";
                            }
                        }
                        if (isset($_POST['estado']) && $_POST['estado']=="Finalizada"){
                            echo "checked";
                        } 
                       ?>
                       value="Finalizada"/> Finalizada
                <?php 
                if (isset($errores['estado'])){
                    echo '<span class="text-danger">'.$errores['estado'].'</span>';
                } 
                ?>
            </p>
            
            <input type="hidden" name="id_tarea" value="<?= $id_tarea; ?>'" />
            
            <input type="submit" name="modificar" value="Modificar tarea"/>
            
        </form>
        <br>
        <hr>
        <p><a href="tareas.php" class="btn btn-info btn-md">Listado de tareas</a></p>
            <?php
        escribe_pie();