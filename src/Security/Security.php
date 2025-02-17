<?php
namespace Security;

use Repositories\BlacklistedTokenRepository;
use Exception;

class Security
{
    private static ?BlacklistedTokenRepository $blacklistedTokenRepo = null;

    public function __construct()
    {
        if (self::$blacklistedTokenRepo === null) {
            self::$blacklistedTokenRepo = new BlacklistedTokenRepository();
        }
    }

    public static function isTokenBlacklisted($token): bool
    {
        if (self::$blacklistedTokenRepo === null) {
            self::$blacklistedTokenRepo = new BlacklistedTokenRepository();
        }
        return self::$blacklistedTokenRepo->isTokenBlacklisted($token);
    }

    public function expireToken($token): void
    {
        if (self::$blacklistedTokenRepo === null) {
            self::$blacklistedTokenRepo = new BlacklistedTokenRepository();
        }
        self::$blacklistedTokenRepo->addTokenToBlacklist($token);
    }

    // Método estático para generar el token JWT
    public static function generateToken($userEmail, $userName): string
    {
        // Los datos del usuario que se incluirán en el token
        $userData = [
            'email' => $userEmail,
            'name' => $userName
        ];

        // La clave secreta que se usará para firmar el token
        $key = self::secretKey();

        // Crear el token con los datos del usuario y la clave secreta
        return self::createToken($key, $userData);
    }
    
    // Crear el token JWT
    final public static function createToken(string $key, array $data): string
    {
        $time = time();
        $token = array(
            "iat" => $time,  // Fecha de emisión
            "exp" => $time + 3600,  // Fecha de expiración (1 hora después)
            "data" => $data  // Datos del usuario (email, nombre, etc.)
        );

        return self::encode($token, $key, 'HS256');
    }

    public static function validateJWTToken($token)
    {
        try {
            if (self::isTokenBlacklisted($token)) {
                return false;
            }

            $key = self::secretKey();
            $payload = self::decode($token);

            if (isset($payload['exp']) && time() > $payload['exp']) {
                return false;
            }

            if (!isset($payload['data']['email']) || !isset($payload['data']['name'])) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // ... (rest of the methods remain the same)

    public static function decode(string $jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $header = json_decode(self::base64UrlDecode($base64Header), true);
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);

        $signature = self::base64UrlDecode($base64Signature);
        $validSignature = hash_hmac('sha256', "$base64Header.$base64Payload", self::secretKey(), true);

        if (!hash_equals($validSignature, $signature)) {
            return false;
        }

        if (self::isTokenBlacklisted($jwt)) {
            return false;
        }

        if (isset($payload['exp']) && time() > $payload['exp']) {
            return false;
        }

        return $payload;
    }

    // ... (rest of the methods remain the same)

    // Método privado para codificar el JWT
    private static function encode(array $payload, string $key, string $alg = 'HS256'): string
    {
        // Cabecera: se codifica en formato JSON y luego en base64
        $header = json_encode(['typ' => 'JWT', 'alg' => $alg]);
        $base64Header = self::base64UrlEncode($header);

        // Carga (payload): también se codifica en formato JSON y luego en base64
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        // Firma: se crea con el algoritmo seleccionado (por defecto HS256)
        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $key, true);
        $base64Signature = self::base64UrlEncode($signature);

        // Retorna el JWT completo: header, payload y signature
        return "$base64Header.$base64Payload.$base64Signature";
    }

    // Método para encriptar la contraseña
    public function encryptPassword(string $password): string
    {
        // Encriptamos la contraseña con bcrypt
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 5]);
    }

    // Método para validar la contraseña
    public function validatePassword(string $inputPassword, string $databasePassword): bool
    {
        // Verificamos si la contraseña ingresada coincide con la contraseña encriptada
        return password_verify($inputPassword, $databasePassword);
    }

    // Obtener la clave secreta
    public static function secretKey(): string
    {
        // Obtiene la clave secreta desde una variable de entorno
        if (!isset($_ENV['SECRET_KEY'])) {
            throw new Exception('La clave secreta no está configurada en el entorno.');
        }

        return $_ENV['SECRET_KEY'];  // Retorna la clave secreta
    }

    // Método privado para codificar en base64url
    private static function base64UrlEncode(string $data): string
    {
        // Codifica el dato en base64, luego lo convierte a formato URL
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Método privado para decodificar base64url
    private static function base64UrlDecode(string $data): string
    {
        // Decodifica la cadena base64url al formato base64 estándar y luego lo decodifica
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
