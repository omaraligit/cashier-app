<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Utils\PrepareResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminController extends AbstractController
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
     * @Route("/admin/products", name="admin_products_list", methods={"GET"})
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->json([
            "products"=>$products
        ],Response::HTTP_OK,[],[]);
    }

    /**
     * @Route("/admin/products", name="admin_products_add", methods={"POST"})
     */
    public function addProducts(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();

        $data = json_decode($request->getContent());

        $product->setName($data->name);
        $product->setCost($data->cost);
        $product->setBarcode($data->barcode);
        $product->setVat($data->vat);

        $errors = $this->validator->validate($product);
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
            ],Response::HTTP_BAD_REQUEST,[],[]);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        return $this->json([
            "product"=>$product
        ],Response::HTTP_CREATED,[],[]);
    }
}
