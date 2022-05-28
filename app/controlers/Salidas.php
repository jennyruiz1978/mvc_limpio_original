<?php


//require('AlbaranPDF.php');
//require '../public/vendor/autoload.php';
//require('CargarStock.php');
//require('DescargarStock.php');

class Salidas extends Controlador
{

    public function __construct()
    {
        $this->modeloSalidas = $this->modelo('ModeloSalidas');
        //$this->modeloArticulos = $this->modelo('ModeloArticulos');
        //$this->modeloClientes = $this->modelo('ModeloClientes');
    }

    public function index($a = false, $b = false)
    {
        session_start();
        //$clientes = $this->modeloSalidas->clientes();
        $formasDePago = $this->modeloSalidas->formasDePago();
        
        if ($a == false && $b == false) {                    
            
            if ($_SESSION['usuario'] == 'Admin') {
                $tabla = $this->tablaAlbaranesSalidaScrollInfinitoAdmin('index');
            }else{
                $tabla = $this->tablaAlbaranesSalidaScrollInfinito('index');
            }  

            $formasDePago = $this->modeloSalidas->formasDePago();
            $datos = [
                "permisos" => $this->permisos_roles(),
                "url" => $this->control_acceso_urls($_SESSION['usuario']),
                'controlador' => get_class(),
                //'clientes' => $clientes,
                'tabla' => $tabla,
                'formasDePago' =>  $formasDePago
            ];

            //aquí muestro la vista según el rol de usuario
            if ($_SESSION['usuario'] == 'Admin') {
                $this->vista('salidas/salidasAdmin', $datos);
            }else{
                $this->vista('salidas/salidas2', $datos);               
            }            
       
        } else {

            if ($a == "N") {
                $accion = "nuevo";

                //cargo todos los input que se llamarán en el formulario de entrada
                $formasDePago = $this->modeloSalidas->formasDePago();
                $estadosDePago = $this->modeloSalidas->estadosDePago();
                $agentes = $this->modeloSalidas->agentes();
                $tarifas = $this->modeloArticulos->obtenerTarifas();
                $subfamilias = $this->modeloSalidas->listaSubfamiliasActivas();
                $albaranEditable = 1;

                $datos = [
                    "permisos" => $this->permisos_roles(),
                    "url" => $this->control_acceso_urls($_SESSION['usuario']),
                    'accion' => $accion,
                    'controlador' => get_class(),
                    'formasDePago' =>  $formasDePago,
                    'estadosDePago' =>  $estadosDePago,
                    'tarifas' =>  $tarifas,
                    //'clientes' => $clientes,
                    'agentes' => $agentes,
                    "subfamilias" => $subfamilias,
                    'albaranEditable' => $albaranEditable
                ];
                $this->vista('salidas/crearNuevo', $datos);
                
            } else if ($a == "V" && $b != '') { //para ver/editar un albarán existente

                $accion = "ver";                
                //$idcab = $this->modeloSalidas->obtenerIdCabeceraSalida($b);
                $cabecera = $this->modeloSalidas->albaranSalidaCabecera($b);
                $lineas = $this->modeloSalidas->consultaDetallesSalidaPorIdCab($b);
                $conceptos = $this->modeloSalidas->albaranSalidaConceptos($b);
                $lotes = $this->albaranSalidaLotes($b);
                $lotesEdicion = $this->albaranSalidaLotesEdicion($b,$lineas);
                $credito = $this->obtenerCreditoPendientePorIdCliente($cabecera->idcliente);
                
                //cargo todos los input que se llamarán en el formulario de entrada
                $formasDePago = $this->modeloSalidas->formasDePago();                      
                $agentes = $this->modeloSalidas->agentes();
                $tiposIva = $this->modeloSalidas->tiposIva();
                $albaranEditable = $this->modeloSalidas->albaranSalidaEditable($b);
                $albaranNoEditable = $this->modeloSalidas->albaranSalidaNoEditable($b);
                //$tarifas = $this->modeloArticulos->obtenerTarifas();
                $tarifas = []; 

                $datos = [
                    "permisos" => $this->permisos_roles(),
                    "url" => $this->control_acceso_urls($_SESSION['usuario']),
                    'accion' => $accion,
                    'controlador' => get_class(),
                    'formasDePago' =>  $formasDePago,                    
                    //'clientes' => $clientes,
                    'agentes' => $agentes,
                    'idcab' => $b,
                    'cabecera' => $cabecera,
                    'lineas' => $lineas,
                    'conceptos' => $conceptos,
                    'tiposIva' => $tiposIva,
                    'albaranEditable' => $albaranEditable,
                    'albaranNoEditable' => $albaranNoEditable,
                    'lotes' => $lotes,
                    'lotesEdicion' => $lotesEdicion,
                    'creditoUtilizado' => $credito,
                    "tarifas" => $tarifas                    
                ];

                $this->vista('salidas/crearNuevo', $datos);
            }
        }
    }

    //construyo lotes para ver/edicion de albaran
    public function albaranSalidaLotes($idcab)
    {        
        $lotesTodo = $this->modeloSalidas->albaranSalidaConceptos($idcab);
       
        if (count($lotesTodo) >0 ) {
            $arr = [];
            foreach ($lotesTodo as $key) {
                if (count(json_decode($key->lotes)) > 0) {                    
                    $lotes = json_decode($key->lotes);   
                    foreach ($lotes as $lot) {
                        $tmp = [];
                        $tmp['idArticulo'] = $key->idarticulo;
                        $tmp['numeroLote'] = $lot->numeroLote;
                        $tmp['cantidadLote'] = $lot->cantidadLote;
                        $arr[] = $tmp;
                    }
                }
            }
        }

        return $arr;

    }

    public function albaranSalidaLotesEdicion($idcab,$lineas)
    {
        $lotesTodo = $this->modeloSalidas->albaranSalidaConceptos($idcab);

        $ordenArticulos = [];
        $cont = 0;
        foreach ($lineas as $lin) {
            $cont++;
            $ordenArticulos[$lin->idarticulo] = $cont;
        }

        $arr = [];
        if (isset($lotesTodo) && count($lotesTodo) >0 ) {
           

            foreach ($lotesTodo as $key) {
                if (count(json_decode($key->lotes)) > 0) {                    
                    $lotes = json_decode($key->lotes);   
                    //$fila = $k + 1;         

                    foreach ($lotes as $lot) {
                        $tmp = [];                        


                        if (array_key_exists($key->idarticulo, $ordenArticulos)) {
                            $numOrden = $ordenArticulos[$key->idarticulo];
                        }

                        $tmp['idArticulo'] = $key->idarticulo;
                        $tmp['numeroLote'] = $lot->numeroLote;
                        $tmp['cantidadLote'] = $lot->cantidadLote;
                        //$tmp['numFila'] = $fila;
                        $tmp['numFila'] = $numOrden;
                        $arr[] = $tmp;
                    }
                }
            }
        }

        return $arr;
    }

    //creando tabla con scroll infinito
    /*public function salidasScrollInfinito($a = false, $b = false)
    {
        session_start();
        $clientes = $this->modeloSalidas->clientes();
        
        if ($a == false && $b == false) {

            $tabla = $this->tablaAlbaranesSalidaScrollInfinito('index');
            
            $datos = [
                "permisos" => $this->permisos_roles(),
                "url" => $this->control_acceso_urls($_SESSION['usuario']),
                'controlador' => get_class(),
                'clientes' => $clientes,
                'tabla' => $tabla
            ];
            $this->vista('salidas/salidas2', $datos);
        }
    }*/


    public function tablaAlbaranesSalidaScrollInfinito($origen='')
    {               
        if (isset($_POST['offset'])) {
            $offset = $_POST['offset'];
        } else {
            $offset = 0;
        }

        if (isset($_POST['num'])) {
            $limit = $_POST['num'];
        } else {
            $limit = 25;
        }

        $buscar = '';
        if (isset($_POST['buscar'])) {
            $buscar = $_POST['buscar'];
        }
        $campo = '';
        if (isset($_POST['campo'])) {
            $campo = $_POST['campo'];
        }
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        /*
        if (isset($_POST['rolUsuario'])) {
            $rolUsuario = $_POST['rolUsuario'];
        }*/

        /*
        $activarFiltro = '';
        if ($_POST['activarFiltro'] && $_POST['activarFiltro']==1) {

            $condCompleta = "";
            $activarFiltro = $_POST['activarFiltro'];
            $fechaFinal = $_POST['fechaFinal'];
            $fechaInicial = $_POST['fechaInicial'];

            $condFecha = " AND sal.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' "; 

            if ($_POST['idCliente'] && $_POST['idCliente'] >0) {
                $condCliente = " AND sal.idcliente =". $_POST['idCliente'];
            }
            if ($_POST['estFactura'] && $_POST['estFactura'] >0) {
                $condEstFact = " AND sal.estadofactura =". $_POST['estFactura'];
            }
            if ($_POST['estCobro'] && $_POST['estCobro'] >0) {
                $condEstCobro = " AND sal.estado =". $_POST['estCobro'];
            }
            if ($_POST['tipoIva'] && $_POST['tipoIva'] >0) {
                $tipoIva = " AND mov.iva =". $_POST['tipoIva'];
            }
            $condCompleta = $condFecha . $condCliente . $condEstFact . $condEstCobro . $tipoIva;
        
        }
        */

        $detalles = [];
        $tipoLista = '';
        $html = '';

        if ($_SESSION['usuario'] == 'Admin' ){
            
            $detalles = $this->modeloSalidas->consultaDatosSalidasScrollParaUsuarioAdmin($offset, $limit, $buscar, $campo);   
            $html = $this-> construyoFilasTablaSalidasScrollInfinitoAdmin($detalles, $tipoLista);

        /*
            $detalles = $this->modeloSalidas->consultaDatosSalidasScrollParaUsuarioAdmin($offset, $limit, $buscar, $campo);
            $tipoLista = 'Admin';
            $html = $this-> construyoFilasTablaSalidasScrollInfinito($detalles, $tipoLista);

        }else if ($activarFiltro ==1) {
        
            $detalles = $this->modeloSalidas->consultaDatosSalidasScrollParaUsuarioAdminFiltrado($offset, $limit, $activarFiltro, $condCompleta);
            //$tipoLista = 'Admin';
            
            $html = $this->construyoFilasTablaFiltrada($detalles, $_POST['tipoIva']);
            */
        }else{
          
            $detalles = $this->modeloSalidas->consultaDatosSalidasScroll($offset, $limit, $buscar, $campo);   
            $html = $this-> construyoFilasTablaSalidasScrollInfinito($detalles, $tipoLista);
        }                

        if($origen == 'index'){           
            return $html;
        }else{
            print($html);
        }

        
    }

    public function construyoFilasTablaSalidasScrollInfinito($detalles, $tipoLista)
    {
        $html = '';
        
        foreach ($detalles as $key) {            
            
            $fila = '';
            $fila .= '<tr class="rows">
                        <td style="display: none;">' . $key->idcab . '</td>
                        <td nowrap class="cells colAngosta fila filaArticulo">' . $key->numalbcompleto . '</td>
                        <td class="cells colAngosta fila filaArticulo">' . $key->Fecha . '</td>                     
                        <td class="cells colAngosta fila filaArticulo">' . $key->Cliente . '</td>
                        <td  nowrap class="cells colCliente fila filaArticulo">' . $key->Nombre . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->formadepago . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->Factura . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->Total . '</td>';

                if ($tipoLista == 'Admin') {
                    $iva4 = 0;
                    if ($key->iva4) {
                        $iva4 = $key->iva4;
                    }
                    $fila .= '<td class="cells colNormal fila filaArticulo">' . $key->exento  . '</td>
                            <td class="cells colNormal fila filaArticulo">' . $iva4  . '</td>
                            <td class="cells colNormal fila filaArticulo">' . $key->iva10 . '</td>
                            <td class="cells colNormal fila filaArticulo">' . $key->iva21 . '</td>';
                }
                $clase = '';
                $cobro = '';
                if ($key->estado == 1) {
                    $clase = 'pendiente';
                    $cobro = $key->Cobro;
                }else if ($key->estado == 2) {
                    $clase = 'cobrado';
                    $cobro = $key->Cobro;
                }else if ($key->estado == 3){
                    $clase = 'cobradoparcial';
                    $cobro = 'C. Parc.';
                }
                $fila .=   '<td class="cells colNormal fila filaArticulo"><div class="'.$clase.'">' . $cobro . '</div></td>
                        <td class="tdBotones botones">
                            <div class="d-flex">
                                <input type="checkbox" name="check_albaran" class="mr-2">
                                <a class="btn btn-warning mr-1 botonTablaAjax exportarPdf" title="Imprimir">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a class="btn btn-success mr-1 botonTablaAjax facturarVenta" title="Facturar">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </a>
                                <a class="btn btn-primary mr-1 botonTablaAjax enviarEmail" data-modal="enviarPedido" title="email">
                                    <i class="far fa-envelope"></i>
                                </a>
                                <a class="btn btn-danger mr-1 botonTablaAjax cobrarVenta" title="cobrar">
                                    <i class="fas fa-euro-sign iconoGenerico"></i>
                                </a>
                                <a class="btn btn-dark mr-1 botonTablaAjax devolucion" title="devolver">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>';
            
            $html .= $fila;
        }
        return $html;

    }

    public function construyoFilasTablaFiltrada($detalles, $tipoIva)
    {

        $html = '';
         
        foreach ($detalles as $key) {            
            
            $fila = '';
            $fila .= '<tr class="rows">
                        <td nowrap class="cells colAngosta fila filaArticulo">' . $key->numalbcompleto . '</td>
                        <td class="cells colAngosta fila filaArticulo">' . $key->Fecha . '</td>                     
                        <td class="cells colAngosta fila filaArticulo">' . $key->Cliente . '</td>
                        <td  nowrap class="cells colCliente fila filaArticulo">' . $key->Nombre . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->formadepago . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->Factura . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->Total . '</td>';
                
                        
                            $arrIvas = [0, 4,10,21];
                            for ($i=0; $i < count($arrIvas); $i++) {
                        
                                if ($tipoIva) {
                                    if ($tipoIva == $arrIvas[$i]) {
                                        $fila .= '<td class="cells colNormal fila filaArticulo">' . $key->bimp . '</td>';
                                    } else {
                                        $fila .= '<td class="cells colNormal fila filaArticulo">0.00</td>';
                                    }
                                }else{
                                    $fila .= '<td class="cells colNormal fila filaArticulo">0.00</td>';
                                }
                            }
                                       
                
                $fila .=   '<td class="cells colNormal fila filaArticulo">' . $key->Cobro . '</td>
                        <td class="cells colNormal fila filaArticulo">' . $key->Imp . '</td>                        
                        <td class="tdBotones botones">
                            <div class="d-flex">
                                <input type="checkbox" name="check_albaran" class="mr-2">
                                <a class="btn btn-warning mr-1 botonTablaAjax exportarPdf" title="Imprimir">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a class="btn btn-success mr-1 botonTablaAjax facturarVenta" title="Facturar">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </a>
                                <a class="btn btn-primary mr-1 botonTablaAjax enviarEmail" data-modal="enviarPedido" title="email">
                                    <i class="far fa-envelope"></i>
                                </a>
                                <a class="btn btn-danger mr-1 botonTablaAjax cobrarVenta" title="cobrar">
                                    <i class="fas fa-euro-sign iconoGenerico"></i>
                                </a>
                                <a class="btn btn-dark mr-1 botonTablaAjax devolucion" title="devolver">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>';
            
            $html .= $fila;
        }

        return $html;

    }



    //para la vista del administrador
    public function tablaAlbaranesSalidaScrollInfinitoAdmin($origen='')
    {    
        if (isset($_POST['offset'])) {
            $offset = $_POST['offset'];
        } else {
            $offset = 0;
        }

        if (isset($_POST['num'])) {
            $limit = $_POST['num'];
        } else {
            $limit = 25;
        }

        $buscar = '';
        if (isset($_POST['buscar'])) {
            $buscar = $_POST['buscar'];
        }
        $campo = '';
        if (isset($_POST['campo'])) {
            $campo = $_POST['campo'];
        }

        /*
        if (isset($_POST['rolUsuario'])) {
            $rolUsuario = $_POST['rolUsuario'];
        }*/

        /*
        $activarFiltro = '';
        if ($_POST['activarFiltro'] && $_POST['activarFiltro']==1) {

            $condCompleta = "";
            $activarFiltro = $_POST['activarFiltro'];
            $fechaFinal = $_POST['fechaFinal'];
            $fechaInicial = $_POST['fechaInicial'];

            $condFecha = " AND sal.fecha BETWEEN '$fechaInicial' AND '$fechaFinal' "; 

            if ($_POST['idCliente'] && $_POST['idCliente'] >0) {
                $condCliente = " AND sal.idcliente =". $_POST['idCliente'];
            }
            if ($_POST['estFactura'] && $_POST['estFactura'] >0) {
                $condEstFact = " AND sal.estadofactura =". $_POST['estFactura'];
            }
            if ($_POST['estCobro'] && $_POST['estCobro'] >0) {
                $condEstCobro = " AND sal.estado =". $_POST['estCobro'];
            }
            if ($_POST['tipoIva'] && $_POST['tipoIva'] >0) {
                $tipoIva = " AND mov.iva =". $_POST['tipoIva'];
            }
            $condCompleta = $condFecha . $condCliente . $condEstFact . $condEstCobro . $tipoIva;
        
        }
        */

        $detalles = [];
        $tipoLista = '';
        $html = '';

        

        /*if ($_SESSION['usuario'] == 'Admin' ){

            $detalles = $this->modeloSalidas->consultaDatosSalidasScrollParaUsuarioAdmin($offset, $limit, $buscar, $campo);
            $tipoLista = 'Admin';
            $html = $this-> construyoFilasTablaSalidasScrollInfinito($detalles, $tipoLista);

        }else if ($activarFiltro ==1) {
        
            $detalles = $this->modeloSalidas->consultaDatosSalidasScrollParaUsuarioAdminFiltrado($offset, $limit, $activarFiltro, $condCompleta);
            //$tipoLista = 'Admin';
            
            $html = $this->construyoFilasTablaFiltrada($detalles, $_POST['tipoIva']);
        }else{*/
          
            $detalles = $this->modeloSalidas->consultaDatosSalidasScrollParaUsuarioAdmin($offset, $limit, $buscar, $campo);   
            $html = $this-> construyoFilasTablaSalidasScrollInfinitoAdmin($detalles);
        //}        
                
        if($origen == 'index'){           
            return $html;
        }else{
            print($html);
        }        
    }

    public function construyoFilasTablaSalidasScrollInfinitoAdmin($detalles)
    {
        $html = '';
        
        foreach ($detalles as $key) {            
            
            $fila = '';
            $fila .= '<tr class="rows">
                        <td style="display: none;">' . $key->idcab . '</td>
                        <td nowrap class="cells fila filaArticulo">' . $key->numalbcompleto . '</td>
                        <td class="cells fila filaArticulo">' . $key->Fecha . '</td>                     
                        <td class="cells fila filaArticulo">' . $key->Cliente . '</td>
                        <td  nowrap class="cells fila filaArticulo">' . $key->Nombre . '</td>
                        <td class="cells fila filaArticulo">' . $key->formadepago . '</td>
                        <td class="cells fila filaArticulo">' . $key->Factura . '</td>
                        <td class="cells fila filaArticulo">' . $key->total . '</td>';

                    $fila .= '
                            <td class="cells fila filaArticulo">' . $key->iva4 . '</td>
                            <td class="cells fila filaArticulo">' . $key->iva10 . '</td>
                            <td class="cells fila filaArticulo">' . $key->iva21 . '</td>';
                
                    $clase = '';
                    $cobro = '';
                    if ($key->estado == 1) {
                        $clase = 'pendiente';
                        $cobro = $key->Cobro;
                    }else if ($key->estado == 2) {
                        $clase = 'cobrado';
                        $cobro = $key->Cobro;
                    }else if ($key->estado == 3){
                        $clase = 'cobradoparcial';
                        $cobro = 'C. Parc.';
                    }

                $fila .=   '<td class="cells fila filaArticulo"><div class="'.$clase.'">' . $cobro . '</div></td>
                            <td class="tdBotones botones">
                                <div class="d-flex">
                                    <input type="checkbox" name="check_albaran" class="mr-2">
                                    <a class="btn btn-warning mr-1 botonTablaAjax exportarPdf" title="Imprimir">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a class="btn btn-success mr-1 botonTablaAjax facturarVenta" title="Facturar">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
                                    <a class="btn btn-primary mr-1 botonTablaAjax enviarEmail" data-modal="enviarPedido" title="email">
                                        <i class="far fa-envelope"></i>
                                    </a>
                                    <a class="btn btn-danger mr-1 botonTablaAjax cobrarVenta" title="cobrar">
                                        <i class="fas fa-euro-sign iconoGenerico"></i>
                                    </a>
                                    <a class="btn btn-dark mr-1 botonTablaAjax devolucion" title="devolver">
                                        <i class="fas fa-undo-alt"></i>
                                    </a>
                                </div>
                            </td>
                </tr>';
            
            $html .= $fila;
        }
        return $html;

    }


    public function crearTabla()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $buscar = $_POST['busqueda'];
            $filas = $_POST['filas'];
            $pagina = $_POST['pagina'];
            $orden = $_POST['orden'];
            $tipoOrden = $_POST['tipoOrden'];
        }
        print_r(json_encode($this->modeloSalidas->consultadatos($buscar, $orden, $filas, $pagina, $tipoOrden)));
    }

    public function totalRegistros()
    {        
        $contador = $this->modeloSalidas->totalregistros();
        $cont = $contador->contador;        
        print($cont);
    }

    public function crearTablaSalida($idcab)
    {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            print_r(json_encode($this->modeloSalidas->consultadatossalida($idcab)));
        }
    }

    public function agregarLineaGrilla()
    {

        if ($_POST['filaOrden']) {
            $orden = $_POST['filaOrden'];

            $lineaGrilla =
                '<tr class="thead-light">                        
                        <td><input class="inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrden_' . $orden . '" value="' . $orden . '" readonly></td>

                        <td class="d-flex">
                            <input type="text" name="idArticulo[]" id="idArticulo_' . $orden . '" class="inputGrillaArticulo inputclick dblClickInput inputReadCodeBar" autocomplete="off">
                            <a class="btn-secondary btnBuscarArticulo px-1" data-toggle="modal" data-target="#buscadorArticulosModal" title="Buscar">
                                <i class="fas fa-search text-white"></i>
                            </a>
                            <input type="hidden" name="loteObligatorio[]" id="loteObligatorio_' . $orden . '" class="loteObligatorio">
                            <input type="hidden" name="carnetObligatorio[]" id="carnetObligatorio_' . $orden . '" class="carnetObligatorio">
                        </td>
                        <td class="celdaDescripcion">
                            <input type="text" name="descripcion[]" id="descripcion_' . $orden . '" class="inputGrillaDescripcion inputclick dblClickInput"></td>
                        <td>
                            <input type="number" step="0.01" class="inputGrillaAuto cantidad inputclick text-right dblClickInput" step="0.01" name="cantidadArticulo[]" id="cantidadArticulo_' . $orden . '" value="0"></td>
                        <td>
                            <input type="number" step="0.01" class="inputGrillaAuto precio inputclick text-right dblClickInput" name="precioArticulo[]" id="precioArticulo_' . $orden . '" value="0"></td>
                        <td>
                            <input type="number" step="0.01" class="inputGrillaAuto descuento inputclick text-right dblClickInput" step="0.01" name="descuento[]" id="descuento_' . $orden . '" value="0"></td>
                        <td>
                            <input type="number" class="inputGrillaAuto totalLinea inputclick text-right dblClickInput" step="0.01" name="totalLinea[]" id="totalLinea_' . $orden . '" value="0" readonly></td>                    
                        <td class="lineaIva">
                            <select class="inputGrillaAuto iva" name="iva[]" id="iva_' . $orden . '">
                                <option value="21">21</option>
                                <option value="10">10</option>
                                <option value="4">4</option>
                                <option value="0">0</option>
                            </select></td>
                        <td class="d-flex justify-content-center">
                            <a class="btn btn-danger px-1 py-0 mx-1 botonTablaAjax btnDeleteLinea" title="quitar" style="font-size: 0.75rem;">
                            <i class="fas fa-trash-alt" style="color:white;"></i></a>
                            <a class="btn btn-primary px-1 py-0 mx-1 botonTablaAjax btnVerFichaArticulo" title="ver ficha" style="font-size: 0.75rem;">
                            <i class="fas fa-eye" style="color:white;"></i></a>
                        </td>
                    </tr>';

            echo $lineaGrilla;
        } else {
            echo '';
        }
    }

    
    public function agregarLineaGrillaEdicion()
    {

        if ($_POST['filaOrden']) {
            $orden = $_POST['filaOrden'];

            $lineaGrilla =
                '<tr class="thead-light">                        
                        <td><input class="inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrden_' . $orden . '" value="' . $orden . '" readonly></td>

                        <td class="d-flex">
                            <input type="text" name="idArticulo[]" id="idArticulo_' . $orden . '" class="inputGrillaArticulo inputclick dblClickInput inputReadCodeBar" autocomplete="off">
                            <a class="btn-secondary btnBuscarArticulo px-1" data-toggle="modal" data-target="#buscadorArticulosModal" title="Buscar">
                                <i class="fas fa-search text-white"></i>
                            </a>
                            <input type="hidden" name="loteObligatorio[]" id="loteObligatorio_' . $orden . '" class="loteObligatorio">
                            <input type="hidden" name="carnetObligatorio[]" id="carnetObligatorio_' . $orden . '" class="carnetObligatorio">
                        </td>
                        <td class="celdaDescripcion">
                            <input type="text" name="descripcion[]" id="descripcion_' . $orden . '" class="inputGrillaDescripcion inputclick dblClickInput"></td>
                        <td>
                            <input type="number" class="inputGrillaAuto cantidad inputclick text-right dblClickInput" step="0.01" name="cantidadArticulo[]" id="cantidadArticulo_' . $orden . '" value="0"></td>
                        <td>
                            <input type="text" class="inputGrillaAuto precio inputclick text-right dblClickInput" name="precioArticulo[]" id="precioArticulo_' . $orden . '" value="0"></td>
                        <td>
                            <input type="number" class="inputGrillaAuto descuento inputclick text-right dblClickInput" step="0.01" name="descuento[]" id="descuento_' . $orden . '" value="0"></td>
                        <td>
                            <input type="number" class="inputGrillaAuto totalLinea inputclick text-right dblClickInput" step="0.01" name="totalLinea[]" id="totalLinea_' . $orden . '" value="0" readonly></td>                    
                        <td class="lineaIva">
                            <select class="inputGrillaAuto iva" name="iva[]" id="iva_' . $orden . '">                                
                                <option value="21">21</option>
                                <option value="10">10</option>
                                <option value="4">4</option>
                                <option value="0">0</option>
                            </select></td>
                        <td class="d-flex justify-content-center">
                            <a class="btn btn-danger px-1 py-0 mx-1 botonTablaAjax btnDeleteLinea" title="quitar" style="font-size: 0.75rem;">
                            <i class="fas fa-trash-alt" style="color:white;"></i></a>
                            <a class="btn btn-primary px-1 py-0 mx-1 botonTablaAjax btnVerFichaArticulo" title="ver ficha" style="font-size: 0.75rem;">
                            <i class="fas fa-eye" style="color:white;"></i></a>
                        </td>
                    </tr>';

            echo $lineaGrilla;
        } else {
            echo '';
        }
    }


    public function crearFacturaVentaDesdeAlabaranes()
    {  
        session_start();
        
        /*
        echo"<br>crear factura<br>";
        print_r($_POST);
        die;
        */

        if (isset($_POST['facturarVentas'])) {
            //debe validar que todos los albaranes que vienen tienen estado pendiente de facturar
            //validaciones           

            $arrayFacturaNumeros = [];
            if ($_POST['clienteAFacturar'] && $_POST['clienteAFacturar'] > 0) {
                if ($_POST['fechaFacturar']) {
                    if (isset($_POST['numAlbaranSelect']) && count($_POST['numAlbaranSelect']) > 0) {
                        $albaranes = $_POST['numAlbaranSelect'];
                        $masivo = false;

                        //verifico que el estado de todos los albaranes sea "Pendiente"
                        $valid = $this->modeloSalidas->validarAlbaranesAFacturar($albaranes);
                        
                        if ($valid == 'true') {
                            
                            //formo una cadena con todos los idCab                                
                            $cadena = 'IN (';
                            $cont = 0;
                            foreach ($albaranes as $key) {
                                $cont++;
                                //$idCab = $this->modeloSalidas->obtenerIdCabeceraSalida($key);
                                if ($cont !=  (count($albaranes))) {
                                    $cadena .= $key . ",";
                                } else {
                                    $cadena .= $key . ")";
                                }
                            }
                            
                            //obtengo las líneas de todos los albaranes:
                            $lineas = $this->modeloSalidas->obtenerLineasAlbaranSalida($cadena);

                            $formaDepago = 4; //Otro
                            if (isset($_POST['formaDePagoParaFacturar']) && $_POST['formaDePagoParaFacturar'] != '') {
                                $formaDepago = $this->modeloSalidas->obtenerIdFormaDePagoPorNombre($_POST['formaDePagoParaFacturar']);
                            }

                            $datos = [
                                'cliente' => $_POST['clienteAFacturar'],
                                'fecha' => $_POST['fechaFacturar'],
                                'idsAlbaran' => $_POST['numAlbaranSelect'],
                                'observaciones' => $_POST['observaciones'],
                                'orden' => "fecha",
                                'todasLasLineas' => $lineas,
                                'condicion' => $cadena,
                                'formaDePago' => $formaDepago
                            ];                          

                            $factura = $this->facturarAlbaranesSeleccionados($datos, $masivo);

                            if ($factura && $factura > 0) {
                                $this->modeloSalidas->cambiarEstadoAlbaranPendienteFacturadoByIdCab($datos['idsAlbaran']);
                                $this->modeloSalidas->vincularAlbaranFacturaByIdCab($datos['idsAlbaran'], $factura);

                                foreach ($albaranes as $key) {
                                    $numeroFactura = $this->modeloSalidas->cogerNumeroFacturaByIdCab($key);
                                    array_push($arrayFacturaNumeros, $numeroFactura);
                                }
                                $this->modeloSalidas->updatearEstadoFacturaAlbaran($arrayFacturaNumeros);
                            
                                $numFacturaComp = $this->modeloSalidas->obtenerNumFacturaByIdFactura($factura);
                                $_SESSION['message'] = 'Se ha generado corréctamente la factura Nº ' . $numFacturaComp->numfaccompleto . ' para el(los) albaran(es) seleccionado(s).';
                            }
                        } else {
                            $_SESSION['message'] = 'No se puede facturar porque hay albaranes en estado Facturado.';
                        }
                    } else {
                        $_SESSION['message'] = 'No se puede facturar porque no ha seleccionado ningún albarán.';
                    }
                } else {
                    $_SESSION['message'] = 'No se puede facturar porque no ha seleccionado la fecha para la factura.';
                }
            } else {
                $_SESSION['message'] = 'No se puede facturar porque no ha seleccionado un cliente.';
            }
        } else {
            $_SESSION['message'] = 'Ha ocurrido un error y no se han facturado los albaranes seleccionados.';
        }

        redireccionar('/FacturasVentas');
    }

    
    public function facturarAlbaranesSeleccionados($datos, $masivo)
    {
        //1-crear cabecera de la factura
        $condicion = $datos['condicion'];
        $totales = $this->modeloSalidas->calcularTotalesVariosAlbaranes($condicion);        
        
        $insCabecera = $this->crearCabeceraFacturaVenta($totales, $datos,$masivo);
        
        //2-crear detalles de la factura
        if ($insCabecera > 0) {
            $this->modeloSalidas->vincularAlbaranFacturaByIdCab($datos['idsAlbaran'], $insCabecera);

            if (!$masivo) {
                $insLineas = $this->modeloSalidas->crearLineasFacturaVenta($datos, $insCabecera);
            } else {
                $insLineas = $this->crearLineasFacturaVentaMasivo($insCabecera);
            }
            if ($insLineas) {
                return $insCabecera;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function crearLineasFacturaVentaMasivo($insCabecera)
    {       
        $totales = $this->modeloSalidas->obtenerTotalesDeTodasLasLinesPorTipoDeIva($insCabecera);

        $numeroLineas = count($totales);
        $cont = 0;
        foreach ($totales as $key) {
            $ins = $this->modeloSalidas->crearLineasFacturaVentaMasivo($key,$insCabecera);
            if ($ins) {
                $cont++;
            }            
        }
        if ($cont == $numeroLineas) {
            return true;
        } else {
            return false;
        }
    }

    public function crearCabeceraFacturaVenta($totales, $datos,$masivo)
    {
        $serie = NUM_SERIE;     
        $datos['serie'] = $serie;

        //verifico el año del documento
        $numfactura = $this->generarNumFactura($datos['fecha']);
        $datos['numfactura'] = $numfactura;
        $largo = strlen($numfactura);

        if ($largo == 1) {
            $numFacCompleto = $serie . "-00000" . $numfactura;
        } else if ($largo == 2) {
            $numFacCompleto = $serie . "-0000" . $numfactura;
        } else if ($largo == 3) {
            $numFacCompleto = $serie . "-000" . $numfactura;
        } else if ($largo == 4) {
            $numFacCompleto = $serie . "-00" . $numfactura;
        } else if ($largo == 5) {
            $numFacCompleto = $serie . "-0" . $numfactura;
        } else {
            $numFacCompleto = $serie . "-" . $numfactura;
        }
        $datos['numFacCompleto'] = $numFacCompleto;        

        $albaranes = $datos['idsAlbaran'];
        
        $salida = '[';
        for ($i = 0; $i < count($albaranes); $i++) {
            if ($i != (count($albaranes) - 1)) {
                $salida .=  $albaranes[$i] . ",";
            } else {
                $salida .=  $albaranes[$i] . "]";
            }
        }
       
        $datos['albaranes'] = $salida;  
        $datos['creacion'] = date("Y-m-d H i s");      
        
        $ins = $this->modeloSalidas->crearCabeceraFacturaVenta($totales, $datos,$masivo);
        return $ins;
    }

    public function generarNumFactura($fechaInicio)
    {
        $ultimoAnio = $this->modeloSalidas->obtenerAnioDeUltimaFechaCabeceraFactVentas();
        $anioComparar = date('Y');
        if (isset($ultimoAnio) && $ultimoAnio > 0) {
            $anioComparar = $ultimoAnio;
        }
        $partes = explode('-',$fechaInicio);
        $anio = $partes[0];

        //1- si el año fecha que llega >= fecha de la bd, tomo el último códigoProyecto del año en cuestión y le sumo 1
        if ($anio >= $anioComparar) {
            $numFactura = $this->modeloSalidas->obtenerNumFacturaCorrelativo($anio);
            if (isset($numFactura) && $numFactura > 0) {
                $retorno = $numFactura + 1;
            }else{
                $retorno =   1;
            }
        }else {            
            //2- si el año fecha que llega > fecha de la bd, entonces reiniciar el codigoProyecto a 1
            $numFactura = 1; //se reinicia con 1
        }        
        return $retorno;

    }

    public function crearFacturaVentaDesdeAlabaranesMasivo()
    {      
        var_dump($_POST);
        die;

        session_start();
        if (isset($_POST['facturarVentasMasivo'])) {
            //debe validar que todos los albaranes que vienen tienen estado pendiente de facturar
            //validaciones   
            $arrayFacturaNumeros = [];

            if ($_POST['fechaFacturar']) {
                if (count($_POST['numAlbaranSelect']) > 0) {
                    $albaranes = $_POST['numAlbaranSelect'];
                    $masivo = true;

                    //verifico que el estado de todos los albaranes sea "Pendiente"
                    $valid = $this->modeloSalidas->validarAlbaranesAFacturar($albaranes);
                    if ($valid == 'true') {

                        //formo una cadena con todos los idCab                                
                        $cadena = 'IN (';
                        $cont = 0;
                        foreach ($albaranes as $key) {
                            $cont++;
                            //$idCab = $this->modeloSalidas->obtenerIdCabeceraSalida($key);
                            if ($cont !=  (count($albaranes))) {
                                $cadena .= $key . ",";
                            } else {
                                $cadena .= $key . ")";
                            }
                        }

                        //obtengo las líneas de todos los albaranes:
                        $lineas = $this->modeloSalidas->obtenerLineasAlbaranSalida($cadena);
                        
                        $datos = [
                            'cliente' => $_POST['clienteAFacturar'],
                            'fecha' => $_POST['fechaFacturar'],
                            'idsAlbaran' => $_POST['numAlbaranSelect'],
                            'observaciones' => $_POST['observaciones'],
                            'orden' => $_POST['orden'],
                            'todasLasLineas' => $lineas,
                            'condicion' => $cadena
                        ];

                        $factura = $this->facturarAlbaranesSeleccionados($datos, $masivo);
                        if ($factura && $factura > 0) {
                            $this->modeloSalidas->cambiarEstadoAlbaranPendienteFacturadoByIdCab($datos['idsAlbaran']);
                            foreach ($albaranes as $key) {
                                $numeroFactura = $this->modeloSalidas->cogerNumeroFacturaByIdCab($key);
                                array_push($arrayFacturaNumeros, $numeroFactura);
                            }
                            $this->modeloSalidas->updatearEstadoFacturaAlbaran($arrayFacturaNumeros);
                        }

                        if ($factura > 0) {
                            $numFacturaComp = $this->modeloSalidas->obtenerNumFacturaByIdFactura($factura);
                            $_SESSION['message'] = 'Se ha generado corréctamente la factura Nº ' . $numFacturaComp->numfaccompleto . ' el(los) albaran(es) seleccionado(s).';
                        }
                    } else {
                        $_SESSION['message'] = 'No se puede facturar porque hay albaranes en estado Facturado.';
                    }
                } else {
                    $_SESSION['message'] = 'No se puede facturar porque no ha seleccionado ningún albarán.';
                }
            } else {
                $_SESSION['message'] = 'No se puede facturar porque no ha seleccionado la fecha para la factura.';
            }
        } else {
            $_SESSION['message'] = 'Ha ocurrido un error y no se han facturado los albaranes seleccionados.';
        }

        redireccionar('/FacturasVentas');
    }

    public function obtenerTotalesDeAlbaranPorIdFactura()
    {
        if ($_POST['idcab']) {
            $idcab = $_POST['idcab'];
            //$totales = $this->modeloSalidas->albaranSalidaCabecera($idcab);
            $totales = $this->modeloSalidas->albaranTotalesSalidaCabecera($idcab);

            if ($totales) {
                $retorno = $totales;
            } else {
                $retorno = '';
            }
        } else {
            $retorno = '';
        }

        print_r(json_encode($retorno));
    }

    public function exportarAlbaranSalida($id)
    {

        $datosFactura = $this->modeloSalidas->exportarAlbaranSalida($id);

        generarPdf::documentoPDF('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'albaranModelo.php', $datosFactura);
    }

    public function obtenerEmailClienteByNumAlbaran()
    {
        $correo = '';
        $email = $this->modeloSalidas->obtenerEmailClienteByNumAlbaran($_POST['idAlbaran']);
        if (isset($email->email)) {
            $correo = $email->email;
        }
        echo $correo;
    }
   
    public function obtenerImportePendienteByNumAlbaran()
    {
        $importe = $this->modeloSalidas->obtenerImportePendienteByNumAlbaran($_POST['numAlbaran']);

        $historialCobros = $this->modeloSalidas->obtenerHistorialCobrosByNumAlbaran($_POST['numAlbaran']);
        $tabla = '<tr class="thead-light">
        <th>Fecha cobro</th>
        <th>||</th>
        <th>Importe</th>
        <th>Forma Pago</th>
        <th>Concepto</th>
        <th>Observaciones</th>
        </tr>';
        foreach ($historialCobros as $key) {
            $tabla .=
                '<tr class="thead-light">
                <td>' . $key->fechaCobro . '</td>
                <td>||</td>
                <td>' . $key->totalCobro . '</td>
                <td>' . $key->formadepago . '</td>
                <td>' . $key->concepto . '</td>
                <td>' . $key->observaciones . '</td>
                </tr>';
        }
        $datos = ["importe" => round($importe,2), "tabla" => $tabla];
        echo json_encode($datos);
    }

    public function obtenerImportesIndividualesByListAlbaran()
    {
        $listAlbaran = $_POST;
        $listadoNumeros = "";
        $listadoIds = "";
        $importe = 0;
        $tabla = '<tr class="thead-light">
        <th>Fecha cobro</th>
        <th>||</th>
        <th>Importe</th>
        <th>Forma Pago</th>
        <th>Concepto</th>
        <th>Observaciones</th>
        </tr>';

        foreach ($listAlbaran['idsAlbaranes'] as $idAlbaran) {
            
            $listadoIds .= $idAlbaran . " ";
            $numAlbaranSalida = $this->modeloSalidas->obtenerDatosAlbaranById($idAlbaran);
            $listadoNumeros .= $numAlbaranSalida->numalbcompleto . " ";
            $facturar = $this->modeloSalidas->validarAlbaranACobrar($idAlbaran);
            if ($facturar) {
                $importe += $this->modeloSalidas->obtenerImportePendienteByNumAlbaran($idAlbaran);
                $historialCobros = $this->modeloSalidas->obtenerHistorialCobrosByNumAlbaran($idAlbaran);
                if (isset($historialCobros) && count($historialCobros) >0) {
                    foreach ($historialCobros as $key) {
                        $tabla .=
                            '<tr class="thead-light">
                         <td>' . $key->fechaCobro . '</td>
                        <td>||</td>
                        <td>' . $key->totalCobro . '</td>
                        <td>' . $key->formadepago . '</td>
                        <td>' . $key->concepto . '</td>
                        <td>' . $key->observaciones . '</td>
                        </tr>';
                    }
                }

            }
        }
        $datos = ["listadoNumeros" => $listadoNumeros, "listadoIds" => $listadoIds, "importe" => round($importe,2) , "tabla" => $tabla];
        echo json_encode($datos);
    }

    public function insertarCobroAlbaranGeneral()
    {        
        
        $listaIdsAlbaranes = explode(" ", $_POST['idsAlbaran'], -1);      
        $arrayFacturaNumeros = [];
        
        $cont1 = 0;
        $cont2 = 0;

        /*
        var_dump($_POST);
        print_r($listaIdsAlbaranes);        
        die;
      */
        
        foreach ($listaIdsAlbaranes as $idAlbaran) {

            $facturar = $this->modeloSalidas->validarAlbaranACobrar($idAlbaran);
            $importeCobrar = $this->modeloSalidas->obtenerImportePendienteByNumAlbaran($idAlbaran);
            if ($facturar && $importeCobrar >0) {
                $cont1++; 
                $nuevaArrayList = array(
                    "idAlbaran" => $idAlbaran,
                    "fechaCobro" => $_POST['fechaCobro'],
                    "importeCobrado" => $importeCobrar,
                    "conceptoCobro" => $_POST['conceptoCobro'],
                    "contrapartida" => $_POST['contrapartida'],
                    "observaciones" => $_POST['observaciones']
                );
                
                $ins = $this->modeloSalidas->insertarCobroAlbaran($nuevaArrayList);
                if ($ins == 1) {  
                    $cont2++;                   
                    $numeroFactura = $this->modeloSalidas->cogerNumeroFacturaByIdCab($nuevaArrayList['idAlbaran']);
                    if (isset($numeroFactura) && $numeroFactura != '') {
                        array_push($arrayFacturaNumeros, $numeroFactura);
                    }  
                }
                            
            }
        }
        if (count($arrayFacturaNumeros) >0) {
            $this->modeloSalidas->updatearEstadoFacturaAlbaran($arrayFacturaNumeros);    
        }
        if ($cont1 == $cont2) {
            $retorno = 1;    
        }   

        /*
        echo"<br>post<br>";
        var_dump($_POST);
        echo"<br><br>";
        print_r($arrayFacturaNumeros);     
        echo"<br><br>";
        print_r($cont1);
        echo"<br><br>";
        print_r($cont2);
        die;*/
        echo $retorno;
    }


    public function insertarCobroAlbaran()
    {
        $retorno = 0;

        $arrayFacturaNumeros = [];
        $ins = $this->modeloSalidas->insertarCobroAlbaran($_POST);
        
        if ($ins == 1) {
            $retorno = 1;
            $numeroFactura = $this->modeloSalidas->cogerNumeroFacturaByIdCab($_POST['idAlbaran']);
            if (isset($numeroFactura)) {
                array_push($arrayFacturaNumeros, $numeroFactura);
                $this->modeloSalidas->updatearEstadoFacturaAlbaran($arrayFacturaNumeros);
            }
        }
        echo $retorno;

    }

    public function generarPdfAlbaran($idAlbaran, $option, $enviar = '')
    {
        //if ($_GET) {
        //$idAlbaran = $this->modeloSalidas->obtenersIdCabByNumAlbaran($numAlbaran);
        $datosAlbaran = $this->modeloSalidas->obtenerDatosAlbaranById($idAlbaran);
        $idCliente = $this->modeloSalidas->obtenersClienteIdByIdAlbaran($idAlbaran);
        $datosCliente = $this->modeloSalidas->obtenerDatosClienteById($idCliente);
        $formasDePago = $this->modeloSalidas->obtenerFormaDePago($idAlbaran);
        $lineasAlbaran = $this->modeloSalidas->obtenerlineasAlbaranById($idAlbaran); //controlar que es de salida

        $pdf = new AlbaranPDF();

        $pdf->verImagen(RUTA_URL . "/public/img/ModeloAlbaran.png");

        $pdf->verDatosCliente($datosCliente, $option);
        $pdf->verDatosAlbaran($datosAlbaran, $formasDePago);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        //$pdf->SetMargins(20, 20);
        $pdf->SetAutoPageBreak(true, 60);
        $pdf->SetFont('Times', '', 12);
        $pdf->AddFont("courierb", "", "courierb.php");

        foreach ($lineasAlbaran as $linea) {
            $lotes = json_decode($linea->lotes);
            $loteformateado = "";
            if (!empty($lotes)) {
                $loteformateado = "(";

                foreach ($lotes as $lote) {
                    $loteformateado .= "lote:" . $lote->numeroLote . "->cant:" . $lote->cantidadLote . " ";
                }
                $loteformateado .= ")";
            }

            $pdf->Ln(12);

            $pdf->SetFont('Arial', '', 8);
            //$articulo = $this->modeloSalidas->obtenerNombreArticulosById($linea->idarticulo);
            $pdf->Cell(30, 10, $linea->idarticulo,  0, 0, 'L');
            $pdf->Cell(1);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(50, 8, utf8_decode($linea->descripcion) . "\n" . $loteformateado,  0, 'L');
            $pdf->SetXY($x + 60, $y);
            $option != 3 ? $pdf->Cell(30, 10, round($linea->cantidad, 2),  0, 0, 'L') : "";
            $pdf->Cell(3);
            //$pdf->Cell(30, 10, round($linea->iva, 2). '%',  0, 0, 'L');
            $pdf->Cell(-7);
            $option != 3 ? $pdf->Cell(30, 10, number_format($linea->precioventa, 2, ',', '.'),  0, 0, 'L') : "";
            $pdf->Cell(-7);
            $subtotal = $linea->cantidad * $linea->precioventa;
            $option != 3 ? $pdf->Cell(30, 10, number_format($subtotal, 2, ',', '.'),  0, 0, 'L') : "";
            $pdf->Cell(-7);
            $option != 3 ? $pdf->Cell(15, 10, round($linea->descuento, 2) . '%',  0, 0, 'L') : "";
            $pdf->Cell(-14);
            $option != 3 ? $pdf->Cell(30, 10, number_format($linea->total, 2, ',', '.'),  0, 1, 'R') : "";
        }

        if ($enviar == 'S') {
            $doc = $pdf->Output("S", "");
        } else {
            $doc = $pdf->Output();
        }

        return $doc;


        //}
    }


    public function generarPdfAlbaranSinGuardar($form = "", $option = "", $numero = "", $enviar = "")
    {
        $datosCliente = $this->modeloSalidas->obtenerDatosClienteById($_GET['idCliente']);
        $formasDePago = $this->modeloSalidas->obtenerFormaDePagoByidFormadePago($_GET['formadepago']);
        $datosAlbaran = new stdClass;
        $datosAlbaran->numalbcompleto = $_GET['serie'] . "-" . $_GET['idcab'];
        $datosAlbaran->fecha = $_GET['fecha'];
        $datosAlbaran->tipo = $_GET['tipo'];
        $datosAlbaran->baseimponible = $_GET['totalBaseImponible'];
        $datosAlbaran->valordescuento = $_GET['totalValorDescuento'];
        $datosAlbaran->valoriva = $_GET['totalValorIva'];
        $datosAlbaran->valortotal = $_GET['totalBaseImponible'];

        $pdf = new AlbaranPDF();
        $pdf->verImagen(RUTA_URL . "/public/img/ModeloAlbaran.png");

        $pdf->verDatosCliente($datosCliente, $option);
        $pdf->verDatosAlbaran($datosAlbaran, $formasDePago);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        //$pdf->SetMargins(20, 20);
        $pdf->SetAutoPageBreak(true, 60);
        $pdf->SetFont('Times', '', 12);
        $pdf->AddFont("courierb", "", "courierb.php");
        $numeroLineas = count($_GET['idArticulo']);

        for ($i = 0; $i < $numeroLineas; $i++) {
            $pdf->Ln(12);

            $pdf->SetFont('Arial', '', 8);

            $pdf->Cell(30, 10, $_GET['idArticulo'][$i],  0, 0, 'L');
            $pdf->Cell(1);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $lote = "";

            if(isset($_GET['numFilaArticulo'])){
                for ($index = 0; $index < count($_GET['numFilaArticulo']); $index++) {
                    if ($_GET['numFilaArticulo'][$index] == $i + 1) {
                        $lote .="(". $_GET['numeroLote'][$index] . "->cant:" . $_GET['cantidadLote'][$index] . " )";
                    }
                }
            }


            $pdf->MultiCell(50, 10, $_GET['descripcion'][$i] . "\n" . $lote,  0, 'L');
            $pdf->SetXY($x + 60, $y);
            $option != 3 ? $pdf->Cell(30, 10, round($_GET['cantidadArticulo'][$i], 2),  0, 0, 'L') : "";
            $pdf->Cell(3);
            $pdf->Cell(-7);
            $option != 3 ? $pdf->Cell(30, 10, number_format($_GET['precioArticulo'][$i], 2, ',', '.'),  0, 0, 'L') : "";
            $pdf->Cell(-7);
            $subtotal = $_GET['cantidadArticulo'][$i] * $_GET['precioArticulo'][$i];
            $option != 3 ? $pdf->Cell(30, 10, number_format($subtotal, 2, ',', '.'),  0, 0, 'L') : "";
            $pdf->Cell(-7);
            $option != 3 ? $pdf->Cell(15, 10, round($_GET['descuento'][$i], 2) . '%',  0, 0, 'L') : "";
            $pdf->Cell(-14);
            $option != 3 ? $pdf->Cell(30, 10, number_format($_GET['totalLinea'][$i], 2, ',', '.'),  0, 1, 'R') : "";
        }

        if ($enviar == 'S') {
            $doc = $pdf->Output("S", "");
        } else {
            $doc = $pdf->Output();
        }

        return $doc;
    }


    public function crearRegistroSalida()
    {                              
        session_start();

        if (isset($_POST['guardar']) || isset($_POST['guardarSeguir']) || isset($_POST['guardarNuevo']) || isset($_POST['guardarCobrar'])) {
        
            //datos de las líneas
            $numerosOrden = $_POST['numeroOrden'];
            $articulos = $_POST['idArticulo'];
            $descripcion = $_POST['descripcion'];
            $cantidadArticulo = $_POST['cantidadArticulo'];
            $precioArticulo = $_POST['precioArticulo'];
            $descuento = $_POST['descuento'];
            $iva = $_POST['iva'];
            
            
            if ($articulos != '' && count($articulos) > 0) {
                $tmp = [];
                $lineas = [];

                $totBImpNuevosArt = 0;
                $totDsctoNuevosArt = 0;
                $totIvaNuevosArt = 0;
                $totNuevosArt = 0;

                //construyo array de lotes si hubiesen
                if (isset($_POST['numFilaArticulo'])) {
                    $arrLotes = $_POST['numFilaArticulo'];
                    $arrNumerosLote = $_POST['numeroLote'];
                    $arrCantidadesLote = $_POST['cantidadLote'];

                    $tmpLotes = [];
                    $lineasLotes = [];
                    for ($j = 0; $j < count($arrLotes); $j++) {

                        $tmpLotes['numFilaArticulo'] = $arrLotes[$j];                    
                        $tmpLotes['numeroLote'] = $arrNumerosLote[$j];
                        $tmpLotes['cantidadLote'] = $arrCantidadesLote[$j];

                        $lineasLotes[] = $tmpLotes;
                    }
                }

                //construyo array de datos de cada artículo
                $ordenFila = 1;
                for ($i = 0; $i < count($articulos); $i++) {

                    $tmp['ordenfila'] = $ordenFila++;
                    //coste medio ponderado actual (cmp)
                    $codigoArticulo = trim($articulos[$i]);
                    $codigoExiste = $this->modeloSalidas->buscarCodigoArticulo($codigoArticulo);
                    $cmpActual = 0;
                    $existe = 0;
                    if (isset($codigoExiste->codigoarticulo) && $codigoExiste->codigoarticulo != '') {
                        $cmpActual = $this->modeloSalidas->obtenerCosteMedioPonderadoActual($codigoArticulo);
                        $existe = 1;
                    }                    
                    $tmp['cmpActual'] = $cmpActual;
                    $tmp['numeroOrden'] = $numerosOrden[$i];
                    $tmp['idArticulo'] = $codigoArticulo;
                    $tmp['descripcion'] = trim($descripcion[$i]);
                    $cantidadSalida = 0;
                    if (isset($cantidadArticulo[$i]) && trim($cantidadArticulo[$i]) != '') {
                        $cantidadSalida = trim($cantidadArticulo[$i]);
                    }
                    $tmp['cantidadArticulo'] = $cantidadSalida;
                    $tmp['precioArticulo'] = $cmpActual;

                    $precioSalida = 0;
                    if (isset($precioArticulo[$i]) && trim($precioArticulo[$i]) !='') {
                        $precioSalida = trim($precioArticulo[$i]);
                    }
                    $tmp['precioVenta'] = $precioSalida;

                    $dsctoSalida = 0;
                    if (isset($descuento[$i]) && trim($descuento[$i]) != '') {
                        $dsctoSalida = trim($descuento[$i]);
                    }                    
                    $tmp['descuento'] = $dsctoSalida;

                    $tmp['iva'] = $iva[$i];
                    $tmp['existe'] = $existe;

                    //calculo los totales por línea nueva
                    $bImpArt = $cantidadSalida * $precioSalida;
                    $valDsctoArt = $bImpArt * $dsctoSalida / 100;
                    $bImpDsctoArt = $bImpArt - $valDsctoArt;
                    $valIvaArt = $bImpDsctoArt * $iva[$i] / 100;
                    $valTotArt = $bImpDsctoArt + $valIvaArt;

                    //calculo los totales de todas las líneas nuevas                 
                    $tmp['totalLinea'] = $bImpDsctoArt; //total no incluye iva                    

                    $totBImpNuevosArt = $totBImpNuevosArt + $bImpArt;
                    $totDsctoNuevosArt = $totDsctoNuevosArt + $valDsctoArt;
                    $totIvaNuevosArt = $totIvaNuevosArt + $valIvaArt;
                    $totNuevosArt = $totNuevosArt + $valTotArt;

                    //construyo los lotes por cada artículo
                    if (isset($_POST['numFilaArticulo'])) {
                        $arr = [];
                        foreach ($lineasLotes as $k) {
                            if ($k['numFilaArticulo'] == $numerosOrden[$i]) {
                                $arr[] = $k;
                            }
                            $tmp['lotes'] = $arr;
                        }
                    }

                    $lineas[] = $tmp;
                }
            }

            $estado = 1; //pendiente por defecto

            //si llega cliente no habitual            
            if (isset($_POST['idClienteNoHabitual']) && $_POST['idClienteNoHabitual'] > 0) {
                $idCliente = 0;
                $idclinohabitual = $_POST['idClienteNoHabitual'];
            } else {
                $idCliente = $_POST['idCliente'];
                $idclinohabitual = 0;
            }
            
            $cabecera = [
                'tipo' => $_POST['tipo'],
                'serie' => $_POST['serie'],
                'fecha' => $_POST['fecha'],
                'formadepago' => $_POST['formadepago'],
                'estado' => $estado,
                'idCliente' => $idCliente,
                'direccion' => $_POST['direccion'],
                'almacen' => $_POST['almacen'],
                'agente' => $_POST['agente'],
                'totalBaseImponible' => $totBImpNuevosArt,
                'totalValorDescuento' => $totDsctoNuevosArt,
                'totalValorIva' => $totIvaNuevosArt,
                'totalDocumento' => $totNuevosArt,
                'totalCobrado' => $_POST['totalCobrado'],
                'totalPendienteCobro' => $_POST['totalPendienteCobro'],
                'numArticulos' => count($articulos),
                'idclinohabitual' => $idclinohabitual
            ];

            
            /*
            echo "llega al controlador";                    
            var_dump($_POST);            
            echo "<br>cabecera<br>";
            var_dump($cabecera);
            echo "<br>lineas<br>";
            var_dump($lineas);
            die;
            */
            
            $newSalida = new DescargarStock();                    
            $ins = $newSalida->crearRegistroSalidaCabeceraYLineas($cabecera, $lineas);

            if (isset($ins) && $ins > 0 && $ins != '') {
                $_SESSION['message'] = 'Registro guardado corréctamente.';

                if (isset($_POST['guardar'])) {
                    redireccionar('/Salidas');
                } else if (isset($_POST['guardarSeguir'])) {
                    //$numAlbIns = $this->modeloSalidas->obtenerNumAlbCompletoPorIdCab($ins);
                    redireccionar('/Salidas/V/' . $ins);
                } else if (isset($_POST['guardarNuevo'])) {
                    redireccionar('/Salidas/N');
                }
            } else {
                $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha guardado.';
                redireccionar('/Salidas');
            }

            //}

        } else {
            $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha guardado.';
            redireccionar('/Salidas');
        }
    }

    /*
    public function crearRegistroSalidaCabeceraYLineas($cabecera, $lineas)
    {
        //datos de los totales para la cabecera:
        $insCabecera = $this->crearCabeceraSalida($cabecera);
        
        if ($insCabecera > 0) {

            if ($cabecera['tipo'] == 'albaranSalida') {
                $tipo = 'SALIDA';
                $subtipo = 'ALBARAN';
            }
    
            $cont = 0;
            foreach ($lineas as $key) { 
                
                $lotes = [];
                if (isset($key['lotes']) && count($key['lotes']) >0) {
                    $lotes = $key['lotes'];
                }
                $lotesJson = json_encode($lotes);

                $insLinea = $this->modeloSalidas->crearLineasSalida($insCabecera, $key, $cabecera, $lotesJson, $tipo, $subtipo);

                if ($insLinea) {
                    $cont++;
                    
                    //si la cantidad del artículo es mayor que cero y el codigodelarticuloexiste
                    if ($key['cantidadArticulo'] > 0 && $key['existe'] == 1) {                     
                        //Recálculo del CMP - costo medio ponderado de cada artículo vendido y del stock
                        $this->actualizacionColaPartidasEntradasYStock($key, $insCabecera, $cabecera);   
                    }
                }
               
            }
                
            if ($cont == $cabecera['numArticulos']) { //valido que se han insertado todas las líneas
                return $insCabecera;
            } else {
                return 0;
            }
              
        } else {
            return 0;
        }
    }
    */  
    
    /*
    public function crearCabeceraSalida($cabecera)
    {

        if ($cabecera['tipo'] == 'albaranSalida') {
            $tipo = 'SALIDA';
            $subtipo = 'ALBARAN';
        }

        //verifico el año del documento
        $numAlbaran = $this->generarNumAlbaran($cabecera['fecha']);

        $largo = strlen($numAlbaran);

        if ($largo == 1) {
            $numAlbCompleto = $cabecera['serie'] . "-00000" . $numAlbaran;
        } else if ($largo == 2) {
            $numAlbCompleto = $cabecera['serie'] . "-0000" . $numAlbaran;
        } else if ($largo == 3) {
            $numAlbCompleto = $cabecera['serie'] . "-000" . $numAlbaran;
        } else if ($largo == 4) {
            $numAlbCompleto = $cabecera['serie'] . "-00" . $numAlbaran;
        } else if ($largo == 5) {
            $numAlbCompleto = $cabecera['serie'] . "-0" . $numAlbaran;
        } else {
            $numAlbCompleto = $cabecera['serie'] . "-" . $numAlbaran;
        }

        $creacion = date("Y-m-d H i s");
        $ins = $this->modeloSalidas->crearCabeceraSalida($cabecera, $tipo, $subtipo, $numAlbaran, $numAlbCompleto, $creacion);      
        return $ins;        
    }
    */
    
    /*
    public function generarNumAlbaran($fechaInicio)
    {
        $ultimoAnio = $this->modeloSalidas->obtenerAnioDeUltimaFechaCabeceraSalidas();
        $anioComparar = date('Y');
        if (isset($ultimoAnio) && $ultimoAnio > 0) {
            $anioComparar = $ultimoAnio;
        }
        $partes = explode('-',$fechaInicio);
        $anio = $partes[0];

        //1- si el año fecha que llega >= fecha de la bd, tomo el último códigoProyecto del año en cuestión y le sumo 1
        if ($anio >= $anioComparar) {
            $numAlbaran = $this->modeloSalidas->obtenerNumAlbaranCorrelativo($anio);
            if (isset($numAlbaran) && $numAlbaran > 0) {
                $retorno = $numAlbaran + 1;
            }else{
                $retorno =   1;
            }
        }else {            
            //2- si el año fecha que llega > fecha de la bd, entonces reiniciar el codigoProyecto a 1
            $numAlbaran = 1; //se reinicia con 1
        }        
        return $retorno;

    }
    */

    public function actualizarRegistroSalida() 
    {      
        session_start();

        /*
        echo"<br>1- todos los datos<br>";
        var_dump($_POST);
        die;*/

        if ($_POST['idcab'] && $_POST['idcab'] > 0) {
                       
            if (isset($_POST['guardar']) || isset($_POST['guardarSeguir']) || isset($_POST['guardarNuevo'])) {

                //elimino las filas antiguas
                $cabeceraUpd = [
                    'tipo' => 'modificacionSalida',
                    'fecha' => date('Y-m-d'),
                    'almacen' => 1
                ];

                $eliminarLineas = $this->eliminarLineasAlbaran($_POST['idcab'],$cabeceraUpd);
                
                if ($eliminarLineas == 1) {
                
                    //datos de las líneas nuevas
                    $numerosOrden = $_POST['numeroOrden'];
                    $articulos = $_POST['idArticulo'];
                    $descripcion = $_POST['descripcion'];
                    $cantidadArticulo = $_POST['cantidadArticulo'];
                    $precioArticulo = $_POST['precioArticulo'];
                    $descuento = $_POST['descuento'];
                    $iva = $_POST['iva'];
                    
                    if ($articulos != '' && count($articulos) > 0) {
                        $tmp = [];
                        $lineasNuevas = [];

                        $totBImpNuevosArt = 0;
                        $totDsctoNuevosArt = 0;
                        $totIvaNuevosArt = 0;
                        $totNuevosArt = 0;

                        //construyo array de lotes si hubiesen
                        if (isset($_POST['numFilaArticulo'])) {
                            $arrLotes = $_POST['numFilaArticulo'];
                            $arrNumerosLote = $_POST['numeroLote'];
                            $arrCantidadesLote = $_POST['cantidadLote'];

                            $tmpLotes = [];
                            $lineasLotes = [];
                            for ($j = 0; $j < count($arrLotes); $j++) {

                                $tmpLotes['numFilaArticulo'] = $arrLotes[$j];                    
                                $tmpLotes['numeroLote'] = $arrNumerosLote[$j];
                                $tmpLotes['cantidadLote'] = $arrCantidadesLote[$j];

                                $lineasLotes[] = $tmpLotes;
                            }
                        }

                        //construyo array de datos de cada artículo
                        $ordenFila = 1;
                        for ($i = 0; $i < count($articulos); $i++) {

                            $tmp['ordenfila'] = $ordenFila++;
                            //coste medio ponderado actual (cmp)
                            $codigoArticulo = trim($articulos[$i]);
                            $codigoExiste = $this->modeloSalidas->buscarCodigoArticulo($codigoArticulo);
                            $cmpActual = 0;
                            $existe = 0;
                            if (isset($codigoExiste->codigoarticulo) && $codigoExiste->codigoarticulo != '') {
                                $cmpActual = $this->modeloSalidas->obtenerCosteMedioPonderadoActual($codigoArticulo);
                                $existe = 1;
                            } 
                            $tmp['cmpActual'] = $cmpActual;
                            $tmp['numeroOrden'] = $numerosOrden[$i];
                            $tmp['idArticulo'] = $codigoArticulo;
                            $tmp['descripcion'] = trim($descripcion[$i]);
                            $cantidadSalida = 0;
                            if (isset($cantidadArticulo[$i]) && trim($cantidadArticulo[$i]) != '') {
                                $cantidadSalida = trim($cantidadArticulo[$i]);
                            }
                            $tmp['cantidadArticulo'] = $cantidadSalida;
                            $tmp['precioArticulo'] = $cmpActual;

                            $precioSalida = 0;
                            if (isset($precioArticulo[$i]) && trim($precioArticulo[$i]) !='') {
                                $precioSalida = trim($precioArticulo[$i]);
                            }
                            $tmp['precioVenta'] = $precioSalida;

                            $dsctoSalida = 0;
                            if (isset($descuento[$i]) && trim($descuento[$i]) != '') {
                                $dsctoSalida = trim($descuento[$i]);
                            }                    
                            $tmp['descuento'] = $dsctoSalida;

                            $tmp['iva'] = $iva[$i];
                            $tmp['existe'] = $existe;                            

                            //calculo los totales por línea nueva
                            $bImpArt = $cantidadSalida * $precioSalida;
                            $valDsctoArt = $bImpArt * $dsctoSalida / 100;
                            $bImpDsctoArt = $bImpArt - $valDsctoArt;
                            $valIvaArt = $bImpDsctoArt * $iva[$i] / 100;
                            $valTotArt = $bImpDsctoArt + $valIvaArt;

                            //calculo los totales de todas las líneas nuevas                 
                            $tmp['totalLinea'] = $bImpDsctoArt; //total no incluye iva                    

                            $totBImpNuevosArt = $totBImpNuevosArt + $bImpArt;
                            $totDsctoNuevosArt = $totDsctoNuevosArt + $valDsctoArt;
                            $totIvaNuevosArt = $totIvaNuevosArt + $valIvaArt;
                            $totNuevosArt = $totNuevosArt + $valTotArt;

                            //construyo los lotes por cada artículo
                            if (isset($_POST['numFilaArticulo'])) {
                                $arr = [];
                                foreach ($lineasLotes as $k) {
                                    if ($k['numFilaArticulo'] == $numerosOrden[$i]) {
                                        $arr[] = $k;
                                    }
                                    $tmp['lotes'] = $arr;
                                }
                            }

                            $lineasNuevas[] = $tmp;
                        }
                    }            

                    $cabeceraNueva = [
                        'idcab' => $_POST['idcab'],
                        'agente' => $_POST['agente'],
                        'totalBaseImponible' => $totBImpNuevosArt,
                        'totalValorDescuento' => $totDsctoNuevosArt,
                        'totalValorIva' => $totIvaNuevosArt,
                        'totalDocumento' => $totNuevosArt,
                        'totalCobrado' => $_POST['totalCobrado'],
                        'totalPendienteCobro' => $_POST['totalPendienteCobro'],
                        'numArticulos' => count($articulos),
                        'fecha' => $_POST['fecha'],
                        'almacen' => $_POST['almacen'],
                        'tipo' => $_POST['tipo']
                    ];

                    $upd = $this->actualizarRegistroSalidaCabeceraYLineas($cabeceraNueva, $lineasNuevas);

                    if ($upd) {
                        

                        $_SESSION['message'] = 'Registro actualizado corréctamente.';

                        if (isset($_POST['guardar'])) {
                            redireccionar('/Salidas');
                        } else if (isset($_POST['guardarSeguir'])) {
                            redireccionar('/Salidas/V/'.$_POST['idcab']);
                        } else if (isset($_POST['guardarNuevo'])) {
                            redireccionar('/Salidas/N');
                        }
                    } else {
                        
                        /*
                        echo"<br>llega ok hasta con error1<br>";
                        die;
                        */

                        $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado.';
                        redireccionar('/Salidas');
                    }
                }else{
                    
                    /*
                    echo"<br>llega ok hasta con error2<br>";
                    die;
                    */

                    $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado porque no se puden eliminar el detalle anterior.';
                    if (isset($_POST['guardar'])) {
                        redireccionar('/Salidas');
                    } else if (isset($_POST['guardarSeguir'])) {
                        redireccionar('/Salidas/V/'.$_POST['idcab']);
                    } else if (isset($_POST['guardarNuevo'])) {
                        redireccionar('/Salidas/N');
                    }
                }

            } else {

                /*
                echo"<br>llega ok hasta con error3<br>";
                die;*/

                $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado.';
                redireccionar('/Salidas');
            }
        } else {
            $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado.';
            redireccionar('/Salidas');
        }
    }

    public function eliminarLineasAlbaran($idcab,$cabecera) //queda
    {        
        $lineas = $this->modeloSalidas->consultaDetallesSalidaPorIdCab($idcab);

        /*
        echo"<br>3-lineas antiguas<br>";
        var_dump($lineas);
        */

        $contador = 0;
        $artEliminar = 0;
        foreach ($lineas as $linea) {

            if ($linea->existe == 1) {
                $artEliminar++;
                $tmp = [];
                //datos para cada línea                
                $tmp['idArticulo'] = $linea->idarticulo;
                $tmp['descripcion'] = $linea->descripcion;
                $tmp['cantidadArticulo'] = $linea->cantidad;
                $tmp['precioArticulo'] = $linea->precio;                
                $tmp['descuento'] = $linea->descuento;
                $tmp['iva'] = $linea->iva;

                //construyo los lotes por cada artículo
                $arrE = [];
                if (isset($linea->lotes) && $linea->lotes != '' && count(json_decode($linea->lotes)) > 0) {
                    
                    foreach (json_decode($linea->lotes) as $lot) {
                        $arrE[$lot->numeroLote] = $lot->cantidadLote;
                    }                    
                }
                $tmp['lotesE']= $arrE;
                $tmp['totalLinea'] = $linea->total;
                $cmpActual = $this->modeloSalidas->obtenerCosteMedioPonderadoActual($linea->idarticulo);
                $tmp['cmpActual'] = $cmpActual;                     
          
                /*
                echo"<br>tmp lotesE<br>";
                var_dump($arrE);
                */


                /*
                $newCargaStock = new CargarStock();
                $newCargaStock->recalculosCostesStocksInventarioParaEntradaDeArticulo($cabecera,$idcab,$tmp,'modificacionSalida');
                
                //si alguna partida que tiene (tenía) lotes ha quedado con cantidad en cero ... eliminar partida de movimientosentradas
                $tieneStkCero = $this->modeloSalidas->loteTieneStockCero($linea->idarticulo);                

                if (isset($tieneStkCero) && isset($tieneStkCero[0]->idmov) && $tieneStkCero[0]->idmov >0) {
                    foreach ($tieneStkCero as $del) {
                        $this->modeloSalidas->eliminarPartidaNegativa($del->idmov);   
                    }
                }
                */
                
                //eliminar salida de movimientos
                $del = $this->modeloSalidas->eliminarMovimientoPorIdMov($linea->idmov);
                

                if ($del == 1) {
                    $contador++;
                }
                
            }else if($linea->existe == 0){                                
                $del = $this->modeloSalidas->eliminarMovimientoPorIdMov($linea->idmov);
            }
        }
        
        $retorno = 0;
        if ($contador == $artEliminar) {
            $retorno = 1;
        }
        return $retorno;
    }

    /*
    public function actualizarRegistroSalida1() //eliminar al terminar el anterior
    {

        session_start();

        if ($_POST['idcab'] && $_POST['idcab'] > 0) {

            if (isset($_POST['guardar']) || isset($_POST['guardarSeguir']) || isset($_POST['guardarNuevo'])) {

                $idcab = $_POST['idcab'];

                //datos de las líneas
                $articulos = $_POST['idArticulo'];
                $descripcion = $_POST['descripcion'];
                $cantidadArticulo = $_POST['cantidadArticulo'];
                $precioArticulo = $_POST['precioArticulo'];
                $descuento = $_POST['descuento'];
                $iva = $_POST['iva'];
                //$totalLinea = $_POST['totalLinea'];

                $numArticulos = 0;
                if ($articulos) {
                    $numArticulos = count($articulos);
                }
                if ($articulos != '' && $numArticulos > 0) {
                    $tmp = [];
                    $lineas = [];

                    $totBImpNuevosArt = 0;
                    $totDsctoNuevosArt = 0;
                    $totIvaNuevosArt = 0;
                    $totNuevosArt = 0;

                    for ($i = 0; $i < $numArticulos; $i++) {


                        $partes = explode(',', $precioArticulo[$i]);
                        if (strpos($partes[0], ".")) {
                            $precioFinal = str_replace(".", "", $partes[0]) . "." . $partes[1];
                        } else {
                            $precioFinal = str_replace(",", ".", $precioArticulo[$i]);;
                        }

                        $tmp['idArticulo'] = $articulos[$i];
                        $tmp['descripcion'] = $descripcion[$i];
                        $tmp['cantidadArticulo'] = $cantidadArticulo[$i];
                        $tmp['precioArticulo'] = $precioFinal;
                        $tmp['descuento'] = $descuento[$i];
                        $tmp['iva'] = $iva[$i];


                        //calculo los totales por línea nueva
                        $bImpArt = $cantidadArticulo[$i] * $precioFinal;
                        $valDsctoArt = $bImpArt * $descuento[$i] / 100;
                        $bImpDsctoArt = $bImpArt - $valDsctoArt;
                        $valIvaArt = $bImpDsctoArt * $iva[$i] / 100;
                        $valTotArt = $bImpDsctoArt + $valIvaArt;

                        //calculo los totales de todas las líneas nuevas                 
                        $tmp['totalLinea'] = $bImpDsctoArt; //total no incluye iva

                        $totBImpNuevosArt = $totBImpNuevosArt + $bImpArt;
                        $totDsctoNuevosArt = $totDsctoNuevosArt + $valDsctoArt;
                        $totIvaNuevosArt = $totIvaNuevosArt + $valIvaArt;
                        $totNuevosArt = $totNuevosArt + $valTotArt;

                        $lineas[] = $tmp;
                    }
                }

                $cabeceraGuardada = $this->modeloSalidas->obtenerCabeceraGuardada($idcab);

                $baseImpAct = $cabeceraGuardada->baseimponible;
                $valorDsctoAct = $cabeceraGuardada->valordescuento;
                $valorIvaAct = $cabeceraGuardada->valoriva;
                $valorTotalAct = $cabeceraGuardada->valortotal;

                //sumo los totales guardados con los nuevos
                $totBImpFinalArt = $baseImpAct + $totBImpNuevosArt;
                $totDsctoFinalArt = $valorDsctoAct + $totDsctoNuevosArt;
                $totIvaFinalArt = $valorIvaAct + $totIvaNuevosArt;
                $totFinalArt = $valorTotalAct + $totNuevosArt;

                $cabeceraNueva = [
                    'formadepago' => $_POST['formadepago'],
                    'numArticulos' => $numArticulos,
                    'idcab' => $idcab,
                    'totBImpFinalArt' => $totBImpFinalArt,
                    'totDsctoFinalArt' => $totDsctoFinalArt,
                    'totIvaFinalArt' => $totIvaFinalArt,
                    'totFinalArt' => $totFinalArt,
                    'almacen' => $cabeceraGuardada->almacen,
                    'tipo' => 'albaranSalida'
                ];

                $upd = $this->actualizarRegistroSalidaCabeceraYLineas($cabeceraNueva, $lineas);

                if ($upd) {
                    $_SESSION['message'] = 'Registro actualizado corréctamente.';

                    if (isset($_POST['guardar'])) {
                        redireccionar('/Salidas');
                    } else if (isset($_POST['guardarSeguir'])) {
                        redireccionar('/Salidas/V/' . $idcab);
                    } else if (isset($_POST['guardarNuevo'])) {
                        redireccionar('/Salidas/N');
                    }
                } else {
                    $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado.';
                    redireccionar('/Salidas');
                }
            } else {
                $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado.';
                redireccionar('/Salidas');
            }
        } else {
            $_SESSION['message'] = 'Ha ocurrido un error y el registro no se ha actualizado.';
            redireccionar('/Salidas');
        }
    }
    */

    public function actualizarRegistroSalidaCabeceraYLineas($cabecera, $lineas) //queda
    {
        //datos de los totales para actualizar la cabecera:
        $updCabecera = $this->modeloSalidas->actualizarCabeceraSalida($cabecera);
        
        $idcab = $cabecera['idcab'];

        if ($updCabecera) {

            
            if ($cabecera['tipo'] == 'albaranSalida') {
                $tipo = 'SALIDA';
                $subtipo = 'ALBARAN';
            }
    
            $cont = 0;
            foreach ($lineas as $key) { 
                
                $lotes = [];
                if (isset($key['lotes']) && count($key['lotes']) >0) {
                    $lotes = $key['lotes'];
                }
                $lotesJson = json_encode($lotes);

                $insLinea = $this->modeloSalidas->crearLineasSalida($idcab, $key, $cabecera, $lotesJson, $tipo, $subtipo);

                if ($insLinea) {
                    $cont++;
                    
                    //si la cantidad del artículo es mayor que cero y el codigodelarticuloexiste
                    /*if ($key['cantidadArticulo'] > 0 && $key['existe'] == 1) {
                        //Recálculo del CMP - costo medio ponderado de cada artículo vendido y del stock
                        $newSalida = new DescargarStock();
                        //$this->actualizacionColaPartidasEntradasYStock($key, $idcab, $cabecera);   
                        $newSalida->actualizacionColaPartidasEntradasYStock($key, $idcab, $cabecera);
                    }*/
                }
               
            }
                
            if ($cont == $cabecera['numArticulos']) { //valido que se han insertado todas las líneas
                return 1;
            } else {
                return 0;
            }            


        } else {
            return 0;
        }
    }


    
    public function actualizacionColaPartidasEntradasYStock($articulo, $insCabecera, $cabecera) 
    {                
        $idArticulo =  $articulo['idArticulo'];
        $cantidad = $articulo['cantidadArticulo'];
        
        $lotes = '';
        if (isset($articulo['lotes'])) {
            $lotes = $articulo['lotes'];
        
            //1- SI TRAE LOTES
            if (count($lotes) > 0) {

                /*
                echo"<br>entra CON lotes<br>";               
                echo"<br><br>idArticulo<br>";
                print_r($idArticulo);
                echo"<br><br>cantidad<br>";
                print_r($cantidad);
                echo"<br><br>lotes<br>";
                print_r($lotes);
                */

                //recorro el array que trae los lotes que vienen del albarán de salida           

                $saldo = $cantidad;
                while ($saldo > 0 ) {

                    $lotesNuevos = [];
                    foreach ($lotes as $lote) {
                        $saldoLote = $this->actualizarColaMovimientosEntradasPorLote($idArticulo, $lote, $cantidad);
                        /*echo"<br>imprimo saldoLote en el foreach<br>";
                        print_r($saldoLote);*/
                        if ($saldoLote == 0) {
                            $saldo -= $lote['cantidadLote'];
                            /*echo"<br>imprimo el SALDO en el foreach<br>";
                            print_r($saldo);*/
                        }
                        
                        //SI SALDOLOTE VIENE > 0, SE GENERARÁ STOCK NEGATIVO
                        else if ($saldoLote > 0){
                            //echo"<br>ENTRA A ELSE DE SALDO NEGATIVO<br>";
                            $tmp = [];
                            $tmp['numeroLote'] = $lote['numeroLote'];
                            $tmp['cantidadLote'] = $saldoLote;
                            $lotesNuevos[] = $tmp;                            
                            $saldo -= $lote['cantidadLote'];
                        }
                    }   
                    
                }

                /*echo"<br>cantidad final despues del while<br>";
                print_r($saldo);*/
                
                //die;    
                if ($lotesNuevos && count($lotesNuevos) >0) {
                    $precio = $this->modeloSalidas->precioCompraProveedorHabitual($idArticulo);
                    
                    //insert en movimientosentradas de los lotes con cantidad negativa
                    /*echo"<br>imprimo lotes y cantidades a insertar en negativo<br>";
                    print_r($lotesNuevos);*/
                    $this->generarStockNegativoArticulosConLote($articulo, $lotesNuevos, $precio, $insCabecera, $cabecera);
                }
                        
            }

            
            //ACTUALIZACIONES INVENTARIO Y FICHA ARTÍCULO
            if ($saldo == 0) {
        
                //una vez terminadas las actualizaciones de la cola
                //busco nuevamente las partidas del artículo en la tabla temporal "movimientosentradas" para calcular el stock final          
                $actualizado = $this->modeloSalidas->calculoCostoMedioPorArticulo($idArticulo);
                
                $cmp = $actualizado['cmp'];
                $stock = $actualizado['stock'];
    
                //actualizo el cmp y stock NUEVOS en el inventario
                $this->modeloSalidas->actualizarCMPyStockEnInventario($idArticulo, $stock, $cmp);
    
                //actualizo el cmp y stock NUEVOS en la ficha artículo
                $this->modeloSalidas->actualizarCMPyStockEnFichaArticulo($idArticulo, $stock, $cmp);
            }

        //2- SI NO TRAE LOTES
        }else{
                    
            //echo"<br>entra SIN lotes<br>";
            if ($cantidad > 0) {
                     
                /*echo"<br><br>idArticulo<br>";
                print_r($idArticulo);
                echo"<br><br>cantidad<br>";
                print_r($cantidad);*/
               
                
                    //busco las partidas del artículo en la tabla temporal "movimientosentradas"
                    $partidasEnStock = $this->modeloSalidas->stockPorPartidasSegunIdArticulo($idArticulo);
                    /*echo"<br><br>filas en movimientosentradas<br>";
                    var_dump($partidasEnStock); */                             
                
                    
                    $saldo = $cantidad;
                    for ($i=0; $i < count($partidasEnStock); $i++) { 
            
                        if ($saldo > 0) {
            
                            //la cantidad a retirar es mayor que el current lote en stock 
                            if ($saldo >= $partidasEnStock[$i]->cantidad) {    
                                    
                                /*echo"<br>entra al if, cantidad a retirar es: <br>";
                                print_r($saldo);*/
            
                                $saldo -= $partidasEnStock[$i]->cantidad;
                        
                                /*echo"<br>saldo a retirar es: <br>";
                                print_r($saldo); */                           
            
                                /*echo"<br>estoy analizando el idmov<br>";
                                print_r($partidasEnStock[$i]->idmov);*/

                                //ACTUALIZACIONES
            
                                //1- elimino la partida vacía
                                $this->modeloSalidas->eliminarPartidaVacia($partidasEnStock[$i]->idmov);

                                //$cantMov = $this->modeloSalidas->stockPorPartida($partidasEnStock[$i]->idmov);
                                
                                /*echo"<br> despues de actualizar en el if la cantidad del movimiento es: <br>";
                                print_r($cantMov);*/
                                    
                                                                                    
                            
                            }else{
                                        
                                /*echo"<br>entra al else, cantidad a retirar es: <br>";
                                print_r($saldo);*/
                                            
                                $nuevaCantTotal = $partidasEnStock[$i]->cantidad - $saldo;
                                /*echo"<br>nueva cantidad total<br>";
                                print_r($nuevaCantTotal);*/
                                        
                                $saldo -= $saldo;
                                
                                /*echo"<br>saldo a retirar es: <br>";
                                print_r($saldo);*/
            
                                //ACTUALIZACIONES
                                //1- actualizo el total de la partida
                                $this->modeloSalidas->updateCantidadTotalPartidaPorIdMov($nuevaCantTotal, $partidasEnStock[$i]->idmov);    
                    
                            }
                            
                        }
            
                    }
                                
                /*echo"<br>cantidad despues del for<br>";
                print_r($saldo);*/
                //die;            
            }

            //ACTUALIZACIONES INVENTARIO Y FICHA ARTÍCULO
       
            if ($saldo == 0) {
        
                //una vez terminadas las actualizaciones de la cola
                //busco nuevamente las partidas del artículo en la tabla temporal "movimientosentradas" para calcular el stock final          
                $actualizado = $this->modeloSalidas->calculoCostoMedioPorArticulo($idArticulo);
                
                $cmp = $actualizado['cmp'];
                $stock = $actualizado['stock'];
    
                //actualizo el cmp y stock NUEVOS en el inventario
                $this->modeloSalidas->actualizarCMPyStockEnInventario($idArticulo, $stock, $cmp);
    
                //actualizo el cmp y stock NUEVOS en la ficha artículo
                $this->modeloSalidas->actualizarCMPyStockEnFichaArticulo($idArticulo, $stock, $cmp);
            }
            
            else if ($saldo > 0) { //SI HA QUEDADO SALDO POR RETIRAR, ENTONCES GENERA STOCK NEGATIVO
    
                $cantNegativa = - $saldo;
                $precio = $this->modeloSalidas->precioCompraProveedorHabitual($idArticulo);
    
                //insert en movimientosentradas del negativo
                $this->modeloSalidas->generarStockNegativoEnMovimientosEntradas($articulo, $cantNegativa, $precio, $insCabecera, $cabecera);
    
                //insert en inventario de stock negativo con precio de compra como cmp
                $this->modeloSalidas->actualizarCMPyStockEnInventario($idArticulo, $cantNegativa, $precio);
    
                //insert en ficha producto de stock negativo con precio de compra como cmp
                $this->modeloSalidas->actualizarCMPyStockEnFichaArticulo($idArticulo, $cantNegativa, $precio);
            }

        }


    }

    public function generarStockNegativoArticulosConLote($articulo, $lotesNuevos, $precio, $insCabecera, $cabecera)
    {

        foreach ($lotesNuevos as $key) {

            $lote = [$key['numeroLote'] => -$key['cantidadLote'] ];
            $loteJson = json_encode($lote);
            
            $this->modeloSalidas->insertarNuevosLotesQueVienenDeUnaVenta($articulo,$precio, $insCabecera, $cabecera, $key, $loteJson);
        }
    }

    public function actualizarColaMovimientosEntradasPorLote($idArticulo,$lote, $cantidad)
    {         
        
        $lotesEnStock = $this->modeloSalidas->obtenerTodasLasPartidaSegunLote($idArticulo, $lote['numeroLote']);
        /*echo"<br>imprimo todas las partidas del lote ".$lote['numeroLote']."<br>";
        var_dump($lotesEnStock); */
        
        $cantidadLote = $lote['cantidadLote'];
    
        if ($lotesEnStock) {

            //echo"entra por que hay stock del lote";

            for ($i=0; $i < count($lotesEnStock); $i++) {
                if ($cantidadLote > 0) {

                    //la cantidad a retirar es mayor que el current lote en stock
                    if ($cantidadLote >= $lotesEnStock[$i]->cantLote) {
                        
                        /*echo"<br>entra al if, cantidad a retirar es: <br>";
                        print_r($cantidadLote);*/

                        $cantidadLote -= $lotesEnStock[$i]->cantLote;
                
                        /*echo"<br>saldo a retirar es: <br>";
                        print_r($cantidadLote);*/
                        
                        /*echo"<br>estoy analizando el idmov<br>";
                        print_r($lotesEnStock[$i]->idmov);*/

                        //ACTUALIZACIONES

                        //1- elimino el lote de la partida
                        $this->modeloSalidas->deleteCantidadLoteEnPartidaPorIdMov($lote['numeroLote'], $lotesEnStock[$i]->idmov);

                        //2- actualizo el total de la partida
                        $nuevaCantTotal = $lotesEnStock[$i]->cantTotPartida - $lotesEnStock[$i]->cantLote;
                        /*echo"<br>nueva cantidad total<br>";
                        print_r($nuevaCantTotal);*/

                        $this->modeloSalidas->updateCantidadTotalPartidaPorIdMov($nuevaCantTotal, $lotesEnStock[$i]->idmov);
                        //3- elimino la partida si la cantidad es cero
                        $cantMov = $this->modeloSalidas->stockPorPartida($lotesEnStock[$i]->idmov);
                        if ($cantMov == 0) {
                            /*echo"<br> despues de actualizar en el if la cantidad del movimiento es: <br>";
                            print_r($cantMov);*/
                            $this->modeloSalidas->eliminarPartidaVacia($lotesEnStock[$i]->idmov);
                        }
                    } else {
                        /*echo"<br>entra al else, cantidad a retirar es: <br>";
                        print_r($cantidadLote);*/

                        $nuevaCant = $lotesEnStock[$i]->cantLote - $cantidadLote;
                        /*echo"<br>nueva cantidad del lote<br>";
                        print_r($nuevaCant);*/

                        $nuevaCantTotal = $lotesEnStock[$i]->cantTotPartida - $cantidadLote;
                        /*echo"<br>nueva cantidad total<br>";
                        print_r($nuevaCantTotal);*/
                        

                        $cantidadLote -= $cantidadLote;                        
                        /*echo"<br>saldo a retirar es: <br>";
                        print_r($cantidadLote);*/

                        //ACTUALIZACIONES

                        //1- actualizo el lote de la partida
                        $this->modeloSalidas->updateCantidadLoteEnPartidaPorIdMov($lote['numeroLote'], $lotesEnStock[$i]->idmov, $nuevaCant);
                        //2- actualizo el total de la partida
                        $this->modeloSalidas->updateCantidadTotalPartidaPorIdMov($nuevaCantTotal, $lotesEnStock[$i]->idmov);
                        //3- elimino la partida si la cantidad es cero
                        $cantMov = $this->modeloSalidas->stockPorPartida($lotesEnStock[$i]->idmov);
                        if ($cantMov == 0) {
                            /*echo"<br> despues de actualizar en el else la cantidad del movimiento es: <br>";
                            print_r($cantMov);*/
                            $this->modeloSalidas->eliminarPartidaVacia($lotesEnStock[$i]->idmov);
                        }
                    }
                }
            }
        }
        
        return $cantidadLote;

    }     


    public function insertarDatosEmail($fecha, $nombreUsuario, $idDocumento, $tipoDoc, $email)
    {
        $this->modeloSalidas->insertarDatosEmail($fecha, $nombreUsuario, $idDocumento, $tipoDoc, $email);
    }

    public function obtenerArticulos()
    {
        $resultado = $this->modeloArticulos->obtenerTodosArticulos();
        echo json_encode($resultado);
    }

    public function obtenerInformacionArticulo()
    {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $datosPost = $_POST;

            if ($datosPost['salida'] == 1 && $datosPost['entrada'] == 0) {
                $datos = $this->modeloArticulos->obtenerInformacionArticuloSalida($datosPost);
            } else if ($datosPost['entrada'] == 1 && $datosPost['salida'] == 0) {
                $datos = $this->modeloArticulos->obtenerInformacionArticuloEntrada($datosPost);
            } else {
                $datos = $this->modeloArticulos->obtenerInformacionArticulo($datosPost);
            }

            $tabla = '<table class="table">'
                . '<thead class="thead-light">'
                . '<tr>'
                .     '<th scope="col">Fecha</th>'
                .     '<th scope="col">Tipo Movimiento</th>'
                .     '<th scope="col">subtipo</th>'
                .     '<th scope="col">Almacen</th>'
                .     '<th scope="col">Cli/Pro</th>'
                .     '<th scope="col">Unidades</th>'
                .     '<th scope="col">Coste</th>'
                .     '<th scope="col">Pre. Vta.</th>'
                .     '<th scope="col">Descuento</th>'
                .     '<th scope="col">Total</th>'
                . '</tr>'
                . '</thead>'
                . '<tbody>';
            foreach ($datos as $key) {
                if ($key->tipo == "ENTRADA") {

                    $date = new DateTime($key->fecha);

                    $tabla  .= '<tr>'
                        . '<td>' . date_format($date, 'd-m-Y') . '</td>'
                        .  '<td>' . $key->tipo . '</td>'
                        .  '<td>' . $key->subtipoEntradas . '</td>'
                        . '<td>GEN</td>'
                        .  '<td>' . $key->proveedor . '</td>'
                        .  '<td>' .  number_format($key->cantidad, 2, ',', '.') . '</td>'
                        . '<td>' .  number_format($key->precio, 2, ',', '.') . '</td>'
                        . '<td>' .  number_format($key->precioventa, 2, ',', '.') . '</td>'
                        .  '<td>' .  number_format($key->descuento, 2, ',', '.') . '</td>'
                        .  '<td>' .  number_format($key->total, 2, ',', '.') . '</td>'
                        . '</tr>';
                } else {

                    $date = new DateTime($key->fecha);

                    $tabla  .= '<tr>'
                        . '<td>' . date_format($date, 'd-m-Y') . '</td>'
                        .  '<td>' . $key->tipo . '</td>'
                        .  '<td>' . $key->subtipoSalidas . '</td>'
                        . '<td>GEN</td>'
                        .  '<td>' . $key->cliente . '</td>'
                        .  '<td>' .  number_format($key->cantidad, 2, ',', '.') . '</td>'
                        . '<td>' .  number_format($key->precio, 2, ',', '.') . '</td>'
                        . '<td>' .  number_format($key->precioventa, 2, ',', '.') . '</td>'
                        .  '<td>' .  number_format($key->descuento, 2, ',', '.') . '</td>'
                        .  '<td>' .  number_format($key->total, 2, ',', '.') . '</td>'
                        . '</tr>';
                }
            }
            $tabla .= '</tbody>'
                . '</table>';

            echo $tabla;
        }
    }

    public function obtenerDescuentosPorClientePorArticulo()
    {
        if ($_POST['idCliente']) {

            if (isset($_POST['idArticulo']) && $_POST['idArticulo'] != '') {
                $idArticulo = $_POST['idArticulo'];
            }else if(isset($_POST['codigoArticulo']) && $_POST['codigoArticulo'] != ''){
                $idArticulo = $this->modeloSalidas->idArticuloPorCodigoArticulo($_POST['codigoArticulo']);
            }
            $post = [
                'idCliente' => $_POST['idCliente'],
                'idArticulo' => $idArticulo

            ];

            $datos = $this->modeloSalidas->obtenerDescuentosPorClientePorArticulo($post);
           

            $tabla = '<table class="table">'
                . '<thead class="thead-light">'
                . '<tr>'
                .     '<th scope="col">Fecha</th>'             
                .     '<th scope="col">Doc.</th>'                
                .     '<th scope="col">Cliente</th>'
                .     '<th scope="col">Unidades</th>'
                .     '<th scope="col">Pre. Vta.</th>'
                .     '<th scope="col">Dscto(%)</th>'
                .     '<th scope="col">Valor Dscto.</th>'
                .     '<th scope="col">Total</th>'
                .     '<th scope="col">Coste</th>'
                . '</tr>'
                . '</thead>'
                . '<tbody>';
            foreach ($datos as $key) {

                    $tabla  .= '<tr>'
                        . '<td>' . date('d-m-Y', strtotime($key->fecha)) . '</td>'
                        .  '<td>' . $key->documento . '</td>'
                        .  '<td>' . $key->nombrefiscal . '</td>'
                        . '<td>' . $key->cantidad . '</td>'
                        .  '<td>' . $key->precioventa . '</td>'
                        .  '<td>' .  ((isset($key->descuento))? $key->descuento: 0) . '</td>'
                        . '<td>' . ((isset($key->valordescuento))? $key->valordescuento: 0) . '</td>'
                        . '<td>' . $key->total . '</td>'
                        .  '<td>' . $key->coste . '</td>'                        
                        . '</tr>';
               
            }
            $tabla .= '</tbody>'
                . '</table>';

            echo $tabla;
        }
    }
        
    public function obtenerDescuentosPorArticulo()
    {

        if ($_POST['idArticulo']) {

            $datos = $this->modeloSalidas->obtenerTodosLosDescuentosPorArticulo($_POST);

            $tabla = '<table class="table">'
                . '<thead class="thead-light">'
                . '<tr>'
                .     '<th scope="col">Fecha</th>'             
                .     '<th scope="col">Doc.</th>'                
                .     '<th scope="col">Cliente</th>'
                .     '<th scope="col">Unidades</th>'
                .     '<th scope="col">Pre. Vta.</th>'
                .     '<th scope="col">Dscto(%)</th>'
                .     '<th scope="col">Valor Dscto.</th>'
                .     '<th scope="col">Total</th>'
                .     '<th scope="col">Coste</th>'
                . '</tr>'
                . '</thead>'
                . '<tbody>';
            foreach ($datos as $key) {

                    $tabla  .= '<tr>'
                        . '<td>' . date('d-m-Y', strtotime($key->fecha)) . '</td>'
                        .  '<td>' . $key->documento . '</td>'
                        .  '<td>' . $key->nombrefiscal . '</td>'
                        . '<td>' . $key->cantidad . '</td>'
                        .  '<td>' . $key->precioventa . '</td>'
                        .  '<td>' .  $key->descuento . '</td>'
                        . '<td>' . $key->valordescuento . '</td>'
                        . '<td>' . $key->total . '</td>'
                        .  '<td>' . $key->coste . '</td>'                        
                        . '</tr>';
               
            }
            $tabla .= '</tbody>'
                . '</table>';

            echo $tabla;
        }
    }


    public function generarListadoExcelAlbaranesVenta($ini, $fin, $idCliente)
    {
        $arr = [
            'fechaInicio' => $ini,
            'fechaFin' => $fin,
            'idCliente' => $idCliente
        ];

        $datos = $this->modeloSalidas->listarAlbaranesConCriterios($arr);

        if ($datos && $datos != '') {

            $datosList = [];
            $tmp = [];
            foreach ($datos as $key) {

                $tmp['albaran'] = $key->numalbcompleto;
                $tmp['fecha'] = $key->fecha;
                $tmp['venta'] = $key->tipo;
                $tmp['documento'] = $key->subtipo;
                $tmp['formaPago'] = $key->formapago;
                $tmp['facturacion'] = $key->estadodefactura;
                $tmp['cobro'] = $key->estadodecobro;
                $tmp['agente'] = $key->nombreagente;
                $tmp['idCliente'] = $key->idcliente;
                $tmp['Cliente'] = $key->nombrefiscal;
                $tmp['bimponible'] = $key->baseimponible;
                $tmp['descuento'] = $key->valordescuento;
                $tmp['iva'] = $key->valoriva;
                $tmp['total'] = $key->valortotal;
                $tmp['factura'] = $key->idFact;
                $datosList[] = $tmp;
            }


            $titulos = $datosList[0]; //obtengo un elemento del array para extraer los nombres para la cabecera del fichero
            //print_r( $titulos );

            $d = 'A'; //se refiere a la columna A del excel donde se recibirán los datos

            $inicio = 1; //se refiere a la fila 1 del excel donde se escribirán las cabeceras

            $cabecerasTmp = [];
            //incorporo las letras correlativamente empezando en 'A'
            foreach ($titulos as $key => $value) {
                $cabecerasTmp[$d . $inicio] = $key;
                ++$d . PHP_EOL;
            }

            $nombreListado = 'ListadoAlbaranes';
            //llamo a la librería
            ExportImportExcel::exportToExcel($cabecerasTmp, $datosList, $nombreListado);
        } else {
            echo "no hay datos que exportar";
        }
    }

    public function montarLoteSeleccionado()
    {
        if ($_POST['lineaSelected'] && $_POST['filaOrden'] && $_POST['loteSelected'] && $_POST['idArtSelected']) {
            $lineaSelected = $_POST['lineaSelected'];
            $filaOrden = $_POST['filaOrden']; //correlativo tabla lotes
            $idArtSelected = $_POST['idArtSelected'];
            $loteSelected = $_POST['loteSelected'];

            $filaLote =
                '<tr>
                        <td>' . $filaOrden . '</td>
                        <td style="display: none;" ><input class="inputGrillaAuto filaArticulo idLoteArticulo_'.$idArtSelected.'" name="numFilaArticulo[]" value="' . $lineaSelected . '" readonly></td>
                        <td><input class="text-left inputGrillaAuto" name="idArtSelected[]" value="' . $idArtSelected . '" readonly></td>
                        <td><input class="text-left inputGrillaAuto inputSerieLote numLote_' . $lineaSelected . '" name="numeroLote[]" value="' . $loteSelected . '" readonly></td>
                        <td><input type="number" class="text-left inputCantidadLote inputGrillaAuto cantidadLote_' . $lineaSelected . '" name="cantidadLote[]" value="0"></td>
                        <td class="d-flex justify-content-center"><a class="btn-danger px-1 btnDeleteLote" title="quitar" >
                        <i class="fas fa-trash-alt" style="color:white;"></i></a></td>
                    </tr>';

            echo $filaLote;
        } else {
            echo '';
        }
    }

    public function validarDNI()
    {
        $resultado = $this->modeloSalidas->validarDNIByIdCliente($_POST['idCliente']);
        echo $resultado;
    }

    public function validarDNIYEstadoDeFactura()
    {
        $tieneDni = $this->modeloSalidas->validarDNIByIdCliente($_POST['idCliente']);       
        $estadoFactura = $this->modeloSalidas->datosAlbaranById($_POST['idAlbaran']);        
        $resultado = ['dni'=>$tieneDni, 'estadofacturacion'=>$estadoFactura->estadofactura];
        echo json_encode($resultado);    
    }

    public function comprobarLoteObligatorio()
    {
        if ($_POST['idArtSelected']) {
            $resultado = $this->modeloSalidas->comprobarLoteObligatorio($_POST['idArtSelected']);
            echo $resultado;
        }
    }


    public function obtenerCreditoPendientePorIdCliente($idCliente)
    {    
        /*
        $pendiente = $this->modeloClientes->obtenerCreditoPagosPendiente($idCliente);
        $parciales = $this->modeloClientes->obtenerCreditoPagosParciales($idCliente);

        $valorPendiente = isset($pendiente->pendiente)?$pendiente->pendiente:0;
       
        $pagoParcial = $parciales[0]->parcial;

        $pagoPendiente = $parciales[0]->total;

        $pagoRestante = $pagoPendiente  - $pagoParcial;

        $totalPendiente = $valorPendiente + $pagoRestante;

        $creditoCliente = $this->modeloClientes->obtenerCreditoTotal($idCliente);

        $resultado = [
            "pendiente" => round($totalPendiente, 2),
            "total" => round($creditoCliente->credito,2)
        ];

        return round($totalPendiente, 2);
        */
        return round(0, 2);

    }

    public function obtenerEstadoDeCobroAlbaranById()
    {
        $estadodepago = 1;
        if (isset($_POST['idAlbaran']) && $_POST['idAlbaran'] > 0) {
            $estado = $this->modeloSalidas->estadoCobroDeAlbaranById($_POST['idAlbaran']);
            $estadodepago = $estado->id;
        }        
        echo $estadodepago;
    }

}
