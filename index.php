<?php
// Página inicial de registro de usuarios y acceso para usuarios registrados
    session_start();
    ini_set('display_errors',1);
    require 'lib/helper.php';
    
    escribe_cabecera();
?>
            <h2>Aplicación para registrar y actualizar tareas</h2>
            <p>Si aún no estas registrado como usuario debes registrarte previamente 
            para acceder a la aplicación</p>
            <p>Si ya estás registrado, accede para administrar tus tareas</p>
            <br>
            <ul class="list-inline">
                <li><a href='reg.php' class="btn btn-primary btn-lg">Registro de usuarios</a></li>
                <li><a href='log.php' class="btn btn-primary btn-lg">Acceder</a></li>
            </ul>
            <br>
        </div>
    </body>
</html>