<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="wild_add", methods="GET|POST")
     */
    public function add(Request $request, CategoryRepository $categoryRepository)
    {

        $category = new Category();
        $form = $this->createForm(
            CategoryType::class, 
            $category, 
            ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);
        
        $categories = $categoryRepository->findAll();
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            
            return $this->redirectToRoute('wild_index');
        }
            
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

}
