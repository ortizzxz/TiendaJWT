<?php
namespace Repositories;

use Lib\Database;
use PDO;

class BlacklistedTokenRepository
{
    private Database $db;
    public function __construct()
    {
        $this->db = new Database();

    }

    // Agregar un token a la lista negra
    public function addTokenToBlacklist(string $token): void
    {
        $stmt = $this->db->prepare("INSERT INTO blacklisted_tokens (token) VALUES (:token)");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }

    // Verificar si un token está en la lista negra
    public function isTokenBlacklisted(string $token): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM blacklisted_tokens WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;  // Retorna true si el token está en la lista negra
    }

    // Opcional: Eliminar tokens expirados de la lista negra
    public function removeExpiredTokens(): void
    {
        $stmt = $this->db->prepare("DELETE FROM blacklisted_tokens WHERE created_at < NOW() - INTERVAL 30 DAY");
        $stmt->execute();
    }
}
