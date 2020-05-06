<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, SerializerInterface $serializer, UserRepository $userRepository)
    {

        $page = $request->query->get('page');
        if(is_null($page) || $page < 1) {
            $page = 1;
        }
        $limit = 10;

        $users = $userRepository->findAllUsers($page, $limit);

        $data = $serializer->serialize($users, 'json');
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/{id}", name="show_phone", methods={"GET"})
     * @param User $user
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(User $user, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $user = $userRepository->find($user->getId());
        $data = $serializer->serialize($user, 'json');
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
