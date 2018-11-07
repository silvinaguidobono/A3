CREATE SCHEMA IF NOT EXISTS `sguidobono_todo` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `sguidobono_todo` ;

-- -----------------------------------------------------
-- Create Table usuarios
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios(
id int unsigned not null auto_increment,
email varchar(100) COLLATE utf8_general_ci not null,
clave varchar(255) COLLATE utf8_general_ci not null,
nombre varchar(50) COLLATE utf8_general_ci not null,
apellidos varchar(100) COLLATE utf8_general_ci not null,
fecha_creado datetime not null,
fecha_act datetime default null,
primary key(id),
UNIQUE INDEX email_unique (email ASC))
ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci;

-- -----------------------------------------------------
-- Create Table tareas
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS tareas(
id int unsigned not null auto_increment,
id_usuario int unsigned not null,
titulo varchar(100) COLLATE utf8_general_ci not null,
descripcion text COLLATE utf8_general_ci not null,
estado tinyint(1) not null,
fecha_creado datetime not null,
fecha_act datetime default null,
primary key(id),
INDEX fk_UsuarioTarea (id_usuario ASC),
CONSTRAINT FK_UsuarioTarea
FOREIGN KEY (id_usuario) REFERENCES usuarios(id))
ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci;