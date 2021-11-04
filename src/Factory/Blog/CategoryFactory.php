<?php

namespace App\Factory\Blog;

use App\Entity\Blog\Category;
use App\Repository\Blog\CategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Category>
 *
 * @method static Category|Proxy createOne(array $attributes = [])
 * @method static Category[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Category|Proxy find(object|array|mixed $criteria)
 * @method static Category|Proxy findOrCreate(array $attributes)
 * @method static Category|Proxy first(string $sortedField = 'id')
 * @method static Category|Proxy last(string $sortedField = 'id')
 * @method static Category|Proxy random(array $attributes = [])
 * @method static Category|Proxy randomOrCreate(array $attributes = [])
 * @method static Category[]|Proxy[] all()
 * @method static Category[]|Proxy[] findBy(array $attributes)
 * @method static Category[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Category[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CategoryRepository|RepositoryProxy repository()
 * @method Category|Proxy create(array|callable $attributes = [])
 */
final class CategoryFactory extends ModelFactory
{
    protected static function getClass(): string
    {
        return Category::class;
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    protected function getDefaults(): array
    {
        return [
            'name' => ucfirst(self::faker()->word()),
            'description' => self::faker()->text(500),
            'isActive' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Category $category) {})
        ;
    }
}
