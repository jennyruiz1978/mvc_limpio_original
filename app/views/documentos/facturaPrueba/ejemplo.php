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
        <table>
            <tr>
                <td class="factura">Factura</td>
                <td><h3>Fecha:  <?php echo date('d-m-Y'); ?></h3></td>
            </tr>
        </table>
        
            <table>

        </table>
     
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <table class="conceptos">
            <thead>
                <tr>
                    <th class="nombre">Nombre</th>
                    <th class="apellidos">Apellidos</th>
                    <th class="edad">Edad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $listado = [["Pedro","Silva",59],["Juan","Gómez",39],
                    ["Carlos","Sánchez",59],["Pedro","Silva",59]];
                
                foreach($listado as $tabla => $value){
                    echo "<tr>";
                    echo "<td class='nombre'>" . $value[0] . "</td>";
                    echo "<td class='apellidos'>" . $value[1] . "</td>";
                    echo "<td class='edad'>" . $value[2] . "</td>";
                    echo "</tr>";
                }
                
                ?>
               
            </tbody>


        </table>

    </body>
</html>