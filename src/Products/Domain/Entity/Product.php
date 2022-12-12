<?php

namespace App\Products\Domain\Entity;


class Product
{

    private int $id;
    private string $name;
    private string $description;
    private string $weight;
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

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function getGWeight(): ?string
    {
        $weightExploded = explode(' ', $this->weight);
        if($weightExploded[1] == 'g') return floatval($weightExploded[0]);
        return floatval($weightExploded[0]) * 1000;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

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
