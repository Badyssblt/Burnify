<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\MealRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MealCollectionProvider implements ProviderInterface
{
    public function __construct(private Security $security,
                                private MealRepository $mealRepository,
                                )
    {
    }


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {

        $user = $this->security->getUser();


        $stringDate = $context['filters']['date'] ?? null;

        $date = isset($stringDate) ? \DateTimeImmutable::createFromFormat('m-d-Y', $stringDate) : new \DateTimeImmutable();

        $meals = $this->mealRepository->findMealsByDate($user, $date);

        $calories = 0;
        $result = [];
        foreach ($meals as $meal) {
            $calories += $meal->getCalories();
            if ($meal !== null) {
                $result[$meal->getType()] = $meal;
            }
        }

        return [$result, $calories];

    }
}
