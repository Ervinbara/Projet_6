<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // Création des catégories //

        $category = new Category();
        $category->setTitle("Backflip");
        $manager->persist($category);

        $category = new Category();
        $category->setTitle("Frontflip");
        $manager->persist($category);

        // Création des figures //

        // Figure 1
        $figure = new Figure();
        $figure->setName('Le straight air');
        $figure->setDescription('Tout comme le 50-50 sur une box ou un rail, la réussite d’un saut dépend du respect de certains principes : bien vous positionner dans l’axe pour le décollage, maintenir votre base bien plate à l’approche du tremplin, et réaliser un ollie au moment où votre planche quitte le bord. Maintenir votre base à plat, vos genoux fléchis et votre torse bien droit constituent des éléments clés pour conserver son équilibre lors d’un saut.');
        $manager->persist($figure);

        // Figure 2
        $figure = new Figure();
        $figure->setName('Le ollie');
        $figure->setDescription('Un ollie est une manière spécifique de « sauter », de décoller du sol verticalement en cours de ride. En général, nous recommandons d’apprendre le ollie en premier, car il s’agit d’une étape cruciale pour l’apprentissage d’autres figures de snowboard. Une fois que vous maîtrisez le ollie, vous pouvez l’utiliser pour les figures sur plat, sur rail et les sauts.');
        $manager->persist($figure);

        // Figure 3
        $figure = new Figure();
        $figure->setName('Le press');
        $figure->setDescription('Un press est l’action d’incliner votre poids sur la spatule (nosepress) ou sur le talon (tailpress) de la planche, de manière à faire décoller l’autre extrémité de la planche. Cette figure est vraiment fun et peut être réalisée n’importe où en montagne, des tremplins et rails jusqu’en plein milieu d’une descente.
                                Variante : Lorsque vous faites légèrement pivoter votre press, de sorte que la planche ne pointe pas directement vers la pente, vous réalisez ce qu’on appelle un « butter », car cela ressemble au mouvement d’un couteau étalant du beurre sur une tartine.');
        $manager->persist($figure);

        // Figure 4
        $figure = new Figure();
        $figure->setName('50-50');
        $figure->setDescription('Un 50-50 est lorsque vous glissez sur une box ou un rail (parfois appelé « jib ») tout en maintenant votre snowboard parallèle au support. La figure de snowboard 50-50 est le moyen parfait de s’habituer à la glisse en snowpark, de tester de nouvelles installations et de s’échauffer. Nous allons décrire les différentes étapes à suivre pour rider sur une box, mais ces conseils s’appliquent également aux rails, aux tubes, aux mailboxes et à la plupart des jibs.');
        $manager->persist($figure);

        // Figure 5
        $figure = new Figure();
        $figure->setName('Le tripod');
        $figure->setDescription('Un tripod est une figure de snowboard très fun qui se réalise sur terrain plat. Il peut sembler compliqué, mais s’apprend en réalité plutôt rapidement. La réalisation du tripod consiste à distribuer une partie de votre poids dans la partie supérieure de votre corps et dans vos bras, tout en l’équilibrant sur la spatule ou le talon de votre planche, selon votre préférence. Nous choisirons ici l’exemple de la spatule.');
        $manager->persist($figure);

        // Figure 6
        $figure = new Figure();
        $figure->setName('Le ride en switch');
        $figure->setDescription('Bien que rider en switch ne soit pas nécessairement une vraie figure de snowboard, c\'est un composant fondamental de bien d\'autres figures qui vaut certainement la peine d\'être appris. Rider en switch consiste simplement à faire du snowboard en descente dans la position opposée à celle que l\'on a l\'habitude d\'adopter : si l\'on ride normalement (pied gauche en avant), alors le ride en switch signifierait effectuer une descente de façon opposée (pied droit en avant). Si vous arrivez à maîtriser le ride en switch, vous serez à l\'aise pour réaliser des sauts et des rails ou pour atterrir en switch.');
        $manager->persist($figure);

        // Figure 7
        $figure = new Figure();
        $figure->setName('Dual Snowboard');
        $figure->setDescription('Assez décrié, le dual snowboard est considéré par beaucoup comme l\'une des inventions les plus inutiles (et ridicules) de ces dernières années dans le monde de la glisse. Concrètement il s\'agit de mini-patinettes spécialement conçues pour les tricks. Voilà voilà.');
        $manager->persist($figure);

        // Figure 8
        $figure = new Figure();
        $figure->setName('Embase');
        $figure->setDescription('C’est le corps de la fixation sur laquelle la boots est posée. Les matériaux utilisés déterminent en grande partie le flex général de l’embase, soit sa capacité à se déformer.');
        $manager->persist($figure);

        // Figure 9
        $figure = new Figure();
        $figure->setName('Fart');
        $figure->setDescription('Le fartage consiste à appliquer (à froid ou à chaud) une paraffine spéciale qui permet de la créer une micro pellicule d\'eau entre la semelle et la neige sur laquelle la planche pourra glisser. Un fartage régulier permet de mieux glisser et d\’entretenir la longévité de la board.');
        $manager->persist($figure);

        // Figure 10
        $figure = new Figure();
        $figure->setName('Grabs');
        $figure->setDescription('Les grabs sont la base des figures freestyle en snowboard. C’est le fait d\’attraper sa planche avec une ou deux mains pendant un saut. On en compte six de base : indy, mute, nose grab, melon, stalefish et tail grab.');
        $manager->persist($figure);

        // Création d'un utilisateur //

        $user = new User();
        $user->setUsername('ADMIN');
        $user->setEmail('admin@example.com');
        $user->setPassword('azerty');
        $hash = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);
        $user->setActive(true);
        $manager->persist($user);

        $manager->flush();
    }
}