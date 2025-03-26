<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/post', name: 'api_post_')]
final class PostController extends AbstractController
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max((int) $request->query->get('page', 1), 1);
        $limit = max((int) $request->query->get('limit', 10), 1);

        return $this->json($this->postService->getPaginatedPosts($page, $limit));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User must be authenticated.'], 401);
        }

        $post = $this->postService->createPost($request->toArray(), $user->getUserIdentifier());

        return $this->json([
            'message' => 'Post created!',
            'post' => $post
        ], 201);
    }

    #[Route('/{id}', name: 'single', methods: ['GET'])]
    public function single(Post $post): JsonResponse
    {
        return $this->json($this->postService->getPost($post));
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Post $post): JsonResponse
    {
        $this->postService->updatePost($post, $request->toArray());
        return $this->json(['message' => 'Post updated successfully!']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Post $post): JsonResponse
    {
        $this->postService->deletePost($post);
        return $this->json(['message' => 'Post deleted!'], 204);
    }
}
