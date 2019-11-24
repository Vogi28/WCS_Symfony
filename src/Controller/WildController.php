<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Wild/")
 */
class WildController extends AbstractController
{
    /**
     * @Route("index", name="wild_index")
     */
    public function index() :Response
    {
        return $this->render('Wild/index.html.twig', [
            'website' => 'Wild Séries'
        ]);
    }

    /**
   * @Route("show/{slug<[a-z0-9\-]+>?}", name="wild_show")
   */
   public function show($slug): Response
   {
        if($slug !== null)
        {
            $slug = str_replace('-', ' ', $slug);
            $slug = ucwords($slug,' ');
        }
        else
        {
            $slug = "Aucune série sélectionnée, veuillez choisir une série";    
        }
        return $this->render('Wild/show.html.twig', ['slug' => $slug]);
   }
}
