<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Program;
use App\Entity\Category;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
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
    public function index(ProgramRepository $programRepository) :Response
    {
        // return $this->render('Wild/index.html.twig', [
        //     'website' => 'Wild Séries'
        // ]);

        $programs = $programRepository->findAll();

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
   * @Route("/show/{slug<[a-zA-Z0-9\-\s]+>?}", name="wild_show")
   */
   public function show(ProgramRepository $programRepository, SeasonRepository $seasonRepository, ?string $slug): Response
   {
    
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $programRepository->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        $season= $seasonRepository->findByProgram($program->getId());
        return $this->render('Wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
            'seasons' => $season,
        ]);
   }

   /**
    *
    *@Route("/showBySeason/{id}", name="wild_showBySeason")
    */
    public function showBySeason(EpisodeRepository $episodeRepository, ProgramRepository $programRepository, SeasonRepository $seasonRepository, int $id)
    {
        if(!$id)
        {
            throw $this->createNotFoundException(
                'No season '.$id.' found in season\'s table.'
            );
        }
        
        $season = $seasonRepository->findOneById($id);
        $episode = $episodeRepository->findBy(['season' => $id], ['id' => 'asc']);
        $program =$programRepository->findBy(['id' => $season->getProgram()]);
        
        return $this->render('Wild/season.html.twig', ['season' => $season,
        'program' => $program,
        'episodes' => $episode]);
    }

    /**
    * @Route("/category/{categoryName<[a-z0-9\-]+>?}", name="show_category")
    */
   public function category(CategoryRepository $CategoryRepository, ProgramRepository $programRepository, ?string $categoryName)
   {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $CategoryRepository->findOneByName($categoryName);
        
        if(!$category)
        {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in category\'s table.'
            );
        }
        
        $program = $programRepository->findBy(array('category' => $category->getId()),
                                    array('id' => 'desc'), 3);
        
        return $this->render('Wild/category.html.twig', [
            'programs' => $program,
            'category'  => $category,
        ]);
   }
}
