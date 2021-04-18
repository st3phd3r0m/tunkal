<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 */
class Comments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $pseudo;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $is_moderated;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface
     */
    private $sent_at;

    /**
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="comments")
     * @var Posts
     */
    private $post;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsModerated(): ?bool
    {
        return $this->is_moderated;
    }

    public function setIsModerated(bool $is_moderated): self
    {
        $this->is_moderated = $is_moderated;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTimeInterface $sent_at): self
    {
        $this->sent_at = $sent_at;

        return $this;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): self
    {
        $this->post = $post;

        return $this;
    }
}
