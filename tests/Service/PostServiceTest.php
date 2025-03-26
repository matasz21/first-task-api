<?php

namespace App\Tests\Service;

use App\DTO\PostDTO;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\PaginationService;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationList;

class PostServiceTest extends TestCase
{
    private PostService $postService;
    private MockObject&PostRepository $postRepository;
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&ValidatorInterface $validator;
    private MockObject&Security $security;
    private MockObject&PaginationService $paginationService;

    protected function setUp(): void
    {
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->paginationService = $this->createMock(PaginationService::class);

        $this->postService = new PostService(
            $this->postRepository,
            $this->entityManager,
            $this->security,
            $this->validator,
            $this->paginationService
        );
    }

    public function testCreatePostSuccess()
    {
        $author = 'user123';
        $data = ['title' => 'Test Title', 'content' => 'Test Content'];

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $post = $this->postService->createPost($data, $author);

        $this->assertInstanceOf(PostDTO::class, $post);
        $this->assertEquals('Test Title', $post->title);
        $this->assertEquals('Test Content', $post->content);
        $this->assertEquals('user123', $post->author);
    }

    public function testCreatePostFailsWhenMissingData()
    {
        $this->expectException(BadRequestHttpException::class);
        $this->postService->createPost([], 'user123');
    }

    public function testGetPostSuccess()
    {
        $post = new Post();

        $result = $this->postService->getPost($post);

        $this->assertInstanceOf(PostDTO::class, $result);
    }
}
