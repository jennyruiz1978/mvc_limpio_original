<?php
    require '../public/vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class ExportImportExcel {


        public static function exportToExcel($cabeceras,$lineas){
    
            $file = new Spreadsheet();
    
            $active_sheet = $file->getActiveSheet();
    
            $sentencia = $lineas;
            
            $active_sheet = $file->getActiveSheet();
                        
            foreach ($cabeceras as $letra => $cabecera) {                
                $active_sheet->setCellValue($letra, $cabecera);                
            }
            $count = 2;

            foreach($sentencia as $row)
            {                                
                foreach ($row as $key => $val) {

                    if ($clave = array_search($key,$cabeceras)) {
                        $columna= $clave[0];
                    }
                    
                    $active_sheet->setCellValue($columna . $count, $val);
                    
                }
                $count = $count + 1;

            }


          
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
            date_default_timezone_set("Europe/Madrid");
            $file_name = date('Y-m-d_H_i_s') . 'nombreFicheroEjemplo' . '.' . strtolower('Xlsx');
          
            $writer->save($file_name);
          
            header('Content-Type: application/x-www-form-urlencoded');
          
            header('Content-Transfer-Encoding: Binary');
          
            header("Content-disposition: attachment; filename=\"".$file_name."\"");
          
            readfile($file_name);
          
            unlink($file_name);
          
            exit;
        }

        
    }