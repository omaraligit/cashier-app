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
     * @param PrepareResponse $prepareResponse
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Route("/cashier/product/{barcode}", name="get_product_by_code")
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
     * @Route("/cashier/receipt", name="cashier_receipt_add", methods={"POST"})
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


    /**
     * @Route("/cashier/receipt/{id}/product/{barcode}", name="cashier_receipt_add_product", methods={"POST"})
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

        $receipt->addProduct($product);
        // tell Doctrine you want to (eventually) save the entity (no queries yet)
        $entityManager->persist($receipt);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return $this->json([
            "receipt"=>$receipt,
            "product"=>$product
        ],Response::HTTP_CREATED,[]);
    }

    /**
     * @Route("/cashier/receipt/{id}/finish", name="cashier_receipt_add", methods={"PUT"})
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

}
