<?php
namespace Models;

use DateTime;

class Order
{
    protected static array $errors = [];

    public function __construct(
        private ?int $id,
        private int $userId,
        private string $province,
        private string $locality,
        private string $address,
        private float $cost,
        private string|null $orderState,
        private ?DateTime $date = null,
        private ?DateTime $time = null
    ) {
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getProvince(): string
    {
        return $this->province;
    }
    public function getLocality(): string
    {
        return $this->locality;
    }
    public function getAddress(): string
    {
        return $this->address;
    }
    public function getCost(): float
    {
        return $this->cost;
    }
    public function getOrderState(): string
    {
        return $this->orderState;
    }
    public function getDate(): ?DateTime
    {
        return $this->date;
    }
    public function getTime(): ?DateTime
    {
        return $this->time;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
    public function setProvince(string $province): void
    {
        $this->province = $province;
    }
    public function setLocality(string $locality): void
    {
        $this->locality = $locality;
    }
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }
    public function setOrderState(string $orderState): void
    {
        $this->orderState = $orderState;
    }
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
    public function setTime(?DateTime $time): void
    {
        $this->time = $time;
    }

    public static function getErrors(): array
    {
        return self::$errors;
    }

    public static function setErrors(array $errors): void
    {
        self::$errors = $errors;
    }

    public function validation(): bool
    {
        self::$errors = [];

        if (empty($this->userId)) {
            self::$errors[] = "User ID Es obligatorio";
        }

        if (empty($this->province)) {
            self::$errors[] = "La propvincia es obligatoria.";
        } elseif (strlen($this->province) > 30) {
            self::$errors[] = "La provincia excede el límite de carácteres (max 30).";
        }

        if (empty($this->locality)) {
            self::$errors[] = "La localidad es obligatoria.";
        } elseif (strlen($this->locality) > 100) {
            self::$errors[] = "La localidad excede el límite de carácteres (max 100).";
        }

        if (empty($this->address)) {
            self::$errors[] = "La dirección es obligatoria.";
        } elseif (strlen($this->address) > 255) {
            self::$errors[] = "La dirección excede el límite de carácteres (max 255).";
        }


        if (empty(self::$errors)) {
            $this->sanitize();
        }

        return empty(self::$errors);
    }

    public function sanitize(): void
    {
        $this->province = trim(filter_var($this->province, FILTER_SANITIZE_STRING));
        $this->locality = trim(filter_var($this->locality, FILTER_SANITIZE_STRING));
        $this->address = trim(filter_var($this->address, FILTER_SANITIZE_STRING));
        $this->orderState = trim(filter_var($this->orderState, FILTER_SANITIZE_STRING));
    }

    public static function fromArray(array $data): Order
    {
        $currentDateTime = new DateTime();

        return new Order(
            $data['id'] ?? null,
            $data['userId'],
            $data['provincia'],
            $data['localidad'],
            $data['direccion'],
            $data['cost'],
            $data['orderState'] ?? 'pedido en tramite',
            isset($data['date']) ? new DateTime($data['date']) : $currentDateTime,
            isset($data['time']) ? new DateTime($data['time']) : $currentDateTime
        );
    }

}
