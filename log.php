<?php
// Página de login con formulario de acceso para usuario registrado
// El formulario da la opción de recordar el usuario en este equipo
// Si marca esta opción, se guardará una cookie con el email del usuario
// y su contraseña para no solicitarlo en futuras visitas de ese usuario.
// Validación de los datos ingresados
// Si el usuario existe en la base de datos, permite el acceso a la app
    
    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    // Si no existe la cookie, muestro el formulario de acceso
    $titulo="Iniciar sesión";
    escribe_cabecera($titulo);

    // Si recibo datos del formulario de login
    if ($_POST){
        // Valido los datos ingresados
        $errores=array(); // inicializo vector de errores
        
        // Valido que haya ingresado un correo válido y una contraseña
        if(isset($_POST['email']) && !empty($_POST['email'])){
            if (isset($_COOKIE['email']) && $_COOKIE['email']!=$_POST['email']) {
                $errores['email']="Debe iniciar sesión y pedir acceso con otro usuario";
            }else{
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                    $email=$_POST['email'];
                }else{
                    $errores['email']="El correo electrónico es inválido";
                }    
            }
            
        }else{
            $errores['email']="Debe ingresar su correo electrónico";
        }
        
        if(isset($_POST['clave']) && !empty($_POST['clave'])){
            $clave=$_POST['clave'];
        }else{
            $errores['clave']="Debe ingresar la contraseña";
        }
        // Si los datos ingresados son válidos
        if (count($errores)==0){
            // Busco el correo electrónico y contraseña ingresados en la bd
            // y rescato sus datos (id, nombre, apellidos)
            $row=validar_usuario($email, $clave);
            
            // Si existe un correo con esa contraseña en la base de datos
            if (count($row)>0){  
                // si marcó la opción de recordar usuario
                if (isset($_POST['recordar']) && $_POST['recordar']=="Si"){
                    guardar_cookies($email, $clave);
                }
                // guardo los datos del usuario en las variables de sesión
                $_SESSION['id']=$row[0]['id'];
                $_SESSION['email']=$row[0]['email'];
                $_SESSION['clave']=$row[0]['clave'];
                $_SESSION['nombre']=$row[0]['nombre'];
                $_SESSION['apellidos']=$row[0]['apellidos'];
                
                // Muestro el listado de sus tareas
                header("Location: tareas.php");
                
            }else{
                // No recibo datos de la base de datos
                $errores['clave']="Contraseña o usuario inválidos";
            }
        }
    }else{
        // Si hay un usuario logeado voy a mostrar sus tareas
        if (isset($_SESSION['id'])){
            header("Location: tareas.php");
        }
    }
 

?>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <p>
                <label for="email">Correo electrónico</label>
                <br>
                <input type="email" placeholder="Ingrese su correo" name="email"
                       size="50"
                        value="<?php 
                            if (isset($_COOKIE['email'])){
                                echo $_COOKIE['email'];
                            }else{
                                if (isset($_POST['email'])){
                                    echo $_POST['email'];
                                }    
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
                <input type="password" placeholder="Ingrese su contraseña" 
                       name="clave" size="50"
                       value="<?php 
                            if (isset($_COOKIE['clave'])){
                                echo $_COOKIE['clave'];
                            } 
                               ?>"/>
                <?php 
                if (isset($errores['clave'])){
                    echo '<span class="text-danger">'.$errores['clave'].'</span>';
                } ?>
            </p>
            <p>
                <input type="checkbox" name="recordar" 
                       value="Si" />Recuérdame en este equipo
            </p>
                
            <input type="submit" name="iniciar" value="Iniciar sesión"/>
            
        </form>
        <br>
        <hr>
        <p><a href="index.php" class="btn btn-primary btn-md">Volver</a></p>
        </div>
    </body>
</html>