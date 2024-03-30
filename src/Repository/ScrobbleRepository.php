<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Scrobble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Scrobble>
 *
 * @method Scrobble|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scrobble|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scrobble[]    findAll()
 * @method Scrobble[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScrobbleRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Scrobble::class);
    $this->security = $security;
  }


  /**
   * Get query for pagination
   * @return Query
   */
  public function paginationQuery(): Query
  {
    $user = $this->security->getUser();

    return $this->createQueryBuilder('scrobble')
      ->select('scrobble, track, album, user, image, lovedTrack, track_loved, track_loved.id as loved_track, image.url as image_url')
      ->leftJoin('scrobble.user', 'user')
      ->leftJoin('user.lovedTracks', 'lovedTrack', 'WITH', 'lovedTrack.track = scrobble.track')
      ->leftJoin('lovedTrack.track', 'track_loved')
      ->leftJoin('scrobble.track', 'track')
      ->leftJoin('track.album', 'album')
      ->leftJoin('album.image', 'image')
      ->where('scrobble.user = :user')
      ->andWhere('image.size = 1')
      ->setParameter('user', $user->getId())
      ->orderBy('scrobble.timestamp', 'DESC')
      ->getQuery();
  }

  /**
   * Get query for pagination with filters
   * @param SearchBarData $dataSearchBar
   * @return Query
   */
  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $dateFilter = false;
    $trackFilter = false;
    $artistFilter = false;
    $albumFilter = false;

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $dateFilter = true;
    }
    if ($dataSearchBar->track !== '') {
      $trackFilter = true;
    }
    if ($dataSearchBar->artist !== '') {
      $artistFilter = true;
    }
    if ($dataSearchBar->album !== '') {
      $albumFilter = true;
    }

    $query = $this->createQueryBuilder('scrobble')
      ->select('scrobble, track, user, lovedTrack, track_loved, track_loved.id as loved_track')
      ->join('scrobble.track', 'track')
      ->join('scrobble.user', 'user')
      ->leftJoin('user.lovedTracks', 'lovedTrack', 'WITH', 'lovedTrack.track = track.id')
      ->leftJoin('lovedTrack.track', 'track_loved')

      ->where('scrobble.user = :user')
      ->setParameter('user', $user->getId())
      ->orderBy('scrobble.timestamp', 'ASC');

    if ($dateFilter) {
      $query
        ->andWhere('scrobble.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from->getTimestamp())
        ->setParameter('to', $dataSearchBar->to->getTimestamp());
    }

    if ($trackFilter) {
      $query
        ->andWhere('track.name LIKE :trackName')
        ->setParameter('trackName', '%' . trim($dataSearchBar->track) . '%');
    }

    if ($artistFilter) {
      $query
        ->join('track.artist', 'artist')
        ->andWhere('artist.name LIKE :artistName')
        ->setParameter('artistName', '%' . trim($dataSearchBar->artist) . '%');
    }

    if ($albumFilter) {
      $query
        ->join('track.album', 'album')
        ->andWhere('album.name LIKE :albumName')
        ->setParameter('albumName', '%' . trim($dataSearchBar->album) . '%');
    }

    $query->orderBy('scrobble.timestamp', 'DESC');

    return $query->getQuery();
  }

  /**
   * Get total scrobbles imported for a user
   * @param UserInterface $user
   * @return mixed
   */
  public function getTotalScrobbleForUser(UserInterface $user)
  {
    $result = $this->createQueryBuilder('s')
      ->select('count(s.id) as count')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->getQuery()
      ->getResult();

    return $result[0]['count'];
  }

}
