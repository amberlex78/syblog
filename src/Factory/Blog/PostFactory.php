<?php

namespace App\Factory\Blog;

use App\Entity\Blog\Post;
use App\Repository\Blog\PostRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Post>
 *
 * @method static Post|Proxy createOne(array $attributes = [])
 * @method static Post[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Post|Proxy find(object|array|mixed $criteria)
 * @method static Post|Proxy findOrCreate(array $attributes)
 * @method static Post|Proxy first(string $sortedField = 'id')
 * @method static Post|Proxy last(string $sortedField = 'id')
 * @method static Post|Proxy random(array $attributes = [])
 * @method static Post|Proxy randomOrCreate(array $attributes = [])
 * @method static Post[]|Proxy[] all()
 * @method static Post[]|Proxy[] findBy(array $attributes)
 * @method static Post[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Post[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method Post|Proxy create(array|callable $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        parent::__construct();

        $this->slugger = $slugger;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }

    #[ArrayShape([
        'title' => "string",
        'slug' => "string",
        'preview' => "array|string",
        'content' => "array|string",
        'isActive' => "bool"
    ])] protected function getDefaults(): array
    {
        $text = '';
        for ($i = 0; $i < 5; $i++) {
            $text .= '<p>' . self::faker()->paragraphs(self::faker()->numberBetween(1, 5), true) . '</p>';
        }

        $title = self::faker()->sentence();
        return [
            'title' => $title,
            'slug' => $this->slugger->slug($title)->lower(),
            'preview' => self::faker()->paragraph(),
            'content' => $text,
            'isActive' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Post $post) {})
        ;
    }
}
