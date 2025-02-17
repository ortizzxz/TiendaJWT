<?php
namespace Models;

class Product {
    private static array $errors = [];

    public function __construct(
        private int | null $id,
        private int $categoria_id,
        private string $nombre,
        private string $descripcion,
        private float $precio,
        private int $stock,
        private float | null $oferta,
        private string | null $fecha,
        private string $imagen
    ) {
    }

    public static function getErrors(): array {
        return self::$errors;
    }

    public static function setErrores(array $errors): void {
        self::$errors = $errors;
    }

    public function validation(): bool {
        self::$errors = [];

        if (empty($this->nombre) || strlen($this->nombre) > 20) {
            self::$errors[] = "El nombre es obligatorio y no debe exceder los 20 caracteres";
        }

        if (empty($this->descripcion) || strlen($this->descripcion) > 100) {
            self::$errors[] = "La descripción es obligatoria y no debe exceder los 100 caracteres";
        }

        if ($this->precio <= 0 || $this->precio > 999999.99) {
            self::$errors[] = "El precio debe ser mayor que 0 y menor que 1,000,000";
        }

        if ($this->stock < 0 || $this->stock > 999999) {
            self::$errors[] = "El stock debe ser un número entre 0 y 999,999";
        }

        if ($this->oferta !== null && ($this->oferta < 0 || $this->oferta > 100)) {
            self::$errors[] = "La oferta debe ser un porcentaje entre 0 y 100";
        }

        if ($this->fecha !== null && !$this->validateDate($this->fecha)) {
            self::$errors[] = "La fecha no es válida. Use el formato YYYY-MM-DD HH:MM:SS";
        }

        if (empty($this->imagen)) {
            self::$errors[] = "La imagen del producto es obligatoria.";
        }

        if (empty(self::$errors)) {
            $this->sanitize();
        }

        return empty(self::$errors);
    }

    private function validateDate($date, $format = 'Y-m-d H:i:s'): bool {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function sanitize($file = null) {
        // Sanitizar cadenas de texto
        $this->nombre = filter_var(trim($this->nombre), FILTER_SANITIZE_STRING);
        $this->descripcion = filter_var(trim($this->descripcion), FILTER_SANITIZE_STRING);
    
        // Asegurarse de que los valores numéricos sean válidos
        $this->precio = filter_var($this->precio, FILTER_VALIDATE_FLOAT) ? $this->precio : 0;
        $this->stock = filter_var($this->stock, FILTER_VALIDATE_INT) ? $this->stock : 0;
        $this->oferta = $this->oferta !== null ? (filter_var($this->oferta, FILTER_VALIDATE_FLOAT) ? $this->oferta : null) : null;
    
        // Sanitizar la fecha si existe
        if ($this->fecha !== null) {
            $sanitizedDate = date('Y-m-d H:i:s', strtotime($this->fecha));
            $this->fecha = $this->validateDate($sanitizedDate) ? $sanitizedDate : null;
        }
    
    }
    

    public static function fromArray(array $data): Product {
        return new Product(
            $data['id'] ?? null,
            (int)($data['categoria_id'] ?? 1),
            $data['nombre'] ?? '',
            $data['descripcion'] ?? '',
            (float)($data['precio'] ?? 0),
            (int)($data['stock'] ?? 0),
            isset($data['oferta']) ? (float)$data['oferta'] : null,
            $data['fecha'] ?? date('Y-m-d H:i:s'),
            $data['imagen'] ?? ''
        );
    }

    public function toArray(): array
{
    return [
        'id' => $this->getId(),
        'categoria_id' => $this->getCategoriaId(),
        'nombre' => $this->getNombre(),
        'descripcion' => $this->getDescripcion(),
        'precio' => $this->getPrecio(),
        'stock' => $this->getStock(),
        'oferta' => $this->getOferta(), 
        'fecha' => $this->getFecha(),
        'imagen' => $this->getImagen(),
    ];
}


    // Getters
    public function getId(): ?int { return $this->id; }
    public function getCategoriaId(): int { return $this->categoria_id; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): string { return $this->descripcion; }
    public function getPrecio(): float { return $this->precio; }
    public function getStock(): int { return $this->stock; }
    public function getOferta(): ?float { return $this->oferta; }
    public function getFecha(): ?string { return $this->fecha; }
    public function getImagen(): string { return $this->imagen; }

    // Setters
    public function setId(int $id): void { $this->id = $id; }
    public function setCategoriaId(int $categoria_id): void { $this->categoria_id = $categoria_id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }
    public function setPrecio(float $precio): void { $this->precio = $precio; }
    public function setStock(int $stock): void { $this->stock = $stock; }
    public function setOferta(?float $oferta): void { $this->oferta = $oferta; }
    public function setFecha(?string $fecha): void { $this->fecha = $fecha; }
    public function setImagen(string $imagen): void { $this->imagen = $imagen; }
}
?>
