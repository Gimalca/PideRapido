<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * EN-Revision: 22075
 */
return array(
    //LoginAccount
    "Not existent identity. A record with the supplied identity could not be found." => "Usuario Invalido",
    // Zend_Validate_Alnum
    "Invalid type given, value should be float, string, or integer" => "El tipo especificado no es válido, el valor debe ser de tipo float, una cadena de texto o entero",
    "The input contains characters which are non alphabetic and no digits" => "%value% contiene caracteres que no son alfabéticos ni dígitos",
    "The input is an empty string" => "%value% es una cadena de texto vacia",

    // Zend_Validate_Alpha
    "Invalid type given, value should be a string" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",
    "The input contains non alphabetic characters" => "%value% contiene caracteres no alfabéticos",
    "The input is an empty string" => "%value% es una cadena de texto vacia",

    // Zend_Validate_Barcode
    "The input failed checksum validation" => "%value% Fallo la validación de  checksum",
    "The input contains invalid characters" => "%value% contiene caracteres no válidos",
    "The input should have a length of %length% characters" => "%value% debe tener una longitud de %length% caracteres",
    "Invalid type given, value should be string" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",

    // Zend_Validate_Between
    "The input is not between '%min%' and '%max%', inclusively" => "%value% no está incluido entre '%min%' y '%max%'",
    "The input is not strictly between '%min%' and '%max%'" => "%value% no está exactamente entre '%min%' y '%max%'",

    // Zend_Validate_Callback
    "The input is not valid" => "%value% no es válido",
    "Failure within the callback, exception returned" => "Falló dentro de la llamada de retorno, ha devuelto una excepción",

    // Zend_Validate_Ccnum
    "The input must contain between 13 and 19 digits" => "%value% debe contener entre 13 y 19 dígitos",
    "Luhn algorithm (mod-10 checksum) failed on The input" => "El algoritmo de Luhn (checksum del módulo 10) fallo en The input",

    // Zend_Validate_CreditCard
    "Luhn algorithm (mod-10 checksum) failed on The input" => "El algoritmo de Luhn (checksum del módulo 10) fallo en The input",
    "The input must contain only digits" => "%value% debe contener solo dígitos",
    "Invalid type given, value should be a string" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",
    "The input contains an invalid amount of digits" => "%value% contiene una cantidad inválida de dígitos",
    "The input is not from an allowed institute" => "%value% no es de una institución autorizada",
    "Validation of The input has been failed by the service" => "La validación de %value% fallo por causa del servicio",
    "The service returned a failure while validating The input" => "El servicio devolvió un fallo durante la validación de The input",

    // Zend_Validate_Date
    "Invalid type given, value should be string, integer, array or Zend_Date" => "El tipo especificado no es válido, el valor debe ser una cadena de texto, entero, array o un objeto Zend_Date",
    "The input does not appear to be a valid date" => "%value% no parece ser una fecha válida",
    "The input does not fit the date format '%format%'" => "%value% no se ajusta al formato de fecha '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching the input was found" => "No fue encontrado ningun registro que coincida con the input",
    "A record matching the input was found" => "Se encontro un registro coincidente con %value%",

    // Zend_Validate_Digits
    "Invalid type given, value should be string, integer or float" => "El tipo especificado no es válido, el valor debe ser una cadena de texto, entero o float",
    "The input contains characters which are not digits; but only digits are allowed" => "%value% contiene caracteres que no son dígitos, solo se permiten dígitos",
    "The input is an empty string" => "%value% es una cadena vacía",

    // Zend_Validate_EmailAddress
    "Invalid type given, value should be a string" => "El tipo especificado no es válido, el valor debe ser una cadena de texto",
    "The input is not a valid email address. Use the basic format local-part@hostname" => "%value% no es una dirección de correo electrónico válido en el formato local-part@hostname",
    "'%hostname%' is not a valid hostname for the email address" => "'%hostname%' no es un nombre de host válido para la dirección de correo electrónico",
    "'%hostname%' does not appear to have a valid MX record for the email address The input" => "%value% no parece tener un registro MX válido para la dirección de correo electrónico The input",
    "'%hostname%' is not in a routable network segment. The email address The input should not be resolved from public network." => "'%hostname%' no esta en un segmento de red ruteable. La dirección de correo electrónico The input no se debe poder resolver desde una red pública.",
    "'%localPart%' can not be matched against dot-atom format" => "%value% no es igual al formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "%value% no es igual al formato quoted-string",
    "'%localPart%' is not a valid local part for email address The input" => "%value% no es una parte local válida para la dirección de correo electrónico The input",
    "The input exceeds the allowed length" => "%value% excede la longitud permitida",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "Demasiados archivos, se permiten un máximo de '%max%' pero se han especificado '%count%'",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "Muy pocos archivos, se esperaba un mí­nimo de '%min%' pero sólo se han especificado '%count%'",

    // Zend_Validate_File_Crc32
    "File The input does not match the given crc32 hashes" => "El CRC32 del archivo The input es incorrecto",
    "A crc32 hash could not be evaluated for the given file" => "No se ha podido calcular el CRC32 del archivo especificado",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_ExcludeExtension
    "File The input has a false extension" => "El archivo The input tiene una extensión incorrecta",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_ExcludeMimeType
    "File The input has a false mimetype of '%type%'" => "El archivo The input tiene un tipo MIME '%type%' incorrecto",
    "The mimetype of file The input could not be detected" => "No se ha podido determinar el tipo MIME del archivo The input",
    "File The input can not be read" => "El archivo The input no se puede leer",

    // Zend_Validate_File_Exists
    "File The input does not exist" => "El archivo The input no existe",

    // Zend_Validate_File_Extension
    "File The input has a false extension" => "El archivo The input tiene una extensión incorrecta",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Todos los archivos deberí­an tener un tamaño máximo de '%max%' pero tiene un tamaño de '%size%'",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Todos los archivos deberí­an tener un tamaño mí­nimo de '%min%' pero tiene un tamaño de '%size%'",
    "One or more files can not be read" => "Uno o más archivos no se pueden leer",

    // Zend_Validate_File_Hash
    "File The input does not match the given hashes" => "El archivo The input no se corresponde con los códigos hash especificados",
    "A hash could not be evaluated for the given file" => "No se ha podido evaluar ningún código hash para el archivo especificado",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image The input should be '%maxwidth%' but '%width%' detected" => "El ancho máxima para la imagen The input deberí­a ser '%maxwidth%' pero es de '%width%'",
    "Minimum expected width for image The input should be '%minwidth%' but '%width%' detected" => "El ancho mí­nima para la imagen The input deberí­a ser '%minwidth%' pero es de '%width%'",
    "Maximum allowed height for image The input should be '%maxheight%' but '%height%' detected" => "La altura máxima para la imagen The input deberí­a ser '%maxheight%' pero es de '%height%'",
    "Minimum expected height for image The input should be '%minheight%' but '%height%' detected" => "La altura mí­nima para la imagen The input deberí­a ser '%minheight%' pero es de '%height%'",
    "The size of image The input could not be detected" => "No se ha podido determinar el tamaño de la imagen The input",
    "File The input can not be read" => "El archivo The input no se puede leer",

    // Zend_Validate_File_IsCompressed
    "File The input is not compressed, '%type%' detected" => "El archivo The input no está comprimido, '%type%' detectado",
    "The mimetype of file The input could not be detected" => "No se ha podido determinar el tipo MIME del archivo The input",
    "File The input can not be read" => "El archivo The input no se puede leer",

    // Zend_Validate_File_IsImage
    "File The input is no image, '%type%' detected" => "El archivo The input no es una imagen, '%type%' detectado",
    "The mimetype of file The input could not be detected" => "No se ha podido determinar el tipo MIME del archivo The input",
    "File The input can not be read" => "El archivo The input no se puede leer",

    // Zend_Validate_File_Md5
    "File The input does not match the given md5 hashes" => "El archivo The input no se corresponde con el MD5 especificado",
    "A md5 hash could not be evaluated for the given file" => "No se ha podido calcular el MD5 del archivo especificado",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_MimeType
    "File The input has a false mimetype of '%type%'" => "El archivo The input tiene un tipo MIME '%type%' falso",
    "The mimetype of file The input could not be detected" => "No se ha podido determinar el tipo MIME del archivo The input",
    "File The input can not be read" => "El archivo The input no se puede leer",

    // Zend_Validate_File_NotExists
    "File The input exists" => "El archivo The input existe",

    // Zend_Validate_File_Sha1
    "File The input does not match the given sha1 hashes" => "El archivo The input no es igual al SHA1 especificado",
    "A sha1 hash could not be evaluated for the given file" => "No se ha podido calcular el SHA1 del archivo especificado",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_Size
    "Maximum allowed size for file The input is '%max%' but '%size%' detected" => "El tamaño máximo permitido para el archivo The input es '%max%' pero se ha detectado un tamaño de '%size%'",
    "Minimum expected size for file The input is '%min%' but '%size%' detected" => "El tamaño mí­nimo permitido para el archivo The input es '%min%' pero se ha detectado un tamaño de '%size%'",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_File_Upload
    "File The input exceeds the defined ini size" => "El tamaño del archivo The input excede el valor definido en el ini",
    "File The input exceeds the defined form size" => "El archivo The input excede el tamaño definido en el formulario",
    "File The input was only partially uploaded" => "El archivo The input ha sido sólo parcialmente subido",
    "File The input was not uploaded" => "El archivo The input no ha sido subido",
    "No temporary directory was found for file The input" => "No se ha encontrado el directorio temporal para el archivo The input",
    "File The input can't be written" => "No se puede escribir en el archivo The input",
    "A PHP extension returned an error while uploading the file The input" => "Una extensión PHP devolvió un error mientras se subí­a el archivo The input",
    "File The input was illegally uploaded. This could be a possible attack" => "El archivo The input ha sido subido ilegalmente, lo cual podrí­a ser un ataque",
    "File The input was not found" => "Archivo The input no encontrado",
    "Unknown error while uploading file The input" => "error desconocido al intentar subir el archivo The input",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "Demasiadas palabras, sólo se permiten '%max%' pero se han contado '%count%'",
    "Too few words, minimum '%min%' are expected but '%count%' were counted" => "Demasiado pocas palabras, se esperaban al menos '%min%' pero se han contado '%count%'",
    "File The input could not be found" => "No se ha podido encontrar el archivo The input",

    // Zend_Validate_Float
    "Invalid type given, value should be float, string, or integer" => "El tipo especificado no es válido, el valor deberí­a ser de tipo float, una cadena de texto o un entero",
    "The input does not appear to be a float" => "%value% no parece ser un float",

    // Zend_Validate_GreaterThan
    "The input is not greater than '%min%'" => "%value% no es mayor que '%min%'",

    // Zend_Validate_Hex
    "Invalid type given, value should be a string" => "El tipo especificado es incorrecto, el valor deberí­a ser una cadena de texto",
    "The input has not only hexadecimal digit characters" => "%value% no consta únicamente de dí­gitos y caracteres hexadecimales",

    // Zend_Validate_Hostname
    "Invalid type given, value should be a string" => "El tipo especificado es incorrecto, el valor deberí­a ser una cadena de texto",
    "The input appears to be an IP address, but IP addresses are not allowed" => "%value% parece una dirección IP, pero éstas no están permitidas",
    "The input appears to be a DNS hostname but cannot match TLD against known list" => "%value% parece ser un nombre de dominio DNS pero el TLD no es válido",
    "The input appears to be a DNS hostname but contains a dash in an invalid position" => "%value% parece ser un nombre de dominio DNS pero contiene una barra en una posición inválida",
    "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "%value% parece ser un nombre de dominio DNS pero su formato no se corresponde con el correcto para el TLD '%tld%'",
    "The input appears to be a DNS hostname but cannot extract TLD part" => "%value% parece ser un nombre de dominio DNS pero no se puede extraer la parte del TLD",
    "The input does not match the expected structure for a DNS hostname" => "%value% no se corresponde con la estructura esperada para un nombre de dominio DNS",
    "The input does not appear to be a valid local network name" => "%value% no parece ser un nombre de área local válido",
    "The input appears to be a local network name but local network names are not allowed" => "%value% parece ser un nombre de área local pero no se permiten nombres de área local",
    "The input appears to be a DNS hostname but the given punycode notation cannot be decoded" => "%value% parece ser un nombre de dominio DNS pero no se puede decodificar la notación de punycode",

    // Zend_Validate_Iban
    "Unknown country within the IBAN The input" => "Paí­s desconocido dentro del IBAN The input",
    "The input has a false IBAN format" => "%value% tiene un formato falso de IBAN",
    "The input has failed the IBAN check" => "La prueba de validación de IBAN de %value% ha fallado",

    // Zend_Validate_Identical
    "The two given tokens do not match" => "Las dos muestras especificados no concuerdan",
    "No token was provided to match against" => "No se ha especificado ninguna muestra a comprobar",

    // Zend_Validate_InArray
    "The input was not found in the haystack" => "No se ha encontrado The input en el argumento especificado",

    // Zend_Validate_Int
    "Invalid type given, value should be string or integer" => "El tipo especificado es inválido, el valor deberí­a ser una cadena de texto o un entero",
    "The input does not appear to be an integer" => "%value% no parece ser un entero",

    // Zend_Validate_Ip
    "Invalid type given, value should be a string" => "El tipo especificado es incorrecto, el valor deberí­a ser una cadena de texto",
    "The input does not appear to be a valid IP address" => "%value% no parece ser una dirección IP válida",

    // Zend_Validate_Isbn
    "Invalid type given, value should be string or integer" => "El tipo especificado es inválido, el valor deberí­a ser una cadena de texto o un entero",
    "The input is not a valid ISBN number" => "El número ISBN especificado (The input) no es válido",

    // Zend_Validate_LessThan
    "The input is not less than '%max%'" => "%value% no es menor que '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given, value should be float, string, array, boolean or integer" => "El tipo especificado es inválido, el valor deberí­a ser un float, una cadena de texto, un array, un boolean o un entero",
    "Value is required and can't be empty" => "Se requiere un valor y éste no puede estar vací­o",

    // Zend_Validate_PostCode
    "Invalid type given. The value should be a string or a integer" => "El tipo especificado es incorrecto, el valor deberí­a ser una cadena de texto",
    "The input does not appear to be a postal code" => "%value% no parece ser un código postal",

    // Zend_Validate_Regex
    "Invalid type given, value should be string, integer or float" => "El tipo especificado es incorrecto, el valor deberí­a ser de tipo float, una cadena de texto o un entero",
    "The input does not match against pattern '%pattern%'" => "%value% no concuerda con el patrón '%pattern%' especificado",
    "There was an internal error while using the pattern '%pattern%'" => "Se ha producido un error interno al usar el patrón '%pattern%' especificado",

    // Zend_Validate_Sitemap_Changefreq
    "The input is not a valid sitemap changefreq" => "%value% no es una especificación válida de frecuencia de cambio",
    "Invalid type given, the value should be a string" => "El tipo especificado es inválido, el valor deberí­a ser una cadena de texto",

    // Zend_Validate_Sitemap_Lastmod
    "The input is not a valid sitemap lastmod" => "%value% no es un lastmod de mapa web válido",
    "Invalid type given, the value should be a string" => "El tipo especificado es inválido, el valor deberí­a ser una cadena de texto",

    // Zend_Validate_Sitemap_Loc
    "The input is not a valid sitemap location" => "%value% no es una ubicación de mapa web válida",
    "Invalid type given, the value should be a string" => "El tipo especificado es inválido, el valor deberí­a ser una cadena de texto",

    // Zend_Validate_Sitemap_Priority
    "The input is not a valid sitemap priority" => "%value% no es una prioridad de mapa web válida",
    "Invalid type given, the value should be a integer, a float or a numeric string" => "El tipo especificado es inválido, el valor deberí­a ser un entero, un float o una cadena de texto numérica",

    // Zend_Validate_StringLength
    "Invalid type given, value should be a string" => "El tipo especificado es incorrecto, el valor deberí­a ser una cadena de texto",
    "The input is less than %min% characters long" => "%id% tiene menos de '%min%' caracteres",
    "The input is more than %max% characters long" => "%value% tiene más de '%max%' caracteres",
    
    // Zend_Validate_CSRF
    "The form submitted did not originate from the expected site" => "El formulario no se originó desde el sitio esperado"
);
