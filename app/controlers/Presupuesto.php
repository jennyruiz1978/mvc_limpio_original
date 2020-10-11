<?php


class Presupuesto extends Controlador
{

    public function __construct()
    {
        //$this->iniciar();
        $this->ModelPresupuesto = $this->modelo('ModeloPresupuesto');
    }


    public function index($msg=0){
        //$this->iniciar();

        $ficheros = $this->ModelPresupuesto->obtenerFicheros();
    
        $datos = [
            'ficheros' => $ficheros,
            'msg' => $msg
        ];

        $this->vista('/presupuesto/ficherosPresupuesto', $datos);
        
    }

    public function uploadFicheroPresupuesto()
    {        
        //este es la carpeta donde se guardará el fichero subido (puede ser presupuestos, proyectos, etc.)
        $subDirectorio = 'presupuesto';
        $ultimoDoc = 1;
        $nombreFichero = $_FILES['ficheroPresupuesto']['name'];
        $nombreFinal = $ultimoDoc."_".$nombreFichero;
        $nombreTmp = $_FILES['ficheroPresupuesto']['tmp_name'];
        $descripcion = $_POST['descripcionFichero'];
        $idPresupuesto = $_POST['idPresupuesto']; //identifica a qué presupuesto pertenece el fichero
        $tipo = $_FILES['ficheroPresupuesto']['type'];
        $datos = [];
        $datos['controlador'] = 'Presupuesto';
  
        //llamamos a la función que subirá el fichero a directorio
        $upload = UpDownLoadFichero::uploadFile($subDirectorio,$nombreFinal,$nombreTmp);
        $retorno = '';
        if ($upload == true) {
            //insertamos el registro del fichero subido en la BD
            $insert = $this->ModelPresupuesto->insertarRegistroFichero($nombreFinal,$idPresupuesto,$tipo,$descripcion);
            if ($insert) {
                $retorno = 1; //inserta registro con éxito
            }else{
                $retorno = 2; //no inserta registro
            }            
        }else{
            $retorno = 3; // no subió fichero ni se insertó en la BD
        }
        $variable = '<script type="text/javascript">window.location.href="'.RUTA_URL.'/'.$datos['controlador'].'/'.$retorno.'"</script>';
        echo $variable;
    }

    public function downloadFicheroPresupuesto($idDoc)
    {
        //$this->iniciar();
        
        //consulto el fichero en la BD
        $directorio = 'presupuesto';
        $row = $this->ModelPresupuesto->verificarFichero($idDoc);
        if ($row) {
            $filename = $row->nombre;
            UpDownLoadFichero::downloadFile($filename,$directorio);
        }else{
            echo"FICHERO NO ENCONTRADO";
        }

    }    
    

    

}
