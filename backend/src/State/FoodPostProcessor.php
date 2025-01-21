<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Food;
use App\Entity\Meal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FoodPostProcessor implements ProcessorInterface
{
    public function __construct(
                                private EntityManagerInterface $entityManager,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
                                private ProcessorInterface $processor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if($data instanceof Food) {
            $meal = $data->getMeal();
            $meal->addCalories($data->getCalories());
            $this->entityManager->persist($meal);
            $this->entityManager->flush();
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
