<?php
namespace Service;
use Repositories\BlacklistedTokenRepository;


class BlacklistedTokenService
{
    private BlacklistedTokenRepository $blacklistedTokenRepository;

    public function __construct()
    {
        $this->blacklistedTokenRepository = new BlacklistedTokenRepository();
    }

    // Agregar un token a la lista negra
    public function addTokenToBlacklist(string $token): void
    {
        $this->blacklistedTokenRepository->addTokenToBlacklist($token);
    }

    // Verificar si un token está en la lista negra
    public function isTokenBlacklisted(string $token): bool
    {

        return $this->isTokenBlacklisted($token);  // Retorna true si el token está en la lista negra
    }

    // Opcional: Eliminar tokens expirados de la lista negra
    public function removeExpiredTokens(): void
    {
        $this->blacklistedTokenRepository->removeExpiredTokens();
    }
}
