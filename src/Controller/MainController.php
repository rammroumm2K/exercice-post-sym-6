<?php

namespace App\Controller;

use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    

    
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'title' => 'Playstation Post Blogger',
            'homepage_text'=> "Welcome",
            
        ]);
    }

    #[Route('/about', name: 'about_me')]
    public function aboutMe(): Response
    {
        return $this->render('main/about.html.twig', [
            'title' => 'About me',
            'homepage_text'=> "Et je parle encore de moi !",
        ]);
    }

     // création de l'url pour le détail d'une section
     #[Route(
        # chemin vers la section avec son id
        path: '/section/{id}',
        # nom du chemin
        name: 'section',
        # accepte l'id au format int positif uniquement
        requirements: ['id' => '\d+'],
        # si absent, donne 1 comme valeur par défaut
        defaults: ['id'=>1])]

    public function section(SectionRepository $sections, int $id): Response
    {
        // récupération de la section
        $section = $sections->find($id);
        return $this->render('main/section.html.twig', [
            'title' => 'Section '.$section->getSectionTitle(),
            'homepage_text'=> $section->getSectionDescription(),
            'section' => $section,
            'sections' => $sections->findAll(),
        ]);
    }
}

