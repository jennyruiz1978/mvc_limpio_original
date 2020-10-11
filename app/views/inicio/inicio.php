<?php require(RUTA_APP . '/views/includes/header.php'); ?>
   <h2>Inicio</h2>

   <br>
   <a href="DocumentosPdf">Generar documento PDF</a>

   <br><br><br>
   <h3>Ejemplo de envío de email simple</h3>
   <?php //envío de email simple ?>
   <!-- Button trigger modal -->
   <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
   Modal envío email
   </button>
   <!-- Modal -->
   <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <form method="post" action="<?php echo RUTA_URL . '/EnviarEmailControlador/enviarEmailSimple'; ?>">
            <div class="modal-content">
            <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Modal Envío de email</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body">
                  <div class="form-group">
                     <label for="exampleFormControlInput1">Email destinatario</label>
                     <input type="email" class="form-control" name="email" placeholder="name@example.com">
                  </div>
                  <div class="form-group">
                     <label for="exampleFormControlInput2">Asunto</label>
                     <input class="form-control" name="asunto">
                  </div>
                  <div class="form-group">
                     <label for="exampleFormControlTextarea1">Mensaje</label>
                     <textarea class="form-control" name="mensaje" rows="3"></textarea>
                  </div>            
            </div>
            <div class="modal-footer">                  
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary"><i class="far fa-envelope"></i> Enviar</button>
            </div>
            </div>
         </form>
      </div>
   </div>


   
   <br><br><br>
   <h3>Ejemplo de envío de email con factura pdf generada del sistema</h3>
   <?php //envío de email con Factura pdf generada del sistema?>
   <!-- Button trigger modal -->   
   <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#facturaModal">
   Modal envío factura pdf
   </button>
   <!-- Modal -->
   <div class="modal fade" id="facturaModal" tabindex="-1" aria-labelledby="facturaModalLabel" aria-hidden="true">
      <div class="modal-dialog">
         <form method="post" action="<?php echo RUTA_URL . '/Factura/enviarEmailConFacturaPdf'; ?>">
            <input type="hidden" name="idFactura" id="idFactura" value="5">
            <div class="modal-content">
            <div class="modal-header">
                  <h5 class="modal-title" id="facturaModalLabel">Modal Envío de email con Factura pdf</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body">
                  <div class="form-group">
                     <label for="exampleFormControlInput1">Email destinatario</label>
                     <input type="email" class="form-control" name="email" placeholder="name@example.com">
                  </div>
                  <div class="form-group">
                     <label for="exampleFormControlInput2">Asunto</label>
                     <input class="form-control" name="asunto">
                  </div>
                  <div class="form-group">
                     <label for="exampleFormControlTextarea1">Mensaje</label>
                     <textarea class="form-control" name="mensaje" rows="3"></textarea>
                  </div>            
            </div>
            <div class="modal-footer">                  
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary"><i class="far fa-envelope"></i> Enviar</button>
            </div>
            </div>
         </form>
      </div>
   </div>

   
   <?php

      //control de mensajes
      $msg = $datos['msg'];
      if ($msg >0) {
         if ($datos['msg'] == 2) {
             $clase = 'alert-success';
             $texto = 'Mensaje enviado con éxito';
         } elseif ($datos['msg'] == 1) {
             $clase = 'alert-danger';
             $texto = 'Mensaje no ha podido enviarse';
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

   <br><br>
   <a href="<?php echo RUTA_URL; ?>/Presupuesto" class="nav-link">
      <i class="fas fa-users-cog"></i>
      <p>Ir a Presupuesto</p>
   </a>

   <a href="<?php echo RUTA_URL; ?>/Factura" class="nav-link">
      <i class="fas fa-users-cog"></i>
      <p>Ir a Facturas</p>
   </a>



<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
