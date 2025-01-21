<?php

namespace App\Repository;

use App\Entity\Meal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meal>
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }

    /**
     * Récupère les repas créés à une date donnée pour un utilisateur
     *
     * @param object $user L'utilisateur
     * @param \DateTimeInterface $date La date cible
     * @return Meal[] Liste des repas créés à la date donnée
     */
    public function findMealsByDate(object $user, \DateTimeInterface $date): array
    {
        $startOfDay = (new \DateTimeImmutable($date->format('Y-m-d')))->setTime(0, 0, 0);
        $endOfDay = $startOfDay->setTime(23, 59, 59);

        return $this->createQueryBuilder('m')
            ->where('m.user = :user')
            ->andWhere('m.created_at BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getResult();
    }

}
