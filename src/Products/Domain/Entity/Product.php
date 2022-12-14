<?php

namespace App\Products\Domain\Entity;


class Product
{

    private int $id;
    private string $name;
    private string $description;
    private int $weight;
    private string $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $weightExploded = explode(' ', $weight);

        $this->weight = $weightExploded[1] == 'g' ? floatval($weightExploded[0]) : floatval($weightExploded[0]) * 1000;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }
}
