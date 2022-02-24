<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\User;
use App\Entity\Videos;
use DateTimeImmutable;
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

        $categoryBf = new Category();
        $categoryBf->setTitle("Backflip");
        $manager->persist($categoryBf);

        $categoryFf = new Category();
        $categoryFf->setTitle("Frontflip");
        $manager->persist($categoryFf);

        // Images et vidéo figure 1
        $imagesStraight = ["straight1.jpg", "straight2.jpg", "straight3.jpg"];
        $videosSraight = ['<iframe width="560" height="315" src="https://www.youtube.com/embed/9-FUTXxvvpQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/Cue7bU3N2NU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/iT1q2Xv2lbY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'];

        // Images et vidéo figure 2
        $imagesOllie = ["ollie1.jpg", "ollie2.jpg"];
        $videosOllie = ['<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"> <iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="https://www.dailymotion.com/embed/video/xqukq2" width="100%" height="100%" allowfullscreen > </iframe> </div>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/QMrelVooJR4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/aAefkzI-zS0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        ];

        // Images et vidéo figure 3
        $imagesPress = ["press1.jpg", "press2.jpg", "press3.jpg"];
        $videosPress = ['<iframe width="560" height="315" src="https://www.youtube.com/embed/LNDVil48oN4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"> <iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="https://www.dailymotion.com/embed/video/x87c9rq" width="100%" height="100%" allowfullscreen > </iframe> </div>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/P72Q5XGMyDo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        ];
        // Création des figures //

        // Images et vidéo figure 4
        $imagesFifty = ["fifty1.jpg", "fifty2.jpg", "fifty3.jpg"];
        $videosFifty = ['<iframe width="560" height="315" src="https://www.youtube.com/embed/zWxBgxq5rP0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/e-7NgSu9SXg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/VAKrFpd2qyo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        ];

        // Images et vidéo figure 5
        $imagesTripod = ["tripod2.jpg", "tripod3.jpg"];
        $videosTripod = ['<iframe width="560" height="315" src="https://www.youtube.com/embed/msD1jQL63dA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/P6crQSwDjJY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/nCFkNIaL7yM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        ];

        // Création des figures //

        // Figure 1
        $figure = new Figure();
        $figure->setName('Straight air');
        $figure->setDescription('Tout comme le 50-50 sur une box ou un rail, la réussite d’un saut dépend du respect de certains principes : bien vous positionner dans l’axe pour le décollage, maintenir votre base bien plate à l’approche du tremplin, et réaliser un ollie au moment où votre planche quitte le bord. Maintenir votre base à plat, vos genoux fléchis et votre torse bien droit constituent des éléments clés pour conserver son équilibre lors d’un saut.');
        $figure->setCategory($categoryBf);
        foreach ($imagesStraight as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosSraight as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 2
        $figure = new Figure();
        $figure->setName('Le ollie');
        $figure->setDescription('Un ollie est une manière spécifique de « sauter », de décoller du sol verticalement en cours de ride. En général, nous recommandons d’apprendre le ollie en premier, car il s’agit d’une étape cruciale pour l’apprentissage d’autres figures de snowboard. Une fois que vous maîtrisez le ollie, vous pouvez l’utiliser pour les figures sur plat, sur rail et les sauts.');
        $figure->setCategory($categoryBf);
        foreach ($imagesOllie as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosOllie as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 3
        $figure = new Figure();
        $figure->setName('Le press');
        $figure->setDescription('Un press est l’action d’incliner votre poids sur la spatule (nosepress) ou sur le talon (tailpress) de la planche, de manière à faire décoller l’autre extrémité de la planche. Cette figure est vraiment fun et peut être réalisée n’importe où en montagne, des tremplins et rails jusqu’en plein milieu d’une descente.
                                Variante : Lorsque vous faites légèrement pivoter votre press, de sorte que la planche ne pointe pas directement vers la pente, vous réalisez ce qu’on appelle un « butter », car cela ressemble au mouvement d’un couteau étalant du beurre sur une tartine.');
        $figure->setCategory($categoryFf);
        foreach ($imagesPress as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosPress as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 4
        $figure = new Figure();
        $figure->setName('50-50');
        $figure->setDescription('Un 50-50 est lorsque vous glissez sur une box ou un rail (parfois appelé « jib ») tout en maintenant votre snowboard parallèle au support. La figure de snowboard 50-50 est le moyen parfait de s’habituer à la glisse en snowpark, de tester de nouvelles installations et de s’échauffer. Nous allons décrire les différentes étapes à suivre pour rider sur une box, mais ces conseils s’appliquent également aux rails, aux tubes, aux mailboxes et à la plupart des jibs.');
        $figure->setCategory($categoryFf);
        foreach ($imagesFifty as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosFifty as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 5
        $figure = new Figure();
        $figure->setName('Le tripod');
        $figure->setDescription('Un tripod est une figure de snowboard très fun qui se réalise sur terrain plat. Il peut sembler compliqué, mais s’apprend en réalité plutôt rapidement. La réalisation du tripod consiste à distribuer une partie de votre poids dans la partie supérieure de votre corps et dans vos bras, tout en l’équilibrant sur la spatule ou le talon de votre planche, selon votre préférence. Nous choisirons ici l’exemple de la spatule.');
        $figure->setCategory($categoryFf);
        foreach ($imagesTripod as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosTripod as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 6
        $figure = new Figure();
        $figure->setName('Ride-switch');
        $figure->setDescription('Bien que rider en switch ne soit pas nécessairement une vraie figure de snowboard, c\'est un composant fondamental de bien d\'autres figures qui vaut certainement la peine d\'être appris. Rider en switch consiste simplement à faire du snowboard en descente dans la position opposée à celle que l\'on a l\'habitude d\'adopter : si l\'on ride normalement (pied gauche en avant), alors le ride en switch signifierait effectuer une descente de façon opposée (pied droit en avant). Si vous arrivez à maîtriser le ride en switch, vous serez à l\'aise pour réaliser des sauts et des rails ou pour atterrir en switch.');
        $figure->setCategory($categoryBf);
        foreach ($imagesOllie as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosOllie as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 7
        $figure = new Figure();
        $figure->setName('Dual-Snow');
        $figure->setDescription('Assez décrié, le dual snowboard est considéré par beaucoup comme l\'une des inventions les plus inutiles (et ridicules) de ces dernières années dans le monde de la glisse. Concrètement il s\'agit de mini-patinettes spécialement conçues pour les tricks. Voilà voilà.');
        $figure->setCategory($categoryBf);
        foreach ($imagesFifty as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosFifty as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 8
        $figure = new Figure();
        $figure->setName('Embase');
        $figure->setDescription('C’est le corps de la fixation sur laquelle la boots est posée. Les matériaux utilisés déterminent en grande partie le flex général de l’embase, soit sa capacité à se déformer.');
        $figure->setCategory($categoryFf);
        foreach ($imagesOllie as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosOllie as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 9
        $figure = new Figure();
        $figure->setName('Fart');
        $figure->setDescription('Le fartage consiste à appliquer (à froid ou à chaud) une paraffine spéciale qui permet de la créer une micro pellicule d\'eau entre la semelle et la neige sur laquelle la planche pourra glisser. Un fartage régulier permet de mieux glisser et d\’entretenir la longévité de la board.');
        $figure->setCategory($categoryBf);
        foreach ($imagesTripod as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosTripod as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 10
        $figure = new Figure();
        $figure->setName('Grabs');
        $figure->setDescription('Les grabs sont la base des figures freestyle en snowboard. C’est le fait d\’attraper sa planche avec une ou deux mains pendant un saut. On en compte six de base : indy, mute, nose grab, melon, stalefish et tail grab.');
        $figure->setCategory($categoryFf);
        foreach ($imagesOllie as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosOllie as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Figure 11
        $figure = new Figure();
        $figure->setName('Le Salto');
        $figure->setDescription('Un press est l’action d’incliner votre poids sur la spatule (nosepress) ou sur le talon (tailpress) de la planche, de manière à faire décoller l’autre extrémité de la planche. Cette figure est vraiment fun et peut être réalisée n’importe où en montagne, des tremplins et rails jusqu’en plein milieu d’une descente.
                                Variante : Lorsque vous faites légèrement pivoter votre press, de sorte que la planche ne pointe pas directement vers la pente, vous réalisez ce qu’on appelle un « butter », car cela ressemble au mouvement d’un couteau étalant du beurre sur une tartine.');
        $figure->setCategory($categoryBf);
        foreach ($imagesTripod as $image) {
            $img = new Images();
            $img->setName($image);
            $figure->addImage($img);
        }
        foreach ($videosTripod as $video) {
            $vdo = new Videos();
            $vdo->setHtml($video);
            $figure->addVideo($vdo);
        }
        $manager->persist($figure);

        // Création d'un utilisateur //

        $user = new User();
        $user->setUsername('ADMIN');
        $user->setEmail('admin@example.com');
        $user->setImageName('admin.jpg');
        $user->setPassword('azerty');
        $hash = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($hash);
        $user->setActive(true);
        $manager->persist($user);

        $manager->flush();

        $user2 = new User();
        $user2->setUsername('USER');
        $user2->setEmail('user@example.com');
        $user2->setImageName('user.jpg');
        $user2->setPassword('azerty');
        $hash2 = $this->encoder->hashPassword($user2, $user2->getPassword());
        $user2->setPassword($hash2);
        $user2->setActive(true);
        $manager->persist($user2);

        $manager->flush();

        $commentForum = new Comment();
        $commentForum->setContent("Bonjour");
        $commentForum->setCreatedAt(new DateTimeImmutable());
        $commentForum->setUser($user);

        $commentForum2 = new Comment();
        $commentForum2->setContent("Test 1");
        $commentForum2->setCreatedAt(new DateTimeImmutable());
        $commentForum2->setUser($user2);

        $commentForum3 = new Comment();
        $commentForum3->setContent("Test 1");
        $commentForum3->setCreatedAt(new DateTimeImmutable());
        $commentForum3->setUser($user);

        $commentForum3 = new Comment();
        $commentForum3->setContent("Test 1");
        $commentForum3->setCreatedAt(new DateTimeImmutable());
        $commentForum3->setUser($user);

        $commentForum4 = new Comment();
        $commentForum4->setContent("Test 1");
        $commentForum4->setCreatedAt(new DateTimeImmutable());
        $commentForum4->setUser($user2);

        $commentForum5 = new Comment();
        $commentForum5->setContent("Test 1");
        $commentForum5->setCreatedAt(new DateTimeImmutable());
        $commentForum5->setUser($user);

        $commentForum6 = new Comment();
        $commentForum6->setContent("Test 1");
        $commentForum6->setCreatedAt(new DateTimeImmutable());
        $commentForum6->setUser($user2);

        $commentForum7 = new Comment();
        $commentForum7->setContent("Test 1");
        $commentForum7->setCreatedAt(new DateTimeImmutable());
        $commentForum7->setUser($user2);

        $commentForum8 = new Comment();
        $commentForum8->setContent("Test 1");
        $commentForum8->setCreatedAt(new DateTimeImmutable());
        $commentForum8->setUser($user);

        $commentForum9 = new Comment();
        $commentForum9->setContent("Test 1");
        $commentForum9->setCreatedAt(new DateTimeImmutable());
        $commentForum9->setUser($user);

        $commentForum10 = new Comment();
        $commentForum10->setContent("Test 1");
        $commentForum10->setCreatedAt(new DateTimeImmutable());
        $commentForum10->setUser($user);

        $commentForum11 = new Comment();
        $commentForum11->setContent("Test 1");
        $commentForum11->setCreatedAt(new DateTimeImmutable());
        $commentForum11->setUser($user2);

        $manager->persist($commentForum);
        $manager->persist($commentForum2);
        $manager->persist($commentForum3);
        $manager->persist($commentForum4);
        $manager->persist($commentForum5);
        $manager->persist($commentForum6);
        $manager->persist($commentForum7);
        $manager->persist($commentForum8);
        $manager->persist($commentForum9);
        $manager->persist($commentForum10);
        $manager->persist($commentForum11);

        $manager->flush();
    }
}
