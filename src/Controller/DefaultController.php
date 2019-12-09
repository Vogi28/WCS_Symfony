<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{    
    /**
     * @Route("/", name="home_index")
     */
    public function index(CategoryRepository $categoryRepository, ProgramRepository $programRepository)
    {
    
        $programs = $programRepository->findAll();
        shuffle($programs);
        
        return $this->render('Wild/home.html.twig',[
        'categories' => $categoryRepository->findAll(),
        'programs' => $programs
        ]);
    }
}