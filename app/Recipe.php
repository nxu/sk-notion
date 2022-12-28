<?php

namespace App;

use Illuminate\Support\Arr;

class Recipe
{
    private string $title;

    private ?string $headerImage;

    private array $ingredients = [];

    private array $paragraphs;

    private ?string $icon = null;

    private ?string $portionSize = null;

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

    public function toNotionJson(): array
    {
        $data = [
            'parent' => [
                'type' => 'page_id',
                'page_id' => env('NOTION_PARENT_PAGE'),
            ],
            'cover' => [
                'external' => [
                    'url' => $this->getHeaderImage(),
                ],
            ],
            'properties' => [
                'title' => [
                    'title' => [
                        [
                            'type' => 'text',
                            'text' => [
                                'content' => $this->getTitle(),
                            ],
                        ],
                    ],
                ]],
            'children' => [
                [
                    'object' => 'block',
                    'type' => 'heading_2',
                    'heading_2' => [
                        'rich_text' => [
                            [
                                'type' => 'text',
                                'text' => [
                                    'content' => 'Hozzávalók',
                                ],
                            ],
                        ],
                    ],
                ],
                ...Arr::flatten(array_map(fn (IngredientGroup $ingredientGroup) => array_filter([
                    $ingredientGroup->getTitle() ? [
                        'object' => 'block',
                        'type' => 'heading_3',
                        'heading_3' => [
                            'rich_text' => [
                                [
                                    'type' => 'text',
                                    'text' => [
                                        'content' => $ingredientGroup->getTitle(),
                                    ],
                                ],
                            ],
                        ],
                    ] : null,
                    ...array_map(fn ($item) => [
                        'object' => 'block',
                        'type' => 'bulleted_list_item',
                        'bulleted_list_item' => [
                            'rich_text' => [
                                [
                                    'type' => 'text',
                                    'text' => [
                                        'content' => $item,
                                    ],
                                ],
                            ],
                        ],
                    ], $ingredientGroup->getIngredients()),
                ]), $this->getIngredients()), 1),
                [
                    'object' => 'block',
                    'type' => 'heading_2',
                    'heading_2' => [
                        'rich_text' => [
                            [
                                'type' => 'text',
                                'text' => [
                                    'content' => 'Elkészítés',
                                ],
                            ],
                        ],
                    ],
                ],
                ...array_map(fn ($paragraph) => [
                    'object' => 'block',
                    'type' => 'paragraph',
                    'paragraph' => [
                        'rich_text' => [
                            [
                                'type' => 'text',
                                'text' => [
                                    'content' => $paragraph,
                                ],
                            ],
                        ],
                    ],
                ], $this->getParagraphs()),
            ],
        ];

        if ($this->getIcon()) {
            $data['icon'] = [
                'emoji' => $this->getIcon(),
            ];
        }

        return $data;
    }
}
