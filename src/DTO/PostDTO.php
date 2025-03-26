<?php

namespace App\DTO;

use App\Entity\Post;

class PostDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $title,
        public readonly ?string $content,
        public readonly ?string $author,
        public readonly ?\DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(Post $post): self
    {
        return new self(
            id: $post->getId(),
            title: $post->getTitle(),
            content: $post->getContent(),
            author: $post->getAuthor(),
            createdAt: $post->getCreatedAt(),
            updatedAt: $post->getUpdatedAt()
        );
    }
}
