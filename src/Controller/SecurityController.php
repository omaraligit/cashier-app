<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\TokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $user->setApiToken(null);

        $entityManager->persist($user);

        $entityManager->flush();

        // controller can be blank: it will never be executed!
        return new JsonResponse(["message"=>"loged out seee you soon"],Response::HTTP_OK);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param TokenAuthenticator $authenticator
     * @return JsonResponse
     * @throws \Exception
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, TokenAuthenticator $authenticator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        if (!isset($data->email) || !isset($data->password)){
            return new JsonResponse(["message"=>"email or password are required","format"=>[
                "email"=>"user@domain.com",
                "password"=>"SuperSecret"
            ]],Response::HTTP_UNAUTHORIZED);

        }
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email'=>$data->email]);
        if($user && $passwordEncoder->isPasswordValid($user,$data->password)){
            dump("hello bro");
            $apiToken = md5($user->getEmail() . time() . random_bytes(1000));
            $user->setApiToken($apiToken);
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse(["message"=>"welcome back here is you apiKey keep it somewhere safe","apiToken"=>$apiToken],Response::HTTP_OK);

        }else{
            return new JsonResponse(["message"=>"email or password are not correct"],Response::HTTP_UNAUTHORIZED);
        }
    }


}
