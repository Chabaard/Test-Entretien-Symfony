<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixture extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $articles = new Article();
        $articles->setTitle('Lorem Ipsum');
        $articles->setSlug($this->slugger->slug($articles->getTitle())->lower());
        $articles->setIntroduction('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec eros justo, pharetra a erat a, rutrum ullamcorper libero. Ut suscipit mauris augue vel.');
        $articles->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi non dictum lacus. Donec rutrum sapien ac lacus posuere, vitae dapibus ipsum facilisis. Vestibulum vel sodales massa. Vestibulum condimentum, tortor vel porttitor lacinia, velit sapien porttitor ex, convallis feugiat sapien libero eget ligula. Nullam porta tempor pulvinar. Etiam maximus libero sed metus varius, ac aliquet ex bibendum. Nam metus enim, efficitur sit amet fringilla eu, dapibus eget orci. Donec sapien ante, eleifend fringilla orci a, gravida tristique neque. Aenean eget ex finibus, pulvinar erat sit amet, laoreet velit. Fusce eget odio metus. Ut pharetra molestie turpis, vitae feugiat elit congue in. Donec sed leo augue.

        Fusce aliquet mauris blandit ipsum venenatis pharetra. Integer sed placerat justo. Donec nec elementum urna. Integer mauris sem, varius vehicula ultrices non, ullamcorper ullamcorper dolor. Integer volutpat metus lectus, at lacinia leo semper quis. Quisque tempus vel est sed imperdiet. Nunc purus sapien, facilisis nec finibus sed, consectetur vitae velit. In hac habitasse platea dictumst. Integer pharetra bibendum tristique. Suspendisse odio tellus, auctor vitae eros eu, euismod ultrices orci. Duis a leo quis arcu condimentum bibendum.

        Vestibulum eu congue nulla. Ut et malesuada justo. Morbi et lorem porta, semper mauris eu, faucibus lacus. Phasellus nec sem nec odio fermentum laoreet nec in turpis. In et ultrices purus, a hendrerit dolor. Donec cursus ipsum id tortor dignissim ornare. Nulla cursus risus sed neque ultrices suscipit. Sed fermentum quam non purus euismod consectetur. Duis suscipit, diam vitae convallis hendrerit, mi massa feugiat quam, ut elementum sem nisi in sapien. Praesent a mattis dolor. Pellentesque vel enim vel tellus rutrum ullamcorper. Nunc scelerisque purus sapien, at ornare lectus hendrerit eleifend. Mauris porta eros in sem aliquam consequat. Donec ac sapien mi. Fusce commodo varius mollis. Curabitur orci est, placerat at pharetra at, placerat vitae mauris.

        Etiam eros metus, imperdiet quis facilisis et, commodo et ex. Nullam ac velit at mi pretium varius. Curabitur est arcu, consequat a lectus vitae, fringilla pulvinar enim. Mauris dignissim quis risus et porta. Sed vitae risus dictum, placerat dui id, vehicula leo. Pellentesque tempus feugiat nisl in sollicitudin. Vestibulum iaculis massa nec lacus cursus, sit amet malesuada lorem laoreet. Suspendisse vel feugiat turpis. Cras eleifend felis lorem. Sed pulvinar arcu et erat luctus finibus. Praesent tristique, nibh non consequat vehicula, elit ipsum mollis lectus, quis ultricies est velit sit amet tortor.

        Phasellus vitae elit dui. Vestibulum mauris augue, tempor mollis justo vel, suscipit consectetur nulla. Fusce laoreet tincidunt nulla. Proin pulvinar sit amet arcu nec lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum vitae sem consectetur, rutrum ligula eget, posuere risus. Vivamus et accumsan tellus. Pellentesque ultricies mauris eu pulvinar bibendum. Pellentesque tristique, magna eleifend mollis porta, nulla magna hendrerit massa, condimentum maximus turpis ante in orci. Sed sit amet leo at elit consequat tristique et iaculis neque. Sed porta odio ut eleifend pharetra. Sed sed ultricies nulla.');
        $articles->setPhoto('paysage0.png');

        $manager->persist($articles);

        for ($i = 1; $i < 7; $i++){
            $article = new Article();
            $article->setTitle($articles->getTitle() . $i);
            $article->setSlug($this->slugger->slug($article->getTitle())->lower());
            $article->setIntroduction($articles->getIntroduction());
            $article->setContent($articles->getContent());
            $article->setPhoto('paysage'. $i .'.png');

            $manager->persist($article);
        }


        $manager->flush();
    }
}
