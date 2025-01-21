<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Meal;
use App\Repository\MealRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MealGetProvider implements ProviderInterface
{
    public function __construct(private Security $security,
                                private MealRepository $mealRepository,
    )
    {
    }


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Meal | null
    {

        $user = $this->security->getUser();

        $id = $uriVariables['id'];

        $meals = $this->mealRepository->findOneBy(['id' => $id, 'user' => $user]);

        return $meals;
    }
}
