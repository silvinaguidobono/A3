<?php
// Página de registro de usuarios
// Valida datos ingresados antes de registrar el usuario en la base de datos
// Si datos válidos, alta del usuario en la base de datos
//
    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    $titulo="Registro de usuarios";
    escribe_cabecera($titulo);

    // Si recibo datos del formulario de registro de usuarios
    if ($_POST){
        // Validaciones para los datos ingresados
        $errores=array(); // inicializo vector de errores

        if(isset($_POST['nombre']) && !empty($_POST['nombre'])){
            $nombre= htmlspecialchars($_POST['nombre']);
            if(strlen($nombre) > 50){
                $errores['nombre']="La longitud del nombre debe ser menor a 50";
            }
        }else{
            $errores['nombre']="Debe ingresar el nombre";
        }
        if(isset($_POST['apellidos']) && !empty($_POST['apellidos'])){
            $apellidos= htmlspecialchars($_POST['apellidos']);
            if(strlen($apellidos) > 100){
                $errores['apellidos']="La longitud de apellidos debe ser menor a 100";
            }
        }else{
            $errores['apellidos']="Debe ingresar sus apellidos";
        }
        if (isset($_POST['email']) && !empty($_POST['email'])){ 
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $email=$_POST['email'];
            }else{
                $errores['email']="El correo electrónico es inválido";
            }
        }else{
            $errores['email']="Debe ingresar un correo electrónico";
        }
        if(isset($_POST['clave']) && !empty($_POST['clave'])){
            // encripto la clave antes de guardar
            $clave= password_hash($_POST['clave'], PASSWORD_DEFAULT);
        }else{
            $errores['clave']="Debe ingresar la contraseña";
        }
        // Si los datos ingresados son válidos        
        if (count($errores)==0){
            // Abro conexión con la base de datos
            $conn=abrir_conexion();
            
            $fecha_actual=date('Y-m-d H:i:s');
            // inserto registro en la tabla usuarios
            $sql="INSERT INTO usuarios(email,clave,nombre,apellidos,fecha_creado)
                    VALUES(?,?,?,?,?)";
            $stmt= mysqli_prepare($conn, $sql);
            // ligamos parametros mysqli ?,?,? con variables php
            mysqli_stmt_bind_param($stmt, "sssss", $email, $clave, $nombre, $apellidos, $fecha_actual);
            // ejecutar sentencia
            mysqli_stmt_execute($stmt);
            // Si no hubo error de inserción, envío mensaje de éxito
            if (mysqli_stmt_errno($stmt)==0){ 
                $_SESSION['mensaje']="Usuario registrado";
                $_SESSION['tipo_mensaje']="success";
            }else{
                $_SESSION['mensaje']="No se pudo insertar el usuario";
                $_SESSION['tipo_mensaje']="danger";
                //echo "No se pudo insertar el usuario".mysqli_error($conn)."<br>";
            }
            //cierro la sentencia preparada
            mysqli_stmt_close($stmt);
            // cierro la conexión con la base de datos
            mysqli_close($conn);

            header("Location: index.php");
        }    
    }
    
?>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <p>
                <label for="nombre">Nombre</label>
                <br>
                <input type="text" placeholder="Ingrese su nombre" name="nombre" 
                       size="50"
                        value="<?php 
                               if (isset($_POST['nombre'])){
                                   echo $_POST['nombre'];
                               }
                               ?>"/>
                <?php 
                if (isset($errores['nombre'])){
                    echo '<span class="text-danger">'.$errores['nombre'].'</span>';
                } ?>
            </p>
            <p>
                <label for="apellidos">Apellidos</label>
                <br>
                <input type="text" placeholder="Ingrese sus apellidos" name="apellidos"
                       size="50"
                        value="<?php 
                               if (isset($_POST['apellidos'])){
                                   echo $_POST['apellidos'];
                               }
                               ?>"/>
                <?php 
                if (isset($errores['apellidos'])){
                    echo '<span class="text-danger">'.$errores['apellidos'].'</span>';
                } ?>
            </p>
            <p>
                <label for="email">Correo electrónico</label>
                <br>
                <input type="email" placeholder="Ingrese su correo" name="email"
                       size="50"
                        value="<?php 
                               if (isset($_POST['email'])){
                                   echo $_POST['email'];
                               }
                               ?>"/>
                <?php 
                if (isset($errores['email'])){
                    echo '<span class="text-danger">'.$errores['email'].'</span>';
                } ?>    
            </p>
            <p>
                <label for="clave">Contraseña</label>
                <br>
                <input type="password" placeholder="Ingrese su contraseña" name="clave"
                       size="50"/>
                <?php 
                if (isset($errores['clave'])){
                    echo '<span class="text-danger">'.$errores['clave'].'</span>';
                } ?>
            </p>
            
            <input type="submit" name="enviar" value="Registrar usuario"/>
            
        </form>
        <br>
        <hr>
        <p><a href="index.php" class="btn btn-primary btn-md">Volver</a></p>
        </div>
    </body>
</html>
