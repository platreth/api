<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


/**
 * @Route("/api")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of products by page and by bearer",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=Product::class, groups={"product"}))
     *        )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="number",
     *     description="Number of page"
     * )
     * @SWG\Tag(name="product")
     * @Security(name="api_key")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(Request $request, SerializerInterface $serializer, ProductRepository $productRepository)
    {
        $page = $request->query->get('page');
        if(is_null($page) || $page < 1) {
            $page = 1;
        }
        $limit = 10;

        $products = $productRepository->findAllProducts($page, $limit);

        $data = $serializer->serialize($products, 'json', [
            'groups' => ['list']
        ]);

        $decode = json_decode($data);
        foreach ($decode as $result) {
            $result->self = '/api/product';
            $result->show = '/api/product/' . $result->id;
        }
        $data = json_encode($decode);

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/product/{id}", name="show_product", methods={"GET"})
     * * @SWG\Response(
     *     response=200,
     *     description="Return a product by id",
     *        @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=Product::class, groups={"product"}))
     *        )
     * )
     * @SWG\Tag(name="product")
     * @Security(name="api_key")
     * @param Product $product
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(Product $product, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $product = $productRepository->find($product->getId());

        $data = $serializer->serialize($product, 'json', [
            'groups' => ['show']
        ]);
        $decode = json_decode($data);
        $decode->self = '/api/product/' . $decode->id;
        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }
}
