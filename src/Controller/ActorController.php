<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Program;
use App\Form\ActorType;
use App\Form\ProgramType;
use App\Repository\ActorRepository;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/actor")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="actor_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, ActorRepository $actorRepository): Response
    {
        
        return $this->render('actor/index.html.twig', [
            'actors' => $actorRepository->findAll(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="actor_new", methods={"GET","POST"})
     */
    public function new(CategoryRepository $categoryRepository, Request $request): Response
    {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actor);
            $entityManager->flush();

            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/new.html.twig', [
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="actor_show", methods={"GET"})
     */
    public function show(CategoryRepository $categoryRepository, Actor $actor): Response
    {
        
        return $this->render('actor/show.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'actor' => $actor
        ]);
    }

    /**
     * @Route("/{id}/edit", name="actor_edit", methods={"GET","POST"})
     */
    public function edit(CategoryRepository $categoryRepository, Request $request, Actor $actor): Response
    {
        
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/edit.html.twig', [
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll(),
            'actor' => $actor
        ]);
    }

    /**
     * @Route("/{id}", name="actor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Actor $actor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actor->getId(), $request->request->get('_token'))) {
            $entityManager->remove($actor);
            $entityManager->flush();
            $entityManager->getConnection()->exec('ALTER TABLE actor AUTO_INCREMENT = 1');
        }

        return $this->redirectToRoute('actor_index');
    }
}
