export class creargrilla {
    ruta = "";
    destino = "";
    contador = 0;
    clasesTabla= "";
    idTabla= "";    
    boton = [];

    constructor(ruta, destino, clasesTabla,boton,idTabla) {
        this.ruta = ruta;
        this.destino = destino;        
        this.clasesTabla = clasesTabla;
        this.idTabla = idTabla;
        this.boton = boton;        
        this.tabla(this.ruta, this.destino, this.clasesTabla,this.boton,this.idTabla);        

    }

    rendertabla(clases =this.clasesTabla,boton=this.boton, idTabla=this.idTabla) {
        this.tabla(this.ruta, this.destino, clases,boton,idTabla);        
    }

    tabla(ruta, destino, clases, boton, idTabla) {
        
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let datos = JSON.parse(this.responseText);

                var contenido = "";
                var numfila = 0;
                for (var i = 0; i < datos.length; i++) {
                    numfila++;
                    var titulos = Object.keys(datos[i]);
                    
                    var cabecera = `
                        <th class="titulos">Lin</th>`;
                    for (let index = 0; index < titulos.length; index++) {
                        
                        let alignTxtTitulo = 'text-right';
                        if (titulos[index] == 'Artículo' || titulos[index] == 'Descripción') {
                            alignTxtTitulo = 'text-left';
                        }

                        cabecera += `                        
                        <th class="titulos ${alignTxtTitulo}">${titulos[index]}</th>`;                    
                    }
                    //if (boton != "") {
                        cabecera += `<th class="titulos">Acciones</th>`;
                    //}
                    
                    contenido += `
                        <tr class="rows thead-light">
                        <td><input class="inputGrillaGuardado" name="numeroOrden[]" value="${numfila}" readonly></input></td>`;

                        
                    
                    for (var j in titulos) {
                        
                        var align = 'text-left';
                        if (titulos[j] != 'Artículo' && titulos[j] != 'Descripción') {
                            align = 'text-right';
                        }

                        var anchoDescripcion ='';
                        if (titulos[j] == 'Descripción') {
                            anchoDescripcion = 'celdaDescripcion';
                        }
                        
                        //incluyo el tag input con id respectivo
                    
                        var idInput ='';                       
                        var nameInput='';
                        var classCalculo='';
                        var classFilaDev = 'dblClickInputDev';
                        //var claseEdit = 'inputGrillaAuto';

                        
                        var edicion = 'readonly';                        

                        if (titulos[j] == 'Artículo') {
                            idInput = 'idArticulo';
                            nameInput="idArticulo[]";                            
                        }else if (titulos[j] == 'Descripción') {
                            idInput = 'descripcion';  
                            nameInput="descripcion[]";
                        }else if (titulos[j] == 'Cantidad') {
                            idInput = 'cantidadArticulo';  
                            nameInput="cantidadArticulo[]";
                            classCalculo="";
                        }else if (titulos[j] == 'Devolver') {
                            idInput = 'cantidadDevolver';  
                            nameInput="cantidadDevolver[]";
                            classCalculo="cantidadDevolver";
                            edicion = '';
                        }else if (titulos[j] == 'Precio') {
                            idInput = 'precioArticulo';
                            nameInput="precioArticulo[]";
                            classCalculo="precio";
                        }else if (titulos[j] == '%Dscto') {
                            idInput = 'descuento';
                            nameInput="descuento[]";
                            classCalculo="";
                        }else if (titulos[j] == '%Dcto. Dev') {
                            idInput = 'descuento';
                            nameInput="descuento[]";
                            classCalculo="descuentodDevolver";
                        }else if (titulos[j] == 'Total') {
                            idInput = 'totalLinea';
                            nameInput="totalLinea[]";
                            classCalculo="total";
                        }else if (titulos[j] == '%Iva') {
                            idInput = 'iva';
                            nameInput="iva[]";
                            classCalculo="";
                        }else if (titulos[j] == '%Iva Dev') {
                            idInput = 'iva';
                            nameInput="iva[]";
                            classCalculo="ivaDevolver";
                        }else if (titulos[j] == 'Cant. Pedida') {
                            idInput = 'cantidadArticulo'; 
                            nameInput="cantidadArticulo[]";
                            classFilaDev = '';
                        }else if (titulos[j] == 'Cant. Recibida') {
                            idInput = 'cantidadArticuloRec'; 
                            nameInput="cantidadArticuloRec[]";
                            classFilaDev = '';
                        }


                        contenido +=`                        
                        <td class="${anchoDescripcion}"><input name="${nameInput}" id="${idInput}_${numfila}" class="inputGrillaGuardado inputclick ${classCalculo} ${classFilaDev} ${align}" value="${datos[i][titulos[j]]}" ${edicion}></td>`;
                    }
                    if (boton != "") {
                        contenido +=`
                        <td class="botones text-center"><div class="d-flex justify-content-center">`;
                        //librerias de botones
                      
                        for (let i = 0; i < boton.length; i++) {
                            if (boton[i] == 'ver') {
                                contenido +=`<a class="btn btn-secondary px-1 mx-1 botonTablaAjaxMini ver"><i class="fas fa-eye pequenio"></i></a>`;
                            }
                            if (boton[i] == 'editar') {
                                contenido +=`<a class="btn btn-success px-1 mx-1 botonTablaAjaxMini"><i class="fas fa-pencil-alt"></i></a>`;
                            }
                            if (boton[i] == 'factura'){
                                contenido +=`<a class="btn btn-success px-1 mx-1 botonTablaAjaxMini facturarVenta" title="Facturar"><i class="fas fa-file-invoice-dollar"></i></a>`;
                            }       
                            if (boton[i] == 'checkbox'){
                                contenido +=`<input type="checkbox" name="check_albaran">`;
                            }
                            if (boton[i] == 'email'){
                                contenido +=`<a class="btn btn-primary px-1 mx-1 botonTablaAjaxMini enviarEmail" title="email"><i class="far fa-envelope"></i></a>`;
                            }
                            if (boton[i] == 'cobrar'){
                                contenido +=`<a class="btn btn-danger px-1 mx-1 botonTablaAjaxMini cobrarVenta" title="cobrar"><i class="fas fa-euro-sign iconoGenerico"></i></a>`;
                            }
                            if (boton[i] == 'pdf'){
                                contenido +=`<a class="btn btn-secondary px-1 mx-1 botonTablaAjaxMini exportarPdf" title="cobrar"><i class="fas fa-file-pdf mr-0 iconoGenerico"></i></a>`;
                            }
                            if (boton[i] == 'eliminar'){
                                contenido +=`<a class="btn btn-danger px-1 px-1 mx-1 botonTablaAjaxMini btnDeleteLinea" title="eliminar"><i class="fas fa-trash-alt mr-0 iconoGenerico"></i></a>`;
                            }
                            if (boton[i] == 'verInfo'){
                                contenido +=`<a class="btn btn-primary px-1 px-1 mx-1 botonTablaAjaxMini verDatosPedidoArticulo" title="ver información"><i class="fas fa-sign-in-alt mr-0 iconoGenerico"></i></a>`;
                            }     
                            if (boton[i] == 'verEstadisticasArtPed') {
                                contenido +=`<a class="btn btn-secondary px-1 mx-1 botonTablaAjaxMini verEstadisticasArtPed"><i class="fas fa-eye pequenio"></i></a>`;
                            }    
                        }
                        
                        contenido +=`</div></td>`;
                    }else{
                        contenido +=`
                        <td class="botones text-center"><div class="d-flex justify-content-center">&nbsp;</div></td>`;                        
                    }

                    contenido +=`</tr>`;
                }

                document.getElementById(destino).innerHTML = `
                <table class="${clases}" id="${idTabla}">
                    <thead><tr class="thead-light">${cabecera}</tr></thead>
                    <tbody>${contenido}</tbody>
                </table>
                `;
            }
        };
        xhr.open("POST", ruta, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(
            
        );
    } // fin metodo tabla

} // fin de la clase creargrilla

export default function construirgrilla(objeto,ruta, destino, clasesTabla,boton,idTabla){

    var objeto = new creargrilla(ruta, destino, clasesTabla,boton,idTabla);
    
}; // fin de la funcion construirGrilla

