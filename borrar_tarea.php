<?php
// Borra tarea de la base de datos

    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    if (isset($_GET['id_tarea'])){
        $id_tarea = $_GET['id_tarea'];
        // Abro conexión con la base de datos
        $conn= abrir_conexion();
        
        // Borra registro de la tabla tareas
        $sql="DELETE from tareas WHERE id=?";
        $stmt= mysqli_prepare($conn, $sql);
        // ligamos parametros mysqli ?,?,? con variables php
        mysqli_stmt_bind_param($stmt, "i", $id_tarea);
        // ejecutar sentencia
        mysqli_stmt_execute($stmt);

        // Si no hubo error en el delete
        if (mysqli_stmt_errno($stmt)==0){ 
            $_SESSION['mensaje']="Tarea eliminada correctamente";
            $_SESSION['tipo_mensaje']="success";
        }else{
            $_SESSION['mensaje']="No se pudo borrar la tarea";
            $_SESSION['tipo_mensaje']="danger";
        }
        //cierro la sentencia preparada
        mysqli_stmt_close($stmt);
        // cierro la conexión con la base de datos
        mysqli_close($conn);
        
        header("Location: tareas.php");
    }else{
        echo "No recibo identificación de tarea";
        exit;
    }