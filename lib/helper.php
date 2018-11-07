<?php

function escribe_cabecera ($titulo="Tareas a Hacer"){
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Tareas a hacer</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <?php
        if (isset($_SESSION['mensaje'])){ ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje']; ?>">
        <?php 
            echo $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);
        ?>
        </div>
        <?php        
            }
        ?>
        <div class="container">
        <h1><?= $titulo; ?></h1>
        
    <?php
}

function escribe_pie(){
    ?>
    <p>
    <a href="cerrar_sesion.php" class="btn btn-danger btn-md">Cerrar sesión</a> 
    <a href="cerrar_usuario.php" class="btn btn-danger btn-md">Acceder con otro usuario</a>
    </p>
    </div>
    </body>
    </html>
    <?php
}

/**
 * conecta_bd
 * @param array $config
 * @return int conexion
 */
function conecta_bd($config){
    return mysqli_connect($config['host'], $config['user'], $config['password'], $config['db']);
}

function abrir_conexion(){
// Funcion para abrir conexión con la base de datos
    $conf=(array)json_decode(file_get_contents('lib/config.json'));
    if(is_array($conf)&&!empty($conf)){
        $conn= conecta_bd($conf);
        if (!$conn){
            echo "Error: ".mysqli_connect_error();
            exit;
        }
    }else{
        echo "Error en el archivo de configuración de la base de datos";
        exit;
    }
    return $conn;
}

function validar_usuario($email,$clave){
// Función para validar usuario contra la base de datos
// Recibe email y clave para validar
// Retorna un array con los datos del usuario rescatados de la base de datos
// Retorna un array vacio si no encuentra el registro en la tabla
    $conn= abrir_conexion();
    // Busco usuario y contraseña en la base de datos 
    // Preparo la sentencia con comodin ?
    $sql="SELECT id,clave,nombre,apellidos FROM usuarios WHERE email=?";
    // preparo la consulta
    $stmt=mysqli_prepare($conn, $sql);
    // indico los datos a reemplazar con su tipo
    mysqli_stmt_bind_param($stmt, "s", $email);
    // ejecuto la consulta
    mysqli_stmt_execute($stmt);
    // asoscio los nombres de campos a nombres de variables
    mysqli_stmt_bind_result($stmt,$id,$clave_codificada,$nombre,$apellidos);
    // capturo los resultados y los guardo en un array
    $row=array();
    while (mysqli_stmt_fetch($stmt)){
        // verifico si la clave ingresada es igual a la clave de bd
        $iguales= password_verify($clave, $clave_codificada);
        if ($iguales){
        // genera un array con un elemento que es el registro de la tabla 
        // que cumple la condición, en formato array asociativo
            $row[]=array(
                'id'=>$id,
                'email'=>$email,
                'clave'=>$clave_codificada, 
                'nombre'=>$nombre,
                'apellidos'=>$apellidos
            );    
        }
    }
    // cierro la conexión con la base de datos
    mysqli_close($conn);
    return $row;
}

function imprime_errores($array_errores){
// Función para imprimir errores de validación en formularios
    foreach ($array_errores as $error) {
        echo $error."<br>";
    }
}

function go_to($url){
    header('Location:'.$url);
}

function guardar_cookies($email,$clave){
// Función que guarda correo electrónico y contraseña del usuario en Cookie
    
    // Cookie Producción con ruta
    setcookie('email',$email, time()+1800,"/A3");
    setcookie('clave', $clave, time()+1800,"/A3");
    
    // Cookie desarrollo
    //setcookie('email',$email, time()+1800);
    //setcookie('clave', $clave, time()+1800);
    
    // almaceno fecha y hora de último acceso a la página ppal
    $fecha=date('Y-m-d');
    $hora=date("H:i:s");
    
    // Cookie Producción con ruta
    setcookie('fecha', $fecha, time()+1800,"/A3");  
    setcookie('hora', $hora, time()+1800,"/A3");  
    
    // Cookie desarrollo
    //setcookie('fecha', $fecha, time()+1800);  
    //setcookie('hora', $hora, time()+1800);  

}

function eliminar_cookies(){
// Función que elimina las cookies para permitir el ingreso con otro usuario

    // Cookie producción con ruta
    if (isset($_COOKIE['email'])){
        setcookie('email',"", time()-1800,"/A3");
        //setcookie('email',"", time()-1800);
    }
    if (isset($_COOKIE['clave'])){
        setcookie('clave',"", time()-1800,"/A3");
        //setcookie('clave',"", time()-1800);
    }
    if (isset($_COOKIE['fecha'])){
        setcookie('fecha',"", time()-1800,"/A3");
        //setcookie('fecha',"", time()-1800);
    }
    if (isset($_COOKIE['hora'])){
        setcookie('hora',"", time()-1800,"/A3");
        //setcookie('hora',"", time()-1800);
    }
}