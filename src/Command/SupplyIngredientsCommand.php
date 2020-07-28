<?php

namespace App\Command;

use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SupplyIngredientsCommand extends Command
{
    protected static $defaultName = 'app:supply-ingredients';

    private $em;

    private $ingredientRepo;

    protected function configure()
    {
        $this
            ->setDescription('Supplies ingredients with 0 stock in database')
        ;
    }

    public function __construct(EntityManagerInterface $entityManager,IngredientRepository $ingredientRepository)
    {
        $this->em = $entityManager;
        $this->ingredientRepo = $ingredientRepository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $ingredients = $this->ingredientRepo->findBy(['stock' => 0]);
        foreach ($ingredients as $ingredient)
            $ingredient->supply();
        $this->em->flush();

        $io->success('Success , You have Supplied the ingredients !!!');

        return 0;
    }
}