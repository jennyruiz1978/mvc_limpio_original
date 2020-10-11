<?php require(RUTA_APP . '/views/includes/header.php'); ?>

   <a href="<?php echo RUTA_URL; ?>/Inicio" class="nav-link">
      <i class="fas fa-users-cog"></i>
      <p>Ir al Inicio</p>
   </a>

   <h3>Ejemplo de Exportar a Excel datos de la BD</h3>

   <form action="<?php echo RUTA_URL;?>/Factura/exportarListaFacturas" method="POST" enctype="multipart/form-data">
      <button type="submit" name="export" class="btn btn-primary mb-3" title="Exportar excel" value="Export"><i class="fas fa-file-excel"></i> Exportar Excel</button>
      
      <div>
            <?php            

               if ($datos['facturas'][0]) {
                     echo"
                     <table class='table mt-3'>
                        <thead>
                           <tr>
                                 <th scope='col'>idfactura</th>
                                 <th scope='col'>numfactura</th>
                                 <th scope='col'>fechafactura</th>
                                 <th scope='col'>B. Imponible</th>
                                 <th scope='col'>IVA(%)</th>
                                 <th scope='col'>Total</th>
                                 <th scope='col'>Cliente</th>
                           </tr>
                        </thead>
                        <tbody>";                                     
                           $facturas = $datos['facturas'];
                           foreach ($facturas as $key) {
                                 echo"
                                 <tr>
                                    <td scope='row'>".$key->idfactura."</td>
                                    <td scope='row'>".$key->numfactura."</td>
                                    <td scope='row'>".$key->fechafactura."</td>
                                    <td scope='row'>".$key->importe."</td>
                                    <td scope='row'>".$key->iva."</td>
                                    <td scope='row'>".$key->total."</td>
                                    <td scope='row'>".$key->denominacion."</td>
                                 </tr>";
                           }
                        
                        echo"
                        </tbody>
                     </table>";
               }
            ?>

      </div>
   </form>

   <br>
   <h3>Ejemplo de Importar a la Base de Datos desde Excel</h3>
   <p>Utilizar el fichero listadorFacturas.xlsx que está en la raíz de este proyecto</p>
   <form action="<?php echo RUTA_URL;?>/Factura/importarListaFacturas" method="POST" enctype="multipart/form-data">
      <div class="mb-2">
         <a id="importarPlantilla" class="btn btn-info text-white"><i class="fas fa-file-excel mr-2"></i>Importar</a>      
         <div class='col-sm-4 my-1' id='formularioSubirFichero'>
            <input type="text" class="form-control" name="descripcionFichero" id="descripcionFichero" placeholder="Descripción fichero">
            <input type="file" class="form-control-file my-1" name="plantillaFacturas" id="plantillaFacturas" placeholder="Adjunte fichero excel">
            <input type="submit" name='importFacturas' value="Importar Facturas" class="btn btn-success ml-0" style="color:#fff;"> 
         </div>
      </div>

      <div>
            <?php            

               if ($datos['facturasImport'][0]) {
                     echo"
                     <table class='table mt-3'>
                        <thead>
                           <tr>
                                 <th scope='col'>idfactura</th>
                                 <th scope='col'>numfactura</th>
                                 <th scope='col'>fechafactura</th>
                                 <th scope='col'>B. Imponible</th>
                                 <th scope='col'>IVA(%)</th>
                                 <th scope='col'>Total</th>
                                 <th scope='col'>Cliente</th>
                           </tr>
                        </thead>
                        <tbody>";                                     
                           $facturas = $datos['facturasImport'];
                           foreach ($facturas as $key) {
                                 echo"
                                 <tr>
                                    <td scope='row'>".$key->idfactura."</td>
                                    <td scope='row'>".$key->numfactura."</td>
                                    <td scope='row'>".$key->fechafactura."</td>
                                    <td scope='row'>".$key->importe."</td>
                                    <td scope='row'>".$key->iva."</td>
                                    <td scope='row'>".$key->total."</td>
                                    <td scope='row'>".$key->empresa."</td>
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


