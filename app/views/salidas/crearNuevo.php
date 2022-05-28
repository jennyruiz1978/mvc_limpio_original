<?php require(RUTA_APP . '/views/includes/header.php'); ?>

<?php


$idfac="";
$idpre="";
 
$formasDePago = $datos['formasDePago'];
//$estadosDePago = $datos['estadosDePago'];
//$clientes = $datos['clientes'];
$agentes = $datos['agentes'];
$cabecera = isset($datos['cabecera'])?$datos['cabecera']:"";
$conceptos = isset($datos['conceptos'])?$datos['conceptos']:"";
$idcab = isset($datos['idcab'])?$datos['idcab']:"";
$tiposIva = isset($datos['tiposIva'])?$datos['tiposIva']:"";
$lotes = isset($datos['lotes'])?$datos['lotes']:"";
$lotesEdicion = isset($datos['lotesEdicion'])?$datos['lotesEdicion']:"";

$albaranEditable = isset($datos['albaranEditable'])?$datos['albaranEditable']:"";

$controlador = $datos['controlador'];
$accion = $datos['accion'];
$tablaAjax = 'destinonuevasalidaajax';


if ($accion == 'nuevo') {
    $nomMetodo = 'crearRegistroSalida';
} else if ($accion == 'ver') {
    $nomMetodo = 'actualizarRegistroSalida';
}

if ($cabecera) {
    $serie = $cabecera->serie;
    $numAlbaran = $cabecera->numalbaran;
    $largo = strlen($numAlbaran);

    if ($largo == 1) {
        $numero = '00000' . $numAlbaran;
    } else if ($largo == 2) {
        $numero = '0000' . $numAlbaran;
    } else if ($largo == 3) {
        $numero = '000' . $numAlbaran;
    } else if ($largo == 4) {
        $numero = '00' . $numAlbaran;
    } else if ($largo == 5) {
        $numero = '0' . $numAlbaran;
    } else if ($largo == 6) {
        $numero = '' . $numAlbaran;
    }


    $fecha = $cabecera->fecha;
    $formaDePago = $cabecera->formadepago;
    $nombreFormapago = $cabecera->nomFormaPago;
    $idEstadoCobro = $cabecera->estado;
    $estadoCobro = $cabecera->estadoCobro;
    $idCliente = $cabecera->idcliente;
    $nombreCliente = $cabecera->nomCliente;
    $almacen = $cabecera->almacen;
    $nombreAlmacen = $cabecera->nomAlmacen;
    $idAgente = $cabecera->agente;
    $nombreAgente = $cabecera->nomAgente;
    $direccion = $cabecera->direccion;
    $tarifa = $cabecera->nombreTarifa;
    $idTarifa = $cabecera->tarifa;
    $carnet = $cabecera->carnet;
    $readonly="readonly";

} else {
    $fecha = date('Y-m-d');
    $formaDePago = 2;
    $idEstadoCobro = 1;
    $serie="";
    $numAlbaran = "";
    $largo = "";
    $nombreFormapago = "";
    $idEstadoCobro = "";
    $estadoCobro = "";
    $idCliente = "";
    $nombreCliente = "";
    $almacen = "";
    $nombreAlmacen = "";
    $idAgente = "";
    $nombreAgente ="";
    $direccion = "";
    $tarifa = "";
    $idTarifa = "";
    $carnet = "";
    $numero="";
    $readonly="";
   
}

$tipo = 'Albarán de Venta';
if (!$idcab) {
    $parte = 'Nuevo ';
}else{
    $parte = 'Editar ';
}

             
$idDocumento = '';

if ($idcab) {
    $idDocumento = $idcab;        
}


?>


<!-- start contenido de la pagina -->
<div class="container">

    <br><br>

    <div >
        <div class="" role="document">

            <div class=" modalContenidoVentana">
                <div class="modal-header modalHeaderVentana">
                    <?php
                    echo "
                            <h5 class='modal-title' id='exampleModalLabel'>" . $parte . " " . $tipo . "</h5>
                            <a href='" . RUTA_URL . "/" . $controlador . "' class='close cerrar'>
                                <span aria-hidden='true'>&times;</span>                       
                            </a>";
                    ?>
                </div>
                <?php //Inicio del form 
                ?>
                <?php
                echo "
                            <form id='formRegistroEntradasSalidas' action='" . RUTA_URL . "/Salidas/" . $nomMetodo . "' method='POST'>";
                ?>


                <div class="modal-footer cintaAcciones" id="footerArticulo">

                    <div class="d-flex">
                        <?php
                        if (!$idcab || $albaranEditable) {
                        ?>
                            <?php //require(RUTA_APP . '/views/botones/guardarCerrar.php'); ?>
                            <?php require(RUTA_APP . '/views/botones/guardarSeguir.php'); ?>
                            <?php //require(RUTA_APP . '/views/botones/guardarNuevo.php'); ?>
                        <?php
                        }
                        ?>

                        <button id='btnVerCliente' class='btn btnTipoGeneral'>
                            <i class='fas fa-file-export iconoGenerico'></i>Ver cliente
                        </button>

                        <?php if($parte!="Editar "){?>
                        <a id="sinGuardar" class='btn btnTipoGeneral' style='font-size: 12px;opacity:1; text-shadow:none;font-weight:normal;'>
                        <i class='fas fa-file-export iconoGenerico'></i>Sin guardar</a>
                        <?php }?>

                        <?php

                        if (!$idcab) {
                            echo "                                                        
                                                    <button class='btn btnTipoGeneral' id='btnNuevoCliente' data-toggle='modal' data-target='#modalVer'>
                                                        <i class='fas fa-user-tie iconoGenerico'></i>Nuevo Cliente
                                                    </button>                  

                                                    <button class='btn btnTipoGeneral' id='btnNoHabitual' data-toggle='modal' data-target='#modalVer'>
                                                        <i class='far fa-address-card iconoGenerico'></i>No habitual
                                                    </button>";
                        }

                        ?>

                        <?php
                        if ($idcab && $cabecera->estado != 2) {
                            echo "
                                                <button type='button' class='btn btnTipoGeneral' id='cobrarAlbaran'><i class='fas fa-euro-sign iconoGenerico'></i>Cobrar</button>";
                        }
                        if ($idcab && $cabecera->estadofactura != 2) {
                            echo "
                                                <button type='button' class='btn btnTipoGeneral' id='emitirFacturaVenta'><i class='fas fa-file-invoice-dollar iconoGenerico'></i>Factura</button>
                                                ";
                        }
                        ?>
                                                
                        <?php //require(RUTA_APP . '/views/botones/enviarEmail.php'); ?>
                        <?php //require(RUTA_APP . '/views/botones/emitirPdf.php'); ?>
                        <?php //require(RUTA_APP . '/views/botones/enviarImpresion.php'); 
                        ?>
                        <?php //require(RUTA_APP . '/views/botones/facturaSimplificada.php'); 
                        ?>

                    </div>

                </div>

                <?php //Inicio de encabezado 
                ?>
                <div class="modal-body modalEncabezadoVentana">
                    <span id="mensajeValidacion"></span>

                    <div class="form-row">
                        <input type="hidden" name="tipo" id="tipo" value="albaranSalida">
                        <div class="d-flex col-sm-12 col-lg-6 col-xl-3">
                            <label for="serie" class="col-sm-4 col-lg-4 col-form-label labelVentana">Serie/Número</label>
                            <?php
                            if ($cabecera) {
                                echo " <input type='text' id='serieNumeroModal' $readonly class=' col-sm-4 col-lg-2 form-control form-control-sm' value='" . $serie . "'>";
                            } else {
                            ?>
                                <select class="col-sm-4 col-lg-2 form-control form-control-sm" name="serie" id="serie">
                                    <option disabled>&nbsp;</option>
                                    <?php

                                        if (TIENDA && TIENDA !='' ) {
                                            echo"<option value='".NUM_SERIE."' selected>".NUM_SERIE."</option>";
                                        }else{
                                            for ($i=1; $i <=9 ; $i++) {
                                                echo"<option value='".$i."' ".(($i == $serie)? 'selected': '').">".$i."</option>";
                                            }
                                        }

                                    ?>
                                </select>
                            <?php
                            }
                            ?>
                            <input type="hidden" value="<?php echo $idcab; ?>" id="idcab" name="idcab">
                            <?php
                            echo " <input type='text' readonly class=' col-sm-4 col-lg-6 form-control form-control-sm' id='numeroAlbaranSalidas' value='" . $numero . "'>   ";
                            ?>
                        </div>
                        <div class="d-flex col-sm-12 col-lg-6 col-xl-3">
                            <label for="fecha" class="col-sm-4 col-lg-4 col-xl-2 col-form-label labelVentana">Fecha</label>
                            <div class="col-sm-8 col-lg-8 col-xl-10 px-0">
                                <?php
                                $edicion = '';
                                if ($cabecera) {
                                    $edicion = 'readonly';
                                }
                                echo "
                                                <input type='date' class='form-control form-control-sm' id='fecha' name='fecha' value='" . $fecha . "' " . $edicion . ">";
                                ?>
                            </div>
                        </div>
                        <div class="d-flex col-sm-12 col-lg-6 col-xl-3">
                            <label for="formadepago" class="col-sm-4 col-lg-4 col-xl-6 col-form-label labelVentana">Forma de pago</label>
                            <div class="col-sm-8 col-lg-8 col-xl-6 px-0">
                                <?php
                                $edicion = '';
                                if ($cabecera && $albaranEditable == false) {
                                    echo "<input class='form-control' value='" . $nombreFormapago . "' $readonly>";
                                } else {
                                ?>
                                    <select name="formadepago" id="formadepago" class="form-control  pr-0">

                                        <?php
                                        foreach ($formasDePago as $formas) {
                                            echo "<option value='" . $formas->id . "' " . (($formas->id == $formaDePago) ? 'selected' : '') . ">" . $formas->formadepago . "</option>";
                                        }
                                        ?>
                                    </select>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="d-flex col-sm-12 col-lg-6 col-xl-3">
                            <?php
                            if ($cabecera) {
                                echo "<label for='estado' class='col-sm-4 col-lg-4 col-xl-6 col-form-label labelVentana'>Estado</label>
                                <div class='col-sm-8 col-lg-8 col-xl-6 px-0'>
                                    <input class='form-control' value='" . $estadoCobro . "' $readonly>";
                            ?>
                                <!--
                                                        <select name="estado" id="estado" class="form-control  pr-0">
                                                            <option disabled selected>Seleccionar</option>
                                                            <?php
                                                            /*
                                                                foreach ($estadosDePago as $estado) {
                                                                    echo"<option value='".$estado->id."' ".(($estado->id == $idEstadoCobro)? 'selected':'').">".$estado->estadodepago."</option>";
                                                                }*/
                                                            ?>                        
                                                        </select>
                                                -->
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                

                    <div class="form-row">
                        <div class="d-flex col-sm-12 col-lg-6">
                            <label for="idCliente" class="col-sm-4 col-lg-2 col-form-label labelVentana">Cliente</label>

                            <?php
                                $readonlyIdCliente = '';
                                if ($idcab) {
                                    $readonlyIdCliente = 'readonly';
                                }
                            ?>
                            <input class="col-sm-2 col-lg-2 form-control" <?php echo $readonlyIdCliente;?> name="idClienteInput" id="idClienteInput" value="<?php echo $idCliente; ?>">    
                            <?php
                                echo '<input type="hidden" id="booleanEditable" value="' . $albaranEditable . '">';
                                if (!$cabecera || $albaranEditable) {
                            ?>                            
                                <select class="col-sm-6 col-lg-8 form-control" name="idCliente" id="idCliente">                               
                                    <?php                                
                                    echo "<option value='" . $idCliente . "'>" . $idCliente . " - " . $nombreCliente . "</span></option>";                                
                                    ?>
                                </select>
                                <input type="hidden" id="idClienteNoHabitual" name="idClienteNoHabitual" value="">
                            <?php
                            } else {
                                echo "
                                    <input class='col-sm-8 col-lg-10 form-control' id='nombreClienteModal' value='" . $nombreCliente . "' $readonly>";
                            }
                            ?>
                        </div>
                        <div class="d-flex col-sm-12 col-lg-6">
                            <label for="direccion" class="col-sm-4 col-lg-4 col-xl-3 col-form-label labelVentana">Dirección</label>
                            <?php
                            echo "
                                                <input class='col-sm-8 col-lg-8 col-xl-9 form-control ' name='direccion' id='direccion' value='" . $direccion . "' readonly>";
                            ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="d-flex col-sm-12 col-lg-3">
                            <label for="tarifaCliente" class="col-sm-4 col-lg-3 col-form-label labelVentana">Tarifa</label>
                            <?php
                            echo "
                                                <input class='col-sm-8 col-lg-9 form-control ' name='tarifaCliente' id='tarifaCliente' value='" . $tarifa . "' readonly>
                                                <input type='hidden' name='idTarifa' id='idTarifa' value='" . $idTarifa . "'>
                                                <input type='hidden' name='carnet' id='carnet' value='" . $carnet . "'>";
                            ?>

                        </div>
                        <div class="d-flex col-sm-12 col-lg-3">
                            <label for="agente" class="col-sm-4 col-lg-3 col-form-label labelVentana">Agente</label>

                            <?php
                            if ($cabecera) {
                                echo "<input class='col-sm-8 col-lg-9 form-control' name='agente' id='agente' value='" . $nombreAgente . "' $readonly>
                                <input type='hidden' name='agente' value='".$idAgente."'>";
                            } else {
                            ?>
                                <select class="col-sm-8 col-lg-9 form-control  select2" name="agente" id="agente">
                                    <option disabled selected>Seleccionar</option>
                                    <?php
                                    foreach ($agentes as $agente) {
                                        echo "<option value='" . $agente->ID . "' " . (($agente->ID == $idAgente) ? 'selected' : '') . ">" . $agente->ID . "-" . $agente->user_name . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="d-flex col-sm-12 col-lg-3">
                            <label for="almacen" class="col-sm-4 col-lg-3 col-form-label labelVentana">Almacén</label>
                            <?php
                            if ($cabecera) {
                                echo "<input class='col-sm-8 col-lg-9 form-control'  value='" . $nombreAlmacen . "' $readonly>
                                <input type='hidden' value='".$almacen."' name='almacen'>";
                            } else {
                            ?>

                                <select class="col-sm-8 col-lg-9 form-control " name="almacen" id="almacen">
                                    <option value="1">General</option>
                                </select>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="d-flex col-sm-12 col-lg-3">
                            <label for="riesgo" class="col-sm-4 col-lg-3 col-form-label labelVentana">Riesgo</label>
                            <?php
                            $creditoUtilizado = '';                 
                            if (isset($datos['creditoUtilizado'])) {
                                $creditoUtilizado = $datos['creditoUtilizado'];
                            }
                            echo"
                                <input type='text' class='col-sm-8 col-lg-9 form-control form-control-sm' id='riesgoCliente' value='".$creditoUtilizado."' readonly>";

                            ?>
                        </div>
                    </div>

                <?php //Fin de encabezado
                ?>
                
                <?php // Apartado de líneas de detalles ?>

                <div class="form-row mt-2 bg-white">

                <?php
                    if ($idcab != '' &&  $cabecera->estado == 1 && $cabecera->estadofactura == 1) {                        
                ?>
                    <div class="modal-body modalGrillaVentana">

                        <?php
                        if ($idcab == '' || ($idcab != '' &&  $cabecera->estado == 1 && $cabecera->estadofactura == 1) ) {   
                        ?>
                            <div class="form-row">
                                <div class="d-flex col-lg-3">
                                    <a type="button" id="btnAddLinea" title="agregar línea"><i class="fas fa-plus-square btnAddLinea"></i></a>
                                </div>
                            </div>
                        <?php 
                        }
                        ?>

                        <div class="form-row mb-3 tablaLineas">      
                            <div id="listadoBultos" class="col-lg-12 grillaBaja" >
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
                                        <?php 
                                        
                                            $arrayArtConTomaInvent = [];
                                            if (isset($datos['lineas']) && count($datos['lineas']) >0) {
                                                $lineas = $datos['lineas'];
                                                $cont = 0;
                                                                
                                                foreach ($lineas as $linea) {
                                                    $cont++;

                                                    $editarCampoIdArt = '';
                                                    if ($linea->existe == 1) {
                                                        $editarCampoIdArt = 'readonly';
                                                    }

                                                  
                                                    $tieneInventario = $linea->tieneinventario;                                                    

                                                    $editarOtroCampo = '';
                                                    if ( $linea->tieneinventario == 1) {
                                                        $editarOtroCampo = 'readonly';
                                                        $arrayArtConTomaInvent[] = $linea->idarticulo;
                                                    }
                                                    echo'
                                                        <tr class="rows thead-light">
                                                            <td><input class="inputGrillaAuto dblClickInput" name="numeroOrden[]" value="'.$cont.'" readonly></input></td>

                                                            <td class=""><input name="idArticulo[]" id="idArticulo_'.$cont.'" class="inputGrillaArticulo  inputclick dblClickInput text-left" value="'.$linea->idarticulo.'" '.$editarCampoIdArt.'></td>

                                                            <td class="celdaDescripcion"><input name="descripcion[]" id="descripcion_'.$cont.'" class="inputGrillaAuto inputclick dblClickInput text-left" value="'.$linea->descripcion.'" '.$editarOtroCampo.'></td>

                                                            <td class=""><input type="number" name="cantidadArticulo[]" id="cantidadArticulo_'.$cont.'" class="cantidad inputGrillaAuto inputclick dblClickInput text-right" value="'.$linea->cantidad.'" '.$editarOtroCampo.'></td>

                                                            <td class=""><input type="number" name="precioArticulo[]" id="precioArticulo_'.$cont.'" class="inputGrillaAuto inputclick precio dblClickInput text-right" value="',$linea->precioventa.'" '.$editarOtroCampo.'></td>

                                                            <td class=""><input type="number" name="descuento[]" id="descuento_'.$cont.'" class="inputGrillaAuto inputclick dblClickInput text-right" value="'.$linea->descuento.'" '.$editarOtroCampo.'></td>

                                                            <td class=""><input type="number" name="totalLinea[]" id="totalLinea_'.$cont.'" class="inputGrillaAuto inputclick total dblClickInput text-right" value="'.$linea->total.'" readonly></td>

                                                            <td><div class="d-flex">';
                                                            
                                                                echo'
                                                                <select name="iva[]" id="iva_'.$cont.'" class="inputGrillaAuto iva inputclick dblClickInput text-right">';
                                                                
                                                                if ($tieneInventario == 1) {
                                                                    $ivaTipos1 = ['21.00'=>'21','10.00'=>'10','4.00'=>'4','0.00'=>'0'];  
                                                                    echo'<option value="'.$ivaTipos1[$linea->iva].'" >'.$ivaTipos1[$linea->iva].'</option>';
                                                                }else{
                                                                    $ivaTipos2 = [21=>'21',10=>'10',4=>'4',0=>'0'];                                                                    
                                                                    foreach ($ivaTipos2 as $tipo => $valor) {
                                                                        echo'<option value="'.$tipo.'" '.(($tipo == $linea->iva)? "selected":"" ).'>'.$valor.'</option>';
                                                                    }
                                                                }
                                                                echo'
                                                                </select>%</div>
                                                            </td>';

                                                            $botonEliminar = 'visible';
                                                            if ($tieneInventario == 1) {
                                                                $botonEliminar = 'hidden';
                                                            }
                                                            echo'                                                                       
                                                            <td class="botones text-center">
                                                                <div class="d-flex justify-content-center" style="visibility:'.$botonEliminar.'">
                                                                <a class="btn btn-danger px-1 mx-1 botonTablaAjaxMini btnDeleteLinea" title="eliminar"><i class="fas fa-trash-alt mr-0 iconoGenerico"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                }

                                            } 
                                        ?>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr class="divisor">


                  
                        <div class="d-flex">
                            <a class='btn-danger px-1 btnOcutarDetalles' title='Ocultar'>
                                <i class="far fa-eye"></i>
                            </a>
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
                                                                
                                                                if ($lotesEdicion && count($lotesEdicion) >0) {   
                                                                
                                                                    for ($x=0; $x <count($lotesEdicion) ; $x++) { 
                                                                        $editarCantidad = '';
                                                                        $btnEliminarLote = 'visible';
                                                                        if (in_array($lotesEdicion[$x]['idArticulo'], $arrayArtConTomaInvent)) {
                                                                            $editarCantidad = 'readonly';
                                                                            $btnEliminarLote = 'hidden';
                                                                        }                                                                        
                                                                        echo 
                                                                                '<tr>
                                                                                <td>' . ($x+1) . '</td>
                                                                                <td style="display: none;" ><input class="inputGrillaAuto filaArticulo idLoteArticulo_'.$lotesEdicion[$x]['idArticulo'].'" name="numFilaArticulo[]" value="' . $lotesEdicion[$x]['numFila'] . '" readonly></td>
                                                                                <td><input class="text-left inputGrillaAuto" name="idArtSelected[]" value="' . $lotesEdicion[$x]['idArticulo'] . '" readonly></td>
                                                                                <td><input class="text-left inputGrillaAuto inputSerieLote numLote_' . $lotesEdicion[$x]['numFila'] . '" name="numeroLote[]" value="' . $lotesEdicion[$x]['numeroLote'] . '" readonly></td>
                                                                                <td><input type="number" class="text-left inputCantidadLote inputGrillaAuto cantidadLote_' . $lotesEdicion[$x]['numFila'] . '" name="cantidadLote[]" value="'.$lotesEdicion[$x]['cantidadLote'].'" '.$editarCantidad.'></td>
                                                                                <td class="d-flex justify-content-center"><a class="btn-danger px-1 btnDeleteLote" title="quitar" style="visibility:'.$btnEliminarLote.'">
                                                                                <i class="fas fa-trash-alt" style="color:white;"></i></a></td>
                                                                            </tr>';

                                                                    }                                       
                                                                }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-lg-12 mb-2 d-flex justify-content-center">
                                                    <button type="button" class='btn btnTipoGeneral btnNuevoLote mx-3' id='btnNuevoLote'>Nuevo</button>
                                                    <button class='btn btnTipoGeneral btnBuscarLote mx-3' id='btnBuscarLote'>Buscar</button>
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
                                                        name='totalBaseImponible' value='" . (($idDocumento) ? $cabecera->baseimponible : 0) . "'></td>                   
                                                </tr>
                                                <tr class='thead-light'>
                                                    <td width='60%'>Descuento</td>
                                                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalValorDescuento' 
                                                        name='totalValorDescuento' value='" . (($idDocumento) ? $cabecera->valordescuento : 0) . "'></td>                   
                                                </tr>
                                                <tr class='thead-light'>
                                                    <td width='60%'>Iva</td>
                                                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalValorIva' 
                                                        name='totalValorIva' value='" . (($idDocumento) ? $cabecera->valoriva : 0) . "'></td>                   
                                                </tr>
                                                <tr class='thead-light'>
                                                    <td width='60%'>Total</td>
                                                    <td width='40%'><input readonly class='inputGrillaAuto totalBaseImponible' id='totalDocumento' 
                                                        name='totalDocumento' value='" . (($idDocumento) ? $cabecera->valortotal : 0) . "'></td>                   
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
                
                    <?php
                    }else{                        
                        require(RUTA_APP . '/views/componentes/grilla.php');
                    }
                    ?>

                </div>


            </div>            
            


            </form>
            <?php //Fin del form
            ?>

            <?php //require(RUTA_APP . '/views/componentes/modalBuscadorArticulos.php'); ?>
            <?php //require(RUTA_APP . '/views/componentes/modalCliente.php'); ?>
            <?php //require(RUTA_APP . '/views/componentes/modalBuscadorClientesScroll.php'); ?>

        </div>

    </div>
</div>

    <?php //require(RUTA_APP . '/views/componentes/modalEmitirFacturaVenta.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalAlbaranMasivo.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalApunteCobro.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalAlbaranCobroGeneral.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalEnviarAlbaran.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalPdfAlbaran.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalPdfAlbaranSinGuardar.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalImprimirAlbaran.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalLotesSeries.php'); ?>
    <?php //require(RUTA_APP . '/views/componentes/modalVerFichaArticulo.php'); ?>


</div>
<!-- end contenido de la pagina -->

<?php
    if ($idcab != '' &&  ($cabecera->estado != 1 || $cabecera->estadofactura != 1)) {
?>

    <script type="module">
        
        import construirgrilla from "<?php echo RUTA_URL ?>/public/js/grilla/tablagrilla.js"

        construirgrilla("tablagrilla",
            "<?php echo RUTA_URL . '/Salidas/crearTablaSalida/' . $idcab ?>",
            "<?php echo $tablaAjax; ?>",
            "table table-bordered table-striped table-hover",
            [""],
            "tablaGrilla"
        );
        

    </script>

<?php
    }
?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>