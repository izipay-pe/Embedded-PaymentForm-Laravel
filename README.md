<p align="center">
  <img src="https://github.com/izipay-pe/Imagenes/blob/main/logos_izipay/logo-izipay-banner-1140x100.png?raw=true" alt="Formulario" width=100%/>
</p>

# Embedded-PaymentForm-Laravel

## Índice

➡️ [1. Introducción](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#%EF%B8%8F-1-introducci%C3%B3n)  
🔑 [2. Requisitos previos](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#-2-requisitos-previos)  
🚀 [3. Ejecutar ejemplo](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#-3-ejecutar-ejemplo)  
🔗 [4. Pasos de integración](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#4-pasos-de-integraci%C3%B3n)  
💻 [4.1. Desplegar pasarela](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#41-desplegar-pasarela)  
💳 [4.2. Analizar resultado de pago](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#42-analizar-resultado-del-pago)  
📡 [4.3. Pase a producción](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#43pase-a-producci%C3%B3n)  
🎨 [5. Personalización](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#-5-personalizaci%C3%B3n)  
📚 [6. Consideraciones](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#-6-consideraciones)

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
- Para este proyecto utilizamos la herramienta Visual Studio Code.
  > [!NOTE]
  > Tener en cuenta que, para que el desarrollo de tu proyecto, eres libre de emplear tus herramientas preferidas.

## 🚀 3. Ejecutar ejemplo

### Instalar Xampp u otro servidor local compatible con php

Xampp, servidor web local multiplataforma que contiene los intérpretes para los lenguajes de script de php. Para instalarlo:

1. Dirigirse a la página web de [xampp](https://www.apachefriends.org/es/index.html)
2. Descargarlo e instalarlo.
3. Inicia los servicios de Apache desde el panel de control de XAMPP.

### Clonar el proyecto:

```sh
git clone [https://github.com/izipay-pe/Embedded-PaymentForm-Laravel.git]
```

## Datos de conexión

**Nota**: Reemplace **[CHANGE_ME]** con sus credenciales de `API REST` extraídas desde el Back Office Vendedor, ver [Requisitos Previos](#Requisitos_Previos).

- Renombre el archivo `ejemplo.env` a `.env` en la ruta raíz y edite la última sección con sus credenciles:

```sh
IZIPAY_USERNAME=**[CHANGE_ME]**
IZIPAY_PASSWORD=**[CHANGE_ME]**
IZIPAY_ENDPOINT=https://api.micuentaweb.pe
IZIPAY_PUBLIC_KEY=**[CHANGE_ME]**
IZIPAY_SHA256_KEY=**[CHANGE_ME]**
IZIPAY_CLIENT_ENDPOINT=https://static.micuentaweb.pe
```

### Ejecutar proyecto

1. Mueve el proyecto descargado a la carpeta de instalación de proyectos de xammp `c://xampp/htdocs/[proyecto_laravel]`

2. Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP.
Acceder al Proyecto:

3. Abre tu navegador e ingresa a la siguiente url con el nombre de la carpeta del proyecto y realiza una compra.
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
  $auth = env('IZIPAY_USERNAME') . ":" . env('IZIPAY_PASSWORD');
  $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

  $curl = curl_init($url);
  ...
  curl_setopt($curl, CURLOPT_USERPWD, $auth);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
```
ℹ️ Para más información: [Autentificación](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/keys.html)

### Crear formtoken
Para configurar la pasarela se necesita generar un formtoken. Se realizará una solicitud API REST a la api de creación de pagos:  `https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment` con los datos de la compra para generar el formtoken. Podrás encontrarlo en el archivo `./app/Http/Controllers/IzipayController.php`.

```php
  public function getFormToken()
    {
        $datos = array(
            "amount" => 250,
            "currency" => "PEN",
            "orderId" => uniqid("MyOrderId"),
            "customer" => array(
                "email" => "sample@example.com",
                ....
            )
        );

        $auth = env('IZIPAY_USERNAME') . ":" . env('IZIPAY_PASSWORD');
        $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

        $curl = curl_init($url);
        ...
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $raw_response = curl_exec($curl);
        $response = json_decode($raw_response, true);
        ...
        return  $response["answer"]["formToken"];
  }

```
ℹ️ Para más información: [Formtoken](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/formToken.html)

### Visualizar formulario
Para desplegar la pasarela, configura la llave `public key` en el encabezado (Header) del archivo `./resources/views/izipay/checkout.blade.php`. Esta llave debe ser extraída desde el Back Office del Vendedor.

Header: 
Se coloca el script de la libreria necesaria para importar las funciones y clases principales de la pasarela.
```javascript
<script type="text/javascript"
src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
kr-public-key="{{$publicKey}}"
kr-post-url-success="result" kr-language="es-Es">
</script>

<link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css">
<script type="text/javascript" src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js">
</script>
```
Además, se inserta en el body una etiqueta div con la clase `kr-embedded` que deberá tener el atributo `kr-form-token` e incrustarle el `formtoken` generado en la etapa anterior.

Body:
```javascript
<div id="micuentawebstd_rest_wrapper">
  <div class="kr-embedded" kr-form-token="{{$formToken}}"></div>
</div>
```
ℹ️ Para más información: [Visualizar formulario](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/embedded/formToken.html)

## 💳4.2. Analizar resultado del pago

### Validación de firma
Se configura la función `checkhash()` que realizará la validación de los datos del parámetro `kr-answer` utilizando una clave de encriptacón definida por el parámetro `kr-hash-key`. Podrás encontrarlo en el archivo `./resources/views/izipay/checkout.blade.php`.

```php
private function checkHash(Request $request){
  if ($request['kr-hash-key'] == "sha256_hmac") {
      $key = env('IZIPAY_SHA256_KEY');
  } elseif ($request['kr-hash-key'] == "password") {
      $key = env('IZIPAY_PASSWORD');
  } else {
      return false;
  }

  $krAnswer = str_replace('\/', '/',  $request["kr-answer"]);
  $calculateHash = hash_hmac("sha256", $krAnswer, $key);

  return ($calculateHash == $request["kr-hash"]);
}
```

Se valida que la firma recibida es correcta

```php
if (!$this->checkHash($request)) {
    throw new Exception("Invalid signature");
}
```
En caso que la validación sea exitosa, se puede extraer los datos de `kr-answer` a través de un JSON y mostrar los datos del pago realizado.

```php
$answer = json_decode($request['kr-answer'], true);
$orderStatus = $answer['orderStatus'];

return view('izipay.result', compact('orderStatus', 'answer'));
```
ℹ️ Para más información: [Analizar resultado del pago](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/kb/payment_done.html)

### IPN
La IPN es una notificación de servidor a servidor (servidor de Izipay hacia el servidor del comercio) que facilita información en tiempo real y de manera automática cuando se produce un evento, por ejemplo, al registrar una transacción.


Se realiza la verificación de la firma utilizando la función `checkhash()` y se devuelve al servidor de izipay un mensaje confirmando el estado del pago. Podrás encontrarlo en el archivo `./resources/views/izipay/checkout.blade.php`.

```php
public function ipn(Request $request){
  if (empty($request)) throw new Exception("No post data received!");

  // Validación de firma en IPN
  if (!$this->checkHash($request)) throw new Exception("Invalid signature");

  // Ejemplos de extracción de datos
  $answer = json_decode($request["kr-answer"], true);
  $transaction = $answer['transactions'][0];
  
  // Verifica orderStatus PAID
  $orderStatus = $answer['orderStatus'];
  $orderId = $answer['orderDetails']['orderId'];
  $transactionUuid = $transaction['uuid'];

  print 'OK! OrderStatus is ' . $orderStatus;
}
```

La IPN debe ir configurada en el Backoffice Vendedor, en `Configuración -> Reglas de notificación -> URL de notificación al final del pago`

<p align="center">
  <img src="https://i.postimg.cc/zfx5JbQP/ipn.png" alt="Formulario" width=80%/>
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

Reemplace **[CHANGE_ME]** con sus credenciales de PRODUCCIÓN de `API REST` extraídas desde el Back Office Vendedor, revisar [Requisitos Previos](https://github.com/izipay-pe/Readme-Template/tree/main?tab=readme-ov-file#-2-requisitos-previos).

- Editar en el archivo `./.env` en la ruta raiz del proyecto:
```php
// Identificador de su tienda
define("USERNAME", "~ CHANGE_ME_USER_ID ~");

// Clave de Test o Producción
define("PASSWORD", "~ CHANGE_ME_PASSWORD ~");

// Clave Pública de Test o Producción
define("PUBLIC_KEY","~ CHANGE_ME_PUBLIC_KEY ~");

// Clave HMAC-SHA-256 de Test o Producción
define("HMAC_SHA256","~ CHANGE_ME_HMAC_SHA_256 ~");
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