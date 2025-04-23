<?php

namespace App\Service;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountService
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em
    )
    {}

    public function getAll() :array{
        try {
            $acc = $this->accountRepository->findAll();
            if($this->accountRepository->count() == 0) {
                throw new \Exception("La liste des accounts est vide");
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        //Retourne la liste des catègories
        return $acc;
    }

    public function saveAccount(Account $acc){
        try {
            //Test si elle n'existe pas déja
            if($this->accountRepository->findOneBy(["email"=>$acc->getEmail()])) {
                throw new \Exception("Le compte existe déja");
            }
            //Ajouter en BDD
            $this->em->persist($acc);
            $this->em->flush();
            }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return true;
    }
}