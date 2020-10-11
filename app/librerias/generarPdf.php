<?php

require '../public/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

class generarPdf {

    public static function documentoPDF($orientacion,$formato,$idioma,$unicode,
            $codificacion,$margenes, $pdfa, $rutaCarpetasViews, $fichero) {

        ob_start();
        include_once '../app/views/' . $rutaCarpetasViews . '/' . $fichero;

        $html = ob_get_clean();
        $html2pdf = new Html2Pdf($orientacion,$formato,$idioma,$unicode,$codificacion,$margenes,$pdfa);
        $html2pdf->writeHTML($html);
        $html2pdf->output('ejemplo.pdf');
    }
    

    public static function documentoPDFParaEmail($orientacion,$formato,$idioma,$unicode,
        $codificacion,$margenes, $pdfa, $rutaCarpetasViews, $fichero, $datos='', $doc='') {
            
        ob_start();
        include_once '../app/views/' . $rutaCarpetasViews . '/' . $fichero;

        $html = ob_get_clean();    
        $html2pdf = new Html2Pdf($orientacion,$formato,$idioma,$unicode,$codificacion,$margenes,$pdfa);
        $html2pdf->writeHTML($html);        
        $attachment = $html2pdf->output('factura.pdf', $doc);
        return $attachment;
    }


}