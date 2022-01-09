<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Serializable;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable
 * @UniqueEntity(fields={"email"}, message="Vous avez déjà un compte !")
 */
class User implements UserInterface,PasswordAuthenticatedUserInterface, Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="4", minMessage="Le mot de passe doit faire au moins 4 caractère")
     */
    private $password;

    public $confirm_password;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="pictures", fileNameProperty="imageName")
     * @Ignore
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token_activation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $token_expiration;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials() {}

    public function getSalt() {}

     /**
      * @ORM\Column(type="json")
      */
    // private $roles = [];
  
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        return ['ROLE_USER'];
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

     /** @see \Serializable::serialize() */
     public function serialize()
     {
         return serialize(array(
             $this->id,
             $this->username,
             $this->password,
             $this->email,
             $this->updatedAt,
             $this->imageName
         ));
     }
 
     /** @see \Serializable::unserialize() */
     public function unserialize($serialized)
     {
         list (
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->updatedAt,
            $this->imageName
         ) = unserialize($serialized);
     }

     public function getTokenActivation(): ?string
     {
         return $this->token_activation;
     }

     public function setTokenActivation(?string $token_activation): self
     {
         $this->token_activation = $token_activation;
         $this->token_expiration = (new DateTime())->modify('+1 day');
         return $this;
     }

     public function resetTokenActivation(): self
     {
         $this->token_activation = null;
         $this->token_expiration = null;
         return $this;
     }

     public function getActive(): ?bool
     {
         return $this->active;
     }

     public function setActive(bool $active): self
     {
         $this->active = $active;

         return $this;
     }

     public function getTokenExpiration(): ?\DateTimeInterface
     {
         return $this->token_expiration;
     }

     public function setTokenExpiration(?\DateTimeInterface $token_expiration): self
     {
         $this->token_expiration = $token_expiration;

         return $this;
     }

     /**
      * @return Collection|Comment[]
      */
     public function getComments(): Collection
     {
         return $this->comments;
     }

     public function addComment(Comment $comment): self
     {
         if (!$this->comments->contains($comment)) {
             $this->comments[] = $comment;
             $comment->setUser($this);
         }

         return $this;
     }

     public function removeComment(Comment $comment): self
     {
         if ($this->comments->removeElement($comment)) {
             // set the owning side to null (unless already changed)
             if ($comment->getUser() === $this) {
                 $comment->setUser(null);
             }
         }

         return $this;
     }

     
}
    
