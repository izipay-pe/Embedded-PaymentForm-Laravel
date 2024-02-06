
# Izipay Laravel Embedded Payment Form 

Ejemplo de uso del SDK Lyra Payment para crear el formulario de pago incrustado de Izipay en una aplicación de Laravel 10.

Documentación: [Izipay MicuentaWeb Docs](https://secure.micuentaweb.pe/doc/es-PE/)  
Demo: [Playground](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/playground/Charge/CreatePayment) 

## Requisitos

* PHP 8.1 y superiores.  
* Extensión CURL para PHP.
* Composer v2.5.5
* Nodejs v18.15.0
* Servidor PHP(XAMPP)

## Dependencias

lyracom/rest-php-sdk ^4.0

## Configuración    

1. Renombrar el archivo `.env.example` por `.env` y registrar tus credenciales de Integración del [Back Office Vendedor de Izipay](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/keys.html)

```sh
IZIPAY_USERNAME=<Identificador >
IZIPAY_PASSWORD=
IZIPAY_ENDPOINT=
IZIPAY_PUBLIC_KEY=
IZIPAY_SHA256_KEY=
IZIPAY_CLIENT_ENDPOINT=
```
2. Instalar dependencias con el comando composer
```sh
composer install
```

3. Instalar dependencias con el comando npm
```sh
npm install
```

## Ejecución
4. Activar el servidor Apache, opcionalmente configurar un dominio local que apunte a la carpeta pública `dominio.local/public`
5. Ejecutar el comando
```sh
npm run dev
```
