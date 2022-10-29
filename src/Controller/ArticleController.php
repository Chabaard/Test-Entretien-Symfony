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

// Route principal avec son appelation
#[Route('/', name: 'app_')]
class ArticleController extends AbstractController
{
    // SLUGGER pour mofidier facilement un slug et lui donner la forme d'un slug
    public function __construct(private SluggerInterface $slugger){}

    // Page d'accueil en method get
    #[Route('', name: 'article', methods: ['get'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        // fichier twig envoyé avec les datas nécessaires
        return $this->render('article/details.html.twig', [
            'article' => '',
            'articles' => $articleRepository->findAll(),
        ]);
    }

    // Page d'ajout  d'article en method get
    #[Route('articles/add', name: 'add_articles', methods: ['get'])]
    public function add(): Response
    {
        // fichier twig envoyé
        return $this->render('article/add.html.twig');
    }

    // Page d'ajout  d'article en method post pour inserer les données
    #[Route('articles/add', name: 'send_add_articles', methods: ['post'])]
    public function sendAdd(Request $request, EntityManagerInterface $manager): Response
    {
        // image
        $m = "download/".$_FILES['image']['name'];

        $request = Request::createFromGlobals();

        // fabrication avant implémentation
        $article = new Article();
        $article->setTitle($request->get('title'));
        $article->setSlug($this->slugger->slug($article->getTitle())->lower());
        $article->setIntroduction($request->get('introduction'));
        $article->setContent($request->get('content'));
        $article->setPhoto($_FILES['image']['name']);

        $manager->persist($article);
        // implémentation
        $manager->flush();
        // enregistrement du fichiers
        move_uploaded_file($_FILES['image']['tmp_name'],$m);
        // Redirection vers la liste des articles
        return $this->redirect('/articles');
    }

    // Page de récupérations des articles en method get
    #[Route('articles', name: 'list_articles', methods: ['get'])]
    public function getAll(ArticleRepository $articleRepository): Response
    {
        // fichier twig envoyé avec les datas nécessaires
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    // Page de récupérations d'un article en method get grace a son slug
    #[Route('article/{slug}', name: 'details_articles', methods: ['get'])]
    public function getOne(Article $article, ArticleRepository $articleRepository): Response
    {
        // recuperation des articles tiers pour pouvoir les afficher à coté
        $articles = $articleRepository->findAll();
        // filtrage des articles pour ne pas afficher l'article principal dans la liste des secondaires
        $articles = array_filter($articles, static function($art) use($article){
            return $art !== $article;
        });
        // transformations des articles pour limiter le nombre de mots et ajouter ...
        foreach($articles as $art){
            $art->setIntroduction(substr($art->getIntroduction(), 0, 120).' ...');
            $art->setTitle(substr($art->getTitle(), 0, 20).' ...');
        };

        // fichier twig envoyé avec les datas nécessaires
        return $this->render('article/details.html.twig', compact('article', 'articles'));
    }

    // Route de suppression d'un article en method delete grace a son slug
    #[Route('article/delete/{slug}', name: 'delete_articles', methods: ['delete'])]
    public function delete(ManagerRegistry $doctrine, Article $article): Response
    {
        // je verifie qu'il y a bien un article existant
        if (!$article) {
            throw $this->createNotFoundException(
                'No article found '
            );
        }
        // je vérifie que le fichier existe dans le dossier
        If (file_exists('/download'.$article->getPhoto())){
            unlink($article->getPhoto());
        }
        // recuperation du manager et suppression de la data
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        //redirection vers la liste des articles
        return $this->redirect('/articles');
    }

    // Page de modification d'un article en method get grace a son slug
    #[Route('article/update/{slug}', name: 'page_update_articles', methods: ['get'])]
    public function pageUpdate(Article $article): Response
    {
        // fichier twig envoyé avec les datas nécessaires
        return $this->render('article/update.html.twig', compact('article'));
    }

    // Route de modification d'un article en method update grace a son slug
    #[Route('article/update/{slug}', name: 'update_articles', methods: ['update'])]
    public function update(ManagerRegistry $doctrine, Article $article): Response
    {
        // je verifie qu'il y a bien un article existant
        if (!$article) {
            throw $this->createNotFoundException(
                'No Article found '
            );
        }
        $request = Request::createFromGlobals();

        //modification des datas
        $article->setIntroduction($request->get('introduction'));
        $article->setContent($request->get('content'));

        // implémentations des datas
        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        //redirection vers la liste des articles
        return $this->redirect('/articles');
    }
}
