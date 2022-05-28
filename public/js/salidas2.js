
$(document).ready(function () {

    var tiempoValidacion = 3500;

    if (window.location.pathname.includes('/Salidas')) {

        urlCompleta = $('#ruta').val();

        $(document).on('keydown', 'input', function (evento) {
            
            if (evento.key == "Enter") {            
                // Prevenir
                evento.preventDefault();
                return false;
            }
        });

        //Agregar dirección al seleccionar cliente
        $(document).on('change', '#idCliente', function () {
            var id = $(this).attr('option', 'selected').val();

            $.ajax({

                url: urlCompleta+"/Clientes/clienteDetallado",
                type: "POST",
                dataType: "json",
                data: { id: id },
                success: function (data) {

                    direccion = data['direccion'] + " " + data['poblacion'] + " " + data['codigopostal'] + " " + data['provincia'];
                    $('#direccion').val(direccion);
                    $('#tarifaCliente').val(data['nombreTarifa']);
                    $('#idTarifa').val(data['tarifa']);
                    

                }
            });

        });



        //Agregar líneas a la grilla
        $('#btnAddLinea').on('click', function () {

            var idCliente = $('#idCliente').attr('option', 'selected').val();

            var numFilas = $("#tablaGrilla tbody tr").length;
            if (numFilas && numFilas > 0) {
                var fila = $("#tablaGrilla").find("tr").last();

                filaDinamica = fila.find('input').eq(0).val();

            } else {
                filaDinamica = 0;
            }

            var filaOrden = parseInt(filaDinamica) + 1;

            idcab = $('#idcab').val();

            if (idCliente > 0) {

                if (idcab && idcab>0) { 
                                        
                    $.ajax({
                        type: "POST",
                        data: {
                            'filaOrden': filaOrden
                        },
                        url: urlCompleta + "/Salidas/agregarLineaGrillaEdicion",
                        success: function (datos) {
                            $('#tablaGrilla').append(datos);
                            $('#idArticulo_'+filaOrden).focus();
                        }
                    });
                    
                }else{
                    
                    $.ajax({
                        type: "POST",
                        data: {
                            'filaOrden': filaOrden
                        },
                        url: urlCompleta + "/Salidas/agregarLineaGrilla",
                        success: function (datos) {
                            $('#tablaGrilla').append(datos);
                            $('#idArticulo_'+filaOrden).focus();
                        }
                    });                

                }
                
            }
        });

        $(document).on('keydown', '.lineaIva', function (e) {
            var keyCode = e.keyCode || e.which;

            if (keyCode == 9) {
                e.preventDefault();

                var idCliente = $('#idCliente').attr('option', 'selected').val();

                var numFilas = $("#tablaGrilla tbody tr").length;

                if (numFilas && numFilas > 0) {
                    var fila = $("#tablaGrilla").find("tr").last();

                    filaDinamica = fila.find('input').eq(0).val();

                } else {
                    filaDinamica = 0;
                }

                var filaOrden = parseInt(filaDinamica) + 1;    
               

                if (idCliente > 0) {

                    if (idcab && idcab>0) { 
                                            
                        $.ajax({
                            type: "POST",
                            data: {
                                'filaOrden': filaOrden
                            },
                            url: urlCompleta + "/Salidas/agregarLineaGrillaEdicion",
                            success: function (datos) {
                                $('#tablaGrilla').append(datos);
                                $('#idArticulo_'+filaOrden).focus();
                            }
                        });
                        
                    }else{
                        
                        $.ajax({
                            type: "POST",
                            data: {
                                'filaOrden': filaOrden
                            },
                            url: urlCompleta + "/Salidas/agregarLineaGrilla",
                            success: function (datos) {
                                $('#tablaGrilla').append(datos);
                                $('#idArticulo_'+filaOrden).focus();
                            }
                        });                

                    }
                }
               

            }

        });

        //====================

        //BUSCADOR DE ARTÍCULOS 

        //Se llena el modal para buscar artículos

        //1- declaro una variable global para capturar el clic en el buscador de cada linea de la grilla
        var lineaConcepto = 0;

        $(document).on('click', '.btnBuscarArticulo', function () {
            //capturo este id en la variable global

            var filaConcepto = $(this).closest('tr');
            lineaConcepto = filaConcepto.find('input').eq(0).val();
            idTarifa = $('#idTarifa').val();

            cargarArticulos(25, '', '', 1, idTarifa);

        });


        let offset = 0;

        //2- FUNCIÓN QUE LLENA EL MODAL CON LOS ARTÍCULOS
        function cargarArticulos(num, buscar = '', campo = '', marca = '', idTarifa) {

            if (marca == 1) {
                offset = 0;
            }

            $.ajax({

                url: urlCompleta + "/Articulos/articulosActivos",
                type: "POST",

                data: { 'num': num, 'offset': offset, 'buscar': buscar, 'campo': campo, 'idTarifa': idTarifa },
                success: function (data) {
                    if (marca == 1) {
                        $('#tablaBuscadorArticulos tbody').html('');
                    }
                    $('#tablaBuscadorArticulos').append(data);
                }
            });
        }

        //3- SCROLL INFINITO PARA LA TABLA BUSCADOR DE ARTÍCULOS
        $('#tablaBuscadorScrollArticulos').on('scroll', function () {
            idTarifa = $('#idTarifa').val();          
                   
            if (offset == 0) {
                offset = 26;
            } else {
                offset++;
            }

            //recorro los inputs clase inputClickSearch para verificar si hay campos llenos para buscar
            //si alguno está lleno tomar el valor y el data-campos
            strBuscar = '';
            campo = '';
            var x = true;
            inputSearch = $('.inputClickSearch');

            inputSearch.each(function () {

                if ($(this).val() != '') {
                    strBuscar = $(this).val();
                    campo = $(this).data('campo');
                    return x = false;
                }

            });

            cargarArticulos(1, strBuscar, campo, '', idTarifa);
        })



        //4- al seleccionar un artículo de la tabla que lo agregue a la fila donde se hizo click inicialmente
        //y que traiga todos los datos correspondientes al producto seleccionado
        $(document).on("click", "td.filaArticulo", function (e) {

            var fila = $(this).closest('tr');
            id = fila.find('td:eq(0)').text();
            descripcion = fila.find('td:eq(1)').text();
            precioVentaArt = fila.find('td:eq(5)').text();
            obligatorio = fila.find('td:eq(6)').text(); //lote
            carnet = fila.find('td:eq(7)').text();
            idCliente = $('#idCliente').val();

            //traer el iva, coste y margen según tarifa que tiene asignada el cliente
            if (idCliente != '') { //cliente seleccionado
                metodo = 'obtenerDatosPorArticuloSegunTarifaCliente';
            } else {  //no ha seleccionado un cliente
                metodo = 'obtenerDatosPorArticuloGenerico'; //falta hacer éste
            }

            $.ajax({
                url: urlCompleta + "/Articulos/" + metodo,
                type: "POST",
                dataType: "json",
                data: { id: id, idCliente: idCliente },
                success: function (data) {
                    //pinto los datos en la línea
                    $('#idArticulo_' + lineaConcepto).val(id);
                    $('#descripcion_' + lineaConcepto).val(descripcion);
                    $('#iva_' + lineaConcepto).val(data['tipo']);
                    //$('#precioArticulo_' + lineaConcepto).val(data['tarifa']['precioventa']);
                    $('#precioArticulo_' + lineaConcepto).val(precioVentaArt);
                    $('#loteObligatorio_' + lineaConcepto).val(obligatorio);
                    $('#carnetObligatorio_' + lineaConcepto).val(carnet);

                    $('#cantidadArticulo_' + lineaConcepto).focus();
                    $('#verCosto').val(data['coste']);
                    $('#stock').val(data['stock']);
                    $('#buscadorArticulosModal').modal('hide');
                    //lleno todas las tablas:
                    $('#tablaInfoMargenes').html('');
                    $('#tablaInfoProveedores').html('');
                    $('#tablaInfoExistencias').html('');
                    $('#tablaInfoMargenes').append(data['tarifasMargen']);
                    $('#tablaInfoProveedores').append(data['proveedoresLista']);
                    $('#tablaInfoExistencias').append(data['partidasLista']);

                    //reincio la variable global "lineaConcepto"
                    lineaConcepto = 0;
                }
            });

        });

        //5- Cerrar modal buscador de artículos
        $('.cerrarBuscadorArticulos').on('click', function () {
            $('.inputClickSearch').val('');
            $('#buscadorArticulosModal').modal('hide');

        });
        
        $(function () {
            $('#buscadorArticulosModal').on('shown.bs.modal', function (e) {
                $("#contenedorTablaDescuentosCliente").html('');
                $('.inputClickSearch  ').val('');
                $('#nomArtInput').focus();              
            })
        });


        //6- Al hacer click en el el título de la tabla buscador         
        $('.titleClickSearch').on('click', function () {
            $('.inputClickSearch ').val(''); //limpio todos los inputs que hacen búsqueda
            focusInput = $(this).data('input');
            $('#' + focusInput).focus();

        });

        //7- Busco artículos en los inputs titulo con keyup con tiempo controlado después de dejar de escribir de 1 segundo
        let timeout;

        $('.inputClickSearch').on('keyup', function () {

            clearTimeout(timeout)
            timeout = setTimeout(() => {
                $('#tablaBuscadorArticulos tbody').html('');
                idTarifa = $('#idTarifa').val();
                strBuscar = $(this).val();
                campo = $(this).data('campo');
                marca = 1; //viene del input buscador
                cargarArticulos(25, strBuscar, campo, marca, idTarifa); //falta mandar tarifa

                clearTimeout(timeout)
            }, 1000)

        });


        //====================



        //Eliminar líneas de la grilla
        $(document).on('click', '.btnDeleteLinea', function (e) {
            let lineaGrilla = $(this).closest('tr');
            let idArticulo = lineaGrilla.find('input').eq(1).val();  
            lineaGrilla.remove();
            eliminarLotesPorIdArticulo(idArticulo);
            
            //verifico si el documento está guardado o no
            idcab = $('#idcab').val();
            if (idcab) {
                //que busque los totales de todas las líneas guardadas
                $.ajax({
                    type: "POST",
                    data: {
                        'idcab': idcab
                    },
                    dataType: "json",
                    url: urlCompleta + "/Salidas/obtenerTotalesDeAlbaranPorIdFactura",
                    success: function (data) {
                        baseimponible = data['baseimponible'];
                        valordescuento = data['valordescuento'];
                        valoriva = data['valoriva'];
                        //calcularTotalLinea(idLinea, cantidad, precioVenta, descuento, iva);
                        calcularTotalBaseImponible(baseimponible, valordescuento, valoriva);
                    }
                });
            } else {
                //que calcule todas, pues todas son nuevas
                //calcularTotalLinea(idLinea, cantidad, precioVenta, descuento, iva);
                calcularTotalBaseImponible(0, 0, 0);
            }
        });

        
        function eliminarLotesPorIdArticulo(idArticulo) {            
            $('.idLoteArticulo_'+idArticulo).each(function () {
                $(this).closest('tr').remove();
            });           
        }


        var codigoArtValidado;
        //validaciones de campos obligatorios antes de enviar el formulario
        $("#formRegistroEntradasSalidas").submit(function (event) {

            let tipoMov = $('#tipo').val();
            let idCliente = $('#idCliente').attr('option', 'selected').val();
            let fechaDocumento = $('#fecha').val();
            let formaPago = $('#formadepago').attr('option', 'selected').val();
            let agente = $('#agente').attr('option', 'selected').val();
            let numLineas = ($('#tablaGrilla').find('tr').length) - 1;

            if (tipoMov == 'albaranSalida') {

                //1- validaciones de la cabecera
                if (idCliente == 0 || idCliente == '' || idCliente == undefined) {
                    $("#mensajeValidacion").text("Debe seleccionar un cliente").show().fadeOut(tiempoValidacion);
                    event.preventDefault();
                } else if (!fechaDocumento || fechaDocumento == '') {
                    $("#mensajeValidacion").text("Debe seleccionar una fecha").show().fadeOut(tiempoValidacion);
                    event.preventDefault();
                } else if (!formaPago || formaPago == '') {
                    $("#mensajeValidacion").text("Debe seleccionar una forma de pago").show().fadeOut(tiempoValidacion);
                    event.preventDefault();
                } else if (agente == 0 || agente == '' || agente == undefined) {
                    $("#mensajeValidacion").text("Debe seleccionar una agente").show().fadeOut(tiempoValidacion);
                    event.preventDefault();
                } else if (!numLineas || numLineas == '' || numLineas <= 0) {
                    $("#mensajeValidacion").text("No hay artículos seleccionados").show().fadeOut(tiempoValidacion);
                    event.preventDefault();
                }

                //2- validaciones de la líneas
                var exit = false;
                $(".inputGrillaArticulo").each(function () {
                    let articulo = $(this).val();
                    articulo = articulo.trim();                    
                    validarSiCodigoArticuloExiste(articulo);
                    let datosArticulo = codigoArtValidado;                    
                    let lineaGrilla = $(this).closest('tr');
                    let idLinea = lineaGrilla.find('input').eq(0).val();                    
                    let descripcion = $("#descripcion_"+idLinea).val();

                    if (datosArticulo.id != undefined && datosArticulo.id != '' || datosArticulo.id != null) {
                        
                        let cantidadArticulo = $("#cantidadArticulo_"+idLinea).val();
                        let precioArticulo = $("#precioArticulo_"+idLinea).val();
                        let comprobacion = comprobacionDeLotesYCantidades(idLinea);
                        let carnet = $('#carnet').val();

                        if (cantidadArticulo <= 0 || cantidadArticulo == '') {                               
                            $("#mensajeValidacion").text("El artículo "+descripcion+ " no tiene una cantidad válida.").show().fadeOut(tiempoValidacion);                            
                            exit = true;
                            return false;
                        }else
                        
                        if (precioArticulo <= 0 || precioArticulo == '') {                                              
                            $("#mensajeValidacion").text("El artículo "+descripcion+ " no tiene precio válido.").show().fadeOut(tiempoValidacion);                            
                            exit = true;
                            return false;
                        }else

                        if (datosArticulo.loteobligatorio == 1 && (comprobacion[0] == false || comprobacion[1] == false)) {                                
                            $("#mensajeValidacion").text("El artículo " + descripcion + " exige que ingrese lote: verifique que lotes y cantidades sean correctas").show().fadeOut(tiempoValidacion);                            
                            exit = true;
                            return false;
                        }else

                        if (datosArticulo.carnetobligatorio == 1 && (carnet == 0 || carnet == '') ) {                                                        
                            $("#mensajeValidacion").text("El artículo " + descripcion + " requiere carnet").show().fadeOut(tiempoValidacion);                                                       
                            exit = true;
                            return false;
                        }  
                    }
                    //si no existe el codigo de artículo, no validar ni precio ni cantidad, ni lote ni carnet
                });

                if(exit) {                     
                    exit = false; 
                    event.preventDefault();     
                }        
            }
        });

        
        function articulosLoteObligatorio() {
            //valido si el artículo exige lote obligatorio
            arrLineas = [];
            
            $(".inputGrillaArticulo").each(function () {
                let articulo = $(this).val();
                console.log('articulo con trim: ' + articulo.trim());

                validarSiCodigoArticuloExiste(articulo);
                let datosArticulo = codigoArtValidado;
                console.log('datosArticulo: ', datosArticulo);
                console.log('idArticulo1: ', datosArticulo.id);
                console.log('idArticulo2: ', datosArticulo['id']);

                let lineaGrilla = $(this).closest('tr');
                idLinea = lineaGrilla.find('input').eq(0).val();
                console.log('idLinea: ' + idLinea);

                /*if (datosArticulo != '') {
                    arrLineas.push(idLinea);
                }*/

            });
            /*$(".loteObligatorio").each(function () {
                var valorObligatorio = $(this).val();
                console.log('valorObligatorio: ' + valorObligatorio);
                var lineaGrilla = $(this).closest('tr');
                idLinea = lineaGrilla.find('input').eq(0).val();
                console.log('idLinea: ' + idLinea);
                if (valorObligatorio == 'obligatorio') {
                    arrLineas.push(idLinea);
                }

            });*/
            if (arrLineas.length > 0) {
                respuesta = [true, arrLineas];
            } else {
                respuesta = [false, []];
            }
            console.log('fun resp: ' + respuesta);
            return respuesta;
        }

        function validarSiCodigoArticuloExiste(codigoArticulo) {
            $.ajax({
                type: "POST",
                data: { 'codigoArticulo': codigoArticulo },
                async: false,
                url: urlCompleta + '/Articulos/validarSiCodigoArticuloExiste',                
                dataType: "json",
                success: function (res) {                   
                    codigoArtValidado = res;                    
                }
            });
            return codigoArtValidado;
        }

        function comprobacionDeLotesYCantidades(idLinea) {

            filaLotes = $(".numLote_" + idLinea);
            if (filaLotes.length > 0) {

                arrLotes = [];
                arrCantidad = 0;
                cont = 0;
                cantTotalArt = $('#cantidadArticulo_' + idLinea).val();

                //aseguramos que no vienen inputs vacíos
                filaLotes.each(function () {
                    cont++;
                    numLote = $(this).val().trim().length;
                    
                    if (numLote > 0) {                    
                        arrLotes.push($(this).val().trim());
                    }
                });
                if (arrLotes.length == cont) {
                    respLotes = true;
                } else {
                    respLotes = false;
                }
                $(".cantidadLote_" + idLinea).each(function () {
                    //cantFila = $(this).val();
                    cantFila = $(this).val().trim().length;

                    if (cantFila > 0) {
                        arrCantidad = arrCantidad + parseFloat($(this).val().trim());
                    }

                });
                if (arrCantidad == cantTotalArt) {
                    repsCant = true;
                } else {
                    repsCant = false;
                }

            } else {
                respLotes = false;
                repsCant = false;
            }
            return [respLotes, repsCant];
        }

        //array de todos los artículos que tienen lote
        function verificadorDeTodosLosInputsGrillaVersusLote() {

            arrFilasArt = [];
            $('.numeroOrden').each(function () {
                filaArt = $(this).val();
                if (filaArt) {
                    arrFilasArt.push(filaArt);
                }
            });

            arrFilasArtLotes = [];
            $('.filaArticulo').each(function () {
                filaArtLote = $(this).val();
                if (filaArtLote) {
                    arrFilasArtLotes.push(filaArtLote);
                }
            });

            let result = arrFilasArtLotes.filter((item, index) => {
                return arrFilasArtLotes.indexOf(item) === index;
            })

            var iguales = 0;
            arrFinal = [];
            for (let i = 0; i < arrFilasArt.length; i++) {
                for (let j = 0; j < result.length; j++) {

                    if (arrFilasArt[i] == result[j]) {
                        iguales++;
                        arrFinal.push(arrFilasArt[i]); //estos tienen lote 
                    }

                }

            }
            console.log('imrprime arrFinal' + arrFinal);
            return arrFinal;
        }


        //la clase "inputclick" sirve para tomar el código del producto y traer datos del mismo
        $(document).on('click', '.inputclick', function () {
            $('#tablaInfoProveedores').html('');
            var filaVer = $(this).closest('tr').find("input");
            id = filaVer[0].value;

            let codigoArticulo = $('#idArticulo_'+id).val();

            if (codigoArticulo) {
                
                $.ajax({
                    url: urlCompleta + "/Articulos/articuloDetallado",
                    type: "POST",
                    dataType: "json",
                    data: { 'codigoArticulo': codigoArticulo },
                    success: function (data) {
                        $('#stock').val(data['stock']);
                        $('#verCosto').val(data['coste']);
                        $('#tablaInfoProveedores').append(data['proveedoresLista']);
                    }
                });   
            }

        });

        //HASTA AQUI

        $('#facturarVentasEmitirFactura').on('click', function (event) {
            idCliente = $('#clienteAFacturarFacturaVenta').val();
            fechaFactura = $('#fechaFacturarVenta').val();
            //console.log(idCliente);
            if (!idCliente || idCliente == '') {
                $("#msgValidacionFacturar").text("Debe seleccionar un cliente").show().fadeOut(1500);
                event.preventDefault();
            } else if (!fechaFactura || fechaFactura == '') {
                $("#msgValidacionFacturar").text("Debe seleccionar una fecha").show().fadeOut(1500);
                event.preventDefault();
            }
            

        });





        //MASIVOS
        $('#btnFacturarDifClientes').on('click', function () {
            seleccionarMasivoParaFacturar()
        });

        function seleccionarMasivoParaFacturar() {
            let idsClientes = [];
            let estadosFactura = [];

            $("input[type=checkbox]:checked").each(function () {
                var fila = $(this).closest('tr');
                idCliente = fila.find('td:eq(2)').text();
                idsClientes.push(idCliente);

                estado = fila.find('td:eq(6)').text();
                estadosFactura.push(estado);
            });

            //debe validar que todos los albaranes seleccionados están en estado de factura "pendiente"
            let largo = estadosFactura.length;
            var validar = 'false';
            for (let i = 0; i < largo; i++) {
                if (estadosFactura[i] == 'Facturado') {
                    validar = false;
                    break
                }
                validar = true;
            }

            if (idsClientes.length == 0) {
                alert('No ha seleccionado ningún albarán');
            } else {

                idCliente = 3001; //corregir esto

                if (validar == false) {
                    alert('Existen albaranes seleccionados que han sido facturados anteriormente');
                } else {

                    let valoresCheck = [];
                    let valoresIdsCheck = [];
                    $("input[type=checkbox]:checked").each(function () {
                        var fila = $(this).closest('tr');
                        idalbaran = fila.find('td:eq(0)').text();
                        numalbaran = fila.find('td:eq(1)').text();
                        valoresCheck.push(numalbaran);
                        valoresIdsCheck.push(idalbaran);
                    });

                    let albaran = '';
                    let idsAlbaranVenta = '';
                    for (let index = 0; index < valoresCheck.length; index++) {
                        albaran += '<input class="col-md-2 celdaAlbaranFacturar" value="' + valoresCheck[index] + '">';
                        idsAlbaranVenta += '<input  value=' + valoresIdsCheck[index] + ' name="numAlbaranSelect[]">';
                    }

                    $('.albaranesContenedor').append(albaran);
                    $('.idsAlbaranesContenedor').append(idsAlbaranVenta);
                    $("#cantidadAlbaranesMasivo").val(valoresCheck.length);
                    $('#clienteAFacturarMasivo').val(idCliente);
                    $("#facturaraMasivoModal").modal("show");
                    $('.select2').select2({
                        theme: 'bootstrap4'
                    });
                    valoresCheck = []; // nueva instancia
                    valoresIdsCheck = [];
                    albaran = '';
                    idsAlbaranVenta = '';


                    //llenar select2 de clientes en modal  "facturaraMasivoModal"              
                    $('#clienteAFacturarMasivo').select2({                
                        ajax: {
                        url: urlCompleta+"/Clientes/llenarClientesEnSelectTwoConAjax",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                            results: data
                            };
                        },
                        cache: true
                        }
                    });

                }
            }
        }

        //al cerrar el modal que se limpie campos        
        $('#facturaraMasivoModal').on('hidden.bs.modal', function () {            
            let select = '<select class="form-control" name="clienteAFacturar" id="clienteAFacturarMasivo"></select>';
            $('#contenedorClienteFact').html(select);

            $('.albaranesContenedor').html('');
            $('.idsAlbaranesContenedor').html('');

        });


        $('#facturarVentasMasivo').on('click', function (event) {
            fechaFactura = $('#fechaFacturarMasivo').val();
            if (!fechaFactura || fechaFactura == '') {
                $("#msgValidacionFacturar").text("Debe seleccionar una fecha").show().fadeOut(1500);
                event.preventDefault();
            }

        });



        //Enviar masivamente o individualmente albaranes a un cliente
        $('#btnEnviarDocumento').on('click', function () {
            seleccionarAlbaranesParaEnviar();
        });

        function seleccionarAlbaranesParaEnviar() {

            let idsClientes = [];

            $("input[type=checkbox]:checked").each(function () {
                var fila = $(this).closest('tr');
                idCliente = fila.find('td:eq(2)').text();
                idsClientes.push(idCliente);

            });

            //debe verificar que todos los albaranes seleccionados son del mismo cliente
            let tamanio = idsClientes.length;
            var igual = '';

            if (tamanio == 1) {
                igual = true;
            } else {

                for (let i = 0; i < tamanio - 1; i++) {
                    if (idsClientes[i] == idsClientes[i + 1]) {
                        igual = true
                    } else {
                        igual = false
                        break
                    }
                }
            }

            if (idsClientes.length == 0) {
                alert('No ha seleccionado ningún albarán');
            } else {

                idCliente = idsClientes[0];

                if (igual == false) {
                    alert('Los albaranes seleccionados no pertenecen al mismo cliente');
                } else {

                    //aqui debe hacer una consulta y mostrar todos los albaranes con sus respectivo cobros, cobro parcial y pendiente                    

                    $("#envioMasivoAlbaranesModal").modal("show");

                }
            }

        }

        $(document).on("click", "#generarAlbaranPdfSinGuardar", function () {
            let numero = $("#numeroAlbaranPdfSinGuardar").val();
            let form = $("#formRegistroEntradasSalidas").serialize();
            let option = $("#formatoPdfAlbaranSinGuardar").val();
            window.open(urlCompleta + '/Salidas/generarPdfAlbaranSinGuardar?' + form + "/" + option + "/" + numero, '_blank');

        });

        $(document).on("click", ".vistaPrevia", function () {            
            let idAlbaran = $("#idAlbSalidaEnviar").val();
            let option = $("#formatoEnviarAlbaran").val();
            //window.open(urlCompleta + '/Salidas/generarPdfAlbaran/' + numAlbaran + "/" + option, '_blank');
            window.open(urlCompleta + '/Salidas/generarPdfAlbaran/' + idAlbaran + "/" + option, '_blank');

        });

        
        $('#apunteDeCobroAlbaranModal').on('show.bs.modal', function () {
            $("#importeCobradoAlbaranModal").val("");
            $("#conceptoCobroAlbaranModal").val("");
            $("#observacionesCobro").val("");
            
        });  


        /*
        $(document).on('click', '.cobrarVenta', function () {
            var filaConcepto = $(this).closest('tr');
            let estado = filaConcepto.find('td:eq(7)').text();
            if (estado != "Cobrado") {
                var filaConcepto = $(this).closest('tr');
                numAlbaran = filaConcepto.find('td:eq(0)').text();
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: urlCompleta + '/Salidas/obtenerImportePendienteByNumAlbaran',
                    data: { numAlbaran: numAlbaran },
                    success: function (response) {
                        $('.historialCobrosContenedor').empty();

                        $('#numeroAlbaranCobro').val(numAlbaran);
                        $('#importePendienteAlbaranCobro').val(response['importe']);

                        $('.historialCobrosContenedor').append(response['tabla']);

                        $('#apunteDeCobroAlbaranModal').modal("show");
                    }
                });
            } else {
                alert("Este pago ya está realizado");
            }
        });*/

        /*
        $(document).on('click', '.cobrarVenta', function () {
            var filaConcepto = $(this).closest('tr');
            let estado = filaConcepto.find('td:eq(7)').text();
            if (estado != "Cobrado") {
                var filaConcepto = $(this).closest('tr');
                numAlbaran = filaConcepto.find('td:eq(0)').text();
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: urlCompleta + '/Salidas/obtenerImportePendienteByNumAlbaran',
                    data: { numAlbaran: numAlbaran },
                    success: function (response) {
                        $('.historialCobrosContenedor').empty();
                        $('.reset').val("");
                    }

                });
            }
        });
        */
        //==========================

        //Emitir una factura desde la edición de un albarán de venta
        $(document).on('click', '#emitirFacturaVenta', function () {
            let idCliente = $('#idCliente option:selected').val();
            let nombreCliente = $('#idCliente option:selected').text();
            let idAlbaran = $('#idcab').val();
            let arrayIdsPpto = [idAlbaran];            

            $.ajax({
                type: "POST",
                url: urlCompleta + '/Salidas/validarDNI',
                data: { idCliente: idCliente },
                success: function (response) {
                    if (response == 1) {
                        $('#clienteAFacturarFacturaVenta').val(idCliente);
                        $('#nombreClienteFact').val(nombreCliente);
                        $('#contenedorDatosAlbaran').css('visibility', 'hidden');                   
                        $('#facturarAlbaranVentaModal').modal("show");
                        $('#idAlbaranFactura').val(arrayIdsPpto);
                    }else{
                        alert("Este cliente no tiene dni");
                    }                               
                }
            });

        });

        //Apunte de cobro de albarán desde la edición de un albarán de venta
        $(document).on('click', '#cobrarAlbaran', function () {
            numSerie = $("#serieNumeroModal").val();
            numAlbaran = numSerie + "-" + $("#numeroAlbaranSalidas").val();

            $.ajax({
                type: "POST",
                url: urlCompleta + '/Salidas/obtenerImportePendienteByNumAlbaran',
                data: { numAlbaran: numAlbaran },
                success: function (response) {
                    datosJson = JSON.parse(response);
                    $('.historialCobrosContenedor').empty();
                    $('#numeroAlbaranCobro').val(numAlbaran);
                    $('#importePendienteAlbaranCobro').val(datosJson["importe"]);
                    $('.historialCobrosContenedor').append(datosJson['tabla']);
                    $("#apunteDeCobroAlbaranModal").modal("show");
                }
             
            });


        });

        /*

                    success: function () {
                        alert("Pago realizado");
                        window.location.reload();
                        $('#apunteDeCobroAlbaranModal').modal('hide');
                    }
                });
                */


        //Modal envío de albarán a cliente desde la edición de un albarán de venta
        $(document).on('click', '#enviarEmail', function () {
            let editable = $("#booleanEditable").val();      
            let numAlbaran = $('#serieNumeroModal').val() + "-" + $('#numeroAlbaranSalidas').val();      
            let idAlbaran = $("#idcab").val();

            if (editable == 1) {
                clienteSelected = $('#idCliente option:selected').text();
            } else {
                clienteSelected = $("#nombreClienteModal").val();
            }

            $.ajax({
                type: "POST",
                url: urlCompleta + '/Salidas/obtenerEmailClienteByNumAlbaran',
                data: { 'idAlbaran': idAlbaran },
                success: function (response) {
                    $('#numeroAlbaranEnviar').val(numAlbaran);
                    $('#idAlbSalidaEnviar').val(idAlbaran);
                    $('#clienteEnviarAlbaran').val(clienteSelected);
                    $('#emailAlbaran').val(response);
                    $('#envioAlbaranModal').modal('show');
                }
            });
        });

        //generar pdf de albarán desde la edición de un albarán de venta
        $(document).on('click', '#emitirPdfAlbaran', function () {
            let editable = $("#booleanEditable").val();
            let clienteSelected = '';
            let numAlbaran = '';
            if ($('#idcab').val() != '') {
                let numSerie = $("#serieNumeroModal").val();
                numAlbaran = numSerie + "-" + $("#numeroAlbaranSalidas").val();       
                clienteSelected = $('#idCliente option:selected').text();  
                $('#numeroAlbaranPdf').val(numAlbaran);
                $('#idAlbaranSalidaPdf').val($('#idcab').val());
                $('#clienteAlbaranModal').val(clienteSelected);
                $("#emitirPdfAlbaranModal").modal("show");       
            }else{
                alert('No existe albarán para exportar a PDF.');
            }
            
            /*if (editable == 1) {
                clienteSelected = $('#idCliente option:selected').text();
            } else {
                clienteSelected = $("#nombreClienteModal").val();
            }*/
            
        });


        $(document).on('click', '#sinGuardar', function () {
            clienteSelected = $('#idCliente option:selected').text();
            $('#clienteAlbaranModalSinGuardar').val(clienteSelected);
            $("#emitirPdfAlbaranModalSinGuardar").modal("show");
        });

        //enviar impresión de albarán desde la edición de un albarán de venta sin visualizar
        $(document).on('click', '#enviarImpresion', function () {
            $("#imprimirAlbaranModal").modal("show");
        });

          
        $('#envioAlbaranModal').on('hidden.bs.modal', function () {
            $('#tablaEmailsEnvioFactura').html('');
        });

        //envio de email
        $(document).on('click', '#btnElegirEmail', function (e) {
            e.preventDefault();

            let email = $('#emailAlbaran').val();

            if (email != '') {
                let filaEmail = '<tr>' +
                        '<td class="py-1"><input class="form-control destinatario" style="font-size:0.85rem;" name="email[]" value="'+email+'" /></td>' +
                        '<td class="py-1">' +
                        '        <i class="fas fa-trash eliminarEmail"></i>' +
                        '</td>' +
                        '</tr>';
                $('#tablaEmailsEnvioFactura').append(filaEmail);
                $('#emailAlbaran').val('');
            }

            /*
            let lineaEmail = 0;

            $('#tablaEmailsEnvioFactura tr').each(function () {
                lineaEmail += 1;
            })
            
            mail = $('#mail').attr('option', 'selected').val();

            if (mail != '') {
                if (mail != 'otroEmail') {
                    var filaEmail = '<tr>' +
                        '<td><input class="form-control destinatario" style="font-size:0.85rem;" name="email[]" value="" /></td>' +
                        '<td>' +
                        '        <i class="fas fa-trash eliminarEmail"></i>' +
                        '</td>' +
                        '</tr>';
                    $('#tablaEmailsEnvioFactura').append(filaEmail);
                } else if (mail == 'otroEmail') {
                    var filaEmail2 = '<tr>' +
                        '<td><input class="form-control destinatario" style="font-size:0.85rem;" name="email[]" /></td>' +
                        '<td>' +
                        '        <i class="fas fa-trash eliminarEmail"></i>' +
                        '</td>' +
                        '</tr>';
                    $('#tablaEmailsEnvioFactura').append(filaEmail2);
                }
            }
            */

        });

        $(document).on("click", '.eliminarEmail', function () {
            var filaEMail = $(this).closest('tr');
            filaEMail.remove();
        });

      
        //=======================================================
        //INICIO DE GESTIÓN DE LOTES EN LA VENTA

        //variable global para capturar el indice de la fila seleccionada
        var lineaSelected = 0;
        var idArtSelected = 0;
        var nomArticulo = 0;

        //cada vez que se haga clic en los input de la clase dblClickInput
        $(document).on('dblclick', '.dblClickInput', function () {

            lineaSelected = 0;
            idArtSelected = 0;
            nomArticulo = 0;

            var filaConcepto = $(this).closest('tr');

            lineaSelected = filaConcepto.find('input').eq(0).val();
            idArtSelected = filaConcepto.find('input').eq(1).val();
            nomArticulo = filaConcepto.find('input').eq(4).val();


        });

        //se muestra el modal con los lotes del artículo seleccionado
        $('#btnBuscarLote').on('click', function (event) {
            event.preventDefault();
            
            //solo si se ha clikado doble en la clase dblClickInput
            if (lineaSelected > 0 && lineaSelected != '' && idArtSelected > 0 && idArtSelected != '') {
                    
                //verifico si artículo permite ingresar lote:
                $.ajax({
                    type: "POST",
                    data: {
                        'idArtSelected': idArtSelected
                    },
                    url: urlCompleta + "/Salidas/comprobarLoteObligatorio",
                    success: function (respuesta) {
                        if (respuesta == 1) {
                                
                                    $("#verSeriesLotesArticuloModal").modal("show");
                                    $.ajax({
                                        type: "POST",
                                        data: {
                                            'idArtSelected': idArtSelected, ver: false
                                        },
                                        url: urlCompleta + "/Entradas/buscarSeriesLotesPorArticulo",
                                        success: function (filaLote) {
                                            $('#tablaSeriesLotesSalida').append(filaLote);
                                            $('#nombreArticuloTitle').text('Series / lotes del artículo: ' + nomArticulo);
                                        }
                                    });                        

                        }else{
                                alert('Este artículo no admite lotes. Debe modifcar la ficha del artículo');
                        }
                    }
                });
            }
        });

       
        //agrega un lote nueva en la venta

        $(document).on('click', '#btnNuevoLote', function( event ) {
            event.preventDefault();
            
            if (lineaSelected >0 && lineaSelected !='') {
                    
                //verifico si artículo permite ingresar lote:
                $.ajax({
                    type: "POST",
                    data: {
                        'idArtSelected': idArtSelected
                    },
                    url: urlCompleta + "/Salidas/comprobarLoteObligatorio",
                    success: function (respuesta) {
                        if (respuesta == 1) {
                            
                                //validación para verificar el número de orden de las filas
                                var numFilas = $("#tablaSeriesLotes tbody tr").length;
                                
                                if (numFilas && numFilas>0) {
                                    var fila = $("#tablaSeriesLotes").find("tr").last();
                                    filaDinamica = parseInt(fila.find('td:eq(0)').text());      
                                    //console.log('filaDinamicaLote:' +filaDinamica);
                                }else{
                                    filaDinamica = 0;
                                }
                                
                                var filaOrden = parseInt(filaDinamica)+1;

                                console.log('variable global: '+lineaSelected);
                                console.log('variable global: '+idArtSelected);

                                $.ajax({
                                    type: "POST",
                                    data: {
                                    'lineaSelected':lineaSelected, 'filaOrden':filaOrden, 'idArtSelected':idArtSelected
                                    },
                                    url: urlCompleta + "/Entradas/agregarLineaLoteNuevo",
                                    success: function (filaLote) {                                       
                                        $('#tablaSeriesLotes').append(filaLote);                    
                                        //llamo a la función que filtra los lotes  
                                        //$TableFilter("#tablaSeriesLotes", lineaSelected);                        
                                    }            
                                });

                        }else{
                            alert('Este artículo no admite lotes. Debe modifcar la ficha del artículo');
                        }
                    }
                });
            }

        });        

        //seleccionamos el lote con doble click o haciendo clik en el botón
        $(document).on('click', '.btnSelectLote', function () {
            var filaLoteSelected = $(this).parent().parent();
            
            var loteSelected = filaLoteSelected.find('td:eq(0)').text();
            
            seleccionarLote(loteSelected);
        });

        $(document).on('dblclick', '.filaLote', function () {
            var filaLoteSelected = $(this).closest('tr');
            var loteSelected = filaLoteSelected.find('td:eq(0)').text();
            console.log('lote seleccionado: ' + loteSelected);

            seleccionarLote(loteSelected);

        });

        function seleccionarLote(loteSelected) {

            //validación para verificar el número de orden de las filas
            var numFilas = $("#tablaSeriesLotes tbody tr").length;

            if (numFilas && numFilas > 0) {
                var fila = $("#tablaSeriesLotes").find("tr").last();
                filaDinamica = parseInt(fila.find('td:eq(0)').text());
                //console.log('filaDinamicaLote:' +filaDinamica);
            } else {
                filaDinamica = 0;
            }

            var filaOrden = parseInt(filaDinamica) + 1;


            $.ajax({
                type: "POST",
                data: {
                    'loteSelected': loteSelected, 'lineaSelected': lineaSelected, 'filaOrden': filaOrden, 'idArtSelected': idArtSelected
                },
                url: urlCompleta + "/Salidas/montarLoteSeleccionado",
                success: function (filaLote) {
                    $('#tablaSeriesLotes').append(filaLote);
                }
            });

        }

        $('.cerrarModalSeries').on('click', function () {
            $('#tablaSeriesLotesSalida').html('');
            $('#verSeriesLotesArticuloModal').modal('hide');
        });

        //eliminamos lotes del DOM
        $(document).on('click', '.btnDeleteLote', function () {
            var filaLote = $(this).closest('tr');
            filaLote.remove();
        });

        //FIN DE GESTIÓN DE LOTES EN LA VENTA
        //===========================================================



        //DEVOLUCIONES DESDE ALBARAN VENTA
        
        $(document).on("click", ".devolucion", function (e) {

            var fila = $(this).closest('tr');
            dat = fila.find('td:eq(0)').text();        
            window.open(urlCompleta + '/Devoluciones/N/' + dat, '_blank');
        
        });

                    
        //Modal Ver Artículo desde botón                                 
        $(document).on('click', '.btnVerFichaArticulo', function () {
            
            let lineaGrilla = $(this).closest('tr');  
            let idLinea = lineaGrilla.find('input').eq(0).val();               
            let codigoArticulo = lineaGrilla.find('input').eq(1).val();
            codigoArticulo = codigoArticulo.trim();
            let descripcion = $('#descripcion_'+idLinea).val();

            if (codigoArticulo && codigoArticulo >0) {
                        
                $('#idArtModalArticuloVenta').val(codigoArticulo);
                $("#tablaProveedores tbody").html('');
                $("#tablaTarifas").html('');
                $('#modalVerFichaArticulo').on('show.bs.modal', function () {
                    $("#modalVerFichaArticulo input").val("");
                    $("#modalVerFichaArticulo textarea").val("");
                });              

                $.ajax({
                    url: urlCompleta+"/Articulos/articuloDetalladoPorCodigoArticulo",
                    type: "POST",
                    dataType: "json",
                    data: { 'codigoArticulo': codigoArticulo },
                    success: function (data) {
                        //console.log(data[7]['precioCompra']);
                        $('#codigoArt').val(data['codigoarticulo']);
                        $('#descripcion').val(data['Descripción']);
                        $('#familiaArticulo').val(data['nombreFamilia']);
                        $('#etiqueta').val(data['etiqueta']);
                        $('#subfamilia').val(data['SubFamilia']);
                        $('#codigobarras').val(data['codigobarras']);
                        $('#observaciones').val(data['observaciones']);
                        $('#activoArt').val(data['Estado']);
                        $('#precioCompra').val(data['preciocompra']);
                        $('#costeMedio').val(data['costo']);
                        $('#stockactual').val(data['stock']);
                        $('#tipoiva').val(data['tipoiva']);
                        if (data['loteobligatorio'] == 1) {
                            $('#checkLoteObligatorio').prop('checked', true);
                        } else {
                            $('#checkLoteObligatorio').prop('checked', false);
                        }
                        if (data['carnetobligatorio'] == 1) {
                            $('#checkCarnetObligatorio').prop('checked', true);
                        } else {
                            $('#checkCarnetObligatorio').prop('checked', false);
                        }
                        $('#tablaProveedores tbody').append(data['proveedoresLista']);
                        $('#tablaTarifas').append(data['listaTarifas']);
                        $('#proveedorHabitual').val(data['proveedorhabitual']);
                        $('#referencia').val(data['referencia']);
    
                    }
    
                });
    
                $("#tablaUbicaciones tbody").html('');
                $.ajax({
                    url: urlCompleta+ "/Articulos/obtenerUbicaciones",
                    type: "POST",
                    data: { 'codigoArticulo': codigoArticulo },
                    success: function (data) {
                        $("#bodyUbicaciones").append(data);
                    }
                });

                idCliente = $("#idCliente").val();
                $("#contenedorTablaDescuentosCliente").html('');
    
                $.ajax({
                    url: urlCompleta + "/Salidas/obtenerDescuentosPorClientePorArticulo",
                    data: { 'codigoArticulo': codigoArticulo, idCliente: idCliente },
                    type: "POST",
                    success: function (result) {
                        $("#contenedorTablaDescuentosCliente").html(result);
                    }
                });         

                $("#modalVerFichaArticulo").modal("show");
                $(".modal-title.modal-fichaarticulo").text("Ficha de Artículo "+ descripcion);
                $('#modalVerFichaArticulo').modal({ backdrop: 'static', keyboard: false }); //evitamos que se cierre el modal al hacer click fuera
                        
            }else{
                alert('No hay ningún artículo seleccionado para visualizar.');
            }

        });



        $(document).on('click', '.verLotesFichaVenta', function (e) {

            e.preventDefault();


            idArticulo = $("#codigoArt").val();

            $.ajax({
                url: urlCompleta + "/Entradas/buscarSeriesLotesPorArticulo", //falta este método
                data: { idArtSelected: idArticulo, ver: true },
                type: "POST",
                success: function (result) {
                    $("#tablaSerieLoteFichaArticulo").html(result);
                }
            });


        });

        
        $('#dsctoPorCliente').on('click',function (e) {
            
            e.preventDefault();
            idArticulo = $("#codigoArt").val();
            idCliente = $("#idCliente").val();
            $("#contenedorTablaDescuentosCliente").html('');

            $.ajax({
                url: urlCompleta + "/Salidas/obtenerDescuentosPorClientePorArticulo",
                data: { idArticulo: idArticulo, idCliente: idCliente },
                type: "POST",
                success: function (result) {
                    $("#contenedorTablaDescuentosCliente").html(result);
                }
            });

        });

            
        $('#dsctoPorArticulo').on('click',function (e) {
            
            e.preventDefault();
            idArticulo = $("#codigoArt").val();

            $("#contenedorTablaDescuentosPorArticulo").html('');

            $.ajax({
                url: urlCompleta + "/Salidas/obtenerDescuentosPorArticulo",
                data: { idArticulo: idArticulo },
                type: "POST",
                success: function (result) {
                    $("#contenedorTablaDescuentosPorArticulo").html(result);
                }
            });


        });
        

        //FILTRO ESPECIAL PARA FILTRAR ALBARANES           
        $('#filtrarAlbaranes').on('click', function () {

            $('#filtroEspecialParaAlbaranes').modal("show");
         
            $('#selectClientesFiltro').select2({                
                ajax: {
                url: urlCompleta+"/Clientes/llenarClientesEnSelectTwoConAjax",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                cache: true
                }
            });

        });

        /*var activarFiltro = 0;

        $('#mostrarAlbaranesFiltrados').on('click', function () {
                        
            fechaInicial = $('#fechaInicioFiltroSalidas').val();
            fechaFinal = $('#fechaFinFiltroSalidas').val();
            idCliente = $('#selectClientesFiltro').val();
            estFactura = $('#estadoFactFiltro').val();
            estCobro = $('#estadoCobroAlbFiltro').val();
            tipoIva = $('#tipoIvaFiltroAlb').val();             
            
            activarFiltro = 1;            

            $('#tablaSalidasScroll').html('');

            $.ajax({
                url: urlCompleta + "/Salidas/tablaAlbaranesSalidaScrollInfinito",
                data: { fechaInicial:fechaInicial,fechaFinal : fechaFinal,idCliente : idCliente,estFactura : estFactura,estCobro : estCobro,tipoIva : tipoIva, activarFiltro:activarFiltro },
                type: "POST",
                success: function (result) {
                    $('#tablaSalidasScroll').append(result);   
                    $('#filtroEspecialParaAlbaranes').modal('hide');

                }
            });


        });*/

        $('.cerrarModalFiltroAlb').on('click', function () {
            $('#filtroEspecialParaAlbaranes').modal('hide');         
        });

    
        $('#formFacturarAlbaranesVentaMasivo').submit(function (event) {
            let idCliente = $('#clienteAFacturarMasivo').val();
            if (idCliente == null || idCliente == undefined || idCliente == '') {
                $("#msgValidacionFacturarMasivo").text("Debe seleccionar un cliente").show().fadeOut(tiempoValidacion);
                event.preventDefault();
            }
        });
            
    }    
    


});