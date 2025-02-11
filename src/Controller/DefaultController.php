<?php

namespace App\Controller;

use App\Service\ChangeMonnaieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    // Page d'accueil
    #[Route(
        path: '/{_locale}',
        name: 'app_default_index',
        requirements: ['_locale' => '%app.supported_locales%'],
        defaults: ['_locale' => 'fr'],
    )]
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    // Page de contact
    #[Route(
        path: '/{_locale}/contact',
        name: 'app_default_contact',
        requirements: ['_locale' => '%app.supported_locales%'],
    )]
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig');
    }
}
