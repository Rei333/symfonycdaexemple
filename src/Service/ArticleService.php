<?php

namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly EntityManagerInterface $em
    )
    {}

    public function getAllArticles() :array
    {
        try {
            //Récupération de la liste des articles
            $articles = $this->articleRepository->findAll();
            //Test si la liste est vide
            if($this->articleRepository->count() == 0) {
                throw new \Exception("La liste est vide");
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        //Retourner la liste si non vide
        return $articles;
    }

    //Ajout d'un article en BDD
    public function saveArticle(Article $article) {
        try {
            //Test si l'article n'existe pas déja
            if($this->articleRepository->findOneBy([
                "title"=>$article->getTitle(), 
                "content" =>$article->getContent()
                ])) {
                    throw new \Exception("L'article existe déja");
                }
            $this->em->persist($article);
            $this->em->flush();
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }
}