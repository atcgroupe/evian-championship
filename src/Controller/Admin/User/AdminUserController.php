<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\UserCreateType;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user', name: 'admin_user')]
class AdminUserController extends AbstractController
{
    private ObjectManager $manager;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->manager = $managerRegistry->getManager();
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a été ajouté!");

            return $this->redirectToRoute('admin_index');
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/{id}/update', name: '_update')]
    public function update(int $id, Request $request): Response
    {
        $user = $this->userRepository->find($id);
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $user->getPlainPassword()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
                $user->eraseCredentials();
            }

            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a été modifié!");

            return $this->redirectToRoute('admin_index');
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    #[Route('/{id}/delete', name: '_delete')]
    public function delete(int $id): RedirectResponse
    {
        $user = $this->userRepository->find($id);
        $this->manager->remove($user);
        $this->manager->flush();

        $this->addFlash('success', "L'utilisateur a été supprimé!");

        return $this->redirectToRoute('admin_index');
    }
}
