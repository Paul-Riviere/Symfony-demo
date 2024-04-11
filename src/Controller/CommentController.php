<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CommentController.php',
        ]);
    }

    // GET /api/comments
    // Récupérer la liste des commentaires
    #[Route('/api/comments', name: 'app_comments_api_list', methods: ['GET'])]
    public function commentApiList(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $repository = $entityManager->getRepository(Comment::class);
    
        $comments = $repository->findAll();

        $jsonComments = $serializer->serialize($comments, 'json');

        return new JsonResponse($jsonComments, json: true);
    }

    // GET /api/comments/{id}
    // Récupérer les détails d'un commentaire spécifique
    #[Route('/api/comments/{id}', name: 'app_comments_api_get', methods: ['GET'])]
    public function commentApiGet(int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $repository = $entityManager->getRepository(Comment::class);
    
        $comment = $repository->findOneBy(['id' => $id]);

        $jsonComment = $serializer->serialize($comment, 'json');

        return new JsonResponse($jsonComment, json: true);
    }

    // POST /api/comments
    // Créer un nouveau commentaires
    #[Route('/api/comments', name: 'app_comments_api_create', methods: ['POST'])]
    public function commentApiCreate(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $jsonContent = json_decode($request->getContent(), true);

        $content = $jsonContent['content'];
        $articleId = $jsonContent['articleId'];
        $authorId = $jsonContent['authorId'];

        $article = $entityManager->getRepository(Article::class)->find($articleId);
        $author = $entityManager->getRepository(User::class)->find($authorId);

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setCreatedAt(new DateTime());
        $comment->setArticleId($article);
        $comment->setAuthorId($author);
    
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    // PUT /api/comments/{id}
    // Mettre à jour un commentaire existant
    #[Route('/api/comments/{id}', name: 'app_comments_api_update', methods: ['PUT'])]
    public function commentApiUpdate(int $id, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $jsonContent = json_decode($request->getContent(), true);

        $content = $jsonContent['content'];
        $articleId = $jsonContent['articleId'];
        $authorId = $jsonContent['authorId'];

        $comment = $entityManager->getRepository(Comment::class)->find($id);
        $article = $entityManager->getRepository(Article::class)->find($articleId);
        $author = $entityManager->getRepository(User::class)->find($authorId);

        $comment->setContent($content);
        $comment->setArticleId($article);
        $comment->setAuthorId($author);
    
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    // DELETE /api/comments/{id}
    // Supprimer un commentaire existant
    #[Route('/api/comments/{id}', name: 'app_comments_api_delete', methods: ['DELETE'])]
    public function commentApiDelete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Comment::class);

        $comment = $repository->find($id);
        $entityManager->remove($comment);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'ok'
        ]);
    }
}
