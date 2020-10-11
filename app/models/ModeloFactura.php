<?php

class ModeloFactura {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }

    public function obtenerDatosFactura($idFactura) {
    //array de ejemplo, aquí deben venir el array de la Base de datos
        $datosFactura = [
            'idFactura' => $idFactura,
            'nombreEmpresa'=> 'Ejemplo S.A',
            'baseImponible'=> '100.00',
            'iva'=> '10%',
            'total'=> '110.00'
        ];

        return $datosFactura;
    }

    public function obtenerListadoFacturas() {
        $this->db->query('SELECT fac.idfactura, fac.numfactura,fac.fechafactura, fac.importe,
                        fac.iva, fac.total, cli.NOMBREJURIDICO AS denominacion 
                        FROM facturascabecera fac
                        LEFT JOIN empresasclientes cli ON fac.idempresa=cli.idEMPRESA');       

        $facturas = $this->db->registros();

        return $facturas;
    }


    public function insertarFacturasBDDesdeExcel($datos){
        
        //inserta los datos
        $registrosImpor = 0;
        foreach ($datos as $key) {
            
            $this->db->query('INSERT INTO facturasImport (numfactura,fechafactura, importe,
                            iva, total,empresa)
                            VALUES (:numfactura,:fechafactura,:importe,:iva,:total,:empresa)');
            
            $this->db->bind(':numfactura',$key['numfactura']);
            $this->db->bind(':fechafactura',$key['fechafactura']);
            $this->db->bind(':importe',$key['importe']);
            $this->db->bind(':iva',$key['iva']);
            $this->db->bind(':total',$key['total']);
            $this->db->bind(':empresa',$key['denominacion']);
            $this->db->execute();
            
            $registrosImpor++;
        }

        return $registrosImpor++;
        
    }


    public function obtenerListadoFacturasImportadas() {
        $this->db->query('SELECT * FROM facturasImport');       

        $facturas = $this->db->registros();

        return $facturas;
    }


}
