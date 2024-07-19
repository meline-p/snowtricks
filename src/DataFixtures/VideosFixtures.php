<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class VideosFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $urls = [
            1 => "https://www.youtube.com/watch?v=XKoj-e52w30",
            2 => "https://www.youtube.com/watch?v=gbjwHZDaJLE",
            3 => "https://www.youtube.com/watch?v=lD_kj-sD2dY",
            4 => "https://www.youtube.com/watch?v=6iq3ZkdHxUM",
            5 => "https://www.youtube.com/watch?v=e-7NgSu9SXg",
            6 => "https://www.youtube.com/watch?v=gRZCF5_XRsA",
            7 => "https://www.youtube.com/watch?v=_3C02T-4Uug",
            8 => "https://www.youtube.com/watch?v=hPuVJkw1MmI",
            9 => "https://www.youtube.com/watch?v=_CN_yyEn78M",
            10 => "https://www.youtube.com/watch?v=hgy-Ff2DS6Y",
            11 => "https://www.youtube.com/watch?v=azUFH79x_lY",
            12 => "https://www.youtube.com/watch?v=Z1gCwhmTV7A"
        ];

        for ($i = 1; $i <= 12; $i++) {

            $video = new Video();
            $video->setUrl($urls[$i]);

            // on va chercher une reference de trick
            $trick = $this->getReference('trk-'.$i);
            $video->setTrick($trick);

            $manager->persist($video);
        }

        $manager->flush();
    }
}
