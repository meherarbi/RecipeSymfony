<?php

namespace App\Entity;

use App\Entity\Recette;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('fullName')]
#[UniqueEntity('pseudo')]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private array $roles = [];

    private ?string $plainPassword = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank()]
    private ?string $password = 'password';


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(
        min: 3,
        max: 30,
    )]
    private ?string $fullName = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 20,
    )]
    private ?string $pseudo = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ingredient::class, orphanRemoval: true)]
    private Collection $Ingredient;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Recette::class, orphanRemoval: true)]
    private Collection $Recette;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Mark::class, orphanRemoval: true)]
    private Collection $marks;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->Ingredient = new ArrayCollection();
        $this->marks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getfullName(): ?string
    {
        return $this->fullName;
    }

    public function setfullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

	/**
	 * @return string|null
	 */
	public function getPlainPassword(): ?string {
                                    		return $this->plainPassword;
                                    	}
	
	/**
	 * @param string|null $plainPassword 
	 * @return self
	 */
	public function setPlainPassword(?string $plainPassword): self {
                                    		$this->plainPassword = $plainPassword;
                                    		return $this;
                                    	}

	public function getUpdatedAt(): ?\DateTimeImmutable
                                        {
                                            return $this->updatedAt;
                                        }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getRecette(): Collection
    {
        return $this->Recette;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->Recette->contains($recette)) {
            $this->Recette->add($recette);
            $recette->setUser($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->Recette->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getUser() === $this) {
                $recette->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredient(): Collection
    {
        return $this->Ingredient;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->Ingredient->contains($ingredient)) {
            $this->Ingredient->add($ingredient);
            $ingredient->setUser($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->Ingredient->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getUser() === $this) {
                $ingredient->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Mark>
     */
    public function getMarks(): Collection
    {
        return $this->marks;
    }

    public function addMark(Mark $mark): self
    {
        if (!$this->marks->contains($mark)) {
            $this->marks->add($mark);
            $mark->setUser($this);
        }

        return $this;
    }

    public function removeMark(Mark $mark): self
    {
        if ($this->marks->removeElement($mark)) {
            // set the owning side to null (unless already changed)
            if ($mark->getUser() === $this) {
                $mark->setUser(null);
            }
        }

        return $this;
    }

}
