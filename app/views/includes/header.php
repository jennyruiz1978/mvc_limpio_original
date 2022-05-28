<?php
/*
session_start();

if ($_SESSION['token_control'] != 1) {
    redireccionar('/Login');
}*/
?>
<!DOCTYPE html>
<html lang="es">

    <head>
        <title><?php echo NOMBRE_SITIO; ?></title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equive="X-UA-Compatible" content="id=edge">
        <!-- Bootstrap CSS -->

        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL; ?>/public/vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL ?>/public/css/estilos1.css">
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL ?>/public/css/estilos.css">
        <!-- summernote -->
        <link href="<?php echo RUTA_URL ?>/public/summernote/summernote-bs4.css" rel="stylesheet">


        <!-- fontawesome -->
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL; ?>/public/vendor/fortawesome/font-awesome/css/all.min.css">
        <!-- estilos del proyecto -->
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL; ?>/public/css/menu.css">
        <!-- Load c3.css -->
        <link href="<?php echo RUTA_URL; ?>/public/node_modules/c3/c3.css" rel="stylesheet">
        <!-- Sidebar -->
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL ?>/public/css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL ?>/public/css/sidebarIzquierdo.css">
        <!-- Sombras -->
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL ?>/public/css/sombras.css">

    </head>

    <body>
 
                                <a class="nav-link" href="<?php echo RUTA_URL ?>/Login/vaciar" tabindex="-1" aria-disabled="true">Salir</a>
                         



