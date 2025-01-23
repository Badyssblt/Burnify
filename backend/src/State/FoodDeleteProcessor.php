<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Food;
use App\Entity\Meal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FoodDeleteProcessor implements ProcessorInterface
{
    public function __construct(private Security $security,
                                private EntityManagerInterface $entityManager,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
                                private ProcessorInterface $processor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null | Response
    {
        if($data instanceof Food) {
            $user = $this->security->getUser();

            $meal = $data->getMeal();

            if($user !== $meal->getUser()) {
                return new JsonResponse(['message' => 'Accès interdit'], Response::HTTP_UNAUTHORIZED);
            }

            $meal->removeCalories($data->getCalories());

            if($meal->getCalories() < 0) {
                $meal->setCalories(0);
            }

            $this->entityManager->remove($data);
            $this->entityManager->persist($meal);
            $this->entityManager->flush();
        }

        return null;
    }
}
