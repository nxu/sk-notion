<?php

namespace App;

class IngredientGroup
{
    public function __construct(
        private string $title,
        private array $ingredients,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
