<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CategoryController.php',
        ]);
    }

    // GET /api/categories
    // Récupérer la liste des categories
    #[Route('/api/categories', name: 'app_categories_api_list', methods: ['GET'])]
    public function categorieApiList(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $repository = $entityManager->getRepository(Category::class);
    
        $categories = $repository->findAll();

        $jsonCategories = $serializer->serialize($categories, 'json');

        return new JsonResponse($jsonCategories, json: true);
    }

    // GET /api/categories/{id}
    // Récupérer les détails d'une categorie spécifique
    #[Route('/api/categories/{id}', name: 'app_categories_api_get', methods: ['GET'])]
    public function categorieApiGet(int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $repository = $entityManager->getRepository(Category::class);
    
        $category = $repository->findOneBy(['id' => $id]);

        $jsonCategory = $serializer->serialize($category, 'json');

        return new JsonResponse($jsonCategory, json: true);
    }

    // POST /api/categories
    // Créer une nouvelle categorie
    #[Route('/api/categories', name: 'app_categories_api_create', methods: ['POST'])]
    public function categorieApiCreate(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $jsonContent = json_decode($request->getContent(), true);

        $name = $jsonContent['name'];
        $description = $jsonContent['description'];

        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
    
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    // PUT /api/categories/{id}
    // Mettre à jour une categorie existante
    #[Route('/api/categories/{id}', name: 'app_categories_api_update', methods: ['PUT'])]
    public function categorieApiUpdate(int $id, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $jsonContent = json_decode($request->getContent(), true);

        $name = $jsonContent['name'];
        $description = $jsonContent['description'];

        $category = $entityManager->getRepository(Category::class)->find($id);

        $category->setName($name);
        $category->setDescription($description);
    
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    // DELETE /api/categories/{id}
    // Supprimer une categorie existant
    #[Route('/api/categories/{id}', name: 'app_categories_api_delete', methods: ['DELETE'])]
    public function categorieApiDelete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Category::class);

        $category = $repository->find($id);
        $entityManager->remove($category);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'ok'
        ]);
    }
}
