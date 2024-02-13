# Embedded-PaymentForm-Laravel

## Índice

- [1. Introducción](#1-introducción)
- [2. Requisitos previos](#2-requisitos-previos)
- [3. Despliegue](#3-despliegue)
- [4. Datos de conexión](#4-datos-de-conexión)
- [5. Transacción de prueba](#5-transacción-de-prueba)
- [6. Implementación de la IPN](#6-implementación-de-la-ipn)
- [7. Personalización](#7-personalización)
- [8. Consideraciones](#8-consideraciones)

## 1. Introducción

En este manual podrás encontrar una guía paso a paso para configurar un proyecto de **[Laravel]** con la pasarela de pagos de IZIPAY. Te proporcionaremos instrucciones detalladas y credenciales de prueba para la instalación y configuración del proyecto, permitiéndote trabajar y experimentar de manera segura en tu propio entorno local.
Este manual está diseñado para ayudarte a comprender el flujo de la integración de la pasarela para ayudarte a aprovechar al máximo tu proyecto y facilitar tu experiencia de desarrollo.

<p align="center">
  <img src="https://github.com/izipay-pe/Imagenes/blob/main/formulario_incrustado/Imagen-Formulario-Incrustado.png" alt="Formulario" width="350"/>
</p>

<a name="Requisitos_Previos"></a>

## 2. Requisitos previos

- Comprender el flujo de comunicación de la pasarela. [Información Aquí](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/start.html)
- Extraer credenciales del Back Office Vendedor. [Guía Aquí](https://github.com/izipay-pe/obtener-credenciales-de-conexion)
- Para este proyecto utilizamos la herramienta Visual Studio Code.
  > [!NOTE]
  > Tener en cuenta que, para que el desarrollo de tu proyecto, eres libre de emplear tus herramientas preferidas.

## 3. Despliegue

### Instalar Xampp u otro servidor local compatible con php

Xampp, servidor web local multiplataforma que contiene los intérpretes para los lenguajes de script de php. Para instalarlo:

1. Dirigirse a la página web de [xampp](https://www.apachefriends.org/es/index.html)
2. Descargarlo e instalarlo.

<p align="center">
  <img src="./xampp.png" alt="Formulario" />
</p>

### Clonar el proyecto:

```sh
git clone [https://github.com/izipay-pe/Embedded-PaymentForm-Laravel.git]
```

### Ejecutar proyecto

Mueve el proyecto descargado a la carpeta de instalación de proyectos de xammp `c://xampp/htdocs/[proyecto_laravel]`

3. Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP.
Acceder al Proyecto:

4. Abre tu navegador e ingresa a la siguiente url con el nombre de la carpeta del proyecto.
```sh
http://localhost/[carpeta_laravel]/public
```

## 4. Datos de conexión

**Nota**: Reemplace **[CHANGE_ME]** con sus credenciales de `API REST` extraídas desde el Back Office Vendedor, ver [Requisitos Previos](#Requisitos_Previos).

- Renombre el archivo `ejemplo.env` a `.env` y edite la última sección con sus credenciles:

```sh
IZIPAY_USERNAME=12345678
IZIPAY_PASSWORD=testpassword_x0ZDVlzhOnkCSDAsaspOQ9rLbQUlKYmunHSfI5SsaswSDWss
IZIPAY_ENDPOINT=https://api.micuentaweb.pe
IZIPAY_PUBLIC_KEY=12345678:testpublickey_csSdSDASdssSDachYRj29NoDYjBKV6uDAfP42sSDSScxc7u
IZIPAY_SHA256_KEY=WLYdcwRkWCSfasFHGHJdasyhQ91gnqQ36S95YwBh7ByE8
IZIPAY_CLIENT_ENDPOINT=https://static.micuentaweb.pe
```

## 5. Transacción de prueba

Antes de poner en marcha su pasarela de pago en un entorno de producción, es esencial realizar pruebas para garantizar su correcto funcionamiento.

Puede intentar realizar una transacción utilizando una tarjeta de prueba con la barra de herramientas de depuración (en la parte inferior de la página).

<p align="center">
  <img src="https://i.postimg.cc/3xXChGp2/tarjetas-prueba.png" alt="Formulario"/>
</p>

- También puede encontrar tarjetas de prueba en el siguiente enlace. [Tarjetas de prueba](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/kb/test_cards.html)

## 6. Implementación de la IPN

> [!IMPORTANT]
> Es recomendable implementar la IPN para comunicar el resultado de la solicitud de pago al servidor del comercio.

La IPN es una notificación de servidor a servidor (servidor de Izipay hacia el servidor del comercio) que facilita información en tiempo real y de manera automática cuando se produce un evento, por ejemplo, al registrar una transacción.
Los datos transmitidos en la IPN se reciben y analizan mediante un script que el vendedor habrá desarrollado en su servidor.

- Ver manual de implementación de la IPN. [Aquí](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/kb/payment_done.html)
- Vea el ejemplo de la respuesta IPN con PHP. [Aquí](https://github.com/izipay-pe/Redirect-PaymentForm-IpnT1-PHP)
- Vea el ejemplo de la respuesta IPN con NODE.JS. [Aquí](https://github.com/izipay-pe/Response-PaymentFormT1-Ipn)

## 7. Personalización

Si deseas aplicar cambios específicos en la apariencia de la pasarela de pago, puedes lograrlo mediante la modificación de código CSS. En este enlace [Código CSS - Incrustado](https://github.com/izipay-pe/Personalizacion-PaymentForm-Incrustado) podrá encontrar nuestro script para un formulario incrustado.

## 8. Consideraciones

Para obtener más información, echa un vistazo a:

- [Formulario incrustado: prueba rápida](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/quick_start_js.html)
- [Primeros pasos: pago simple](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/javascript/guide/start.html)
- [Servicios web - referencia de la API REST](https://secure.micuentaweb.pe/doc/es-PE/rest/V4.0/api/reference.html)
