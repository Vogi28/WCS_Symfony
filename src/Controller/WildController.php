<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramSearchType;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(CategoryRepository $categoryRepository, ProgramRepository $programRepository, Request $request) :Response
    {
        $prog = new Program();
        $formProg = $this->createForm(
            ProgramSearchType::class, 
            $prog, 
            ['method' => Request::METHOD_GET]);
        $formProg->handleRequest($request);
        
        $programs = $programRepository->findAll();

        if (!$programs) {
          throw $this->createNotFoundException(
          'No program found in program\'s table.'
          );
        }

        if ($formProg->isSubmitted()) {
            $data = $formProg->getData();
            $program = $programRepository->findOneByTitle($data->getTitle());
            
            
            return $this->render(
                'Wild/index.html.twig',
                ['programs' => $program,
                'formProg' => $formProg->createView()
              ]
            );
        }
        $category = $categoryRepository->findAll();
        return $this->render(
              'Wild/index.html.twig',
              ['programs' => $programs,
              'categories' => $category,
              'formProg' => $formProg->createView()
            ]
      );
    }

    /**
   * @Route("/show/{slug<[a-zA-Z0-9\-\s]+>?}", name="wild_show")
   */
   public function show(CategoryRepository $categoryRepository, ProgramRepository $programRepository, SeasonRepository $seasonRepository, ?string $slug): Response
   {
    
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/', ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $programRepository->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        $season = $seasonRepository->findByProgram($program->getId());
        $categories = $categoryRepository->findAll();
        return $this->render('Wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
            'seasons' => $season,
            'categories' => $categories
        ]);
   }

   /**
    *
    *@Route("/showBySeason/{id}", name="wild_showBySeason")
    */
    public function showBySeason(CategoryRepository $categoryRepository, EpisodeRepository $episodeRepository, ProgramRepository $programRepository, Season $seasonEntity)
    {
        if(!$seasonEntity)
        {
            throw $this->createNotFoundException(
                'No season '.$seasonEntity->getId().' found in season\'s table.'
            );
        }
        
        $episode = $episodeRepository->findBy(['season' => $seasonEntity->getId()], ['id' => 'asc']);
        $program =$programRepository->findOneById($seasonEntity->getProgram());
        $categories = $categoryRepository->findAll();
        return $this->render('season/season.html.twig', ['season' => $seasonEntity,
        'program' => $program,
        'episodes' => $episode,
        'categories' => $categories]);
    }

   /**
    *@Route("/showByEpisode/{id}", name="wild_showByEpisode")
    * @param Episode $episode
    */
   public function showByEpisode(CategoryRepository $categoryRepository, Episode $episodeEntity, ProgramRepository $programRepository, SeasonRepository $seasonRepository)
   {
       $season = $seasonRepository->findOneById($episodeEntity->getSeason());
       $program = $programRepository->findOneById($season->getProgram());
       $categories = $categoryRepository->findAll();

       return $this->render('episode/episode.html.twig', ['episode' => $episodeEntity,
        'season' => $season,
        'program' => $program,
        'categories' => $categories]);
   }
}
