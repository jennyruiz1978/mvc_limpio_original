<?php


class ModeloSalidas
{

    private $db;


    public function __construct()
    {
        $this->db = new Base;
    }

    public function totalregistros()
    {
        $this->db->query("SELECT count(idcab) as contador FROM cabeceraSalidas mov 
                        WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN'");
        $fila = $this->db->registro();
        return $fila;

    }

    public function consultadatos($buscar, $orden, $filas, $pagina, $tipoOrden)
    {
        $filaspagina = $filas * $pagina;
        if ($buscar != "") {
                        
            //echo"entra";
            //$camposBD = "sal.numalbcompleto, sal.fecha, sal.idcliente, cli.nombrefiscal, pag.formadepago";
            //cli.nombrefiscal like '%" . $buscar . "%'         
            //CONCAT(".$camposBD.") LIKE '%" . $buscar . "%'

            $salida = "SELECT IF(LENGTH(sal.numalbaran)=1,CONCAT(sal.serie,'-00000',sal.numalbaran),
                                IF(LENGTH(sal.numalbaran)=2,CONCAT(sal.serie,'-0000',sal.numalbaran), 
                                    IF(LENGTH(sal.numalbaran)=3,CONCAT(sal.serie,'-000',sal.numalbaran), 
                                        IF(LENGTH(sal.numalbaran)=4,CONCAT(sal.serie,'-00',sal.numalbaran),
                                            IF(LENGTH(sal.numalbaran)=5,CONCAT(sal.serie,'-0',sal.numalbaran),sal.numalbaran))))) AS 'Número',
                                        DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES') AS Fecha, sal.idcliente AS Cliente, 
                                        cli.nombrefiscal AS Nombre, pag.formadepago AS 'Forma de Pago',  efac.estadofactura AS Factura,
                                        FORMAT(sal.valortotal, 2, 'de_DE') AS Total, ecli.estadodepago AS Cobro, 'N' AS 'Imp.'                                        
                        FROM cabeceraSalidas sal
                        LEFT JOIN clientes cli ON sal.idcliente=cli.id
                        LEFT JOIN formasdepago pag ON sal.formadepago=pag.id
                        LEFT JOIN estadoscobroalbaranes ecli ON sal.estado=ecli.id
                        LEFT JOIN estadosalbaransalida efac ON sal.estadofactura=efac.id
                        WHERE sal.tipo='SALIDA' AND sal.subtipo='ALBARAN' AND
                        cli.nombrefiscal like '%" . $buscar . "%'                              
                        order by " . $orden . " " . $tipoOrden . " limit $filaspagina,$filas";

        } else {

            $salida = "SELECT IF(LENGTH(sal.numalbaran)=1,CONCAT(sal.serie,'-00000',sal.numalbaran),
                                IF(LENGTH(sal.numalbaran)=2,CONCAT(sal.serie,'-0000',sal.numalbaran), 
                                    IF(LENGTH(sal.numalbaran)=3,CONCAT(sal.serie,'-000',sal.numalbaran), 
                                        IF(LENGTH(sal.numalbaran)=4,CONCAT(sal.serie,'-00',sal.numalbaran),
                                            IF(LENGTH(sal.numalbaran)=5,CONCAT(sal.serie,'-0',sal.numalbaran),sal.numalbaran))))) AS 'Número',
                                        DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES') AS Fecha, sal.idcliente AS Cliente, 
                                        cli.nombrefiscal AS Nombre, pag.formadepago AS 'Forma de Pago',  efac.estadofactura AS Factura,
                                        FORMAT(sal.valortotal, 2, 'de_DE') AS Total, ecli.estadodepago AS Cobro, 'N' AS 'Imp.'                                        
                        FROM cabeceraSalidas sal
                        LEFT JOIN clientes cli ON sal.idcliente=cli.id
                        LEFT JOIN formasdepago pag ON sal.formadepago=pag.id
                        LEFT JOIN estadoscobroalbaranes ecli ON sal.estado=ecli.id
                        LEFT JOIN estadosalbaransalida efac ON sal.estadofactura=efac.id
                        WHERE sal.tipo='SALIDA' AND sal.subtipo='ALBARAN'                    
                        order by " . $orden . " " . $tipoOrden . " limit $filaspagina,$filas";
        }

        $this->db->query($salida);
        $resultado = $this->db->registros();
        
        return $resultado;
    }

        //metodo para scroll infinito

        public function consultaDatosSalidasScroll($offset, $limit, $buscar = '', $campo = '')
        {                
            $cond = '';
            $orden = ' ORDER BY sal.idcab DESC ';

            /*s
            if ($buscar != '' && $campo != '') {

                $nomCampo = '';

                if ($campo == 'todos') {
                                                                            
                    $cond = "AND CONCAT(sal.numalbcompleto, DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES'), sal.idcliente, cli.nombrefiscal,pag.formadepago,efac.estadofactura,ecli.estadodepago) LIKE '%" . $buscar . "%' ";                     

                }else{

                    switch ($campo) {
                        case 'numalbcompleto':
                            $nomCampo = 'sal.numalbcomplet';
                            break;
                            
                        case 'fecha':
                            $nomCampo = "DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES')";                                                     
                            break;

                        case 'idcliente':
                            $nomCampo = 'sal.idcliente';
                            break;
                            
                        case 'nombrefiscal':
                            $nomCampo = 'cli.nombrefiscal';
                            break;
                            
                        case 'formadepago':
                            $nomCampo = 'pag.formadepago';
                            break;
                            
                        case 'estadofactura':
                            $nomCampo = 'efac.estadofactura';
                            break;
                            
                        case 'estadodepago':
                            $nomCampo = 'ecli.estadodepago';
                            break;
                            
                        
                        default:
                            $nomCampo = 'sal.numalbcompleto';
                            break;
                    }
                    
                    $cond = "AND " . $nomCampo . " LIKE '" . $buscar . "%' ";
                }
                
            }     */

            //===== nuevo
            if ($buscar != '' && $campo == 'todos') {
                $nomCampo = '';
                if ($campo == 'todos') {
                    $cond = "AND CONCAT(sal.numalbcompleto, DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES'), sal.idcliente, cli.nombrefiscal,pag.formadepago,efac.estadofactura,ecli.estadodepago) LIKE '%" . $buscar . "%' ";   

                } else {
  
                }
            }else if ($campo != 'todos' && $campo !=''){

                switch ($campo) {
                    case 'numalbcompleto':
                        $nomCampo = 'sal.numalbcompleto';
                        break;
                        
                    case 'fecha':
                        $nomCampo = "DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES')";                           
                        break;

                    case 'idcliente':
                        $nomCampo = 'sal.idcliente';
                        break;
                        
                    case 'nombrefiscal':
                        $nomCampo = 'cli.nombrefiscal';
                        break;
                        
                    case 'formadepago':
                        $nomCampo = 'pag.formadepago';
                        break;
                        
                    case 'estadofactura':
                        $nomCampo = 'efac.estadofactura';
                        break;
                        
                    case 'estadodepago':
                        $nomCampo = 'ecli.estadodepago';
                        break;
                        
                    case 'valortotal':
                        $nomCampo = 'sal.valortotal';
                        break;

                    case 'iva4':
                        $nomCampo = 'iva4';
                        break; 

                    case 'iva10':
                        $nomCampo = 'iva10';
                        break; 

                    case 'iva21':
                        $nomCampo = 'iva21';
                        break; 
                    

                    default:
                        $nomCampo = 'sal.numalbcompleto';
                        break;
                }
                if ($buscar != '') {
                    $cond = "AND " . $nomCampo . " LIKE '" . $buscar . "%' ";
                }else{
                    if (isset($_POST['orden']) && $_POST['orden'] != '') {
                        $orden = " ORDER BY ".$nomCampo." ".$_POST['orden'];
                    }
                         
                }                    
            }else if (isset($_POST['filtrar']) && $_POST['filtrar'] == 'busquedaFiltros') {

                if ($_POST['searchFactura'] != '') {
                    $cond .= " AND sal.estadofactura = ".$_POST['searchFactura'];
                }
                if ($_POST['searchCobro'] != '') {
                    $cond .= " AND sal.estado = ".$_POST['searchCobro'];
                }
                if (isset($_POST['searchTipoIva']) && $_POST['searchTipoIva'] != '') {
                    $cond .= " AND mov.iva = ".$_POST['searchTipoIva'];
                }
                
                if (isset($_POST['fechaInicio']) && $_POST['fechaInicio'] != '' && isset($_POST['fechaFin']) && $_POST['fechaFin'] != '') {
                    $cond .= " AND sal.fecha  BETWEEN '". $_POST['fechaInicio'] ."' AND '". $_POST['fechaFin'] ."' ";
                }    
                                                 
            }
            //=====
    
            $salida = "SELECT sal.idcab, sal.numalbcompleto,
                        DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES') AS Fecha, sal.idcliente AS Cliente, 
                        cli.nombrefiscal AS Nombre, pag.formadepago,  efac.estadofactura AS Factura,
                        ROUND(sal.valortotal, 2) AS Total, ecli.estadodepago AS Cobro, sal.estado                                  
                        FROM cabeceraSalidas sal
                        LEFT JOIN clientes cli ON sal.idcliente=cli.id
                        LEFT JOIN formasdepago pag ON sal.formadepago=pag.id
                        LEFT JOIN estadoscobroalbaranes ecli ON sal.estado=ecli.id
                        LEFT JOIN estadosalbaransalida efac ON sal.estadofactura=efac.id
                        WHERE sal.tipo='SALIDA' AND sal.subtipo='ALBARAN'  " . $cond . "
                        ". $orden ."
                        LIMIT " . $limit . " OFFSET " . $offset;
                        
            $this->db->query($salida);
            
            $resultado = $this->db->registros();
    
            return $resultado;
        }
                
        public function consultaDatosSalidasScrollParaUsuarioAdmin($offset, $limit, $buscar = '', $campo = '')
        {        
                $cond = '';
                $orden = ' ORDER BY sal.idcab DESC ';            

                //if ($buscar != '' && $campo != '') {
                if ($buscar != '' && $campo == 'todos') {
                    $nomCampo = '';
                    if ($campo == 'todos') {
                        $cond = "AND CONCAT(sal.numalbcompleto, DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES'), sal.idcliente, cli.nombrefiscal,pag.formadepago,efac.estadofactura,ecli.estadodepago) LIKE '%" . $buscar . "%' ";

                    } else {
      
                    }
                }else if ($campo != 'todos' && $campo !=''){

                    switch ($campo) {
                        case 'numalbcompleto':
                            $nomCampo = 'sal.numalbcompleto';
                            break;
                            
                        case 'fecha':
                            $nomCampo = "DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES')";                           
                            break;

                        case 'idcliente':
                            $nomCampo = 'sal.idcliente';
                            break;
                            
                        case 'nombrefiscal':
                            $nomCampo = 'cli.nombrefiscal';
                            break;
                            
                        case 'formadepago':
                            $nomCampo = 'pag.formadepago';
                            break;
                            
                        case 'estadofactura':
                            $nomCampo = 'efac.estadofactura';
                            break;
                            
                        case 'estadodepago':
                            $nomCampo = 'ecli.estadodepago';
                            break;
                            
                        case 'valortotal':
                            $nomCampo = 'sal.valortotal';
                            break;

                        case 'iva4':
                            $nomCampo = 'iva4';
                            break; 

                        case 'iva10':
                            $nomCampo = 'iva10';
                            break; 

                        case 'iva21':
                            $nomCampo = 'iva21';
                            break; 
                        

                        default:
                            $nomCampo = 'sal.numalbcompleto';
                            break;
                    }
                    if ($buscar != '') {
                        $cond = "AND " . $nomCampo . " LIKE '" . $buscar . "%' ";
                    }else{
                        if (isset($_POST['orden']) && $_POST['orden'] != '') {
                            $orden = " ORDER BY ".$nomCampo." ".$_POST['orden'];
                        }
                             
                    }                    
                }else if (isset($_POST['filtrar']) && $_POST['filtrar'] == 'busquedaFiltros') {

                    if ($_POST['searchFactura'] != '') {
                        $cond .= " AND sal.estadofactura = ".$_POST['searchFactura'];
                    }
                    if ($_POST['searchCobro'] != '') {
                        $cond .= " AND sal.estado = ".$_POST['searchCobro'];
                    }
                    if ($_POST['searchTipoIva'] != '') {
                        $cond .= " AND mov.iva = ".$_POST['searchTipoIva'];
                    }
                    
                    if (isset($_POST['fechaInicio']) && $_POST['fechaInicio'] != '' && isset($_POST['fechaFin']) && $_POST['fechaFin'] != '') {
                        $cond .= " AND sal.fecha  BETWEEN '". $_POST['fechaInicio'] ."' AND '". $_POST['fechaFin'] ."' ";
                    }                                        
                                                     
                }
                        
                $salida = "SELECT sal.idcab, sal.numalbcompleto,
                            DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES') AS Fecha, sal.idcliente AS Cliente, 
                            cli.nombrefiscal AS Nombre, pag.formadepago,  efac.estadofactura AS Factura,
                            ROUND(sal.valortotal, 2) AS total, 
                            SUM(sal.valortotal) AS totales,sal.estado, ecli.estadodepago AS Cobro,
                            if(mov.iva = 21,ROUND((mov.total*mov.iva/100),2),0) AS iva21,
                            if(mov.iva = 10,ROUND((mov.total*mov.iva/100),2),0) AS iva10,
                            if(mov.iva = 4,ROUND((mov.total*mov.iva/100),2),0) AS iva4 
                            FROM cabeceraSalidas sal
                            LEFT JOIN clientes cli ON sal.idcliente=cli.id
                            LEFT JOIN formasdepago pag ON sal.formadepago=pag.id
                            LEFT JOIN estadoscobroalbaranes ecli ON sal.estado=ecli.id
                            LEFT JOIN estadosalbaransalida efac ON sal.estadofactura=efac.id
                            LEFT JOIN movimientos mov ON sal.idcab=mov.idcab
                            WHERE  sal.tipo='SALIDA' AND sal.subtipo='ALBARAN'  " . $cond . "
                            GROUP BY sal.idcab
                            ". $orden ."
                            LIMIT " . $limit . " OFFSET " . $offset;
                                    
                $this->db->query($salida);              
               
                
                $resultado = $this->db->registros();                                                   
                return $resultado;
        }

        public function consultaDatosSalidasScrollParaUsuarioAdminFiltrado($offset, $limit, $activarFiltro, $condCompleta)
        {                                      
           if ($activarFiltro == 1) {
                    
          

                $salida = "SELECT sal.idcab, sal.numalbcompleto,
                            DATE_FORMAT(sal.fecha, '%d/%m/%Y', 'es_ES') AS Fecha, sal.idcliente AS Cliente, 
                            cli.nombrefiscal AS Nombre, pag.formadepago,  efac.estadofactura AS Factura,
                            FORMAT(sal.valortotal, 2, 'de_DE') AS Total, ecli.estadodepago AS Cobro, 'N' AS 'Imp',
                            sal.idcab, mov.cantidad, mov.precioventa, mov.descuento, mov.iva, 
                            ROUND( SUM(mov.cantidad * mov.precioventa * (1 - mov.descuento/100) * (1 - mov.iva/100 )),2) AS bimp
                            FROM movimientos mov
                            LEFT JOIN cabeceraSalidas sal ON mov.idcab = sal.idcab
                            LEFT JOIN clientes cli ON sal.idcliente=cli.id
                            LEFT JOIN formasdepago pag ON sal.formadepago=pag.id
                            LEFT JOIN estadoscobroalbaranes ecli ON sal.estado=ecli.id
                            LEFT JOIN estadosalbaransalida efac ON sal.estadofactura=efac.id
                            WHERE  mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' " . $condCompleta . "
                            GROUP BY mov.idcab
                            ORDER BY sal.idcab DESC
                            LIMIT " . $limit . " OFFSET " . $offset;
                            
              
                $this->db->query($salida);

                $resultado = $this->db->registros();                
                
                return $resultado;
            
            }

           
            
        }

        public function consolidarValorIvaPorTipoPorIdCab($idcab)
        {
            //$idcab = 147;
            $salida = "SELECT 
                        ROUND(SUM(IF(mov.iva = 0, mov.cantidad * mov.precioventa * (1-mov.iva/100), 0)),2) exento,
                        ROUND(SUM(IF(mov.iva = 4, mov.cantidad * mov.precioventa * (1-mov.iva/100), 0)), 2) iva4,
                        ROUND(SUM(IF(mov.iva = 10, mov.cantidad * mov.precioventa * (1-mov.iva/100), 0)), 2) iva10,
                        ROUND(SUM(IF(mov.iva = 21, mov.cantidad * mov.precioventa * (1-mov.iva/100), 0)), 2) iva21
                        FROM cabeceraSalidas sal 
                        LEFT JOIN movimientos mov ON sal.idcab=mov.idcab
                        WHERE sal.idcab = $idcab AND mov.tipo='SALIDA' AND mov.subtipo='ALBARAN'";
                        
            $this->db->query($salida);
         
            $resultado = $this->db->registro();
           
            return $resultado;
        }
    
  
    public function consultadatossalida($idcab)
    {
        $salida = "SELECT 
                        mov.idarticulo AS 'Artículo',
                        mov.descripcion AS 'Descripción',
                        ROUND(mov.cantidad,2) AS 'Cantidad',                        
                        ROUND(mov.precioventa,2) AS 'Precio',
                        ROUND(mov.descuento,2) AS '%Dscto',
                        ROUND(mov.total,2) AS 'Total',
                        ROUND(mov.iva,2) AS '%Iva'            
                        FROM movimientos mov 
                        WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND mov.idcab=" . $idcab;

        $this->db->query($salida);
        $resultado = $this->db->registros();

        return $resultado;
    }


    public function formasDePago()
    {
        $this->db->query("SELECT * FROM formasdepago");
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function estadosDePago()
    {
        $this->db->query("SELECT * FROM estadoscobroalbaranes");
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function clientes()
    {
        $this->db->query("SELECT id, nombrefiscal, nombrecomercial FROM clientes WHERE activo=1 and habitual=1");
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function agentes()
    {
        $this->db->query("SELECT * FROM usuarios usu WHERE usu.status='activo'");
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function albaranSalidaCabecera($idcab)
    {
        $this->db->query("SELECT CONCAT(cli.direccion,' ',cli.poblacion,' ',cli.codigopostal,' ',cli.provincia) AS direccion, 
                        cli.tarifa, tar.tarifa AS nombreTarifa, cli.carnet,
                        cab.*, est.estadodepago as estadoCobro,
                        usu.user_name AS nomAgente, IF(cab.almacen=1,'General','') AS nomAlmacen, 
                        cli.nombrefiscal AS nomCliente, pag.formadepago as nomFormaPago
                        FROM cabeceraSalidas cab
                        LEFT JOIN clientes cli ON cab.idcliente=cli.id
                        LEFT JOIN tipostarifas tar ON cli.tarifa=tar.id
                        LEFT JOIN estadoscobroalbaranes est ON cab.estado=est.id
                        LEFT JOIN usuarios usu ON cab.agente=usu.ID
                        LEFT JOIN formasdepago pag ON cab.formadepago=pag.id
                        WHERE idcab =" . $idcab);
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function albaranTotalesSalidaCabecera($idcab)
    {
        $this->db->query("SELECT ROUND(cab.baseimponible, 2) as baseimponible, 
                        ROUND(cab.valordescuento, 2) as valordescuento, 
                        ROUND(cab.valoriva,2) as valoriva
                        FROM cabeceraSalidas cab       
                        WHERE idcab =" . $idcab);
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function albaranSalidaCabeceraPorNumeroAlbaran($numAlbaran)
    {
        $this->db->query("SELECT CONCAT(cli.direccion,' ',cli.poblacion,' ',cli.codigopostal,' ',cli.provincia) AS direccion, 
                        cli.tarifa, tar.tarifa AS nombreTarifa, cli.carnet,
                        cab.*, est.estadodepago as estadoCobro,
                        usu.user_name AS nomAgente, IF(cab.almacen=1,'General','') AS nomAlmacen, 
                        cli.nombrefiscal AS nomCliente, pag.formadepago as nomFormaPago
                        FROM cabeceraSalidas cab
                        LEFT JOIN clientes cli ON cab.idcliente=cli.id
                        LEFT JOIN tipostarifas tar ON cli.tarifa=tar.id
                        LEFT JOIN estadoscobroalbaranes est ON cab.estado=est.id
                        LEFT JOIN usuarios usu ON cab.agente=usu.ID
                        LEFT JOIN formasdepago pag ON cab.formadepago=pag.id
                        WHERE cab.numalbcompleto= '$numAlbaran' ");
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function albaranSalidaConceptos($idcab)
    {
        $this->db->query("SELECT * FROM movimientos mov 
                        WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND mov.idcab=" . $idcab);
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function tiposIva()
    {
        $this->db->query("SELECT * FROM tiposiva");
        $resultados = $this->db->registros();
        return $resultados;
    }

    public function validarAlbaranesAFacturar($albaranes)
    {
        $retorno = 'true';
        foreach ($albaranes as $key) {
            $this->db->query("SELECT estadofactura FROM cabeceraSalidas cab
                            WHERE cab.tipo='SALIDA' AND cab.subtipo='ALBARAN'
                            AND cab.idcab='$key'");
            $resultado = $this->db->registro();

            if ($resultado->estadofactura != 1) {
                $retorno = 'false';
                break;
            }
        }

        return $retorno;
    }

    public function validarAlbaranesAFacturarByIdCab($albaranes)
    {
        $retorno = 'true';
        foreach ($albaranes as $key) {
            $this->db->query("SELECT estadofactura FROM cabeceraSalidas cab
                            WHERE cab.tipo='SALIDA' AND cab.subtipo='ALBARAN'
                            AND cab.idcab='$key'");
            $resultado = $this->db->registro();

            if ($resultado->estadofactura != 1) {
                $retorno = 'false';
                break;
            }
        }

        return $retorno;
    }

    public function obtenerIdCabeceraSalida($numAlbaranComp)
    {
        $this->db->query("SELECT idcab FROM cabeceraSalidas cab
                        WHERE cab.tipo='SALIDA' AND cab.subtipo='ALBARAN'
                        AND cab.numalbcompleto='$numAlbaranComp'");
        $resultado = $this->db->registro();
        return $resultado->idcab;
    }
    

    public function obtenerLineasAlbaranSalida($condicion)
    {
        $this->db->query("SELECT mov.* FROM cabeceraSalidas cab
                        LEFT JOIN movimientos mov ON cab.idcab=mov.idcab
                        WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND cab.idcab  $condicion ");
                        
        $resultado = $this->db->registros();
        return $resultado;
    }

   

    public function calcularTotalesVariosAlbaranes($condicion)
    {
           // traigo los totales de todos los albaranes y los sumo
           $this->db->query("SELECT SUM(baseimponible) AS totalBaseImponible,
                    SUM(valordescuento) AS totalValorDescuento,
                    SUM(valoriva) AS totalValorIva,
                    SUM(valortotal) AS total
            FROM cabeceraSalidas
            WHERE idcab $condicion ");

            $totales = $this->db->registro();
            return $totales;
    }

    public function crearCabeceraFacturaVenta($totales, $datos,$masivo)
    {     
        $this->db->query("INSERT INTO cabeceraFactVenta (tipo,subtipo,serie,numfactura,numfaccompleto,albaranes,fecha,
                                        formadepago,estadofactura,idcliente,almacen,creacion,baseimponible,
                                        valordescuento,valoriva,valortotal,orden,observaciones,valorcobrado,valorpendientecobro,masivo) 
                            VALUES (:tipo,:subtipo,:serie,:numfactura,:numfaccompleto,:albaranes,:fecha,:formadepago,
                                    :estadofactura,:idcliente,:almacen,:creacion,:baseimponible,
                                    :valordescuento,:valoriva,:valortotal,:orden,:observaciones,:valorcobrado,:valorpendientecobro,:masivo)");
        $this->db->bind(':tipo', 'SALIDA');
        $this->db->bind(':subtipo', 'FACTURA');
        $this->db->bind(':serie', $datos['serie']); 
        $this->db->bind(':numfactura', $datos['numfactura']);
        $this->db->bind(':numfaccompleto', $datos['numFacCompleto']);
        $this->db->bind(':albaranes', $datos['albaranes']); 
        $this->db->bind(':fecha', $datos['fecha']);
        $this->db->bind(':formadepago', $datos['formaDePago']); //otro, pueden venir de varios tipos
        $this->db->bind(':estadofactura', 1); //Pendiente
        $this->db->bind(':idcliente', $datos['cliente']);
        $this->db->bind(':almacen', 1);
        $this->db->bind(':creacion', $datos['creacion']);
        $this->db->bind(':baseimponible', $totales->totalBaseImponible);
        $this->db->bind(':valordescuento', $totales->totalValorDescuento);
        $this->db->bind(':valoriva', $totales->totalValorIva);
        $this->db->bind(':valortotal', $totales->total);
        $this->db->bind(':orden', $datos['orden']);
        $this->db->bind(':observaciones', $datos['observaciones']);
        $this->db->bind(':valorcobrado', 0);
        $this->db->bind(':valorpendientecobro', 0);
        $this->db->bind(':masivo', $masivo);


        if ($this->db->execute()) {
            $this->db->query("SELECT MAX(idfactura) as idfactura FROM cabeceraFactVenta");
            $res = $this->db->registro();
            return $res->idfactura;
        } else {
            return 0;
        }
    }

    public function obtenerAnioNumeracionActualFacturaVenta()
    {
        $this->db->query("SELECT idfactura, numfactura, YEAR(fecha) AS fecha FROM cabeceraFactVenta ORDER BY idfactura DESC LIMIT 1");
        $row = $this->db->registro();
        return $row;
    }

    public function cambiarEstadoAlbaranPendienteFacturado($numAlbaranes)
    {
        foreach ($numAlbaranes as $key) {
            $this->db->query("UPDATE cabeceraSalidas set estadofactura=2 WHERE numalbcompleto='" . $key . "'");
            $this->db->execute();
        }
    }

    public function cambiarEstadoAlbaranPendienteFacturadoByIdCab($idsAlbaranes)
    {
        foreach ($idsAlbaranes as $key) {
            $this->db->query("UPDATE cabeceraSalidas set estadofactura=2 WHERE idcab='" . $key . "'");
            $this->db->execute();
        }
    }

    public function vincularAlbaranFactura($numAlbaranes, $numFactura)
    {
        foreach ($numAlbaranes as $key) {
            $this->db->query("UPDATE cabeceraSalidas set idFact=$numFactura WHERE idcab='" . $key . "'");
            $this->db->execute();
        }
    }

    public function vincularAlbaranFacturaByIdCab($idsAlbaranes, $idFactura)
    {
        foreach ($idsAlbaranes as $key) {
            $this->db->query("UPDATE cabeceraSalidas set idFact=$idFactura WHERE idcab='" . $key . "'");
            $this->db->execute();
        }
    }

    public function crearLineasFacturaVenta($datos, $insCabecera)
    {
        $numeroLineas = count($datos['todasLasLineas']);
        $cont = 0;
        foreach ($datos['todasLasLineas'] as $key) {
            if (isset($key->lotes) && $key->lotes != '') {
                $lotes = $key->lotes;
            }else{
                $lotes = [];
            }                 

            $cont++;

            $this->db->query("INSERT INTO lineasFacVenta (idfactura,almacen,idarticulo,descripcion,cantidad,precio,descuento,iva,total,lotes) 
                                VALUES (:idfactura,:almacen,:idarticulo,:descripcion,:cantidad,:precio,:descuento,:iva,:total,:lotes)");

            $this->db->bind(':idfactura', $insCabecera);
            $this->db->bind(':almacen', 1);
            $this->db->bind(':idarticulo', $key->idarticulo);
            $this->db->bind(':descripcion', $key->descripcion);
            $this->db->bind(':cantidad', $key->cantidad);
            $this->db->bind(':precio', $key->precioventa);
            $this->db->bind(':descuento', $key->descuento);
            $this->db->bind(':iva', $key->iva);
            $this->db->bind(':total', $key->total);
            $this->db->bind(':lotes', json_encode($lotes));

            $this->db->execute();
        }

        if ($cont == $numeroLineas) {
            return true;
        } else {
            return false;
        }
    }

    public function obtenerTotalesDeTodasLasLinesPorTipoDeIva($insCabecera)
    {
        $this->db->query("SELECT
                        mov.iva as iva, 
                        SUM(mov.total) as total
                        FROM cabeceraSalidas sal 
                        LEFT JOIN movimientos mov ON sal.idcab=mov.idcab
                        WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND sal.idfact=$insCabecera
                        GROUP BY mov.iva");
        $totales = $this->db->registros();
        return $totales;
    }

    public function crearLineasFacturaVentaMasivo($key,$insCabecera)
    {    
            $this->db->query("INSERT INTO lineasFactVentaMasivo (idfactura,tipoiva,valortotal) 
                                VALUES (:idfactura,:tipoiva,:valortotal)");

            $this->db->bind(':idfactura', $insCabecera);
            $this->db->bind(':tipoiva', $key->iva);
            $this->db->bind(':valortotal', $key->total);            

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function exportarAlbaranSalida($id)
    {
        $this->db->query("SELECT cab.*, cli.nombrefiscal, cli.nif, 
                        CONCAT(cli.direccion,' ', cli.codigopostal, ' ', cli.provincia) AS direccion 
                        FROM cabeceraSalidas cab 
                        LEFT JOIN clientes cli ON cab.idcliente=cli.id
                        WHERE cab.numalbcompleto='$id'");
        $cabecera = $this->db->registro();

        $this->db->query("SELECT cab.idcab FROM cabeceraSalidas cab WHERE cab.numalbcompleto='$id'");
        $res = $this->db->registro();
        $idcab = $res->idcab;

        $this->db->query("SELECT * FROM movimientos mov WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND mov.idcab=" . $idcab);
        $lineas = $this->db->registros();

        $final = ['cabecera' => $cabecera, 'lineas' => $lineas];
        return $final;
    }

    public function albaranSalidaEditable($idcab)
    {
        $this->db->query("SELECT cab.idcab, cab.estado, cab.estadofactura 
                        FROM cabeceraSalidas cab
                        WHERE cab.estado = 1 AND cab.estadofactura = 1
                        AND cab.idcab = '$idcab' ");

        $resultado = $this->db->registro();

        if (isset($resultado->idcab) && $resultado->idcab > 0 ) {
            return 1;
        } else {
            return 0;
        }        
    }

    public function albaranSalidaNoEditable($idcab)
    {
        $this->db->query("SELECT * FROM cabeceraSalidas sal WHERE sal.idcab=:idcab");
        $this->db->bind(':idcab', $idcab);
        $resultado = $this->db->registro();

        $factura = $resultado->estadofactura;
        $cobro = $resultado->estado;

        if ($factura == 2 && $cobro == 2) { //Albarán facturado y cobrado al 100%
            $albaranNoEditable = true;
        } else {
            $albaranNoEditable = false;
        }
        return $albaranNoEditable;
    }


    public function crearCabeceraSalida($cabecera, $tipo, $subtipo, $numAlbaran, $numAlbCompleto, $creacion)
    {      
        $this->db->query("INSERT INTO cabeceraSalidas (tipo,subtipo,serie,numalbaran,numalbcompleto,fecha,formadepago,estado,idcliente,almacen,agente,creacion,
                                    baseimponible,valordescuento,valoriva,valortotal,valorcobrado,valorpendientecobro,idclinohabitual) 
                            VALUES (:tipo,:subtipo,:serie,:numalbaran,:numalbcompleto,:fecha,:formadepago,:estado,:idcliente,:almacen,:agente,:creacion,
                            :baseimponible,:valordescuento,:valoriva,:valortotal,:valorcobrado,:valorpendientecobro,:idclinohabitual)");

        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':subtipo', $subtipo);
        $this->db->bind(':serie', $cabecera['serie']);
        $this->db->bind(':numalbaran', $numAlbaran);
        $this->db->bind(':numalbcompleto', $numAlbCompleto);
        $this->db->bind(':fecha', $cabecera['fecha']);
        $this->db->bind(':formadepago', $cabecera['formadepago']);
        $this->db->bind(':estado', $cabecera['estado']); //estado de cobro (1-Pendiente, 2-Cobrado, 3-Cobrado parcial)
        $this->db->bind(':idcliente', $cabecera['idCliente']);
        $this->db->bind(':almacen', $cabecera['almacen']);
        $this->db->bind(':agente', $cabecera['agente']);
        $this->db->bind(':creacion', $creacion);
        $this->db->bind(':baseimponible', $cabecera['totalBaseImponible']);
        $this->db->bind(':valordescuento', $cabecera['totalValorDescuento']);
        $this->db->bind(':valoriva', $cabecera['totalValorIva']);
        $this->db->bind(':valortotal', $cabecera['totalDocumento']);
        $this->db->bind(':valorcobrado', $cabecera['totalCobrado']);
        $this->db->bind(':valorpendientecobro', $cabecera['totalPendienteCobro']);
        $this->db->bind(':idclinohabitual', $cabecera['idclinohabitual']);
       
      
        if ($this->db->execute()) {
            $this->db->query("SELECT MAX(idcab) as idcab FROM cabeceraSalidas");
            $res = $this->db->registro();
            $lastId = $res->idcab;
            return $lastId;
        } else {
            return 0;
        }
        
    }

    public function crearLineasSalida($insCabecera, $key, $cabecera, $lotesJson, $tipo, $subtipo)
    {                
        
            $this->db->query("INSERT INTO movimientos (tipo,subtipo,fecha,idcab,almacen,idarticulo,descripcion,cantidad,precio,precioventa,descuento,iva,total,cmpactual,lotes,ordenfila) VALUES (:tipo,:subtipo,:fecha,:idcab,:almacen,:idarticulo,:descripcion,:cantidad,:precio,:precioventa,:descuento,:iva,:total,:cmpactual,:lotes,:ordenfila)");

            $this->db->bind(':ordenfila', $key['ordenfila']);
            $this->db->bind(':tipo', $tipo);
            $this->db->bind(':subtipo', $subtipo);
            $this->db->bind(':fecha', $cabecera['fecha']);
            $this->db->bind(':idcab', $insCabecera);
            $this->db->bind(':almacen', $cabecera['almacen']);
            $this->db->bind(':idarticulo', $key['idArticulo']);
            $this->db->bind(':descripcion', $key['descripcion']);
            $this->db->bind(':cantidad', $key['cantidadArticulo']);
            $this->db->bind(':precio', $key['precioArticulo']);
            $this->db->bind(':precioventa', $key['precioVenta']);
            $this->db->bind(':descuento', $key['descuento']);
            $this->db->bind(':iva', $key['iva']);
            $this->db->bind(':total', $key['totalLinea']); //total no incluye iva
            $this->db->bind(':cmpactual', $key['cmpActual']);
            $this->db->bind(':lotes', $lotesJson);
            
            if ($this->db->execute()) {
                return true;
            }else{
                return false;
            }           
    }


    public function stockPorPartidasSegunIdArticulo($idArticulo)
    {
        $this->db->query("SELECT mov.idmov, mov.lotes, mov.cantidad FROM movimientosentradas mov WHERE mov.idarticulo=  '$idArticulo' ORDER BY mov.idmov ASC");        
        $filas = $this->db->registros();
        return $filas;
    }

    public function stockPorPartida($idMov)
    {
        $this->db->query("SELECT cantidad FROM movimientosentradas WHERE idmov=" . $idMov);
        $fila = $this->db->registro();
        return $fila->cantidad;
    }

    public function eliminarPartidaVacia($idMov)
    {
        $this->db->query('DELETE FROM movimientosentradas
                        WHERE idmov =' .$idMov);
        $this->db->execute();
        
    }

    public function eliminarMovimientoPorIdMov($idMov)
    {
        $this->db->query('DELETE FROM movimientos
                        WHERE idmov =' .$idMov);
        
        if ($this->db->execute()) {
            return 1;
        }else{
            return 0;
        }
        
    }

    
    public function loteTieneStockCero($idArticulo)
    {
        $this->db->query("SELECT idmov
                        FROM movimientosentradas 
                        WHERE cantidad = 0 AND idarticulo = '$idArticulo' ");

        $row = $this->db->registros();
        return $row;
    }
    
    
    public function eliminarPartidaNegativa($idMov)
    {
        $this->db->query("DELETE FROM movimientosentradas WHERE idmov = $idMov");        
        $this->db->execute();                
    }


    public function deleteCantidadLoteEnPartidaPorIdMov($numlote, $idMov)
    {
        $this->db->query('UPDATE movimientosentradas mov 
                        SET mov.lotes = JSON_REMOVE(mov.lotes, "$.'.$numlote.'") WHERE idmov =' . $idMov);
        $this->db->execute();
    }

    public function updateCantidadLoteEnPartidaPorIdMov($numlote, $idMov, $nuevaCant)
    {
        $this->db->query('UPDATE movimientosentradas mov
                        SET mov.lotes = JSON_SET(mov.lotes,   "$.'.$numlote.'", '.$nuevaCant.' )
                        WHERE idmov =' . $idMov);
        $this->db->execute();    
        
    }

    public function updateCantidadTotalPartidaPorIdMov($nuevaCantTotal, $idMov)
    {
        $this->db->query('UPDATE movimientosentradas
                        SET  cantidad =' . $nuevaCantTotal . '      
                        WHERE idmov =' . $idMov);   
        
        $this->db->execute();
    }

    public function obtenerStockTotalArticuloSegunLote($idArticulo, $numeroLote)
    {
        $this->db->query("SELECT SUM(JSON_VALUE(mov.lotes, '$.".$numeroLote."') ) AS suma
                        FROM movimientosentradas mov 
                        WHERE JSON_EXTRACT(mov.lotes,'$.".$numeroLote."') IS NOT null AND mov.idarticulo= '$idArticulo' ");        
        $resultado = $this->db->registro();
        $stockTotalLote = $resultado->suma;
        return $stockTotalLote;
    }

    public function obtenerStockTotalArticulo($idArticulo)
    {
        $this->db->query("SELECT SUM(mov.cantidad) AS suma
                        FROM movimientosentradas mov 
                        WHERE mov.idarticulo= '$idArticulo' ");        
        $resultado = $this->db->registro();
        $stockTotal = $resultado->suma;
        return $stockTotal;
    }    

    public function obtenerTodasLasPartidaSegunLote($idArticulo, $numeroLote)
    {
        $this->db->query("SELECT mov.idmov, mov.cantidad AS cantTotPartida, JSON_VALUE(mov.lotes,'$.".$numeroLote."') AS cantLote         
                        FROM movimientosentradas mov 
                        WHERE JSON_EXTRACT(mov.lotes,'$.".$numeroLote."') IS NOT null AND mov.idarticulo= '$idArticulo' ORDER BY mov.idmov ASC");
        $resultado = $this->db->registros();
        return $resultado;

    }

    public function calculoCostoMedioPorArticulo($idArticulo)
    {
        $this->db->query("SELECT SUM(mov.cantidad) AS cantidadtotal, 
                        SUM(mov.cantidad * mov.precio) AS valortotal
                        FROM movimientosentradas mov 
                        WHERE /*mov.cantidad >0 AND*/ mov.idarticulo= '$idArticulo' ");
        
        $resultado = $this->db->registro();

        $cmp = 0;
        $stock = 0;
        if (isset($resultado->cantidadtotal) && $resultado->cantidadtotal != 0) { //puede ser negativo o positivo pero no cero
            $cmp = $resultado->valortotal / $resultado->cantidadtotal;
            $stock = $resultado->cantidadtotal;
        }
        
        return array('cmp'=>$cmp,'stock'=>$stock);

    }

    public function actualizarCMPyStockEnInventario($idArticulo, $stock, $cmp)
    {
        $this->db->query("SELECT idarticulo FROM inventario inv WHERE inv.idarticulo= '$idArticulo' ");
        $row = $this->db->registro();
        //$codArticulo = $row->idarticulo;

        if (isset($row->idarticulo) && $row->idarticulo != '') { //si existe hago update
            $codArticulo = $row->idarticulo;
            $this->db->query("UPDATE inventario 
                            SET  stock =" . $stock . ", coste=" . $cmp . "
                            WHERE idarticulo = '$codArticulo' ");
            $this->db->execute();
            
        } else { //sino hago insert
            $this->db->query('INSERT INTO inventario 
                            (idarticulo, stock, coste) 
                            VALUES(:idarticulo, :stock, :coste)');

            $this->db->bind(':idarticulo', $idArticulo);
            $this->db->bind(':stock', $stock);
            $this->db->bind(':coste', $cmp);
            $this->db->execute();

        }
    }

    
    public function actualizarCMPyStockEnFichaArticulo($idArticulo, $stock, $cmp)
    {
           $this->db->query("UPDATE articulos 
                            SET  stock =".$stock.", cmp=".$cmp."
                            WHERE codigoarticulo = '$idArticulo' ");
            $this->db->execute();

    }

    public function obtenerCabeceraGuardada($idcab)
    {
        //traigo los datos del albarán guardado
        $this->db->query("SELECT * FROM cabeceraSalidas WHERE idcab= :idcab");
        $this->db->bind(':idcab', $idcab);
        $cabeceraActual = $this->db->registro();
        return $cabeceraActual;
    }

    public function actualizarCabeceraSalida($cabecera)
    {
        //actualizo los datos en la cabecera del albarán        
        $this->db->query("UPDATE cabeceraSalidas 
                        SET agente = :agente, baseimponible=:baseimponible, 
                        valordescuento=:valordescuento, valoriva=:valoriva,valortotal=:valortotal
                        WHERE idcab= :idcab ");

        $this->db->bind(':idcab', $cabecera['idcab']);
        $this->db->bind(':agente', $cabecera['agente']);
        $this->db->bind(':baseimponible', $cabecera['totalBaseImponible']);
        $this->db->bind(':valordescuento', $cabecera['totalValorDescuento']);
        $this->db->bind(':valoriva', $cabecera['totalValorIva']);
        $this->db->bind(':valortotal', $cabecera['totalDocumento']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function insertNuevasLineasSalida($lineas, $cabecera)
    {

        $tipo = 'SALIDA';
        $subtipo = 'ALBARAN';

        $cont = 0;
        foreach ($lineas as $key) {
            if ($key['lotes']) {
                $lotes = $key['lotes'];
            }else{
                $lotes = [];
            }            
            $lotesJson = json_encode($lotes);  

            $this->db->query("INSERT INTO movimientos (tipo,subtipo,fecha,idcab,almacen,idarticulo,descripcion,cantidad,precio,descuento,iva,total,lotes) 
                                    VALUES (:tipo,:subtipo,:fecha,:idcab,:almacen,:idarticulo,:descripcion,:cantidad,:precio,:descuento,:iva,:total,:lotes)");

            $this->db->bind(':tipo', $tipo);
            $this->db->bind(':subtipo', $subtipo);
            $this->db->bind(':fecha', date('Y-m-d'));
            $this->db->bind(':idcab', $cabecera['idcab']);
            $this->db->bind(':almacen', $cabecera['almacen']);
            $this->db->bind(':idarticulo', $key['idArticulo']);
            $this->db->bind(':descripcion', $key['descripcion']);
            $this->db->bind(':cantidad', $key['cantidadArticulo']);
            $this->db->bind(':precio', $key['precioArticulo']);
            $this->db->bind(':descuento', $key['descuento']);
            $this->db->bind(':iva', $key['iva']);
            $this->db->bind(':total', $key['totalLinea']); //total no incluye iva
            $this->db->bind(':lotes', $lotesJson); //total no incluye iva


            $this->db->execute();
            $cont++;

            //Recálculo del CMP - costo medio ponderado de cada artículo vendido y del stock
            //$this->actualizacionColaPartidasEntradasYStock($key);
        }

        if ($cont == $cabecera['numArticulos']) { //valido que se han insertado todas las líneas
            return true;
        } else {
            return false;
        }
    }
        
    public function obtenerEmailClienteByNumAlbaran($idcab)
    {

        $this->db->query("SELECT  email FROM clientes
                        LEFT JOIN cabeceraSalidas ON cabeceraSalidas.idcliente=clientes.id
                        WHERE cabeceraSalidas.idcab=" . "'" . $idcab . "'");
        $resultado = $this->db->registro();        
        return $resultado;
    }
    
    public function obtenerEmailClienteByIdAlbaran($idCab)
    {

        $this->db->query("SELECT  email 
                        FROM clientes
                        LEFT JOIN cabeceraSalidas ON cabeceraSalidas.idcliente=clientes.id
                        WHERE cabeceraSalidas.idcab=" . "'" . $idCab . "'");
        $resultado = $this->db->registro();
        $resultado = $resultado->email;
        return $resultado;
    }



    //modelosCobros
    public function obtenerImportePendienteByNumAlbaran($idAlbaran)
    {
        $this->db->query("SELECT  cabeceraSalidas.valortotal as importeTotal
                        from cabeceraSalidas 
                        WHERE cabeceraSalidas.idcab=" . "'" . $idAlbaran . "'");

        $importeTotal = $this->db->registro();
        $importeTotal = $importeTotal->importeTotal;


        $this->db->query("SELECT  SUM(cobroAlbaran.totalCobro) as importeCobrado,fechaCobro,
                        totalCobro
                        from cobroAlbaran
                        WHERE cobroAlbaran.numAlbaranCompleto=" . "'" . $idAlbaran . "'");

        $importeCobrado = $this->db->registro();
        $importeCobrado = $importeCobrado->importeCobrado;

        $resultado = 0;

        if (!isset($importeCobrado)) {
            $resultado = $importeTotal;
        } else {
            $resultado = $importeTotal - $importeCobrado;
        }

        return $resultado;
    }
    
    public function obtenerHistorialCobrosByNumAlbaran($idAlbaran)
    {
        $this->db->query("SELECT DATE_FORMAT(fechaCobro, '%d/%m/%Y', 'es_ES') AS fechaCobro,ROUND(totalCobro,2) as totalCobro, contrapartida, concepto, observaciones, fom.formadepago
                        from cobroAlbaran
                        LEFT JOIN formasdepago fom ON cobroAlbaran.contrapartida=fom.id
                        WHERE cobroAlbaran.numAlbaranCompleto=" . "'" . $idAlbaran . "'");
        $historialCobros = $this->db->registros();
        return $historialCobros;
    }

    public function insertarCobroAlbaran($data)
    {
        $retorno = 0;
        
        $this->db->query("INSERT INTO cobroAlbaran (fechaCobro,totalCobro,
                        contrapartida,concepto,observaciones,numAlbaranCompleto) 
                        VALUES ('" . $data['fechaCobro'] . "'," . $data['importeCobrado'] . ",'" . $data['contrapartida'] . "','" . $data['conceptoCobro'] . "','" . $data['observaciones'] . "','" . $data['idAlbaran'] . "')");
        
        if ($this->db->execute()) {
            
            $retorno = 1;

            $this->db->query("SELECT  cabeceraSalidas.valortotal as importeTotal
                            from cabeceraSalidas 
                            WHERE cabeceraSalidas.idcab=" . "'" . $data['idAlbaran'] . "'");

            $importeTotal = $this->db->registro();
            $importeTotal = $importeTotal->importeTotal;


            $this->db->query("SELECT  SUM(cobroAlbaran.totalCobro) as importeCobrado
                        from cobroAlbaran
                        WHERE cobroAlbaran.numAlbaranCompleto=" . "'" . $data['idAlbaran'] . "'");

            $importeCobrado = $this->db->registro();
            $importeCobrado = $importeCobrado->importeCobrado;

            if ($importeTotal <= $importeCobrado) {

                $this->db->query("UPDATE cabeceraSalidas set estado=2 WHERE     
                            idcab='" . $data['idAlbaran'] . "'");
                $this->db->execute();

            } else if ($importeCobrado != 0) {
                $this->db->query("UPDATE cabeceraSalidas set estado=3 WHERE 
                                idcab='" . $data['idAlbaran'] . "'");
                $this->db->execute();
            }
        }
             
        return $retorno;
    }

    public function updatearEstadoFacturaAlbaran($arrayIdFactura)
    {        
        foreach ($arrayIdFactura as $idFactura) {
            $this->db->query("SELECT  albaranes as listaAlbaranesDeFactura
                            from cabeceraFactVenta
                            WHERE idfactura=" . $idFactura);

            $listaAlbaranesDeFactura = $this->db->registro();
            $listaAlbaranesDeFactura = $listaAlbaranesDeFactura->listaAlbaranesDeFactura;
            $signos = array("[", "]");
            $listaAlbaranesDeFactura = str_replace($signos, "", $listaAlbaranesDeFactura);
            $listaAlbaranes = explode(",", $listaAlbaranesDeFactura);

            $importePagadoTotal = 0;
            $importeFacturaTotal = 0;

            /*
            echo"<br>modelo<br>";
            var_dump($listaAlbaranes);
            die;
            */
            
            foreach ($listaAlbaranes as $idAlbaran) {

                $this->db->query("SELECT  cabeceraSalidas.valortotal as importeTotal
                                from cabeceraSalidas 
                                WHERE cabeceraSalidas.idcab=" . "'" . $idAlbaran . "'");

                $importeTotal = $this->db->registro();
                $importeTotal = $importeTotal->importeTotal;
                $importeFacturaTotal += $importeTotal;

                $this->db->query("SELECT  SUM(cobroAlbaran.totalCobro) as importeCobrado
                                from cobroAlbaran
                                WHERE cobroAlbaran.numAlbaranCompleto=" . "'" . $idAlbaran . "'");

                $importeCobrado = $this->db->registro();
                $importeCobrado = $importeCobrado->importeCobrado;
                $importePagadoTotal += $importeCobrado;
            }

            if ($importeFacturaTotal <= $importePagadoTotal) {
                //estadso=pagado
                $this->db->query("UPDATE cabeceraFactVenta set estadofactura=3 WHERE idfactura='" . $idFactura . "'");
                $this->db->execute();
            } else if ($importePagadoTotal > 0) {
                //estadso=parcialmente pagado
                $this->db->query("UPDATE cabeceraFactVenta set estadofactura=2 WHERE idfactura='" . $idFactura . "'");
                $this->db->execute();
            } else if ($importePagadoTotal == 0) {
                //estadso= pendiente
                $this->db->query("UPDATE cabeceraFactVenta set estadofactura=1 WHERE idfactura='" . $idFactura . "'");
                $this->db->execute();
            }
        }
    }

    public function cogerNumeroFactura($numAlbaran)
    {
        $this->db->query("SELECT  idFact as numeroFactura
                        from cabeceraSalidas
                        WHERE cabeceraSalidas.numalbcompleto=" . "'" . $numAlbaran . "'");

        $numeroFactura = $this->db->registro();
        $numeroFactura = $numeroFactura->numeroFactura;
        return $numeroFactura;
    }

    public function cogerNumeroFacturaByIdCab($idcab)
    {
        $this->db->query("SELECT  idFact as numeroFactura
                        from cabeceraSalidas
                        WHERE cabeceraSalidas.idcab=" . "'" . $idcab . "'");

        $numeroFactura = $this->db->registro();
        $numeroFactura = $numeroFactura->numeroFactura;
        return $numeroFactura;
    }



    public function validarAlbaranACobrar($idAlbaran)
    {
        $retorno = 'true';

        $this->db->query("SELECT estado FROM cabeceraSalidas cab
                            WHERE cab.idcab='$idAlbaran'");
        $resultado = $this->db->registro();

        if ($resultado->estado != 2) {
            $retorno = 'false';
        }


        return $retorno;
    }

    public function validarSiSeCobraAlbaran($idAlbaran)
    {
        $retorno = 0;

        $this->db->query("SELECT estado FROM cabeceraSalidas cab
                            WHERE cab.idcab='$idAlbaran'");
        $resultado = $this->db->registro();

        if ($resultado->estado == 2) {
            $retorno = 1;
        }


        return $retorno;
    }

    public function insertarDatosEmail($fecha,$nombreUsuario,$idDocumento,$tipoDoc,$email)
    {

        $this->db->query("INSERT INTO enviodocumentos (fechaenvio,nombreusuario,iddocumento,tipodocumento,emailenviado) 
                            VALUES (:fechaenvio,:nombreusuario,:iddocumento,:tipodocumento,:emailenviado) ");
    
        $this->db->bind(':fechaenvio', $fecha);
        $this->db->bind(':nombreusuario', $nombreUsuario);
        $this->db->bind(':iddocumento', $idDocumento);
        $this->db->bind(':tipodocumento', $tipoDoc);
        $this->db->bind(':emailenviado', $email);

        $this->db->execute();
    }

    //==================

    public function obtenersIdCabByNumAlbaran($numAlbaranComp)
    {

        $this->db->query("SELECT idcab FROM cabeceraSalidas WHERE numalbcompleto=" . "'" . $numAlbaranComp . "'");
        $resultado = $this->db->registro();
        $resultado = $resultado->idcab;
        return $resultado;
    }

    public function obtenersClienteIdByIdAlbaran($idAlbaran)
    {
        $this->db->query("SELECT idcliente FROM cabeceraSalidas WHERE idcab=$idAlbaran");
        $resultado = $this->db->registro();
      
        $idCliente = $resultado->idcliente;
        return $idCliente;
    }

    public function obtenerDatosClienteById($id)
    {
        $this->db->query("SELECT * FROM clientes WHERE id=$id");
        $resultado = $this->db->registro();

        return $resultado;
    }

    public function obtenerDatosAlbaranById($idAlbaran)
    {
        $this->db->query("SELECT * FROM cabeceraSalidas WHERE idcab=$idAlbaran");
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function obtenerFormaDePago($idAlbaran)
    {
        $this->db->query("SELECT formasdepago.formadepago FROM formasdepago
        LEFT JOIN cabeceraSalidas ON formasdepago.id=cabeceraSalidas.idcab
         WHERE idcab=$idAlbaran");
        $resultado = $this->db->registro();

        if ($resultado == "") {
            $resultado = "";
        } else {
            $resultado = $resultado->formadepago;
        }

        return $resultado;
    }

    public function obtenerFormaDePagoByidFormadePago($idFormadePago)
    {
        $this->db->query("SELECT formasdepago.formadepago FROM formasdepago
         WHERE id=$idFormadePago");
        $resultado = $this->db->registro();

        if ($resultado == "") {
            $resultado = "";
        } else {
            $resultado = $resultado->formadepago;
        }

        return $resultado;
    }

    public function obtenerlineasAlbaranById($idAlbaran)
    {
        $this->db->query("SELECT * FROM movimientos 
                    WHERE idcab=$idAlbaran 
                    AND subtipo='ALBARAN' AND tipo='SALIDA'");
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function listarAlbaranesConCriterios($arr)
    {
        $cond = '';
        if ($arr['idCliente'] >0) {
            $cond = "AND cab.idcliente =".$arr['idCliente'];
        }
        $this->db->query("SELECT cli.nombrefiscal, cab.*, pag.formadepago AS formapago, estf.estadofactura AS estadodefactura,
                        estc.estadodepago AS estadodecobro, usu.user_name AS nombreagente
                        FROM cabeceraSalidas cab 
                        LEFT JOIN clientes cli ON cab.idcliente=cli.id
                        LEFT JOIN formasdepago pag ON cab.formadepago=pag.id
                        LEFT JOIN estadosalbaransalida estf ON cab.estadofactura=estf.id
                        LEFT JOIN estadoscobroalbaranes estc ON cab.estado=estc.id
                        LEFT JOIN usuarios usu ON cab.agente = usu.ID
                        WHERE cab.fecha BETWEEN '".$arr['fechaInicio']."' AND '".$arr['fechaFin']."' 
                        ".$cond );
         
        
        $resultado = $this->db->registros();
        
        return $resultado;
    }
    

    public function obtenerNumAlbCompleto($numAlbaran)
    {
      
        $this->db->query("SELECT numalbcompleto FROM cabeceraSalidas 
        WHERE numalbaran=$numAlbaran");
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function obtenerNumAlbCompletoPorIdCab($idcab)
    {
      
        $this->db->query("SELECT numalbcompleto FROM cabeceraSalidas 
                        WHERE idcab=$idcab");
        $resultado = $this->db->registro();
        return $resultado->numalbcompleto;
    }

    public function obtenerCosteMedioPonderadoActual($idArticulo)
    {
        $this->db->query("SELECT coste FROM inventario inv WHERE idarticulo = '$idArticulo' ");

        $resultado = $this->db->registro();

        $coste = 0;
        if (isset($resultado->coste) && $resultado->coste != '' && $resultado->coste !=  null) {
            $coste = $resultado->coste;
        }
        return $coste;
    }


    public function validarDNIByIdCliente($idCliente){
        $this->db->query('SELECT nif FROM clientes WHERE id='.$idCliente);

        $resultado = $this->db->registro();

        if (isset($resultado->nif) && $resultado->nif != '') {
            $resultado = 1;
        }else{
            $resultado = 0;
        }
        return $resultado;
    }


    public function generarStockNegativoEnMovimientosEntradas($articulo, $cantNegativa, $precio, $insCabecera, $cabecera)
    {
        
        $this->db->query("INSERT INTO movimientosentradas (tipo,subtipo,fecha,idcab,almacen,idarticulo,descripcion,cantidad,precio,descuento,iva,total) 
        VALUES (:tipo,:subtipo,:fecha,:idcab,:almacen,:idarticulo,:descripcion,:cantidad,:precio,:descuento,:iva,:total)");

        $this->db->bind(':tipo', 'SALIDA');
        $this->db->bind(':subtipo', 'ALBARAN');
        $this->db->bind(':fecha', $cabecera['fecha']);
        $this->db->bind(':idcab', $insCabecera);
        $this->db->bind(':almacen', $cabecera['almacen']);
        $this->db->bind(':idarticulo', $articulo['idArticulo']);
        $this->db->bind(':descripcion', $articulo['descripcion']);
        $this->db->bind(':cantidad', $cantNegativa);
        $this->db->bind(':precio', $precio);
        $this->db->bind(':descuento', 0);
        $this->db->bind(':iva', 0);
        $this->db->bind(':total', 0);        

        $this->db->execute();
        /*if ($this->db->execute()) {
            return 1;
        }else{
            return 0;
        }*/
    }


    public function precioCompraProveedorHabitual($idArticulo)
    {
        $this->db->query("SELECT JSON_EXTRACT(art.proveedores,  CONCAT('$.',art.proveedorhabitual,'.precioCompra')) AS preciodecompra
                        FROM articulos art
                        WHERE art.id=" . $idArticulo);

        $resultado = $this->db->registro();
        
        $precioCompra = 0;
        if (isset($resultado->preciodecompra)) {
            $precio = json_decode($resultado->preciodecompra);
            if (isset($precio) && $precio != '') {
                $precioCompra = $precio;
            }
        }
        return $precioCompra;
    }

    
    public function precioCompraProveedorHabitualPorCodigoArticulo($codigoArticulo)
    {
        $this->db->query("SELECT JSON_EXTRACT(art.proveedores,  CONCAT('$.',art.proveedorhabitual,'.precioCompra')) AS preciodecompra
                        FROM articulos art
                        WHERE art.codigoarticulo= '$codigoArticulo' ");

        $resultado = $this->db->registro();
        
        $precioCompra = 0;
        if (isset($resultado->preciodecompra)) {
            $precio = json_decode($resultado->preciodecompra);
            if (isset($precio) && $precio != '') {
                $precioCompra = $precio;
            }
        }
        return $precioCompra;
    }

    public function insertarNuevosLotesQueVienenDeUnaVenta($articulo,$precio, $insCabecera, $cabecera, $key, $loteJson)
    {
        $cantNegativa = -$key['cantidadLote'];
        $this->db->query("INSERT INTO movimientosentradas (tipo,subtipo,fecha,idcab,almacen,idarticulo,descripcion,cantidad,precio,descuento,iva,total,lotes) 
        VALUES (:tipo,:subtipo,:fecha,:idcab,:almacen,:idarticulo,:descripcion,:cantidad,:precio,:descuento,:iva,:total,:lotes)");

        $this->db->bind(':tipo', 'SALIDA');
        $this->db->bind(':subtipo', 'ALBARAN');
        $this->db->bind(':fecha', $cabecera['fecha']);
        $this->db->bind(':idcab', $insCabecera);
        $this->db->bind(':almacen', $cabecera['almacen']);
        $this->db->bind(':idarticulo', $articulo['idArticulo']);
        $this->db->bind(':descripcion', $articulo['descripcion']);
        $this->db->bind(':cantidad', $cantNegativa);
        $this->db->bind(':precio', $precio);
        $this->db->bind(':descuento', 0);
        $this->db->bind(':iva', 0);
        $this->db->bind(':total', 0);
        $this->db->bind(':lotes', $loteJson);

        $this->db->execute();
    }

    public function comprobarLoteObligatorio($idArticulo)
    {
        $this->db->query("SELECT loteobligatorio FROM articulos ar  
                        WHERE ar.id =$idArticulo");
        $resultado = $this->db->registro();
        return $resultado->loteobligatorio;
    }

    

    public function obtenerDescuentosPorClientePorArticulo($post)
    {
        $this->db->query("SELECT cab.idcab, cab.idcliente, cli.nombrefiscal, CONCAT('ALBARAN : ',cab.numalbcompleto) AS documento, cab.fecha, mov.idarticulo, 
                        mov.descripcion, ROUND(mov.cantidad,2) AS cantidad , ROUND(mov.precioventa,2) AS precioventa, ROUND(mov.descuento, 2) AS pordescto,
                        ROUND((mov.cantidad * mov.precioventa * mov.descuento/100) ,2) AS valodescuento,
                        ROUND(mov.total,2) AS total, ROUND(mov.precio,2) AS coste
                        FROM cabeceraSalidas cab 
                        LEFT JOIN movimientos mov ON cab.idcab=mov.idcab
                        LEFT JOIN clientes cli ON cab.idcliente=cli.id
                        WHERE cab.idcliente= ".$post['idCliente']."
                        AND mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND mov.idarticulo= ".$post['idArticulo']."
                        ORDER BY cab.fecha desc");
        $resultado = $this->db->registros();
        return $resultado;
    }

    
    public function obtenerTodosLosDescuentosPorArticulo($post)
    {
        $this->db->query("SELECT cab.idcab, cab.idcliente, cli.nombrefiscal, 
                        CONCAT('ALBARAN : ',cab.numalbcompleto) AS documento, cab.fecha, mov.idarticulo, 
                        mov.descripcion, ROUND(mov.cantidad,2) AS cantidad , 
                        ROUND(mov.precioventa,2) AS precioventa, ROUND(mov.descuento, 2) AS descuento,
                        ROUND((mov.cantidad * mov.precioventa * mov.descuento/100) ,2) AS valordescuento,
                        ROUND(mov.total,2) AS total, ROUND(mov.precio,2) AS coste
                        FROM cabeceraSalidas cab 
                        LEFT JOIN movimientos mov ON cab.idcab=mov.idcab
                        LEFT JOIN clientes cli ON cab.idcliente=cli.id
                        WHERE mov.tipo='SALIDA' AND mov.subtipo='ALBARAN' AND mov.idarticulo= ".$post['idArticulo']."
                        ORDER BY cab.fecha desc");
        
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function estadoCobroDeAlbaranById($idAlbaran)
    {
        $this->db->query("SELECT est.*  
                        FROM cabeceraSalidas sal 
                        LEFT JOIN estadoscobroalbaranes est ON sal.estado=est.id
                        WHERE sal.idcab = '$idAlbaran' ");

        $resultado = $this->db->registro();
        return $resultado;
    }

    public function obtenerNumFacturaByIdFactura($idFactura)
    {
        $this->db->query("SELECT * FROM cabeceraFactVenta fac
                        WHERE fac.idfactura = '$idFactura' ");
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function datosAlbaranById($idAlbaran)
    {
        $this->db->query("SELECT *  
                        FROM cabeceraSalidas sal                        
                        WHERE sal.idcab = '$idAlbaran' ");

        $resultado = $this->db->registro();
        return $resultado;
    }

    public function listaSubfamiliasActivas()
    {
        $this->db->query("SELECT id, descripcion FROM subfamilias WHERE activo = 1 ");
        $resultado = $this->db->registros();
        return $resultado;
    }

    
    public function idArticuloPorCodigoArticulo($codigoArticulo)
    {
        $this->db->query("SELECT id FROM articulos WHERE codigoarticulo = '$codigoArticulo' ");
        $resultado = $this->db->registro();
        $dato = 0;
        if (isset($resultado->id) && $resultado->id >0) {
            $dato = $resultado->id;
        }
        return $dato;    
    }

    public function buscarCodigoArticulo($codigoArticulo)
    {
        $this->db->query("SELECT * FROM articulos art WHERE art.codigoarticulo = '$codigoArticulo' ");
        $resultado = $this->db->registro();
        return $resultado;
    }

    public function consultaDetallesSalidaPorIdCab($idAlbaran)
    {
        $this->db->query("SELECT mov.*, IF(art.id IS NULL OR art.id = '',0,1) AS existe,
                        IF(tomaInventario.contador > 0,1,0) AS tieneinventario
                        FROM movimientos mov
                        LEFT JOIN articulos art ON mov.idarticulo = art.codigoarticulo                        
                        
                        LEFT JOIN (
                            SELECT tom.idarticulo , COUNT(*) AS contador
                            FROM lineasTomaInvent tom WHERE tom.fecha >= CURDATE()
                            GROUP BY tom.idarticulo
                        ) AS tomaInventario ON mov.idarticulo = tomaInventario.idarticulo
                        
                        
                        WHERE mov.idcab= '$idAlbaran'
                        AND mov.subtipo='ALBARAN' AND mov.tipo='SALIDA' 
                        ORDER BY mov.ordenfila ASC ");
                        
        $resultado = $this->db->registros();
        return $resultado;
    }

    
    public function obtenerAnioDeUltimaFechaCabeceraSalidas()
    {
        $this->db->query("SELECT max(YEAR(cab.fecha)) AS anio FROM cabeceraSalidas cab");
        $res = $this->db->registro();       
        return $res->anio;
    }
    
    public function obtenerNumAlbaranCorrelativo($anio)
    {
        $this->db->query("SELECT max(cab.numalbaran) AS numalbaran  
                        FROM cabeceraSalidas cab 
                        WHERE (YEAR(cab.fecha) = '$anio')");
        $row = $this->db->registro();
        return $row->numalbaran;
    }

        
    public function obtenerAnioDeUltimaFechaCabeceraFactVentas()
    {
        $this->db->query("SELECT max(YEAR(cab.fecha)) AS anio FROM cabeceraFactVenta cab");
        $res = $this->db->registro();       
        return $res->anio;
    }

    public function obtenerNumFacturaCorrelativo($anio)
    {
        $this->db->query("SELECT max(cab.numfactura) AS numfactura
                        FROM cabeceraFactVenta cab 
                        WHERE (YEAR(cab.fecha) = '$anio')");
        $row = $this->db->registro();
        return $row->numfactura;
    }

    public function obtenerIdFormaDePagoPorNombre($formaDePagoTexto)
    {
        $this->db->query("SELECT id FROM formasdepago WHERE formadepago = '$formaDePagoTexto' ");
        $row = $this->db->registro();
        return $row->id;
    }
}
