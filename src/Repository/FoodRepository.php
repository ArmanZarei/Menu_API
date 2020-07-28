<?php

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Food|null find($id, $lockMode = null, $lockVersion = null)
 * @method Food|null findOneBy(array $criteria, array $orderBy = null)
 * @method Food[]    findAll()
 * @method Food[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }


    public function showAvailableFoods()
    {
        $now = new \DateTime('now');
        $qb = $this->createQueryBuilder('f');
        $qb2 = $this->createQueryBuilder('food');
        $qb
            ->select('f.id')
            ->join('f.ingredients','i',Join::WITH , $qb->expr()->orX(
                $qb->expr()->eq('i.stock',0),
                $qb->expr()->lt('i.expires_at',':expire_time')
            ));
        $result = $qb2
            ->where($qb2->expr()->notIn('food.id',$qb->getDQL()))
            ->setParameter('expire_time',$now->format('Y-m-d H:i:s'))
            ->getQuery()->getResult();
        return $result;
    }

}