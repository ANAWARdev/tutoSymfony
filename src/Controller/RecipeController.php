<?php

namespace App\Controller;
use App\Entity\Recipe; 
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Form\RecipeType;

class RecipeController extends AbstractController
{
    #[Route('/recettes/', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $en): Response
    {
        // dd($repository->findTotalDuration());
        // dd($en->getRepository(Recipe::class));
        $recipes = $repository->findwithDurationLowerThan(10);
        
        // Création d'un nouveau enregistrement
    //     $recipe = new Recipe();
    //     $recipe->setTitle('Barbe à papa')
    //         ->setSlug('barbePapa') // Le slug doit être une chaîne de caractères
    //         ->setContent('Mettez du sucre ....')
    //         ->setDuration(2)
    //         ->setCreatedAt(new \DateTimeImmutable())
    //         ->setUpdatedAt(new \DateTimeImmutable());
        
    //     $en->persist($recipe); // Utilisation de $em au lieu de $en
    //     $en->flush();
        
        
    //     // dd($recipes); // Vous pouvez supprimer cette ligne une fois que vous avez vérifié les données
    //    $recipes[0]->setTitle('Pâtes bolognaise');
    //    $en->flush();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+' ])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response // Injection de RecipeRepository ici
    {
        $recipe = $repository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]
        
        );
    }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.edit')]
public function edit(Recipe $recipe, Request $request, EntityManagerInterface $en)
{
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $en->flush();
        $this->addFlash('success', 'La recette a bien été modifiée');
        return $this->redirectToRoute('recipe.index');
   
     }
    return $this->render('recipe/edit.html.twig', [
    'recipe' => $recipe,
     'form' => $form
     
    ]);    
}
#[Route('/recettes/create', name: 'recipe.create')]
public function create(Request $request, EntityManagerInterface $en)
{
    $recipe = new Recipe();
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $en->persist($recipe);
        $en->flush();
        $this->addFlash('success', 'La recette a bien été créée');
        return $this->redirectToRoute('recipe.index');
   
     }
    return $this->render('recipe/create.html.twig', [
     'form' => $form
     
    ]);    
}

#[Route('/recettes/{id}/delete', name: 'recipe.delete')]
public function remove(Recipe $recipe, EntityManagerInterface $en)
{
    $en->remove($recipe);
    $en->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipe.index');
   
     }
}   