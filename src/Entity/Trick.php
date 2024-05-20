<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
class Trick
{
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    private ?Category $category = null;

    #[ORM\OneToOne]
    private ?Image $promoteImage = null;

    #[ORM\OneToMany(targetEntity: UserTrick::class, mappedBy: 'trick')]
    private Collection $userTricks;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'trick', fetch: 'EAGER')]
    private Collection $comments;

    // #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'trick', fetch: 'EAGER')]
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'trick', fetch: 'EAGER', cascade: ['persist'])]
    private Collection $images;

    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'trick', fetch: 'EAGER', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $videos;

    public function __construct()
    {
        $this->userTricks = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getPromoteImage(): ?Image
    {
        if ($this->promoteImage) {
            return $this->promoteImage;
        }
        if (count($this->images) > 0) {
            return $this->images[0];
        }

        return new Image();
    }

    public function setPromoteImage(?Image $promoteImage): static
    {
        $this->promoteImage = $promoteImage;

        return $this;
    }

    /**
     * @return Collection<int, UserTrick>
     */
    public function getUserTricks(): Collection
    {
        return $this->userTricks;
    }

    public function addUserTrick(UserTrick $userTrick): static
    {
        if (!$this->userTricks->contains($userTrick)) {
            $this->userTricks->add($userTrick);
            $userTrick->setTrick($this);
        }

        return $this;
    }

    public function removeUserTrick(UserTrick $userTrick): static
    {
        if ($this->userTricks->removeElement($userTrick)) {
            // set the owning side to null (unless already changed)
            if ($userTrick->getTrick() === $this) {
                $userTrick->setTrick(null);
            }
        }

        return $this;
    }

    public function isDeleted(): bool
    {
        foreach ($this->userTricks as $userTrick) {
            if ('delete' === $userTrick->getOperation()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setTrick($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }
}
