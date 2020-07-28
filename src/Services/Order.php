<?php

namespace App\Services;

use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;

class Order{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function create(Food $food)
    {
        if (!$food->isFoodValid())
            throw new \Exception("The food you ordered is invalid . One ( or some ) of the ingredients are out of stock or expired .");
        $food->decreaseIngredients();
        $this->em->persist($food);
        $this->em->flush();
    }
}