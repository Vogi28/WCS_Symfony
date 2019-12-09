<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="add", methods="GET|POST")
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
            
            return $this->redirectToRoute('category_add');
        }
            
        return $this->render('category/form.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/delete/{id<[0-9]+>}", name="del")
     */
    public function delete(Category $category, EntityManagerInterface $em)
    {
        $em->remove($category);
        $em->flush();
        $em->getConnection()->exec('ALTER TABLE category AUTO_INCREMENT = 1');

        return $this->redirectToRoute('category_add');
    }

    /**
     *
     * @Route("/edit/{id<[0-9]+>}", name="edit", methods="GET")
     */
    public function edit(Request $request, Category $category, EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        
        $categories = new Category();
        $form = $this->createForm(
            CategoryType::class, 
            $categories, 
            ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        $categories = $categoryRepository->findAll();

                
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            
            $category->setName($data->getName());
            
            $em->flush();
            
            return $this->redirectToRoute('category_add');
        }
            
        return $this->render('category/form.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route("/{categoryName<[a-zA-z0-9\-\s]+>?}", name="show")
    */
    public function category(CategoryRepository $categoryRepository, ProgramRepository $programRepository, ?string $categoryName)
   {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneByName($categoryName);
    
        if(!$category)
        {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in category\'s table.'
            );
        }
        
        $program = $programRepository->findBy(array('category' => $category->getId()),
                                    array('id' => 'desc'), 3);
        
        return $this->render('category/category.html.twig', [
            'programs' => $program,
            'category'  => $category,
            'categories' => $categories
        ]);
   }

}
