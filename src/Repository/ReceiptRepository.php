<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Receipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Receipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Receipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Receipt[]    findAll()
 * @method Receipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receipt::class);
    }


    /**
     * @param Product $product
     * @param Receipt $receipt
     * @return mixed
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findIfProductIsInReceipt(Product $product, Receipt $receipt)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT quantity
            FROM receipt_product rp
            WHERE rp.receipt_id = :receipt_id 
            AND   rp.product_id = :product_id
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'receipt_id' => $product->getId(),
            'product_id' => $receipt->getId()
        ]);
        // returns an array of Product objects
        return $stmt->fetchOne();
    }

    /**
     * @param Product $product
     * @param Receipt $receipt
     * @param int $quantity
     * @return bool
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateQuantityBetweenProductAndReceipt(Product $product, Receipt $receipt, int $quantity){
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            update receipt_product rp set quantity = :quantity
            WHERE rp.receipt_id = :receipt_id 
            AND   rp.product_id = :product_id
            ';
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            'quantity'   => $quantity,
            'receipt_id' => $product->getId(),
            'product_id' => $receipt->getId()
        ]);
    }

}
