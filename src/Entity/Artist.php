<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{

  const LIMIT_TOP_ARTIST = 6;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $mbid = null;

  #[ORM\Column(length: 1020)]
  private ?string $name = null;

  #[ORM\OneToMany(mappedBy: 'idArtist', targetEntity: Album::class)]
  private Collection $albums;

  #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Track::class)]
  private Collection $tracks;

  #[ORM\ManyToMany(targetEntity: Image::class)]
  private Collection $image;

  #[ORM\Column(length: 1020, nullable: true)]
  private ?string $url = null;

  #[ORM\Column(type: Types::BIGINT, nullable: true)]
  private ?string $listeners = null;

  #[ORM\Column(type: Types::BIGINT, nullable: true)]
  private ?string $playcount = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $bioSummary = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $bioContent = null;

  private ?string $userPlaycount = null;

  public function __construct()
  {
    $this->albums = new ArrayCollection();
    $this->tracks = new ArrayCollection();
    $this->image = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getMbid(): ?string
  {
    return $this->mbid;
  }

  public function setMbid(string $mbid): static
  {
    $this->mbid = $mbid;

    return $this;
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

  /**
   * @return Collection<int, Album>
   */
  public function getAlbums(): Collection
  {
    return $this->albums;
  }

  public function addAlbum(Album $album): static
  {
    if (!$this->albums->contains($album)) {
      $this->albums->add($album);
      $album->setArtist($this);
    }

    return $this;
  }

  public function removeAlbum(Album $album): static
  {
    if ($this->albums->removeElement($album)) {
      // set the owning side to null (unless already changed)
      if ($album->getArtist() === $this) {
        $album->setArtist(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Track>
   */
  public function getTracks(): Collection
  {
    return $this->tracks;
  }

  public function addTrack(Track $track): static
  {
    if (!$this->tracks->contains($track)) {
      $this->tracks->add($track);
      $track->setArtist($this);
    }

    return $this;
  }

  public function removeTrack(Track $track): static
  {
    if ($this->tracks->removeElement($track)) {
      // set the owning side to null (unless already changed)
      if ($track->getArtist() === $this) {
        $track->setArtist(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Image>
   */
  public function getImage(): Collection
  {
    return $this->image;
  }

  public function addImage(Image $image): static
  {
    if (!$this->image->contains($image)) {
      $this->image->add($image);
    }

    return $this;
  }

  public function removeImage(Image $image): static
  {
    $this->image->removeElement($image);

    return $this;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(?string $url): static
  {
    $this->url = $url;

    return $this;
  }

  public function getListeners(): ?string
  {
    return $this->listeners;
  }

  public function setListeners(?string $listeners): static
  {
    $this->listeners = $listeners;

    return $this;
  }

  public function getPlaycount(): ?string
  {
    return $this->playcount;
  }

  public function setPlaycount(?string $playcount): static
  {
    $this->playcount = $playcount;

    return $this;
  }

  public function getBioSummary(): ?string
  {
    return $this->bioSummary;
  }

  public function setBioSummary(?string $bioSummary): static
  {
    $this->bioSummary = $bioSummary;

    return $this;
  }

  public function getBioContent(): ?string
  {
    return $this->bioContent;
  }

  public function setBioContent(?string $bioContent): static
  {
    $this->bioContent = $bioContent;

    return $this;
  }

  public function getUserPlaycount(): ?string
  {
    return $this->userPlaycount;
  }

  public function setUserPlaycount(?string $userPlaycount): static
  {
    $this->userPlaycount = $userPlaycount;

    return $this;
  }
}
