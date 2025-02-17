<?php
    namespace Models;
    
    class Category{
        protected static array $errors = [];
        public function __construct(
                private int | null $id, 
                private String $name) {
        }

        public function getId(): int {
            return $this->id;
        }

        public function getName(): String {
            return $this->name;
        }

        public function setName(String $name): void {
            $this->name = $name;
        }

        public static function getErrors() : array{
            return self::$errors;
        }

        public static function setErrores( array $errors) : void{
            self::$errors = $errors;
        }

        public function validation(): bool {
            self::$errors = []; 
        
            if (empty($this->name)) {
                self::$errors['name'] = 'El Nombre es obligatorio';
            }
            //si no hay errores, sanitizar
            if (empty(self::$errors)) {
                $this->sanitize();
            }
        
            return empty(self::$errors);
        }
        
        public function sanitize() {
            $this->name = htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
        }
        

        public static function fromArray(array $data) : Category{
            return new Category(
                id: $data['id'] ?? null,
                name: $data['name']
            );
        }
    }
?>  