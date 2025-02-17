<?php
    namespace Services;
    use Repositories\ProductRepository;
    use Models\Product;

    class ProductService{
        private ProductRepository $productRepository;

        public function __construct() {
            $this->productRepository = new ProductRepository();
        }

        public function getAll(){
            return $this->productRepository->getAll();
        }

        public function getById(int $id): array {
                return $this->productRepository->getById($id);
        }

        public function getStockById(int $id): int{
                return $this->productRepository->getStockById($id);
        }

        public function updateProduct($id, $productData){
            return $this->productRepository->updateProduct($id, $productData);
        }
        public function getCategories(){
            return $this->productRepository->getCategories();
        }
        public function updateStock($id, $quantity): bool{
            return $this->productRepository->updateStock($id, $quantity);
        }

        public function save(Product $product) : bool{
            $isSave = $this->productRepository->save($product);
            return $isSave;
        }

        
        public function deleteById($id) : bool{
            return $this->productRepository->deleteById($id);
        }
        
    }