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

               if ($datos['ficheros'][0]) {
                     echo"
                     <table class='table mt-3'>
                        <thead>
                           <tr>
                                 <th scope='col'>Descripción</th>
                                 <th scope='col'>Fichero</th>
                                 <th scope='col'>Eliminar</th>
                           </tr>
                        </thead>
                        <tbody>";                                     
                           $ficheros = $datos['ficheros'];
                           foreach ($ficheros as $key) {
                                 echo"
                                 <tr>
                                    <td scope='row'>".$key->descripcion."</td>
                                    <td><div><a href='".RUTA_URL."/presupuesto/downloadFicheroPresupuesto/".$key->idDocumento."' target='_BLANK'>".$key->nombre."</a></div></td>
                                    <td><a class='btn btn-danger eliminarFichero' title='Quitar' style='color:white;'><i class='fas fa-trash'></i></a></td>
                                 </tr>";
                           }
                        
                        echo"
                        </tbody>
                     </table>";
               }
            ?>

      </div>
   </form>






   <?php require(RUTA_APP . '/views/includes/footer.php'); ?>


