<?php

namespace App\Factory;

use App\Entity\Page;
use App\Repository\PageRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Page>
 *
 * @method static Page|Proxy createOne(array $attributes = [])
 * @method static Page[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Page|Proxy find(object|array|mixed $criteria)
 * @method static Page|Proxy findOrCreate(array $attributes)
 * @method static Page|Proxy first(string $sortedField = 'id')
 * @method static Page|Proxy last(string $sortedField = 'id')
 * @method static Page|Proxy random(array $attributes = [])
 * @method static Page|Proxy randomOrCreate(array $attributes = [])
 * @method static Page[]|Proxy[] all()
 * @method static Page[]|Proxy[] findBy(array $attributes)
 * @method static Page[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Page[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PageRepository|RepositoryProxy repository()
 * @method Page|Proxy create(array|callable $attributes = [])
 */
final class PageFactory extends ModelFactory
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        parent::__construct();

        $this->slugger = $slugger;
    }

    protected static function getClass(): string
    {
        return Page::class;
    }

    #[ArrayShape([
        'title' => "string",
        'slug' => "string",
        'content' => "string",
        'isActive' => "bool"
    ])]
    protected function getDefaults(): array
    {
        $text = '';
        for ($i = 0; $i < 5; $i++) {
            $text .= '<p>' . self::faker()->paragraphs(self::faker()->numberBetween(1, 5), true) . '</p>';
        }

        $title = self::faker()->sentence();
        return [
            'title' => $title,
            'slug' => $this->slugger->slug($title)->lower(),
            'content' => $text,
            'isActive' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Page $page) {})
        ;
    }
}
