<?php

class ModeloPresupuesto {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }

    public function insertarRegistroFichero($nombreFinal,$idPresupuesto,$tipo,$descripcion)
    {
        
        $this->db->query("INSERT INTO presupuestoDocs (idPresupuesto ,tipo,nombre,descripcion) 
        VALUES (:idPresupuesto,:tipo,:nombre,:descripcion)");

        $this->db->bind(':idPresupuesto', $idPresupuesto);
        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':nombre', $nombreFinal);
        $this->db->bind(':descripcion', $descripcion);

        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function obtenerFicheros()
    {
        $this->db->query("SELECT * FROM presupuestoDocs");
        $ficheros = $this->db->registros();
        return $ficheros;
    }

    public function verificarFichero($idDoc)
    {
        $this->db->query('SELECT * FROM presupuestoDocs WHERE idDocumento='.$idDoc);
        $row = $this->db->registro();
        return $row;
    }


}
