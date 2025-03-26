<?php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\DTO\PostDTO;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PostService
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private ValidatorInterface $validator,
        private PaginationService $paginationService
    ) {}

    public function getPaginatedPosts(int $page, int $limit): PaginationDTO
    {
        $query = $this->postRepository->getPaginatedPostsQuery();
        $pagination = $this->paginationService->paginate($query, $page, $limit);

        return $pagination;
    }

    public function createPost(array $data, string $userIdentifier): PostDTO
    {
        if (!isset($data['title']) || !isset($data['content'])) {
            throw new BadRequestHttpException('Title and content are required.');
        }

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setAuthor($userIdentifier);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return PostDTO::fromEntity($post);
    }

    public function getPost(Post $post): PostDTO
    {
        return PostDTO::fromEntity($post);
    }

    public function updatePost(Post $post, array $data): void
    {
        $this->authorize($post);

        $constraints = new Assert\Collection([
            'title'   => new Assert\Optional([new Assert\NotBlank(), new Assert\Length(['max' => 255])]),
            'content' => new Assert\Optional([new Assert\NotBlank()]),
        ]);

        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            throw new BadRequestHttpException((string) $violations);
        }

        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }

        $this->entityManager->flush();
    }

    public function deletePost(Post $post): void
    {
        $this->authorize($post);
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    private function authorize(Post $post): void
    {
        $user = $this->security->getUser();
        if (!$user || $post->getAuthor() !== $user->getUserIdentifier()) {
            throw new UnauthorizedHttpException('Bearer', 'You are not authorized to modify this post.');
        }
    }
}
