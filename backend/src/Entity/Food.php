<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\FoodRepository;
use App\State\FoodPostProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
#[ApiResource]
#[Post(
    security: "is_granted('ROLE_USER')",
    processor: FoodPostProcessor::class
)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'food')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meal $meal = null;

    #[ORM\Column]
    private ?float $calories = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $identifier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): static
    {
        $this->meal = $meal;

        return $this;
    }

    public function getCalories(): ?float
    {
        return $this->calories;
    }

    public function setCalories(float $calories): static
    {
        $this->calories = $calories;

        return $this;
    }


    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }
}
