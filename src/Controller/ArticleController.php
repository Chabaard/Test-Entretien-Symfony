<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/', name: 'app_')]
class ArticleController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger){}

    #[Route('', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/details.html.twig', [
            'article' => '',
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('articles/add', name: 'add_articles', methods: ['get'])]
    public function add(ArticleRepository $articleRepository): Response
    {

        return $this->render('article/add.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('articles/add', name: 'send_add_articles', methods: ['post'])]
    public function sendAdd(Request $request, EntityManagerInterface $manager): Response
    {
        $m = "download/".$_FILES['image']['name'];

        $request = Request::createFromGlobals();

        $article = new Article();
        $article->setTitle($request->get('title'));
        $article->setSlug($this->slugger->slug($article->getTitle())->lower());
        $article->setIntroduction($request->get('introduction'));
        $article->setContent($request->get('content'));
        $article->setPhoto($_FILES['image']['name']);

        $manager->persist($article);

        $manager->flush();

        move_uploaded_file($_FILES['image']['tmp_name'],$m);
        return $this->redirect('/articles');
    }

    #[Route('articles', name: 'list_articles')]
    public function getAll(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('article/{slug}', name: 'details_articles')]
    public function getOne(Article $article, ArticleRepository $articleRepository): Response
    {
        $article;
        $articles = $articleRepository->findAll();
        $nbChar = 120;
        $articles = array_filter($articles, static function($art) use($article){
            return $art !== $article;
        });
        foreach($articles as $art){
            $art->setIntroduction(substr($art->getIntroduction(), 0, $nbChar).' ...');
            $art->setTitle(substr($art->getTitle(), 0, 20).' ...');
        };


        return $this->render('article/details.html.twig', compact('article', 'articles'));
    }

    #[Route('article/delete/{slug}', name: 'delete_articles', methods: ['delete'])]
    public function delete(ManagerRegistry $doctrine, Article $article): Response
    {
        if (!$article) {
            throw $this->createNotFoundException(
                'No article found '
            );
        }
        If (file_exists('/download'.$article->getPhoto())){
            unlink($article->getPhoto());
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirect('/articles');
    }

    #[Route('article/update/{slug}', name: 'page_update_articles', methods: ['get'])]
    public function pageUpdate(Article $article): Response
    {
        return $this->render('article/update.html.twig', compact('article'));
    }

    #[Route('article/update/{slug}', name: 'update_articles', methods: ['update'])]
    public function update(ManagerRegistry $doctrine, Article $article): Response
    {
        if (!$article) {
            throw $this->createNotFoundException(
                'No Article found '
            );
        }
        $request = Request::createFromGlobals();

        $article->setIntroduction($request->get('introduction'));
        $article->setContent($request->get('content'));

        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        return $this->redirect('/articles');
    }
}
