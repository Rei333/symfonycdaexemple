<?php

namespace App\Controller;

use App\Service\ArticleService;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleService $articleService
    )
    {}

    #[Route('/article', name: 'app_article_all')]
    public function showAll(): Response
    {
        try {
            $articles = $this->articleService->getAllArticles();
        } catch (\Exception $th) {
            $articles = null;
        }

        return $this->render('article/articles.html.twig', [
            'articles' => $articles ?? null
        ]);
    }

    #[Route('/article/add', name:'app_article_add')]
    #[IsGranted("ROLE_ADMIN")]
    public function addArticle(Request $request) :Response
    {
        //Objet article (recevoir le résultat du formulaire)
        $article = new Article();
        //Créer un Objet Form
        $form = $this->createForm(ArticleType::class,$article);
        //Récupération du resultat de la requête
        $form->handleRequest($request);

        //Test si le formulaire est soumis
        if($form->isSubmitted()) {
            try {
                $msg = "";
                $type = "";
                if($this->articleService->saveArticle($article)){
                    $msg = "L'article " . $article->getTitle() . " a été ajouté";
                    $type = "success";
                }
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                $type = "danger";
            }
            $this->addFlash($type, $msg);
        }
        

        //Retourner la page html (avec le formulaire)
        return $this->render('article/article_add.html.twig',[
            'formulaire' => $form
        ]);
    }
}
