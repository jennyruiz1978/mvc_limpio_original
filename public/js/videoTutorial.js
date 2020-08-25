$(document).ready(function () {
    $("#btnVideoTutorial").click(function (e) {
        e.preventDefault();
        $('#tutorial').slideToggle();
        let texto = $(this).html();
        texto.includes("Ver") ? $(this).html("Ocultar Tutorial") : $(this).html("Ver Tutorial");
    }); // fin del click
}); // fin del ready


