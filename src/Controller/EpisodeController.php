<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\CategoryRepository;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     */
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll()

        ]);
    }

    /**
     * @Route("/{id}", name="episode_show", methods={"GET"})
     */
    public function show(Episode $episode, CategoryRepository $categoryRepository): Response
    {
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="episode_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Episode $episode, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Episode $episode, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {

            $entityManager->remove($episode);
            $entityManager->flush();

            $entityManager->getConnection()->exec('ALTER TABLE episode AUTO_INCREMENT = 1');

        }

        return $this->redirectToRoute('episode_index');
    }
}
