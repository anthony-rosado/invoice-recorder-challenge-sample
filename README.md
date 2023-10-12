# Invoice Recorder Challenge Sample (v1.0) [ES]

API REST que expone endpoints que permite registrar comprobantes en formato xml.
De estos comprobantes se obteniene la información como el emisor y receptor, sus documentos (dni, ruc, etc), los artículos o líneas, y los montos totales y por cada artículo.
Un comprobante es un documento que respalda una transacción financiera o comercial, y en su versión XML es un archivo estructurado que contiene todos los datos necesarios para cumplir con los requisitos legales y fiscales.
Utilizando el lenguaje XML, se generan comprobantes digitales, que contienen información del emisor, receptor, conceptos, impuestos y el monto total de la transacción.
La API utiliza Json Web Token para la autenticación.

## Detalles de la API

-   Usa PHP 8.1
-   Usa una base de datos en MySQL
-   Puede enviar correos

## Inicia el proyecto con docker

-   Clona el archivo `.env.example` a `.env`
-   Reemplaza las credenciales de correo por las tuyas (puedes obtener unas con gmail siguiendo [esta guía](https://programacionymas.com/blog/como-enviar-mails-correos-desde-laravel#:~:text=Para%20dar%20la%20orden%20a,su%20orden%20ha%20sido%20enviada.))
-   En una terminal ejecuta:

```
docker-compose up
```

-   En otra terminal, ingresa al contenedor web y ejecuta:

```
composer install --ignore-platform-reqs
php artisan migrate
```

-   Consulta la API en http://localhost:8090/api/v1

## Información inicial

Puedes encontrar información inicial para popular la DB en el siguiente enlace:

[Datos iniciales](https://drive.google.com/drive/folders/103WGuWMLSkuHCD9142ubzyXPbJn77ZVO?usp=sharing)

## Nuevas funcionalidades

### 1. Registro de serie, número, tipo del comprobante y moneda

Se desea poder registrar la serie, número, tipo de comprobante y moneda. Para comprobantes existentes, debería extraerse esa información a regularizar desde el campo xml_content de vouchers.

### 2. Carga de comprobantes en segundo plano

Actualmente el registro de comprobantes se realiza en primer plano, se desea que se realice en segundo plano.
Además, en lugar de enviar una notificación por correo para informar subida de comprobantes, ahora deberá enviar dos listados de comprobantes:

-   Los que se subieron correctamente
-   Los que no pudieron registrarse (y la razón)

### 3. Endpoint de montos totales

Se necesita un nuevo endpoint que devuelva la información total acumulada en soles y dólares.

### 4. Eliminación de comprobantes

Se necesita poder eliminar comprobantes por su id.

### 5. Filtro en listado de comprobantes

Se necesita poder filtrar en el endpoint de listado por serie, número y por un rango de fechas (que actuarán sobre las fechas de creación).

**Nota**: En todos los casos de nuevas funcionalidades, se tratan de comprobantes por usuarios.

## Consideraciones

-   Se valorará el uso de código limpio, estándares, endpoints optimizados, tolerancia a fallos y concurrencia.

## Envío del reto

Deberás enviar el reto a través de una Pull Request a este repositorio. Puedes indicar documentación de las nuevas funcionalidades o una descripción/diagramas/etc que creas necesario.

## ¿Tienes alguna duda?

Puedes enviar un correo a `ignacioruedaboada@gmail.com` o a `anthony.rosado747@gmail.com` enviando tus consultas y se te responderá a la brevedad.
