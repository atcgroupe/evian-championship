<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin')]
class AdminController extends AbstractController
{
    private ObjectManager $manager;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private UserRepository $userRepository,
    ) {
        $this->manager = $managerRegistry->getManager();
    }

    #[Route('', name: '_index')]
    public function index()
    {
        $users = $this->userRepository->findAll();

        return $this->render(
            'admin/index.html.twig',
            [
                'users' => $users
            ]
        );
    }
}
