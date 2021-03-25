<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 3/25/2021
 * Time: 10:25 PM
 */

namespace App\Tests;


use App\Entity\Product;
use Monolog\Test\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductVatCalculationTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testVatOfProductCalculation()
    {
        $product = new Product();
        $product->setVat(Product::VAT_21);
        $product->setBarcode(111);
        $product->setCost(10);
        $product->setName("Product X");

        $this->assertEquals(12.10,$product->calculatePriceWithVat());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testSaveProductToDatabaseAndFindByName()
    {
        $product = new Product();

        $product->setVat(Product::VAT_21);
        $product->setBarcode(111);
        $product->setCost(10);
        $product->setName("Product X");

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        /** @var Product $product */
        $product = $this->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'Product X'])
        ;

        $this->assertSame(10, $product->getCost());

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

}