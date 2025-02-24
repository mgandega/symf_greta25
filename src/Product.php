<?php 
namespace App;
    class Product {
        public const FOOD = 'rice';
        public $prix;
        public function __construct(int $prix) {
            // Initialisation du produit
            $this->prix = $prix;
        }

        public function calculPrix($name): int {
            if (self::FOOD == $name) {
                return $this->prix * 5;
            }else{
                return $this->prix * 2;
            }
        }
    }
?>