<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Repository\FoodRepository;
use App\State\FoodDeleteProcessor;
use App\State\FoodPostProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
#[ApiResource]
#[Post(
    security: "is_granted('ROLE_USER')",
    processor: FoodPostProcessor::class
)]
#[Delete(
    security: "is_granted('ROLE_USER')",
    processor: FoodDeleteProcessor::class
)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['item:meal:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'food')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meal $meal = null;

    #[ORM\Column]
    #[Groups(['item:meal:read'])]
    private ?float $calories = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['item:meal:read'])]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    #[Groups(['item:meal:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['item:meal:read'])]
    private ?float $weight = null;

    #[ORM\Column(length: 255)]
    #[Groups(['item:meal:read'])]
    private ?string $unit = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
