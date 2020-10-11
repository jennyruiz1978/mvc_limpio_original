$('document').ready(function () {

    //mostrar apartado para subida de fichero
    $('#anadirFichero').click(function(e){
        e.preventDefault();
        if($('#formularioSubirFichero').is(':visible')){
                $('#formularioSubirFichero').slideUp(300);
        }else{
            $('#formularioSubirFichero').slideDown(300);
        }
      });


});









