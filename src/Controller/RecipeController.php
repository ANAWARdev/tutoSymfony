<?php

namespace App\Controller;
use App\Entity\Recipe; 
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recettes/', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $en): Response
    {
        $recipes = $repository->findwithDurationLowerThan(10);

        $recipe = new Recipe();
        $recipe->setTitle('Barbe à papa')
            ->setSlug('barbePapa') // Le slug doit être une chaîne de caractères
            ->setContent('Mettez du sucre ....')
            ->setDuration(2)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        
        $en->persist($recipe); // Utilisation de $em au lieu de $en
        $en->flush();
        
        
        // dd($recipes); // Vous pouvez supprimer cette ligne une fois que vous avez vérifié les données
       $recipes[0]->setTitle('Pâtes bolognaise');
       $en->flush();
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
}