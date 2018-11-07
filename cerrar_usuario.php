<?php
// Destruye la sesión del usuario y elimina las cookies del usuario
// para permitir el acceso con otro usuario

    session_start();
    ini_set('display_errors',1);
    include 'lib/helper.php';

    // Destruyo la sesión del usuario
    session_destroy();
    
    // elimino las cookies
    eliminar_cookies();
        
    // voy al formulario para acceder con otro usuario
    header("Location: log.php");