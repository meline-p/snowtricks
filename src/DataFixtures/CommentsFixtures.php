<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $comments = [
            "La neige avait l'air parfaite pour ça ! Bravo pour cette descente.",
            "Vraiment stylé ! Tu fais ça tellement naturellement. Tu peux m'apprendre ?",
            "Tellement fluide dans cette poudreuse.",
            "C'est fou comment tu maintiens l'équilibre là-dessus. Respect !",
            "Super technique ! Tu fais ça avec tant de fluidité.",
            "Je suis content d'avoir enfin réussi cette figure.",
            "C'était impressionnant à voir ! Combien de temps ça t'a pris pour maîtriser ça ?",
            "Wow, ça demande tellement de technique et de courage. Tu es un pro !",
            "Boom ! C'est réussi !",
            "Tu as volé haut là-dedans ! Comment tu fais pour atterrir aussi proprement ?",
            "Ça montre vraiment ton niveau de maîtrise."
        ];

        foreach ($comments as $content){
            $comment = new Comment();
            $comment->setContent($content);
    
            // on va chercher une reference de user
            $user = $this->getReference('usr-'.rand(1, 5));
            $comment->setUser($user);
    
            // on va chercher une reference de trick
            $trick = $this->getReference('trk-'.rand(1,12));
            $comment->setTrick($trick);
    
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
            TricksFixtures::class,
        ];
    }
}
