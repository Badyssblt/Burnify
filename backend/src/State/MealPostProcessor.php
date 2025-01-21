<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Meal;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MealPostProcessor implements ProcessorInterface
{
    public function __construct(private Security $security,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
                                private ProcessorInterface $processor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if($data instanceof Meal) {
            $user = $this->security->getUser();
            $data->setUser($user);
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
