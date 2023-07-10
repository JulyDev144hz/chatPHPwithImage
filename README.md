# CodeIgniter 4 Application Starter

## Como instalar este proyecto?
Cabe aclarar que este proyecto esta hecho con PHP codeigniter. asi que para instalarlo
necesitaremos instalar XAMPP e instalar php. desde el menu de xampp tendremos que 
ir a config de apacheServer y php.ini, ahi tendremos que descomentar la linea que dice
`;extension=intl` a `extension=intl`
con eso listo procedemos a iniciar el servidor de apache e instalar composer
cuando tengamos composer y el servidor instalado tendremos que clonar este repositorio
dentro del path `C:\xampp\htdocs`. una vez clonado este repositorio ejecutaremos 2
comandos desde composer:
`composer require codeigniter4/framework`
`composer update`
con esos comandos ya seria suficiente para dirigirnos a [http://localhost/chat](http://localhost/chat)
ahi ya veremos nuestro servidor funcionando.
En caso de que no haya funcionado revisar el archivo `.env` y revisar que la base url este funcionando. hacer lo mismo con el archivo dentro de `/App/Views/index.php` que al conectarse al websocket puede estar fallando con su IP...
Deberia estar asi `var conn = new WebSocket('ws://localhost:8080');`.
Para arrancar el servidor websocket posicionarse en la raiz del proyecto (`C:\xampp\htdocs\chat`)
y ejecutar el siguiente comando `php index.php server index`.
con eso iniciariamos el servidor websocket para enviar y recibir mensajes.
espero que le guste este proyecto

### Como sacar el logo de fuego de la pagina
para sacar el logo de codeigniter dentro de la pagina tenes que dirigirte hacia el archivo
`.env` y en `CI_ENVIRONMENT = development` poner `CI_ENVIRONMENT = production`