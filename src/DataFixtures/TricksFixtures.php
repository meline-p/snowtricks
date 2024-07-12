<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createTrick(
            '360° (trois-six)',
            "Rotation complète de 360 degrés autour de l'axe longitudinal du snowboard.Pour exécuter un 360° sur un snowboard, commencez par prendre de la vitesse et trouvez une section de la piste ou un kick dans le snowpark. Approchez l'obstacle ou le saut avec une légère flexion des genoux pour préparer le pop. Juste avant de décoller, engagez vos épaules dans la direction de la rotation tout en gardant le regard fixé sur votre point de réception. Lorsque vous êtes en l'air, pliez vos jambes pour absorber le terrain et effectuez une rotation complète en faisant glisser votre bras avant pour aider à maintenir l'équilibre. Une fois que vous avez atteint 360 degrés, préparez-vous à atterrir en alignant doucement la planche avec la pente et en absorbant l'impact avec les jambes pour une réception en douceur.",
            4,
            $manager
        );
        $this->createTrick(
            'Tail Grab',
            "Saisie de la partie arrière (tail) du snowboard avec la main pendant un saut. Pour réaliser un Tail Grab, commencez par choisir une petite bosse ou un kick dans le snowpark où vous pouvez obtenir un peu d'air. Prenez de la vitesse et approchez l'obstacle avec confiance. Au moment de décoller, fléchissez vos genoux et tirez légèrement votre planche vers votre corps en pliant votre jambe arrière pour atteindre la partie arrière (tail) du snowboard. Attrapez fermement le tail avec votre main dominante tout en gardant l'équilibre avec l'autre main. Maintenez la prise pendant que vous êtes en l'air, en gardant votre regard sur votre point de réception pour une orientation précise. Pour atterrir, relâchez la prise juste avant l'impact, alignez la planche avec la pente et fléchissez les genoux pour amortir la réception et maintenir le contrôle.",
            4,
            $manager
        );

        $this->createTrick(
            'Powder Turn',
            'Virage fluide et large effectué dans la neige profonde (powder). Pour exécuter un Powder Turn dans la neige profonde, choisissez une pente vierge avec une bonne couche de poudreuse. Prenez de la vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Inclinez votre corps légèrement en amont dans la direction que vous souhaitez tourner, en utilisant le poids de votre corps pour amorcer le virage. Utilisez vos pieds et vos jambes pour glisser en douceur à travers la poudreuse, en ajustant votre angle de virage pour contrôler votre vitesse. Gardez un centre de gravité bas et ajustez votre posture pour naviguer à travers les variations du terrain tout en profitant de la sensation de flottaison offerte par la neige profonde.',
            3,
            $manager
        );
        $this->createTrick(
            'Drop Cliffs',
            "Sauts ou descentes techniques depuis des rochers ou des falaises en terrain hors-piste. Pour réaliser un Drop Cliffs, repérez un cliff ou une falaise avec une bonne réception et un bon dégagement en dessous. Approchez l'obstacle avec vitesse et confiance. Juste avant le saut, fléchissez vos genoux et ajustez votre position pour absorber l'impact à l'atterrissage. Au moment du décollage, engagez-vous légèrement vers l'avant tout en gardant votre regard sur le point de réception. Lorsque vous êtes en l'air, gardez votre corps compact et contrôlé pour maintenir la stabilité. Anticipez l'atterrissage en pliant les genoux pour amortir l'impact, en visant à atterrir avec la planche bien alignée dans la direction de la pente pour une réception en douceur.",
            3,
            $manager
        );

        $this->createTrick(
            '50-50',
            "Glisser droit sur un rail ou une box avec la planche perpendiculaire à l'obstacle. Pour exécuter un 50-50 sur un rail ou une box, approchez l'obstacle avec vitesse et alignez-vous parfaitement avec lui. Fléchissez vos genoux et maintenez une posture stable au-dessus de l'obstacle. Au moment de monter sur le rail ou la box, gardez votre poids centré entre vos pieds pour maintenir l'équilibre. Glissez en ligne droite sur l'obstacle en gardant vos épaules parallèles à la direction de glisse. Utilisez légèrement vos bras pour ajuster l'équilibre tout en gardant vos genoux fléchis pour absorber les imperfections du rail ou de la box. Anticipez la fin de l'obstacle et ajustez votre poids pour descendre proprement de l'autre côté.",
            5,
            $manager
        );
        $this->createTrick(
            'Boardslide',
            "Glisser sur un rail ou une box avec la planche parallèle à l'obstacle. Pour réaliser un Boardslide sur un rail ou une box, approchez l'obstacle avec vitesse et alignez-vous correctement. Au moment de monter sur l'obstacle, orientez votre planche pour qu'elle soit parallèle à l'obstacle, avec votre poids centré entre vos pieds. Engagez le Boardslide en faisant glisser votre planche le long du rail ou de la box tout en gardant vos épaules parallèles à la direction de glisse. Utilisez vos bras pour garder l'équilibre et ajuster la position de votre corps pour maintenir la glisse. Anticipez la fin de l'obstacle et ajustez votre poids pour descendre proprement de l'autre côté.",
            5,
            $manager
        );

        $this->createTrick(
            'Double Cork',
            "Rotation avec deux flips (corkscrew) et plusieurs rotations horizontales. Pour réaliser un Double Cork, commencez par choisir un kick ou un saut avec suffisamment d'élévation pour exécuter une double rotation. Approchez l'obstacle avec vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Au moment de décoller, engagez-vous vers l'avant tout en gardant votre regard sur le point de réception. Lorsque vous êtes en l'air, initiez la première rotation en inclinant votre épaule et votre tête dans la direction de la rotation. Poussez vos pieds et utilisez vos bras pour générer la rotation tout en gardant votre corps compact pour contrôler la vitesse de rotation. Pour la deuxième rotation, répétez le processus en ajustant votre position pour maintenir l'équilibre et terminer la figure avec une réception propre.",
            1,
            $manager
        );
        $this->createTrick(
            'Misty Flip',
            "Backflip avec une rotation horizontale (spin) de 540 ou 720 degrés. Pour réaliser un Misty Flip, trouvez un kick ou un saut avec suffisamment de hauteur pour exécuter une rotation horizontale avec une vrille. Approchez l'obstacle avec vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Au moment de décoller, engagez-vous vers l'avant tout en gardant votre regard sur le point de réception. Lorsque vous êtes en l'air, inclinez votre épaule et votre tête dans la direction de la rotation horizontale tout en initié une vrille. Poussez vos pieds et utilisez vos bras pour générer la rotation tout en gardant votre corps compact pour contrôler la vitesse de rotation. Pour terminer la figure, relâchez progressivement la pression, en alignant la planche avec la pente pour une réception en douceur.",
            1,
            $manager
        );

        $this->createTrick(
            'Backside Air',
            "Saut dans le half-pipe où le rider tourne le dos à la paroi tout en effectuant une rotation. Pour réaliser un Backside Air dans un half-pipe, prenez de la vitesse et approchez la paroi avec confiance. Juste avant de monter sur la lèvre, fléchissez vos genoux et ajustez votre position pour maximiser la hauteur. Lorsque vous montez sur la paroi, tournez votre épaule arrière vers la paroi tout en gardant votre regard fixé sur le point de réception. Initiez le saut en poussant sur vos pieds et en soulevant votre corps pour maximiser la hauteur de l'air. Pendant le vol, maintenez une position compacte et contrôlée pour stabiliser votre rotation. Anticipez l'atterrissage en pliant les genoux pour amortir l'impact et alignez doucement la planche avec la pente pour une réception propre.",
            6,
            $manager
        );
        $this->createTrick(
            'McTwist',
            "Rotation de 540 degrés avec une vrille (twist) sur l'axe longitudinal du snowboard. Pour réaliser un McTwist, prenez de la vitesse et approchez la paroi du half-pipe avec confiance. Juste avant de monter sur la lèvre, fléchissez vos genoux et ajustez votre position pour maximiser la hauteur. Lorsque vous montez sur la paroi, tournez votre épaule arrière vers la paroi tout en gardant votre regard fixé sur le point de réception. Initiez la rotation en inclinant votre tête et vos épaules dans la direction opposée à votre épaule arrière, tout en poussant vos pieds pour générer la rotation. Gardez votre corps compact et contrôlé pour stabiliser la rotation pendant le vol. Anticipez l'atterrissage en pliant les genoux pour amortir l'impact et alignez doucement la planche avec la pente pour une réception en douceur.",
            6,
            $manager
        );

        $this->createTrick(
            'Butter',
            "Rotation du snowboard tout en gardant la planche en contact avec la neige, souvent en effectuant une rotation de 180 ou 360 degrés. Pour réaliser un Butter, commencez par choisir une surface plane ou une petite inclinaison dans le snowpark. Prenez de la vitesse et approchez l'obstacle avec confiance. Au moment du décollage, fléchissez vos genoux et transférez votre poids vers l'avant tout en inclinant légèrement votre épaule du côté où vous souhaitez faire tourner la planche. Engagez le mouvement en appuyant sur le tail ou le nose de la planche avec votre pied arrière ou avant, respectivement, tout en gardant votre équilibre avec l'autre main. Maintenez le mouvement de rotation fluide et contrôlé tout en gardant votre regard sur la direction de déplacement. Pour terminer le Butter, relâchez progressivement la pression et revenez à une position neutre pour maintenir votre équilibre et continuer votre trajet.",
            2,
            $manager
        );
        $this->createTrick(
            'Press',
            "Maintien de la planche dans une position flexion prolongée sur un rail ou une surface plane. Pour réaliser un Press, trouvez un rail ou une surface plane dans le snowpark. Approchez l'obstacle avec vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Au moment de glisser sur l'obstacle, transférez votre poids vers l'avant ou l'arrière de la planche, en fléchissant vos jambes pour maintenir une pression constante sur le rail ou la surface. Utilisez vos bras pour garder l'équilibre tout en ajustant la position de votre corps pour maintenir le Press. Pour terminer, relâchez progressivement la pression et revenez à une position neutre pour terminer la glissade.",
            2,
            $manager
        );

        $manager->flush();
    }

    public function createTrick(string $name, $description, $category, ObjectManager $manager)
    {
        $trick = new Trick();
        $trick->setName($name);
        $trick->setDescription($description);
        $trick->setSlug($this->slugger->slug($name)->lower());

        // on va chercher une reference de catégorie
        $category = $this->getReference('cat-'.$category);
        $trick->setCategory($category);

        // ajouter une référence trick
        $this->addReference('trk-'.$this->counter, $trick);
        $this->counter++;

        $manager->persist($trick);
    }
}
