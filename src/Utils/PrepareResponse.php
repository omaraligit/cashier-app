<?php


namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PrepareResponse
 * @package AppBundle\Util
 */
class PrepareResponse
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PrepareResponse constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $receivedData
     * @param int $code
     * @param array $message
     * @param string $status
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function prepareResponseApis($receivedData, int $code, array $message = [], string $status = null)
    {
        $response = new Response();
        $data = [
            "code" => $code,
            "status" => $status
        ];

        if (!empty($message)) {
            $data = array_merge($data, array("message" => $message["content"]));
        }

        $context = new SerializationContext();
        $groups[] = 'Default';
        $context->setGroups($groups);

        $data = array_merge($data, array("data" => $receivedData));

        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        // Serialize your object in Json
        $dataSerialized = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return null;
            }
        ]);
        $response->setContent($dataSerialized);
        $response->setStatusCode($code);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}