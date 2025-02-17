# Últimos retoques
## Modifica tu aplicación para que:

- Implemente una API REST para el CRUD . Al menos, se realizará en la tabla productos. Usa la API en el proyecto. No olvides devolver JSON.
- Envíe la confirmación del correo con un token y en las cabeceras (no en la URL).

- Además se valorará positivamente si se realiza el siguiente apartado sobre ataques CRSF.
## NOTAS DE INTERÉS:
- Recuperar cabeceras :
    getallheaders():  https://www.php.net/manual/en/function.getallheaders.php

- Nos devuelve un array. Nos interesará explorar este array asociativo para  saber si está o no autorizado
- Hay que comprobar también que el formato de la autorización sea de tipo 'Bearer'