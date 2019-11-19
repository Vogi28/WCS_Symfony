<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/Wild", name="wild_index")
     */
    public function index() :Response
    {
        return $this->render('Wild/index.html.twig', [
            'website' => 'Wild Séries'
        ]);
    }
}
