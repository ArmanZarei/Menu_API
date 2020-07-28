<?php

namespace App\Services;

use App\Repository\FoodRepository;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class Menu{
    private $foodRepository;

    private $serializer;

    public function __construct(FoodRepository $foodRepository,SerializerInterface $serializer)
    {
        $this->foodRepository = $foodRepository;
        $this->serializer = $serializer;
    }

    public function list()
    {
        $availableFoods = $this->foodRepository->showAvailableFoods();
        $jsonFoods = $this->serializer->serialize($availableFoods,'json',[
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['ingredients']
        ]);
        return $jsonFoods;
    }
}