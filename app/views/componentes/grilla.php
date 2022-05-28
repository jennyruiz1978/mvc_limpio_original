<div class="modal-body modalGrillaVentana">



    <?php                    
        
    $claseGrilla = 'grillaBaja';
    if ($controlador == 'Stock/tomaInventario') {
        $claseGrilla = 'grillaAlta';
    }
                
    $idDocumento = '';
    
    if ($idcab) {
        $idDocumento = $idcab;        
    } else if (isset($idfac) && $idfac != '' ) {
        $idDocumento = $idfac;        
    } else if (isset($idpre) && $idpre != '') {        
        $idDocumento = $idpre;
    }    
    
    ?>

    <?php
    if ((isset($idcab) && $idcab != '') /*|| isset($idfac)*/ || (isset($idpre) && $idpre !='') ) {   
       
        echo "
            <div id='buscador'></div>
            <div id='" . $tablaAjax . "'></div>
            <div id='paginador'></div>";
    } else {
    ?>

    <?php // Apartado de líneas de detalles ?>

    <?php    
    //if (($idcab && $albaranEditable) || ($idpre !='' || $cabecera->estado == 1 || $cabecera->estado == 4) ) {    
    ?>
        <div class="form-row">
            <div class="d-flex col-lg-3">
                <a type="button" id="btnAddLinea" title="agregar línea"><i class="fas fa-plus-square btnAddLinea"></i></a>
            </div>
        </div>
    <?php
    //}
    ?>



            <div class="form-row mb-3 tablaLineas">      
                <div id="listadoBultos" class="col-lg-12 <?php echo $claseGrilla;?>" >
                    <table class='table table-bordered table-hover' id='tablaGrilla'>
                        <thead>
                        <tr class="thead-light">                    
                                <th style="width: 4%;">Lin</th>
                                <th class="text-left">Artículo</th>
                                <th class="text-left">Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>%Dscto</th>
                                <th>Total</th>
                                <th style="width: 5%;">%Iva</th>
                                <th>Acciones</th>                                        
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    <?php
    }
    ?>

    <hr class="divisor">

    <div class="d-flex">
        <a class='btn-danger px-1 btnOcutarDetalles' title='Ocultar'>
            <i class="far fa-eye"></i></a>
    </div>
    <div class="form-row">

        <div class="row col-lg-9 mx-0" id="detallesOcultar">

            <?php // Apartado visualización de Series/Lotes 
            ?>
            <div class="col-lg-6 contenedorDetalles px-0">
                <div class="tituloApartadoVentana">Números de Serie/Lote</div>
                <div class="col-lg-12 mt-2">
                    <table class="table table-bordered table-hover" id="tablaSeriesLotes">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th style="display: none;" >Fila Articulo</th>
                                <th>idArticulo</th>
                                <th>Nº Serie/Lote</th>
                                <th>Unidades</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                
                                if ($lotes && count($lotes) >0) {   
                                 
                                    for ($x=0; $x <count($lotes) ; $x++) { 
                                        echo "
                                            <tr>
                                                <td>".($x+1)."</td>
                                                <td style='display: none;'></td>
                                                <td>".$lotes[$x]['idArticulo']."</td>
                                                <td>".$lotes[$x]['numeroLote']."</td>
                                                <td>".$lotes[$x]['cantidadLote']."</td>
                                                <td></td>
                                                </tr>";
                                    }                                       
                                }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12 mb-2 d-flex justify-content-center">

                   <?php
                   //if (($idcab && $albaranEditable) || ($idpre !='' || $cabecera->estado == 1 || $cabecera->estado == 4) ) {
                    if ($albaranEditable) {
                   ?>
                    <button type="button" class='btn btnTipoGeneral btnNuevoLote mx-3' id='btnNuevoLote'>Nuevo</button>                   
                    <button class='btn btnTipoGeneral btnBuscarLote mx-3' id='btnBuscarLote'>Buscar</button>
                    <?php
                   }
                    ?>

                </div>
            </div>

            <?php // Apartado visualización de detalles en extenso 
            ?>
            <div class="col-lg-6 contenedorDetalles px-0">
                <div class="tituloApartadoVentana">Información del Artículo</div>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">

                        <a class="nav-link active" id="nav-precios-tab" data-toggle="tab" href="#nav-precios" role="tab" aria-controls="nav-precios" aria-selected="false">Precios</a>
                        <a class="nav-link" id="nav-proveedores-tab" data-toggle="tab" href="#nav-proveedores" role="tab" aria-controls="nav-proveedores" aria-selected="false">Proveedores</a>
                        <a class="nav-link" id="nav-existencias-tab" data-toggle="tab" href="#nav-existencias" role="tab" aria-controls="nav-existencias" aria-selected="true">Existencias</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">

                    <div class="tab-pane fade show active" id="nav-precios" role="tabpanel" aria-labelledby="nav-precios-tab">
                        <table class='table table-bordered table-hover' id='tablaInfoMargenes'>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-proveedores" role="tabpanel" aria-labelledby="nav-proveedores-tab">
                        <table class='table table-bordered table-hover' id='tablaInfoProveedores'>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-existencias" role="tabpanel" aria-labelledby="nav-existencias-tab">
                        <table class='table table-bordered table-hover' id='tablaInfoExistencias'>
                        </table>
                    </div>
                </div>
            </div>
            <?php // Apartado Otros 
            ?>
            <div class="col-lg-12 row mt-2">

                <div class="d-flex col-lg-3">
                    <label for="verCosto" class="col-lg-6 col-form-label labelVentana">Coste Actual</label>
                    <input type="text" class="col-lg-6 form-control form-control-sm" id="verCosto" style="font-size: 0.8rem;" readonly>
                </div>
                <div class="d-flex col-lg-3">
                    <label for="stock" class="col-lg-6 col-form-label labelVentana">Stock Actual</label>
                    <input type="text" class=" text-right form-control col-lg-7 form-control-sm" id="stock" style="font-size: 0.8rem;" readonly>
                </div>

            </div>

        </div>


        <?php // Apartado visualización de Totales 
        ?>
        <div class="col-lg-3">
            <div class="tituloApartadoVentana">Totales</div>
            <table class='table table-bordered table-hover' id='tablaTotales'>
                <?php

        

                echo "
                <tr class='thead-light'>
                    <td width='60%'>Neto (B.Impon.)</td>
                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalBaseImponible' 
                        name='totalBaseImponible' value='" . (($idDocumento) ? round($cabecera->baseimponible, 2) : 0) . "'></td>                   
                </tr>
                <tr class='thead-light'>
                    <td width='60%'>Descuento</td>
                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalValorDescuento' 
                        name='totalValorDescuento' value='" . (($idDocumento) ? round($cabecera->valordescuento, 2) : 0) . "'></td>                   
                </tr>
                <tr class='thead-light'>
                    <td width='60%'>Iva</td>
                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalValorIva' 
                        name='totalValorIva' value='" . (($idDocumento) ? round($cabecera->valoriva, 2) : 0) . "'></td>                   
                </tr>
                <tr class='thead-light'>
                    <td width='60%'>Total</td>
                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalDocumento' 
                        name='totalDocumento' value='" . (($idDocumento) ? round($cabecera->valortotal, 2) : 0) . "'></td>                   
                </tr>";
                if ($controlador == 'Salidas') {
                    echo "
                <tr class='thead-light'>
                    <td width='60%'>Cobrado</td>
                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalCobrado' 
                        name='totalCobrado' value='" . (($idDocumento) ? $cabecera->valorcobrado : 0) . "'></td>                   
                </tr>          
                <tr class='thead-light'>
                    <td width='60%'>Pendiente cobro</td>
                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalPendienteCobro' 
                        name='totalPendienteCobro' value='" . (($idDocumento) ? $cabecera->valorpendientecobro : 0) . "'></td>                   
                </tr>";
                }
                ?>

            </table>
        </div>

    </div>

    <div class="form-row mx-0">

    </div>



</div>