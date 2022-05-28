$(document).ready(function () {
    

    if (window.location.pathname.includes('/Salidas')) {


        
        $(document).on('keyup', '.inputGrillaAuto', function () {
            
            var lineaGrilla = $(this).closest('tr');
            var idLinea = lineaGrilla.find('input').eq(0).val();
            
            iva = $('#iva_' + idLinea).attr('option', 'selected').val();
            precioVenta = $('#precioArticulo_' + idLinea).val();
            cantidad = $('#cantidadArticulo_' + idLinea).val();
            descuento = $('#descuento_' + idLinea).val();
 

                //que calcule todas, pues todas son nuevas
                calcularTotalLinea(idLinea, cantidad, precioVenta, descuento);
                calcularTotalBaseImponible(0, 0, 0);

            

        });


        $(document).on('change', '.inputGrillaAuto', function () {
            //capturo el id de la línea que estoy modificando
            var lineaGrilla = $(this).closest('tr');
            var idLinea = lineaGrilla.find('input').eq(0).val();

            iva = $('#iva_' + idLinea).attr('option', 'selected').val();
            precioVenta = $('#precioArticulo_' + idLinea).val();
            //precioVenta = precioVenta.replace(",", ".");
            cantidad = $('#cantidadArticulo_' + idLinea).val();
            descuento = $('#descuento_' + idLinea).val();

            calcularTotalLinea(idLinea, cantidad, precioVenta, descuento, iva);
            calcularTotalBaseImponible();

        });

        //FUNCIONES PARA CALCULAR Y RECALCULAR LOS TOTALES Y SUBTOTALES
        function calcularTotalLinea(idLinea, cantidad, precioVenta, descuento) {
            //console.log('idLinea test: '+idLinea);
            total1 = cantidad * precioVenta;
            valorDescuento = total1 * descuento / 100;
            total2 = parseFloat(total1) - parseFloat(valorDescuento);
            //totalLinea = total1 - valorDescuento;
            totalLinea = total2.toFixed(2);
            $('#totalLinea_' + idLinea).val(totalLinea);
        }

        function calcularTotalDocumento() {

            var totalBaseImponible = $('#totalBaseImponible').val();
            var totalValorDescuento = $('#totalValorDescuento').val();
            var totalValorIva = $('#totalValorIva').val();
            var totalDoc = parseFloat(totalBaseImponible) - parseFloat(totalValorDescuento) + parseFloat(totalValorIva);
            totalDocumento = totalDoc.toFixed(2);
            $("#totalDocumento").val(totalDocumento);
        }

        function calcularTotalBaseImponible(baseimponible = 0, valordescuento = 0, valoriva = 0) {

            var totalBaseImp = 0;

            $(".cantidad").each(function () {
                var cantidad = $(this).val();
                var lineaGrilla = $(this).closest('tr');
                idLinea = lineaGrilla.find('input').eq(0).val();

                var precioVenta = $('#precioArticulo_' + idLinea).val();
                //precioVenta = precioVenta.replace(",", ".");
                baseImpLinea = cantidad * precioVenta;
                totalBaseImp = parseFloat(totalBaseImp) + parseFloat(baseImpLinea);

            });

            //totalBaseImponible = totalBaseImp.toFixed(2);
            totalBaseImponible = parseFloat(totalBaseImp) + parseFloat(baseimponible);

            $("#totalBaseImponible").val(totalBaseImponible.toFixed(2));

            //recalculo los totales una vez recalculada la BI            
            calcularTotalDescuento(valordescuento);
            calcularTotalIva(valoriva);
            calcularTotalDocumento();

        }


        function calcularTotalDescuento(valDscto = 0) {

            var totalDescuento = 0;

            $(".descuento").each(function () {
                var descuento = $(this).val();
                var lineaGrilla = $(this).closest('tr');
                idLinea = lineaGrilla.find('input').eq(0).val();

                var cantidad = $('#cantidadArticulo_' + idLinea).val();
                var precioVenta = $('#precioArticulo_' + idLinea).val();
                //precioVenta = precioVenta.replace(",", ".");

                valorDescuento = cantidad * precioVenta * descuento / 100;
                totalDescuento = parseFloat(totalDescuento) + parseFloat(valorDescuento);
            });

            totalValorDescuento = parseFloat(totalDescuento) + parseFloat(valDscto);
            $("#totalValorDescuento").val(totalValorDescuento.toFixed(2));
        }


        function calcularTotalIva(valIva = 0) {

            var totalIva = 0;

            $(".iva").each(function () {
                var iva = $(this).attr('option', 'selected').val();
                var lineaGrilla = $(this).closest('tr');
                idLinea = lineaGrilla.find('input').eq(0).val();

                var cantidad = $('#cantidadArticulo_' + idLinea).val();
                var precioVenta = $('#precioArticulo_' + idLinea).val();
                //precioVenta = precioVenta.replace(",", ".");
                var descuento = $('#descuento_' + idLinea).val();

                importeSinDescuento = cantidad * precioVenta;
                valorDescuento = importeSinDescuento * descuento / 100;
                importeIncluyeDescuento = importeSinDescuento - valorDescuento;
                valorIva = importeIncluyeDescuento * iva / 100;

                totalIva = parseFloat(totalIva) + parseFloat(valorIva);
            });

            totalValorIva = parseFloat(totalIva) + parseFloat(valIva);
            $("#totalValorIva").val(totalValorIva.toFixed(2));
        }

    }    
    


});