
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
        <style>
            html,body {
                height: 100%;
            }

            body {
                display: -ms-flexbox;
                display: -webkit-box;
                display: flex;
                -ms-flex-align: center;
                -ms-flex-pack: center;
                -webkit-box-align: center;
                align-items: center;
                -webkit-box-pack: center;
                justify-content: center;
                padding-top: 40px;
                padding-bottom: 40px;
                /* background-color: #f5f5f5; */
            }

            .form-signin {
                width: 100%;
                max-width: 20em;
                padding-left: 2em;
                padding-left: 2em;
                margin: 0 auto;
            }
            .form-signin .checkbox {
                font-weight: 400;
            }
            .form-signin .form-control{
                position: relative;
                box-sizing: border-box;
                height: auto;
                padding: 10px;
                font-size: 16px;
            }
            .form-signin .form-control:focus {
                z-index: 2;
            }
            .form-signin input[type="email"] {
                margin-bottom: -1px;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
            }
            .form-signin input[type="password"] {
                margin-bottom: 10px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }

            /*
                        img {
                            position: absolute;
                            left: 50%;
                            top: 50%;
                            vertical-align:middle;
                            margin:0 auto; 
                            z-index: -1;
                        }*/

            img {
                position: relative;
                height: auto;
                width: 40em;
                margin: 2em;
                z-index: -1;
            }            

        </style>

    </head>

    <body>
        <div class="row">
            <div class="col-xs-12 col-sm-3">  
                <img src="<?php echo RUTA_URL ?>/public/img/innovation__monochromatic.svg">

            </div>
            <div class="col-xs-12 col-sm-9">



                <form class="form-signin" action="<?php echo RUTA_URL ?>/Login/acceder" method="POST" autocomplete="off">
                    <h1 class='text-center'>MVC ORIGINAL</h1>
                    <h6 class="text-center">Ingresa tus datos</h6>
                    <br>
                    <label for="inputEmail" class="sr-only">Email</label>
                    <input type="email" id="inputEmail" name="mail" class="form-control" placeholder="Email" required autofocus>
                    <br>
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" id="inputPassword" name="pass" class="form-control" placeholder="Password" required>
                    <br>
                    <button class="btn btn-lg btn-success btn-block" type="submit">Entrar</button>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo RUTA_URL ?>/public//js/main.js"></script>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="<?php echo RUTA_URL; ?>/public/vendor/components/jquery/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="<?php echo RUTA_URL; ?>/public/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

    <scrip src="<?php echo RUTA_URL; ?>/public/vendor/fortawesome/font-awesome/js/all.min.js"></scrip>
    <!-- summernote -->
    <script src="<?php echo RUTA_URL; ?>/public/summernote/summernote-bs4.js"></script>
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/edicionTexto.js"></script>
    <!-- efecto mostrar ocultar videotutorial -->
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/videoTutorial.js"></script>
    <!-- sidebarhover -->
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/sidebarhover.js"></script>
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/sidebarIzquierdo.js"></script>
    <!-- interior paginas con ajax -->
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/contenidoPaginas.js"></script>
    <!-- Load d3.js and c3.js -->
    <script src="<?php echo RUTA_URL ?>/public/node_modules/d3/dist/d3.js" charset="utf-8"></script>
    <script src="<?php echo RUTA_URL ?>/public/node_modules/c3/c3.js"></script>




</body>

</html>