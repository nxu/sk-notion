<?php

namespace App;

class Recipe
{
    private string $title;
    private ?string $headerImage;
    private array $ingredients = [];
    private array $paragraphs;
    private ?string $icon;
    private ?string $portionSize;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getHeaderImage(): ?string
    {
        return $this->headerImage;
    }

    public function setHeaderImage(?string $headerImage): void
    {
        $this->headerImage = $headerImage;
    }

    /**
     * @return IngredientGroup[]|array
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function addIngredient(IngredientGroup $ingredients): void
    {
        $this->ingredients[] = $ingredients;
    }

    public function addIngredients(array $ingredients)
    {
        foreach ($ingredients as $ingredient) {
            $this->addIngredient($ingredient);
        }
    }


    public function getParagraphs(): array
    {
        return $this->paragraphs;
    }

    public function setParagraphs(array $paragraphs): void
    {
        $this->paragraphs = $paragraphs;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getPortionSize(): ?string
    {
        return $this->portionSize;
    }

    public function setPortionSize(?string $portionSize): void
    {
        $this->portionSize = $portionSize;
    }

}
