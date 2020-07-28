<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Ingredient;
use App\Repository\FoodRepository;
use App\Repository\IngredientRepository;
use App\Services\Menu;
use App\Services\Order;
use Doctrine\DBAL\Types\DateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use function PHPSTORM_META\type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    private $em;

    private $ingredientRepo;

    private $foodRepo;

    public function __construct(EntityManagerInterface $entityManager,
                                IngredientRepository $ingredientRepo,FoodRepository $foodRepository)
    {
        $this->em = $entityManager;
        $this->ingredientRepo = $ingredientRepo;
        $this->foodRepo = $foodRepository;
    }

    /**
     * @Route("/menu" , methods={"GET"} , name="menu")
     */
    public function menu(SerializerInterface $serializer, Menu $menu)
    {
        return new Response($menu->list());
    }

    /**
     * @Route("/order/{id<\d+>}" , methods={"POST"} , name="order")
     */
    public function order(Food $food,Order $order)
    {
        try {
            $order->create($food);
        }catch (\Exception $exception){
            return $this->json([
                'error' => true,
                'message' => $exception->getMessage()
            ]);
        }

        return $this->json([
            'success' => true,
            'message' => "Your order ( " . $food->getTitle() . " ) was successfully registered"
        ]);
    }
}