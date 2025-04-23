<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Account;
use App\Service\RegisterService;
use App\Form\RegisterType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;

final class RegisterController extends AbstractController
{
    public function __construct(
        private readonly RegisterService $registerService,
        private readonly Recaptcha3Validator $recaptcha3
    )
    {}


    #[Route('/register/add', name: 'app_register_add')]
    public function index(Request $request): Response
    {
        $user = new Account();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        //Tester si le formulaire est submit et valide
        if($form->isSubmitted() && $form->isValid()) {
            try {
                //test l'utilisateur est un bot
                if ($this->recaptcha3->getLastResponse()->getScore() < 0.5) {
                    $msg = "L'utilisateur est un bot";
                    $type = "danger";
                }
                //Sinon on ajoute le compte
                else if($this->registerService->addUser($user)) {
                    $msg = "Le compte " . $user->getEmail() . " a été ajouté";
                    $type = "success";
                }
            } catch (\Exception $e) {
                $msg = "Erreur " . $e->getMessage();
                $type = "danger";
            }
            $this->addFlash($type, $msg);
        }

        return $this->render('register/user_add.html.twig', [
            'form' => $form
        ]);
    }
}
