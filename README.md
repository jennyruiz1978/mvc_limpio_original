Instrucciones para utilizar este framework mvc

1.- Poner an la linea 4 del archivo .htaccess de la carpeta public el directorio correcto de la aplicación
el codigo de la linea 4 es el siguiente: RewriteBase /mvc_framework_base/public
Lo que hay que cambiar en la parte que dice "mvc_framework_base" por la 
carpeta del proyecto que se esta creando
¡¡¡ ATENCION HAY VARIOS FICHEROS .htaccess SOLO HAY QUE CAMBIAR EL DATO DE LA CARPETA PUBLIC !!!

2.- En el fichero ./app/config/configurar.php hay que cambiar las lineas 3,4,5,6 con los datos de la base de datos,
y en la linea 11 hay que actualizar el directorio de la aplicación, ademas, en la linea 13 hay que añadir el titulo del
proyecto que estamos creando

3.- A partir de aquí hay que añadir elcodigo para el header y el footer, en views/includes/
en este caso lo que haya añadido es la base del codigo con los CDN de Bootstrap, pero se puede
sustitutir por lo que se crea conveniente

4.- El codigo de las diferentes paginas o vistas debe incluirse en views/paginas en este caso hay solo 
una pagina que se llama inicio.php, y tambien editar, crear, borrar, actualizar

5.- El usuario de acceso es test@data.es y el pasword es test