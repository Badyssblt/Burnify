<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Meal;
use App\Entity\User;
use App\Repository\MealRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MeProvider implements ProviderInterface
{
    public function __construct(private Security $security,

    )
    {
    }


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): User | null
    {

        $user = $this->security->getUser();

        return $user;
    }
}
