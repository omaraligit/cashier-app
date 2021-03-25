<?php

namespace App\Controller\CashRegister;

use App\Entity\Product;
use App\Entity\Receipt;
use App\Utils\PrepareResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CashierController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * AdminController constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/cashier/product/{barcode}", name="get_product_by_code")
     * @param int $barcode
     * @return Response
     */
    public function getProductByCode(int $barcode): Response
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['barcode'=>$barcode]);
        if ($product)
            return $this->json([
                "product"=>$product
            ],Response::HTTP_OK,[]);
        return $this->json([],Response::HTTP_NOT_FOUND,[]);
    }

    /**
     * @Route("/cashier/receipt/{id}/finish", name="cashier_receipt_finish", methods={"PUT"})
     * @param $id
     * @return Response
     */
    public function finishReceipt($id): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        /** @var Receipt $receipt */
        $receipt = $this->getDoctrine()->getRepository(Receipt::class)->find($id);

        if (!$receipt)
            return $this->json([],Response::HTTP_NOT_FOUND,[]);

        $receipt->setClosedAt(new \DateTime());

        // tell Doctrine you want to (eventually) save the entity (no queries yet)
        $entityManager->persist($receipt);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return $this->json([
            "receipt"=>$receipt
        ],Response::HTTP_OK,[]);
    }

    /**
     * @Route("/cashier/receipt/{id}/product/{barcode}", name="cashier_receipt_add_product", methods={"POST"})
     * @param $id
     * @param $barcode
     * @return Response
     */
    public function addProductToReceipt($id, $barcode): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Product $receipt */
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['barcode'=>$barcode]);
        /** @var Receipt $receipt */
        $receipt = $this->getDoctrine()->getRepository(Receipt::class)->find($id);

        if (!$product)
            return $this->json([],Response::HTTP_NOT_FOUND,[]);

        if (!$receipt)
            return $this->json([],Response::HTTP_NOT_FOUND,[]);

        /**
         * find if the product is already inside the receipt
         * if so wee update the quantity by 1
         * else wee create the link
         */
        $quantity = $this->getDoctrine()->getRepository(Receipt::class)->findIfProductIsInReceipt($product,$receipt);
        if ($quantity != false){
            // update the quantity +1
            $this->getDoctrine()->getRepository(Receipt::class)->updateQuantityBetweenProductAndReceipt($product,$receipt,($quantity + 1));
        }else{
            $receipt->addProduct($product);
        }
        // tell Doctrine you want to (eventually) save the entity (no queries yet)
        $entityManager->persist($receipt);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->json([
            "receipt"=>$receipt,
            "product"=>$product,
            "quantity"=>$quantity ? $quantity : 1
        ],Response::HTTP_CREATED,[]);
    }

    /**
     * @Route("/cashier/receipt", name="cashier_receipt_add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function newReceiptInit(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $receipt = new Receipt();

        $receipt->setCreatedAt(new \DateTime());

        $errors = $this->validator->validate($receipt);
        if (count($errors) > 0) {
            $res = [];
            foreach ($errors as $error){
                $res[]=[
                    "field"=> $error->getPropertyPath(),
                    "message"=>$error->getMessageTemplate()
                ];
            }
            return $this->json([
                "errors"=>$res
            ],Response::HTTP_BAD_REQUEST,[]);
        }

        // tell Doctrine you want to (eventually) save the entity (no queries yet)
        $entityManager->persist($receipt);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return $this->json([
            "receipt"=>$receipt
        ],Response::HTTP_CREATED,[]);
    }

}
