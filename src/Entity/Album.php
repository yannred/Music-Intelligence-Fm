<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{

  const LIMIT_TOP_ALBUMS = 14;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $mbid = null;

  #[ORM\Column(length: 1020)]
  private ?string $name = null;

  #[ORM\ManyToOne(inversedBy: 'albums')]
  private ?Artist $artist = null;

  #[ORM\OneToMany(mappedBy: 'album', targetEntity: Track::class)]
  private Collection $tracks;

  #[ORM\ManyToMany(targetEntity: Image::class)]
  private Collection $image;

  public function __construct()
  {
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

  public function getArtist(): ?Artist
  {
    return $this->artist;
  }

  public function setArtist(?Artist $artist): static
  {
    $this->artist = $artist;

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
      $track->setAlbum($this);
    }

    return $this;
  }

  public function removeTrack(Track $track): static
  {
    if ($this->tracks->removeElement($track)) {
      // set the owning side to null (unless already changed)
      if ($track->getAlbum() === $this) {
        $track->setAlbum(null);
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
}
