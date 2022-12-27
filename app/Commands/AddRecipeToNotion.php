<?php

namespace App\Commands;

use App\IngredientGroup;
use App\Recipe;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AddRecipeToNotion extends Command
{
    protected $signature = 'save
                            {url : StreetKitchen.hu recipe URL}
                            {--icon= : The icon of the recipe}';

    protected $description = 'Saves a StreetKitchen.hu recipe to Notion';

    public function handle()
    {
        $content = file_get_contents($this->argument('url'));

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($content);
        libxml_use_internal_errors(false);

        // Build reciep
        $recipe = $this->buildRecipe($doc);
        dd($recipe);

    }

    private function buildRecipe(DOMDocument $document)
    {
        $xpath = new DOMXPath($document);
        $recipe = new Recipe();

        $recipe->setTitle($this->getTitle($xpath));
        $recipe->setHeaderImage($this->getHeaderImage($xpath));
        $recipe->setPortionSize($this->getPortionSize($xpath));
        $recipe->addIngredients($this->getIngredients($xpath));
        $recipe->setParagraphs($this->getContent($xpath));

        if ($icon = $this->option('icon')) {
            $recipe->setIcon($icon);
        }

        return $recipe;
    }

    private function getTitle(DOMXPath $xpath)
    {
        $title = $xpath->query("//h1[contains(@class, 'entry-title')]");
        $title = $title->item(0)->nodeValue;
        return str_replace(' | Street Kitchen', '', $title);
    }

    private function getHeaderImage(DOMXPath $xpath)
    {
        $img = $xpath->query("//meta[@property='og:image']");
        return $img->item(0)->getAttribute('content');
    }

    private function getPortionSize(DOMXPath $xpath)
    {
        $portions = $xpath->query("//div[contains(@class, 'quantity-box')]");
        $portions = $portions->item(0)->nodeValue;
        return $this->cleanup($portions);
    }

    private function getIngredients(DOMXPath $xpath)
    {
        $groups = $xpath->query("//div[contains(@class, 'sticky-content-left')]/div[contains(@class, 'ingredients')]/div[contains(@class, 'ingredients-content')]/div[contains(@class, 'ingredient-groups')]/div[contains(@class, 'ingredient-group')]");
        $ingredients = [];

        foreach ($groups as $group) {
            $title = $this->cleanup($group->getElementsByTagName('h3')[0]->nodeValue);
            $dds = $group->getElementsByTagName('dd');
            $items = [];

            foreach ($dds as $dd) {
                $items[] = $this->cleanup($dd->nodeValue);
            }

            $ingredients[] = new IngredientGroup(
                title: $title,
                ingredients: $items,
            );
        }

        return $ingredients;
    }

    private function getContent(DOMXPath $xpath)
    {
        $div = $xpath->query("//div[contains(@class, 'the-content-div')]")[0];

        $content = [];

        foreach ($div->childNodes as $child) {
            if (in_array($child->nodeName, ['p'])) {
                $content[] = $this->cleanup($child->nodeValue);
            }
        }

        return $content;
    }

    private function cleanup(?string $string): string
    {
        $string = strip_tags($string ?? '');
        $string = str_replace(["\r", "\n"], '', $string);
        return preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $string);
    }
}
