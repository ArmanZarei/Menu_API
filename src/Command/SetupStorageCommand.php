<?php

namespace App\Command;

use App\Entity\Food;
use App\Entity\Ingredient;
use App\Repository\FoodRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupStorageCommand extends Command
{
    protected static $defaultName = 'app:setup-storage';

    private $em;

    private $ingredientRepo;

    private $foodRepo;

    private $resources;

    protected function configure()
    {
        $this
            ->setDescription('Adding foods and ingredients json to database.')
        ;
    }

    public function __construct(EntityManagerInterface $entityManager,IngredientRepository $ingredientRepository,
                                FoodRepository $foodRepository,$resources)
    {
        $this->resources = $resources;
        $this->em = $entityManager;
        $this->ingredientRepo = $ingredientRepository;
        $this->foodRepo = $foodRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->addIngredients();
        $this->addFoods();

        $io->success('All foods and ingredients successfully added to database.');

        return 0;
    }

    private function addIngredients(){
        $ingredients_content = file_get_contents($this->resources.'/ingredients.json');
        $ingredients = json_decode($ingredients_content , true)['ingredients'];

        foreach ($ingredients as $ingredient) {
            // TODO : should do this with Serializer

            if ($exist_ingredient = $this->ingredientRepo->findOneBy(['title' => $ingredient['title']]))
                $this->em->remove($exist_ingredient);

            $ingredientObj = new Ingredient();
            $ingredientObj->setTitle($ingredient['title']);
            $ingredientObj->setBestBefore(new \DateTime($ingredient['best-before']));
            $ingredientObj->setExpiresAt(new \DateTime($ingredient['expires-at']));
            $ingredientObj->setStock($ingredient['stock']);

            $this->em->persist($ingredientObj);
        }
        $this->em->flush();
    }

    private function addFoods(){
        $foods_content = file_get_contents($this->resources.'/foods.json');
        $foods = json_decode($foods_content , true)['recipes'];

        foreach ($foods as $food) {
            // TODO : should do this with Serializer

            if ($exist_food = $this->foodRepo->findOneBy(['title' => $food['title']]))
                $this->em->remove($exist_food);

            $foodObj = new Food();
            $foodObj->setTitle($food['title']);

            foreach ($food['ingredients'] as $name){
                $ingredient = $this->ingredientRepo->findOneBy(['title' => $name]);
                if ($ingredient)
                    $foodObj->addIngredient($ingredient);
            }

            $this->em->persist($foodObj);
        }
        $this->em->flush();
    }
}