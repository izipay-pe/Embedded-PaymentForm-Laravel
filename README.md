<p align="center">
  <img src="https://github.com/izipay-pe/Imagenes/blob/main/logos_izipay/logo-izipay-banner-1140x100.png?raw=true" alt="Formulario" width=100%/>
</p>

# Embedded-PaymentForm-Laravel

## Índice

➡️ [1. Introducción](#-1-introducci%C3%B3n)  
🔑 [2. Requisitos previos](#-2-requisitos-previos)  
🚀 [3. Ejecutar ejemplo](#-3-ejecutar-ejemplo)  
🔗 [4. Pasos de integración](#4-pasos-de-integraci%C3%B3n)  
💻 [4.1. Desplegar pasarela](#41-desplegar-pasarela)  
💳 [4.2. Analizar resultado de pago](#42-analizar-resultado-del-pago)  
📡 [4.3. Pase a producción](#43pase-a-producci%C3%B3n)  
🎨 [5. Personalización](#-5-personalizaci%C3%B3n)  
📚 [6. Consideraciones](#-6-consideraciones)

## ➡️ 1. Introducción

En este manual podrás encontrar una guía paso a paso para configurar un proyecto de **[Laravel]** con la pasarela de pagos de IZIPAY. Te proporcionaremos instrucciones detalladas y credenciales de prueba para la instalación y configuración del proyecto, permitiéndote trabajar y experimentar de manera segura en tu propio entorno local.
Este manual está diseñado para ayudarte a comprender el flujo de la integración de la pasarela para ayudarte a aprovechar al máximo tu proyecto y facilitar tu experiencia de desarrollo.

> [!IMPORTANT]
> En la última actualización se agregaron los campos: **nombre del tarjetahabiente** y **correo electrónico** (Este último campo se visualizará solo si el dato no se envía en la creación del formtoken). 

<p align="center">
  <img src="https://github.com/izipay-pe/Imagenes/blob/main/formulario_incrustado/Imagen-Formulario-Incrustado.png?raw=true" alt="Formulario" width="350"/>
</p>

## 🔑 2. Requisitos Previos

- Comprender el flujo de comunicación de la pasarela. [Información Aquí](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/start.html)
- Extraer credenciales del Back Office Vendedor. [Guía Aquí](https://github.com/izipay-pe/obtener-credenciales-de-conexion)
- Para este proyecto se utiliza Laravel Framework 10.15.0
- Para este proyecto utilizamos la herramienta Visual Studio Code.
- Servidor Web
- PHP 7.0 o superior
> [!NOTE]
> Tener en cuenta que, para que el desarrollo de tu proyecto, eres libre de emplear tus herramientas preferidas.

## 🚀 3. Ejecutar ejemplo

### Instalar Laragon u otro servidor local compatible con php

Laragon, servidor web local que contiene los intérpretes para los lenguajes de script de php. Para instalarlo:

1. Dirigirse a la página web de [Laragon](https://laragon.org/download/)
2. Descargarlo e instalarlo.
3. Inicia los servicios de Apache desde el panel de control de Laragon.


### Clonar el proyecto
```sh
git clone https://github.com/izipay-pe/Embedded-PaymentForm-Laravel.git
``` 


### Datos de conexión 

Reemplace **[CHANGE_ME]** con sus credenciales de `API REST` extraídas desde el Back Office Vendedor, revisar [Requisitos previos](#-2-requisitos-previos).

- Editar el archivo `.env` en la ruta raiz del proyecto:
```php
IZIPAY_USERNAME=CHANGE_ME_USER_ID
IZIPAY_PASSWORD=CHANGE_ME_PASSWORD
IZIPAY_PUBLIC_KEY=CHANGE_ME_PUBLIC_KEY
IZIPAY_SHA256_KEY=CHANGE_ME_HMAC_SHA_256
```

### Ejecutar proyecto

1. Mover el proyecto y descomprimirlo en la carpeta `www` en la ruta de instalación de Laragon: `C://laragon/www/[proyecto_php]`

2. Abrir la terminar en Laragon y dirígete al directorio del proyecto

3. Ejecuta el siguiente comando para instalar todas las dependencias de Laravel:
    ```bash
     composer install
    ```
4. Abre tu navegador e ingresa a la siguiente url con el nombre de la carpeta del proyecto:
    ```sh
    http://localhost/[carpeta_laravel]/public
    ```


## 🔗4. Pasos de integración

<p align="center">
  <img src="https://i.postimg.cc/pT6SRjxZ/3-pasos.png" alt="Formulario" />
</p>

## 💻4.1. Desplegar pasarela
### Autentificación
Extraer las claves de `usuario` y `contraseña` del Backoffice Vendedor, concatenar `usuario:contraseña` y agregarlo en la solicitud del encabezado `Authorization`. Podrás encontrarlo en el archivo `./app/Http/Controllers/IzipayController.php`.
```php
// Encabezado Basic con concatenación de "usuario:contraseña" en base64
$auth = env('IZIPAY_USERNAME') . ":" . env('IZIPAY_PASSWORD');

$headers = array(
    "Authorization: Basic " . base64_encode($auth),
    "Content-Type: application/json"
);
```
ℹ️ Para más información: [Autentificación](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/keys.html)
### Crear formtoken
Para configurar la pasarela se necesita generar un formtoken. Se realizará una solicitud API REST a la api de creación de pagos:  `https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment` con los datos de la compra para generar el formtoken. Podrás encontrarlo en el archivo `./app/Http/Controllers/IzipayController.php`.

```php
public function checkout(Request $request){
    $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

    ..

    $body = [
        "amount" => $request->input("amount") * 100,
        "currency" => $request->input("currency"),
        "orderId" => $request->input("orderId"),
        "customer" => [
            "email" => $request->input("email"),
            "billingDetails" => [
                "firstName" => $request->input("firstName"),
                "lastName" => $request->input("lastName"),
                "phoneNumber" => $request->input("phoneNumber"),
                "identityType" => $request->input("identityType"),
                "identityCode" => $request->input("identityCode"),
                "address" => $request->input("address"),
                "country" => $request->input("country"),
                "city" => $request->input("city"),
                "state" => $request->input("state"),
                "zipCode" => $request->input("zipCode"),
            ]
        ],
    ];

    $curl = curl_init($url);
    ..
    ..

    $raw_response = curl_exec($curl);

    $response = json_decode($raw_response , true);

    $formToken = $response["answer"]["formToken"];
    
    $publicKey = env("IZIPAY_PUBLIC_KEY");

    return view('izipay.checkout', compact("publicKey", "formToken"));
}

```
ℹ️ Para más información: [Formtoken](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/formToken.html)
### Visualizar formulario
Para desplegar la pasarela, configura la llave `public key` en el encabezado (Header) del archivo `./resources/views/izipay/checkout.blade.php`. Esta llave debe ser extraída desde el Back Office del Vendedor.

Header: 
Se coloca el script de la libreria necesaria para importar las funciones y clases principales de la pasarela.
```javascript
<!-- Libreria JS de la pasarela, debe incluir la clave pública -->
<script type="text/javascript"
src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
kr-public-key="{{$publicKey}}"
kr-post-url-success="result" kr-language="es-Es">
</script>

<!-- Estilos de la pasarela de pagos -->
<link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css">
<script type="text/javascript" src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js">
</script>
```
Además, se inserta en el body una etiqueta div con la clase `kr-embedded` que deberá tener el atributo `kr-form-token` e incrustarle el `formtoken` generado en la etapa anterior.

Body:
```javascript
<div id="micuentawebstd_rest_wrapper">
  <div class="kr-embedded" kr-form-token="{{$formToken}}">
    @csrf
  </div>
</div>
```
ℹ️ Para más información: [Visualizar formulario](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/formToken.html)

## 💳4.2. Analizar resultado del pago

### Validación de firma
Se configura la función `checkHash` que realizará la validación de los datos recibidos por el servidor luego de realizar el pago mediante el parámetro `kr-answer` utilizando una clave de encriptación definida en `key`. Podrás encontrarlo en el archivo `./resources/views/izipay/checkout.blade.php`.

```php
private function checkHash($request, $key)
{
    $krAnswer = str_replace('\/', '/',  $request["kr-answer"]);
    
    $calculateHash = hash_hmac("sha256", $krAnswer, $key);

    return ($calculateHash == $request["kr-hash"]);
}
```

Se valida que la firma recibida es correcta. Para la validación de los datos recibidos a través de la pasarela de pagos (front) se utiliza la clave `HMACSHA256`.

```php
if (!$this->checkHash($request, env("IZIPAY_SHA256_KEY"))) {
    throw new Exception("Invalid signature");
}
```
En caso que la validación sea exitosa, se puede extraer los datos de `kr-answer` a través de un JSON y mostrar los datos del pago realizado.

```php
$answer = json_decode($request['kr-answer'], true);
```
ℹ️ Para más información: [Analizar resultado del pago](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/kb/payment_done.html)

### IPN
La IPN es una notificación de servidor a servidor (servidor de Izipay hacia el servidor del comercio) que facilita información en tiempo real y de manera automática cuando se produce un evento, por ejemplo, al registrar una transacción.


Se realiza la verificación de la firma utilizando la función `checkHash`. Para la validación de los datos recibidos a través de la IPN (back) se utiliza la clave `PASSWORD`. Se devuelve al servidor de izipay un mensaje confirmando el estado del pago.

Se recomienda verificar el parámetro `orderStatus` para determinar si su valor es `PAID` o `UNPAID`. De esta manera verificar si el pago se ha realizado con éxito.

Podrás encontrarlo en el archivo `./resources/views/izipay/checkout.blade.php`.

```php
public function ipn(Request $request)
{ 
    if (empty($request)) {
        throw new Exception("No post data received!");
    }
      
    // Validación de firma en IPN
    if (!$this->checkHash($request, env("IZIPAY_PASSWORD"))) {
        throw new Exception("Invalid signature");
    }

    $answer = json_decode($request["kr-answer"], true);
    $transaction = $answer['transactions'][0];
    
    // Verifica orderStatus PAID
    $orderStatus = $answer['orderStatus'];
    $orderId = $answer['orderDetails']['orderId'];
    $transactionUuid = $transaction['uuid'];

    return 'OK! OrderStatus is ' . $orderStatus;
}
```

La ruta o enlace de la IPN debe ir configurada en el Backoffice Vendedor, en `Configuración -> Reglas de notificación -> URL de notificación al final del pago`

<p align="center">
  <img src="https://i.postimg.cc/XNGt9tyt/ipn.png" alt="Formulario" width=80%/>
</p>

ℹ️ Para más información: [Analizar IPN](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/kb/ipn_usage.html)

### Transacción de prueba

Antes de poner en marcha su pasarela de pago en un entorno de producción, es esencial realizar pruebas para garantizar su correcto funcionamiento.

Puede intentar realizar una transacción utilizando una tarjeta de prueba con la barra de herramientas de depuración (en la parte inferior de la página).

<p align="center">
  <img src="https://i.postimg.cc/3xXChGp2/tarjetas-prueba.png" alt="Formulario"/>
</p>

- También puede encontrar tarjetas de prueba en el siguiente enlace. [Tarjetas de prueba](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/kb/test_cards.html)

## 📡4.3.Pase a producción

Reemplace **[CHANGE_ME]** con sus credenciales de PRODUCCIÓN de `API REST` extraídas desde el Back Office Vendedor, revisar [Requisitos Previos](#-2-requisitos-previos).

- Editar en `.env` en la ruta raiz del proyecto:
```php
IZIPAY_USERNAME=CHANGE_ME_USER_ID
IZIPAY_PASSWORD=CHANGE_ME_PASSWORD
IZIPAY_PUBLIC_KEY=CHANGE_ME_PUBLIC_KEY
IZIPAY_SHA256_KEY=CHANGE_ME_HMAC_SHA_256
```

## 🎨 5. Personalización

Si deseas aplicar cambios específicos en la apariencia de la pasarela de pago, puedes lograrlo mediante la modificación de código CSS. En este enlace [Código CSS - Incrustado](https://github.com/izipay-pe/Personalizacion/blob/main/Formulario%20Incrustado/Style-Personalization-Incrustado.css) podrá encontrar nuestro script para un formulario incrustado.

<p align="center">
  <img src="https://i.postimg.cc/zDddmKpH/persona.png" alt="Formulario"/>
</p>

## 📚 6. Consideraciones

Para obtener más información, echa un vistazo a:

- [Formulario incrustado: prueba rápida](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/quick_start_js.html)
- [Primeros pasos: pago simple](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/start.html)
- [Servicios web - referencia de la API REST](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/reference.html)
