<?php

declare(strict_types=1);

namespace App\Entity\Fixture;

use App\Entity\Podcast;
use App\Entity\PodcastCategory;
use App\Entity\Station;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PodcastFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $station = $this->getReference('station', Station::class);

        $podcastStorage = $station->getPodcastsStorageLocation();

        $podcast = new Podcast($podcastStorage);

        $podcast->setTitle('The AplusTest Podcast');
        $podcast->setLink('https://demo.example.com');
        $podcast->setLanguage('en');
        $podcast->setDescription('The unofficial testing podcast for the Aplus development team.');
        $podcast->setAuthor('Aplus');
        $podcast->setEmail('demo@example.com');
        $manager->persist($podcast);

        $category = new PodcastCategory($podcast, 'Technology');
        $manager->persist($category);

        $manager->flush();

        $this->setReference('podcast', $podcast);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            StationFixture::class,
        ];
    }
}
