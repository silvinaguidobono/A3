<?php
// Destruye la sesión del usuario
// Va al formulario de acceso a la aplicación

    session_start();
    
    // Destruye la sesión del usuario
    session_destroy();
    
    header("Location: log.php");