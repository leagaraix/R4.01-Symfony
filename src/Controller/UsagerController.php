<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Form\UsagerType;
use App\Repository\UsagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path : '/usager', requirements: ['_locale' => '%app.supported_locales%'])]
final class UsagerController extends AbstractController
{
    #[Route(name: 'app_usager_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('usager/index.html.twig', [
            'usager' => $this->getUser(),
        ]);
    }

    #[Route('/new', name: 'app_usager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usager = new Usager();
        $form = $this->createForm(UsagerType::class, $usager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encoder le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($usager, $usager->getPassword());

            $usager->setPassword($hashedPassword);

            // Définir le rôle de l'usager qui va être créé
            $usager->setRoles(["ROLE_CLIENT"]);

            $entityManager->persist($usager);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('usager/new.html.twig', [
            'usager' => $usager,
            'form' => $form,
        ]);
    }
}
