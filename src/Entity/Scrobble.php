<?php

namespace App\Entity;

use App\Repository\ScrobbleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScrobbleRepository::class)]
class Scrobble
{
  const LIMIT_RECENT_TRACKS = 5;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'scrobbles')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Track $track = null;

  #[ORM\Column]
  private ?int $timestamp = null;

  #[ORM\ManyToOne(inversedBy: 'scrobbles')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTrack(): ?Track
  {
    return $this->track;
  }

  public function setTrack(?Track $track): static
  {
    $this->track = $track;

    return $this;
  }

  public function getTimestamp(): ?int
  {
    return $this->timestamp;
  }

  public function setTimestamp(int $timestamp): static
  {
    $this->timestamp = $timestamp;

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }
}
