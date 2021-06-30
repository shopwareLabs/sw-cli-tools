<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator;

use Faker\Factory;
use Faker\Generator;

class RandomDataProvider
{
    /**
     * Fake data generator
     *
     * @var Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @param string|null $locale
     */
    public function setProviderLocale($locale): void
    {
        if ($locale === null) {
            $this->faker = Factory::create();
        } else {
            $this->faker = Factory::create($locale);
        }
    }

    public function getRandomCity(): string
    {
        return $this->faker->city;
    }

    public function getRandomFirstName(): string
    {
        return $this->faker->firstName;
    }

    public function getRandomLastName(): string
    {
        return $this->faker->lastName;
    }

    public function getRandomWord(): string
    {
        return $this->faker->word;
    }

    /**
     * @param int $wordCount
     */
    public function getSentence($wordCount): string
    {
        return $this->faker->sentence(\max($wordCount, 1));
    }

    public function getRandomIpv4(): string
    {
        return $this->faker->ipv4;
    }
}
