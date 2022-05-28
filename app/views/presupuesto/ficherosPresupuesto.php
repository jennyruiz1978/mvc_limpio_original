<?php require(RUTA_APP . '/views/includes/header.php'); ?>

   <a href="<?php echo RUTA_URL; ?>/Inicio" class="nav-link">
      <i class="fas fa-users-cog"></i>
      <p>Ir al Inicio</p>
   </a>

   <br><br><br>
   <h3>Ejemplo de Upload fichero</h3>

   
   <?php

      //control de mensajes
      $msg = $datos['msg'];
      if ($msg >0) {
         if ($datos['msg'] == 1) {
             $clase = 'alert-success';
             $texto = 'Fichero subido con éxito';
         } elseif ($datos['msg'] == 2 || $datos['msg'] == 3) {
             $clase = 'alert-danger';
             $texto = 'Fichero no ha podido subirse';
         }
         echo"
         <div class='alert ".$clase." alert-dismissible fade show' role='alert'>
            <strong>".$texto."</strong>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
               <span aria-hidden='true'>&times;</span>
            </button>
         </div>";
      }
   ?>


   <form action="<?php echo RUTA_URL;?>/Presupuesto/uploadFicheroPresupuesto" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="idPresupuesto" value="2">
      <div class="form-group" style="padding-bottom:15px;">
         
            <a id="anadirFichero" class="btn btn-success text-white">Añadir fichero</a>
            <div class='col-sm-4 my-1' id='formularioSubirFichero' style='display:none;'>
               <input type="text" class="form-control" name="descripcionFichero" id="descripcionFichero" placeholder="Descripción fichero">
               <input type="file" class="form-control-file my-1" name="ficheroPresupuesto" id="ficheroPresupuesto" placeholder="Adjunte fichero">
               <input type="submit" value="Upload Fichero" class="btn btn-danger">
            </div>
         
      </div>
      
      <div>
            <?php            

             
            ?>

      </div>
   </form>






   <?php require(RUTA_APP . '/views/includes/footer.php'); ?>


