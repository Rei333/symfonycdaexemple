<?php

namespace App\Controller;

use App\Entity\Account;
use App\Service\AccountService;
use App\Form\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountController extends AbstractController
{
    public function __construct(
        private readonly AccountService $accountService
    )
    {}

    #[Route('/account', name: 'app_account_all')]
    public function showAll(): Response
    {
        try {
            $accounts = $this->accountService->getAll();
        }
        catch(\Exception $e) {
            $accounts = null;
        }

        return $this->render('account/accounts.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    #[Route('/account/add', name:'app_account_add')]
    public function addAccount(Request $request, UserPasswordHasherInterface $passwordHasher) {
        $acc = new Account();
        $form = $this->createForm(AccountType::class, $acc);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $acc->setRoles(['ROLE_USER']);
            $hashedPassword = $passwordHasher->hashPassword(
                $acc,
                $form->get('password')->getData()
            );
            $acc->setPassword($hashedPassword);
            $message = "";
            $type = "";
            try {
                $this->accountService->saveAccount($acc);
                $message = "Le compte a été ajouté";
                $type = "success";
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $type = "danger";
            }
            $this->addFlash($type, $message);
        }

        return $this->render('account/account_add.html.twig',[
            'formulaire' => $form
        ]);
    }
}