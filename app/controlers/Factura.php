<?php
require '../public/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;


class Factura extends Controlador
{

    public function __construct()
    {
        //$this->iniciar();
        $this->ModelFactura = $this->modelo('ModeloFactura');
    }


    public function index($msg=0){
        //$this->iniciar();

        $facturas = $this->ModelFactura->obtenerListadoFacturas();
        $facturasImport = $this->ModelFactura->obtenerListadoFacturasImportadas();
    
        $datos = [
            'facturas' => $facturas,
            'facturasImport' => $facturasImport,
            'msg' => $msg
        ];

        $this->vista('/facturas/listadoFacturas', $datos);
        
    }

    public function enviarEmailConFacturaPdf()
    {  
        $datos = [
            'email' => $_POST['email'],
            'asunto' => $_POST['asunto'],
            'mensaje' => $_POST['mensaje'],
            'controlador' => 'inicio', //el nombre del controlador a donde regresara, puede ser dinámico
            'idFactura' => $_POST['idFactura'], //el nombre del controlador a donde regresara, puede ser dinámico
         ];

        $nombreRemitente = 'Nombre Remitente Ejemplo'; // puede ser el $_SESSION['nombre'] del usuario logueado o lo que sea
        $emailRemitente = 'info@dataleanmakers.es'; // o $_SESSION['mail'] si no usa casilla de correo de Data Lean Makers
        $nombreDestinatario = 'Nombre Destinatario Ejemplo'; //puedes traer el nombre con el idPersona que viene por post.
        $emailDestinatario = $datos['email'];
        $asunto = $datos['asunto'];   
        $message = $datos['mensaje'];

        //construyo el fichero pdf        
        $datosFactura = $this->ModelFactura->obtenerDatosFactura($datos['idFactura']);
        
        $attachment = generarPdf::documentoPDFParaEmail('L', 'A4', 'es', true, 'UTF-8', array(10,20,8,10), true, 'documentos/facturaPrueba', 'factura.php', $datosFactura, 'S');
        // la letra 'S' para no generar el documento sino un attachment
        
        //$attachment = generarPdf::documentoPDFParaEmail('L', 'A4', 'es', true, 'UTF-8', array(10,20,8,10), true, 'documentos/factura', 'factura.php', $datosFactura);

        Email::enviarEmailDestinatario($nombreRemitente, $emailRemitente, $nombreDestinatario, $emailDestinatario,$asunto,$message,$attachment,$datos);

    }


    public function exportarListaFacturas()
    {
        //datos de la consulta
        $datos = $this->ModelFactura->obtenerListadoFacturas();
        $titulos = $datos[0]; //obtengo un elemento del array para extraer los nombres para la cabecera del fichero
        
        $d = 'A'; //se refiere a la columna A del excel donde se recibirán los datos

        $inicio = 1; //se refiere a la fila 1 del excel donde se escribirán las cabeceras
        
        $cabecerasTmp = [];        
        //incorporo las letras correlativamente empezando en 'A'
        foreach ($titulos as $key => $value) {
            $key->col = $d.$inicio;                
            $cabecerasTmp[$d.$inicio] = $key;
            ++$d . PHP_EOL;
        }

        //las cabeceras puede tener los nombres de los campos de la BD, 
        //o en el array "reemplazos" se pueden cambiar los nombres
        $cambiarCabeceras = true;
        $reemplazos = array(
                    'A1' => 'Id Factura', 'B1' => 'Nº Factura', 'C1' => 'Fecha Factura', 'D1' => 'B. Imponible', 
                    'E1' => 'Tipo IVA(%)', 'F1' => 'Total(€)', 'G1' => 'Denominación', 'H1' => 'Cantidad', 
                    'I1' => 'Código', 'J1' => 'Concepto');
        
        //formatos para los diferentes datos
        
        
        if ($cambiarCabeceras == true) {
            $cabeceras = array_replace($cabecerasTmp, $reemplazos);
        }else{
            $cabeceras = $cabecerasTmp;
        }

        // si queremos darle formato a los datos podemos formatearlo desde la consulta msql
        // o con este bucle para cada una columna en particular
        foreach ($datos as $key) {
            foreach ($key as $k => $v) {                
                if ($k=='fechafactura') {
                    $val = date('d-m-Y',strtotime($v));
                    $key->fechafactura = $val; //sobreescribo la "fechafactura"          
                }else if($k=='importe'){
                    $val = number_format($v, 2, ',', '.');
                    $key->importe = $val; //sobreescribo el "importe"
                }
            }
        }
        
        //llamo a la librería
        ExportImportExcel::exportToExcel($cabeceras,$cabecerasTmp,$datos);

    }
    

    public function importarListaFacturas()
    {

        $subDirectorio = 'facturas';
        $ultimoDoc = 1;
        $nombreFichero = $_FILES['plantillaFacturas']['name'];
        $nombreFinal = $ultimoDoc."_".$nombreFichero;
        $nombreTmp = $_FILES['plantillaFacturas']['tmp_name'];
        $descripcion = $_POST['descripcionFichero'];
        $idPresupuesto = $_POST['idPresupuesto']; //identifica a qué presupuesto pertenece el fichero
        $tipo = $_FILES['plantillaFacturas']['type'];
        $datos = [];
        $datos['controlador'] = 'Presupuesto';
        
        //primero importamos el fichero
        $upload = UpDownLoadFichero::uploadFile($subDirectorio,$nombreFinal,$nombreTmp);
        if ($upload) {
            //si ha subido, que lo lea e importe los datos a la BD
            $importados = $this->obtenerDatosFicheroExcel($nombreFinal);
            if ($importados) {
                $retorno = true;
                redireccionar('/Factura');
            }else{
                echo"no se ha guardado los datos";
                $retorno = false;                
            }
        }else{
            echo"no se ha subido el fichero";
            $retorno = false;
        }
        return $retorno;
    
    }

    public function obtenerDatosFicheroExcel($name) {        
        
        $rutaArchivo = DOCUMENTOS_PRIVADOS."facturas/".$name;
       
        $documento = IOFactory::load($rutaArchivo);

        $sheet = $documento->getSheet(0);

        $numeroFacturas = 0;        
        
        $arr_data_facturas=[];

        // 2 es la fila donde empiezan los datos en el fichero excel
        foreach ($sheet->getRowIterator(2) as $row) {
            //a partir de aquí utiliza la librería directamente pero no es esa la idea, 
            //voy a modificarlo para que sea dinámico y
            //sirva para cualquier fichero excel que se importe
            $numfactura = trim($sheet->getCellByColumnAndRow(1,$row->getRowIndex()));
            $fechafactura = trim($sheet->getCellByColumnAndRow(2,$row->getRowIndex()));
            $importe = trim($sheet->getCellByColumnAndRow(3,$row->getRowIndex()));
            $iva = trim($sheet->getCellByColumnAndRow(4,$row->getRowIndex()));
            $total = trim($sheet->getCellByColumnAndRow(5,$row->getRowIndex()));
            $denominacion = trim($sheet->getCellByColumnAndRow(6,$row->getRowIndex()));

            if($numfactura=='' || $fechafactura=='' || $importe=='' || $iva=='' || $total=='' || $denominacion=='')
            continue;

            $data_facturas=[               
                'numfactura'=>$numfactura,
                'fechafactura'=>$fechafactura,
                'importe'=>$importe,
                'iva'=>$iva,
                'total'=>$total,
                'denominacion'=>$denominacion
            ];
            $arr_data_facturas[]=$data_facturas;
            $numeroFacturas++;

        }      
        
        $facturasImportadas= $this->ModelFactura->insertarFacturasBDDesdeExcel($arr_data_facturas);
        $data['facturasImportados']=$facturasImportadas;
        $data['numeroFacturas']=$numeroFacturas;

        //echo"Total de Trabajadores leidos: ".$data['numeroFacturas'];
        //echo"Total de Trabajadores importados: ".$data['facturasImportados'];
        if ($facturasImportadas>0) {
            // como ejemplo valido si se el número de registros insertados es mayor a cero
            //pero se puede validar el total de registros leídos versus registros insertados, etc
            $retorno = true;
        }else{
            $retorno = false;
        }
        return $retorno;
    }

}
