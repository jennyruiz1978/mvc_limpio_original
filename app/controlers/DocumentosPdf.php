<?php

class DocumentosPdf extends Controlador {

   

    public function __construct() {
        
    
    }

    public function index() {
        generarPdf::documentoPDF('P','A4','es',true,'UTF-8',array(10,20,8,10),true, 'documentos/facturaPrueba/','ejemplo.php');
    }

   
}