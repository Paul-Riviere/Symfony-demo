<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(LoggerInterface $logger): JsonResponse
    {
        $logger->info("test");
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArticleController.php',
        ]);
    }

    // GET /api/articles
    // Récupérer la liste des articles
    #[Route('/api/articles', name: 'app_articles_api_list', methods: ['GET'])]
    public function articleApiList(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $entityManager->getRepository(Article::class);
    
        $articles = $repository->findAll();

        $jsonArticles = $serializer->serialize($articles, 'json');

        return new JsonResponse($jsonArticles, json: true);
    }

    // GET /api/articles/{id}
    // Récupérer les détails d'un article spécifique
    #[Route('/api/articles/{id}', name: 'app_articles_api_get', methods: ['GET'])]
    public function articleApiGet(int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $repository = $entityManager->getRepository(Article::class);
    
        $articles = $repository->findOneBy(['id' => $id]);

        $jsonArticles = $serializer->serialize($articles, 'json');

        return new JsonResponse($jsonArticles, json: true);
    }

    // POST /api/articles
    // Créer un nouvel article
    #[Route('/api/articles', name: 'app_articles_api_create', methods: ['POST'])]
    public function articleApiCreate(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $jsonContent = json_decode($request->getContent(), true);

        $title = $jsonContent['title'];
        $content = $jsonContent['content'];
        $slug = $jsonContent['slug'];
        $categoryId = $jsonContent['categoryId'];
        $authorId = $jsonContent['authorId'];

        $author = $entityManager->getRepository(User::class)->find($authorId);
        $category = $entityManager->getRepository(Category::class)->find($categoryId);

        $article = new Article();
        $article->setTitle($title);
        $article->setContent($content);
        $article->setSlug($slug);
        $article->setCategoryId($category);
        $article->setAuthorId($author);
        $article->setPublishedAt(new DateTime());
        $article->setStatus('draft');
    
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    // PUT /api/articles/{id}
    // Mettre à jour un article existant
    #[Route('/api/articles/{id}', name: 'app_articles_api_update', methods: ['PUT'])]
    public function articleApiUpdate(int $id, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $jsonContent = json_decode($request->getContent(), true);

        $title = $jsonContent['title'];
        $content = $jsonContent['content'];
        $slug = $jsonContent['slug'];
        $categoryId = $jsonContent['categoryId'];
        $authorId = $jsonContent['authorId'];

        $article = $entityManager->getRepository(Article::class)->find($id);
        $author = $entityManager->getRepository(User::class)->find($authorId);
        $category = $entityManager->getRepository(Category::class)->find($categoryId);

        $article->setTitle($title);
        $article->setContent($content);
        $article->setSlug($slug);
        $article->setCategoryId($category);
        $article->setAuthorId($author);
    
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    // DELETE /api/articles/{id}
    // Supprimer un article existant
    #[Route('/api/articles/{id}', name: 'app_articles_api_delete', methods: ['DELETE'])]
    public function articleApiDelete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Article::class);

        $article = $repository->find($id);
        $entityManager->remove($article);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'ok'
        ]);
    }
}
