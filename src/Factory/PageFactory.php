<?php

namespace App\Factory;

use App\Entity\Page;
use App\Repository\PageRepository;
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
    protected static function getClass(): string
    {
        return Page::class;
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    protected function getDefaults(): array
    {
        return [
            'title' => rtrim(self::faker()->sentence(2), '.'),
            'content' => implode(
                "\r\n",
                array_map(
                    fn($paragraph) => '<p>' . $paragraph . '</p>',
                    self::faker()->paragraphs(rand(5, 10))
                )
            ),
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
