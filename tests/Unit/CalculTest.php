<?php

namespace App\Tests\Unit;

use App\Product;
use PHPUnit\Framework\TestCase;

class CalculTest extends TestCase
{
    // public function testCalculProduitEstCorrecte(){
    //     $product = new Product(250);
    //     $this->assertEquals(1250, $product->calculPrix('rice'));
    //     $this->assertEquals(500, $product->calculPrix('mil'));
    //     $this->assertEquals(500, $product->calculPrix('autre foods'));
    // // }
    //  public function testCalculProduitEstCorrecte(){
        
    // }
    //    public function testCalculProduitEstIncorrecte(){
    //     $product = new Product(250);
    //     $this->assertNotEquals(1250, $product->calculPrix('rice'));
    //     $this->assertNotEquals(3200, $product->calculPrix('mil'));
    //     $this->assertNotEquals(700, $product->calculPrix('autre foods'));
    // }



    public static function additionProvider(): array
    {
        return [
            [ 'rice', 250, 1250],
            [ 'mil', 250, 500],
            [ 'autre foods', 250, 500],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testCalculProduitEstCorrecte(string $name, int $prix,int $expected): void
    {
    
        $product = new Product($prix);
        $this->assertEquals($expected, $product->calculPrix($name));
        // $this->assertSame($expected, $product->calculPrix($prix) );
    }
    

}
