<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    //TODO : Encrypt password
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lastFmApiKey = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lastFmApiSessionKey = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lasFmApiSecret = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lastFmUserName = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Scrobble::class)]
    private Collection $scrobbles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Import::class)]
    private Collection $imports;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WidgetGrid::class)]
    private Collection $widgetGrids;

    #[ORM\ManyToOne]
    private ?Image $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LovedTrack::class)]
    private Collection $lovedTracks;

    public function __construct()
    {
        $this->scrobbles = new ArrayCollection();
        $this->imports = new ArrayCollection();
        $this->widgetGrids = new ArrayCollection();
        $this->lovedTracks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getLastFmApiKey(): ?string
    {
        return $this->lastFmApiKey;
    }

    public function setLastFmApiKey(string $lastFmApiKey): static
    {
        $this->lastFmApiKey = $lastFmApiKey;

        return $this;
    }

    public function getLastFmApiSessionKey(): ?string
    {
        return $this->lastFmApiSessionKey;
    }

    public function setLastFmApiSessionKey(string $lastFmApiSessionKey): static
    {
        $this->lastFmApiSessionKey = $lastFmApiSessionKey;

        return $this;
    }

    public function getLasFmApiSecret(): ?string
    {
        return $this->lasFmApiSecret;
    }

    public function setLasFmApiSecret(string $lasFmApiSecret): static
    {
        $this->lasFmApiSecret = $lasFmApiSecret;

        return $this;
    }

    public function getLastFmUserName(): ?string
    {
        return $this->lastFmUserName;
    }

    public function setLastFmUserName(string $lastFmUserName): static
    {
        $this->lastFmUserName = $lastFmUserName;

        return $this;
    }

    /**
     * @return Collection<int, Scrobble>
     */
    public function getScrobbles(): Collection
    {
        return $this->scrobbles;
    }

    public function addScrobble(Scrobble $scrobble): static
    {
        if (!$this->scrobbles->contains($scrobble)) {
            $this->scrobbles->add($scrobble);
            $scrobble->setUser($this);
        }

        return $this;
    }

    public function removeScrobble(Scrobble $scrobble): static
    {
        if ($this->scrobbles->removeElement($scrobble)) {
            // set the owning side to null (unless already changed)
            if ($scrobble->getUser() === $this) {
                $scrobble->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Import>
     */
    public function getImports(): Collection
    {
        return $this->imports;
    }

    public function addImport(Import $import): static
    {
        if (!$this->imports->contains($import)) {
            $this->imports->add($import);
            $import->setUser($this);
        }

        return $this;
    }

    public function removeImport(Import $import): static
    {
        if ($this->imports->removeElement($import)) {
            // set the owning side to null (unless already changed)
            if ($import->getUser() === $this) {
                $import->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WidgetGrid>
     */
    public function getWidgetGrids(): Collection
    {
        return $this->widgetGrids;
    }

    public function addWidgetGrid(WidgetGrid $widgetGrid): static
    {
        if (!$this->widgetGrids->contains($widgetGrid)) {
            $this->widgetGrids->add($widgetGrid);
            $widgetGrid->setUser($this);
        }

        return $this;
    }

    public function removeWidgetGrid(WidgetGrid $widgetGrid): static
    {
        if ($this->widgetGrids->removeElement($widgetGrid)) {
            // set the owning side to null (unless already changed)
            if ($widgetGrid->getUser() === $this) {
                $widgetGrid->setUser(null);
            }
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, LovedTrack>
     */
    public function getLovedTracks(): Collection
    {
        return $this->lovedTracks;
    }

    public function addLovedTrack(LovedTrack $lovedTrack): static
    {
        if (!$this->lovedTracks->contains($lovedTrack)) {
            $this->lovedTracks->add($lovedTrack);
            $lovedTrack->setUser($this);
        }

        return $this;
    }

    public function removeLovedTrack(LovedTrack $lovedTrack): static
    {
        if ($this->lovedTracks->removeElement($lovedTrack)) {
            // set the owning side to null (unless already changed)
            if ($lovedTrack->getUser() === $this) {
                $lovedTrack->setUser(null);
            }
        }

        return $this;
    }
}
