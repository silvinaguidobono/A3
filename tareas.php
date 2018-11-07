<?php
// Página principal de la aplicación con listado de tareas del usuario 
// Permite ir a agregar, ver, editar y eliminar tarea

    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    if(isset($_SESSION['id'])){
        $id_usuario=$_SESSION['id'];
    }else{
        $id_usuario=0;
    }
    if(isset($_SESSION['nombre'])){
        $nombre=$_SESSION['nombre'];
    }else{
        $nombre="";
    }
    if(isset($_SESSION['apellidos'])){
        $apellidos=$_SESSION['apellidos'];
    }else{
        $apellidos="";
    }
    
    $titulo="Tareas del usuario: ".$nombre." ".$apellidos;
    escribe_cabecera($titulo);
    
    // Abro conexión con la base de datos
    $conn= abrir_conexion();
    
    // rescato las tareas del usuario logueado
    $sql="SELECT id,titulo,descripcion,estado,fecha_creado,fecha_act FROM tareas "
            . "WHERE id_usuario=$id_usuario ORDER BY fecha_creado DESC";
    $resultado = mysqli_query($conn, $sql);   
    if(mysqli_errno($conn)){
        die($mysqli_error($conn));
    }
    if (mysqli_num_rows($resultado) == 0) {  
        $cant_tareas=0; 
    }else{
        $array_tareas=array();
        while($fila = mysqli_fetch_assoc($resultado)){  
            $array_tareas[]=$fila;
        }
        $cant_tareas=count($array_tareas);
    }
    
    // cierro la conexión con la base de datos
    mysqli_close($conn);
    
    if ($cant_tareas==0){
        echo "<p>El usuario no tiene tareas asignadas</p>";
    }    
    else{
?>
        <div class="col-md-12">
            <div class="table-responsive">
                <p>
                    Total tareas: <span id="total"><?= $cant_tareas;?></span>
                </p>

                <br>					
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Fecha Modificación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($array_tareas as $tarea){ ?>
                        <tr data-id="id_usuario">
                            <td><?= $tarea['titulo']?></td>
                            <td>
                                <?php 
                                if($tarea['estado']==0){
                                    echo "<strong>Pendiente</strong>";
                                }elseif($tarea['estado']==1){
                                    echo "<strong>Finalizada</strong>";
                                } 
                                ?>    
                            </td>
                            <td><?= $tarea['fecha_creado']?></td>
                            <td><?= $tarea['fecha_act']?></td>
                            
                            <td class="actions">
                                <a href="ver_tarea.php?id_tarea=<?= $tarea['id']?>" class="btn btn-sm btn-info">
                                    Ver
                                </a>

                                <a href="editar_tarea.php?id_tarea=<?= $tarea['id']?>" class="btn btn-sm btn-primary">
                                    Editar
                                </a>

                                <a href="borrar_tarea.php?id_tarea=<?= $tarea['id']?>" class="btn btn-sm btn-danger btn-delete">
                                    Borrar
                                </a>
                            </td>
                        </tr>
                    <?php }  ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php }  ?>

    <hr>
    <p><a href="agregar_tarea.php" class="btn btn-primary btn-md">Nueva tarea</a></p>
    <?php
        escribe_pie();