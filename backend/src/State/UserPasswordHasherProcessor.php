<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherProcessor implements ProcessorInterface
{

    public function __construct(private UserPasswordHasherInterface $hasher,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
                                private ProcessorInterface $processor)
    {
    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if($data instanceof User){
            $password = $this->hasher->hashPassword($data, $data->getPassword());

            $calories = $this->calculateCalories($data);
            $data->setPassword($password);
            $data->setCaloryPerDay($calories);
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }


    /**
     * Calcul du nombre de calories nécessaires par jour pour être en déficit calorique.
     *
     * @param User $user
     * @return float
     */
    private function calculateCalories(User $user): float
    {
        $weight = $user->getWeight();
        $height = $user->getHeight();
        $age = $user->getAge();
        $sex = $user->getSex();
        $activityLevel = $user->getActivityLevel();

        if ($sex == "male") {
            $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }

        // Multiplication du BMR par le facteur d'activité
        switch ($activityLevel) {
            case 1:
                $tdee = $bmr * 1.2; // Sédentaire
                break;
            case 2:
                $tdee = $bmr * 1.375; // Activité légère
                break;
            case 3:
                $tdee = $bmr * 1.55; // Activité modérée
                break;
            case 4:
                $tdee = $bmr * 1.725; // Activité intense
                break;
            case 5:
                $tdee = $bmr * 1.9; // Activité très intense
                break;
            default:
                $tdee = $bmr * 1.2; // Sédentaire par défaut
        }

        $calories = $tdee * 0.85;

        return round($calories);
    }
}