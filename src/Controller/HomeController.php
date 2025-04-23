<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly WeatherService $weather
    )
    {
    }

    #[Route('/home/weather', name: 'app_home_weather')]
    public function home() :Response{
        dd($this->weather->getWeather());
        return new Response();
    }

    #[Route('/home/{firstname}', name: 'app_home_homefirstname')]
    public function homeFirstname(mixed $firstname) :Response {
        return new Response("Bienvenue ". $firstname);
    }

    #[Route('/addition/{nbr1}/{nbr2}')]
    public function addition(mixed $nbr1, mixed $nbr2) :Response {
        if(!is_numeric($nbr1) || !is_numeric($nbr2)) {
            $reponse = "Ce ne sont pas des nombres";
        }
        else {
            if($nbr1 < 0 || $nbr2 < 0 ) {
                $reponse = "Le nombre sont négatifs";
            }
            else {
                $reponse = "nbr1 " . $nbr1 . " plus nbr2 " . $nbr2 . " est égal à : " .($nbr1+$nbr2);
            }
        }
        return new Response($reponse);
    }

    #[Route('/calcul/{nbr1}/{nbr2}/{operateur}')]
    public function calculatrice(mixed $nbr1, mixed $nbr2, string $operateur) :Response {
        //Test si $nbr1 et $nbr2 sont des nombres
        if(is_numeric($nbr1) && is_numeric($nbr2)) {
            switch($operateur) {
                //Test des cas d'operation
                case 'add':
                    $resultat = "<p>nbr1 + nbr2 est égal à : " . ($nbr1 + $nbr2) . "</p>";
                    break;
                case 'sous':
                    $resultat = "<p>nbr1 - nbr2 est égal à : " . ($nbr1 - $nbr2) . "</p>";
                    break;
                case 'multi':
                    $resultat = "<p>nbr1 x nbr2 est égal à : " . ($nbr1 * $nbr2) . "</p>";
                    break;
                case 'div' :
                    //Test division par 0
                    if($nbr2 == 0) {
                        $resultat = "Division par zéro impossible";
                    }
                    else {
                        $resultat = "<p>nbr1 / nbr2 est égal à : " . ($nbr1 / $nbr2) . "</p>";
                    }
                    break;
                default :
                    $resultat = "Opération impossible";
                    break;
            }
        }
        //Sinon on affiche une erreur
        else {
            $resultat = "nbr1 ou nbr2 ne sont pas des nombres";
        }

        return new Response($resultat);
    }
}
