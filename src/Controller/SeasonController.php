<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/season")
 * */

 class SeasonController extends AbstractController
 {
     /**
      * @Route("/{title<[a-zA-Z0-9\-\s]+>?}", name="season_index")
      */
       public function index(ProgramRepository $programRepository, SeasonRepository $seasonRepository, CategoryRepository $categoryRepository, $title)
       {
        if (!$title) {
            throw $this
                ->createNotFoundException('No title has been sent to find a program in program\'s table.');
        }
        $title = preg_replace(
            '/-/', ' ', ucwords(trim(strip_tags($title)), "-")
        );

        $program = $programRepository->findOneBy(['title' => mb_strtolower($title)]);
    
        return $this->render('season/season.html.twig', [
            'program' => $program,
            'categories' => $categoryRepository->findAll(),
            'seasons' => $seasonRepository->findByProgram($program->getId())
        ]);
       } 

     /**
      * @Route("/{title<[a-zA-Z0-9\-\s]+>?}/new", name="season_new", methods={"GET","POST"})
      */
      public function new(CategoryRepository $categoryRepository, ProgramRepository $programRepository, SeasonRepository $seasonRepository, Request $request, EntityManagerInterface $em, $title): Response
      {
        if (!$title) {
            throw $this
                ->createNotFoundException('No title has been sent to find a program in program\'s table.');
        }
        $title = preg_replace(
            '/-/', ' ', ucwords(trim(strip_tags($title)), "-")
        );

        $program = $programRepository->findOneBy(['title' => mb_strtolower($title)]);

        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);
          
        if($form->isSubmitted() && $form->isValid())
        {
        $em->persist($season);
        $em->flush();

        return $this->redirectToRoute('season_index', ['title' => $program->getTitle()]);
        }

        return $this->render('season/new.html.twig', [
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll(),
            'program' => $program
        ]);
    }

        /**
         * @Route("/{title<[a-zA-Z0-9\-\s]+>?}/{id}", name="season_show")
         */
         public function show(ProgramRepository $programRepository, SeasonRepository $seasonRepository, CategoryRepository $categoryRepository, $title, Season $season, EpisodeRepository $episodeRepository)
         {
            if (!$title) {
                throw $this
                    ->createNotFoundException('No title has been sent to find a program in program\'s table.');
            }
            $title = preg_replace(
                '/-/', ' ', ucwords(trim(strip_tags($title)), "-")
            );
    
            $program = $programRepository->findOneBy(['title' => mb_strtolower($title)]);
            $season = $seasonRepository->findOneById($season->getId());
            return $this->render('season/show.html.twig', [
                'program' => $program,
                'categories' => $categoryRepository->findAll(),
                'season' => $season,
                'episodes' => $episodeRepository->findBySeason($season->getId())
            ]);
         }

         /**
          * @Route("/{title<[a-zA-Z0-9\-\s]+>?}/{id}/delete", name="season_delete", methods={"DELETE"})
          */
        public function delete(Request $request, Season $season, EntityManagerInterface $em, $title): Response
        {
                        
            if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) 
            {
                $em->remove($season);
                $em->flush();
                $em->getConnection()->exec('ALTER TABLE season AUTO_INCREMENT = 1');

            }
            
            return $this->redirectToRoute('season_index', ['title' => $title]);
        }

        /**
     * @Route("/{title<[a-zA-Z0-9\-\s]+>?}/{id}/edit", name="season_edit", methods={"GET","POST"})
     */
    public function edit(CategoryRepository $categoryRepository, Request $request, Season $season, $title): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('season_show', ['title' => $title,
            'id' => $season->getId()]);
        }

        return $this->render('season/edit.html.twig', [
            // 'program' => $program,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll()
        ]);
    }
 }