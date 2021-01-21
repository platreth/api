<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hateoas\Configuration\Annotation as Hateoas;
use http\Exception\InvalidArgumentException;
use http\Exception\UnexpectedValueException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;


/**
 *
 *
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of users by page and by bearer",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"user"}))
     *        )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="number",
     *     description="Number of page"
     * )
     * @SWG\Tag(name="user")
     * @Security(name="api_key")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, SerializerInterface $serializer, UserRepository $userRepository)
    {
        $actualUser = $this->getUser();
        $actualUser_id = $actualUser->getId();

        $page = $request->query->get('page');
        if(is_null($page) || $page < 1) {
            $page = 1;
        }
        $limit = 10;

        $users = $userRepository->findAllUsers($page, $limit, $actualUser_id);
        
        

        $data = $serializer->serialize($users, 'json', [
            'groups' => ['list']
        ]);

        $decode = json_decode($data);
        foreach ($decode as $result) {
            $result->self = '/api/user';
            $result->show = '/api/user/' . $result->id;
        }
        $data = json_encode($decode);



        return new JsonResponse($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/users/{id}", name="show_user", methods={"GET"}, requirements={"id":"\d+"})
     * * @SWG\Response(
     *     response=200,
     *     description="Return a user by id",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"user"}))
     *        )
     * )
     * @SWG\Tag(name="user")
     * @Security(name="api_key")
     * @param User $user
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(User $user, UserRepository $userRepository, SerializerInterface $serializer)
    {

        $actualUser = $this->getUser();
        $actualUser_id = $actualUser->getId();


        $user = $userRepository->find($user->getId());
        if ($user->getClient()->getId() != $actualUser_id) {
            return new JsonResponse('Unauthorized content', Response::HTTP_UNAUTHORIZED, [
                'Content-Type' => 'application/json'
            ]);
        }
        $data = $serializer->serialize($user, 'json', [
            'groups' => ['show']
        ]);

        $decode = json_decode($data);
        $decode->self = '/api/user/' . $decode->id;
        $decode->update = '/api/user/' . $decode->id;
        $decode->delete = '/api/user/' . $decode->id;

        $data = json_encode($decode);

        return new JsonResponse($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Create a new user",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"user"}))
     *        )
     * )
     * @SWG\Parameter(
     *     name="user",
     *     in="query",
     *     type="string",
     *     description="The field  used to create user"
     * )
     * @SWG\Tag(name="user")
     * @Security(name="api_key")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse|Response
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        $actualUser = $this->getUser();
        $actualUser_id = $actualUser->getId();


        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($actualUser);
        $errors = $validator->validate($user);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new JsonResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR, [
                'Content-Type' => 'application/json'
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'L\'utilisateur  a bien été ajouté',
            'show' => '/api/users/' . $user->getId()
        ];

        return new JsonResponse($data, Response::HTTP_CREATED);
    }


    /**
     * @Route("/users/{id}", name="update_user", methods={"PUT"})
     *      * @SWG\Response(
     *     response=200,
     *     description="edit a user",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"user"}))
     *        )
     * )
     * @SWG\Parameter(
     *     name="user",
     *     in="query",
     *     type="string",
     *     description="The field  used to create user"
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param User $user
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse|Response
     */
    public function update(Request $request, SerializerInterface $serializer, User $userUpdate, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $actualUser = $this->getUser();
        $actualUser_id = $actualUser->getId();

        // $userUpdate = $entityManager->getRepository(User::class)->find($id);
        // if (is_null($userUpdate)) {
        //     throw new NotFoundHttpException("ressource not found");
        // }

        if ($userUpdate->getClient()->getId() != $actualUser_id) {
            return new JsonResponse('Unauthorized content', Response::HTTP_UNAUTHORIZED, [
                'Content-Type' => 'application/json'
            ]);
        }

        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $userUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($userUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new JsonResponse($errors, Response::HTTP_INTERNAL_SERVER_ERROR, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'L\'utilisateur a bien été mis à jour',
            'show' => '/api/users/' . $userUpdate->getId()
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
     *  * @SWG\Response(
     *     response=204,
     *     description="Delete a user",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"user"}))
     *        )
     * )
     * @SWG\Parameter(
     *     name="user id",
     *     in="query",
     *     type="string",
     *     description="The field with the id of the user"
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(User $userDelete, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $actualUser = $this->getUser();
        $actualUser_id = $actualUser->getId();
        $errors = $validator->validate($userDelete);

        
        if ($userDelete->getClient()->getId() != $actualUser_id) {
            return new JsonResponse('Unauthorized content', Response::HTTP_UNAUTHORIZED, [
                'Content-Type' => 'application/json'
            ]);
        }

        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, Response::HTTP_INTERNAL_SERVER_ERROR, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->remove($userDelete);
        $entityManager->flush();


        return new Response("", Response::HTTP_NO_CONTENT);
    }
}
