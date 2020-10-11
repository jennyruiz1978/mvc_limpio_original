<!DOCTYPE HTML">
<html lang="es">
    <head>

        <meta charset="utf-8">
        <link rel="stylesheet" href="estilos.css" />
        <style>
            .conceptos {
                border-collapse: collapse;

            }
            .conceptos, .nombre, .apellidos, .edad {
                border: 1px solid black;    
            }
            th {
                background-color: burlywood;
            }
            th, td {
                padding: 5px;
                text-align: left;
            }
            td {
                height: 5px;
                vertical-align: bottom;
            }
            .nombre {
                width: 150px;  
            }
            .apellidos {
                width: 150px;  
            }
            .edad {
                width: 50px;  
            }
            .conceptos {
                margin-left: 50px;
                margin-top:30px;
            }
            .factura {
                width: 500px;
                font-size: 40px;
                font-weight: bold;
                
            }


        </style>



    </head>
    <body>
        <?php
            echo"
            <h3>".$datos['nombreEmpresa']."</h3>        
        
        <table>
            <tr>
                <td class='factura'>Factura Nº ".$datos['idFactura']." </td>
                <td><h3>Fecha:  ".date('d-m-Y')." </h3></td>
            </tr>
        </table>";
        ?>
            <table>

        </table>
     
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <table class="conceptos">
            <thead>
                <tr>
                    <th class="nombre">Base Imponible</th>
                    <th class="apellidos">Iva</th>
                    <th class="edad">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php                                             
                    echo "<tr>";
                    echo "<td class='nombre'>" .$datos['baseImponible']. "</td>";
                    echo "<td class='apellidos'>" .$datos['iva']. "</td>";
                    echo "<td class='edad'>" .$datos['total']. "</td>";
                    echo "</tr>";                               
                ?>
               
            </tbody>


        </table>

    </body>
</html>