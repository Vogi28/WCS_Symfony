<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/Wild")
 */
class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     * 
     * @Route("/", name="wild_index")
     * @return Response A response instance
     */
    public function index() :Response
    {
        // return $this->render('Wild/index.html.twig', [
        //     'website' => 'Wild Séries'
        // ]);

        $programs = $this->getDoctrine()
          ->getRepository(Program::class)
          ->findAll();

      if (!$programs) {
          throw $this->createNotFoundException(
          'No program found in program\'s table.'
          );
      }

      return $this->render(
              'Wild/index.html.twig',
              ['programs' => $programs]
      );
    }

    /**
   * @Route("/show/{slug<[a-z0-9\-]+>?}", name="wild_show")
   */
   public function show(?string $slug): Response
   {
    
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('Wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
   }

    /**
    * @Route("/category/{categoryName<[a-z0-9\-]+>?}", name="show_category")
    */
   public function category(?string $categoryName)
   {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::Class)
            ->findOneByName($categoryName);
        
        if(!$category)
        {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in category\'s table.'
            );
        }
        
        $program = $this->getDoctrine()->getRepository(Program::Class);
        $program = $program->findBy(array('category' => $category->getId()),
                                    array('id' => 'desc'), 3);
        
        return $this->render('Wild/category.html.twig', [
            'programs' => $program,
            'category'  => $category,
        ]);
   }
}
