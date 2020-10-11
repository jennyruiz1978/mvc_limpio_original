<?php

    /**
     * Clase UpDownLoadFichero :
     * 
     * Clase para la subida o descarga de ficheros
     * 
     * @autor Jenny Ruiz
     * 
     * @version 1.0 - Versión inicial - Octubre 10 de 2020
     * 
     * @copyright Data Lean Makers
     * 
     */


    class UpDownLoadFichero {

        /**
         * Función para la subida de un fichero
         * 
         * @return boolean true si el fichero se hasubido con éxito
         * 
         */
        public static function uploadFile($subDirectorio,$nombre,$nombreTmp){

            //DOCUMENTOS_PRIVADOS es una constante global definida en el config
            if(!file_exists(DOCUMENTOS_PRIVADOS.$subDirectorio)){
                mkdir(DOCUMENTOS_PRIVADOS.$subDirectorio,0777,true);
                    if(file_exists(DOCUMENTOS_PRIVADOS.$subDirectorio)){
                        if(move_uploaded_file($nombreTmp, DOCUMENTOS_PRIVADOS.$subDirectorio.'/'.$nombre)){
                            //echo "Archivo guardado con exito";
                            $upload = true;                        
                        }else{
                            $upload = false;                        
                            //echo "Archivo no se pudo guardar";
                        }
                    }
            }else{
                if(move_uploaded_file($nombreTmp, DOCUMENTOS_PRIVADOS.$subDirectorio.'/'.$nombre)){
                    $upload = true;                
                    //echo "Archivo guardado con exito";
                }else{
                    $upload = false;                
                    //echo "Archivo no se pudo guardar";
                }
            }
            
            return $upload;
        }

        public static function downloadFile($filename,$directorio)
        {                
            $file = DOCUMENTOS_PRIVADOS.$directorio."/".$filename;
            $mime = mime_content_type($file);
            header('Content-disposition: attachment; filename='.str_replace(" ",'_',$filename));
            header('Content-type: '.$mime);
            readfile($file);
            
        }    

        
    }