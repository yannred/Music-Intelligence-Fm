<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Album>
 *
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Album::class);
    $this->security = $security;
  }

  /**
   * return an array of top albums
   * @return array
   */
  public function getTopAlbums(): array
  {
    $user = $this->security->getUser();

    $topResult = $this->createQueryBuilder('album')
      ->select('album.id, count(scrobble.id) as count')
      ->join('album.tracks', 'track')
      ->join('track.scrobbles', 'scrobble')
      ->where('scrobble.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('album.id')
      ->orderBy('count(scrobble.id)', 'DESC')
      ->setMaxResults(Album::LIMIT_TOP_ALBUMS)
      ->getQuery()
      ->getResult();

    $albums = [];
    foreach ($topResult as $result) {
      $album = $this->createQueryBuilder('album')
        ->select('album, count(scrobble.id) as count, count(distinct track.id) as totalTrack')
        ->join('album.tracks', 'track')
        ->join('track.scrobbles', 'scrobble')
        ->where('scrobble.user = :user')
        ->andWhere('album.id = :albumId')
        ->setParameter('user', $user->getId())
        ->setParameter('albumId', $result['id'])
        ->getQuery()
        ->getOneOrNullResult();

      $albums[] = $album;
    }

    return $albums;
  }

  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $query = $this->createQueryBuilder('album')
      ->select('album, count(scrobble.id) as count, count(distinct track.id) as totalTrack')
      ->join('album.tracks', 'track')
      ->join('track.scrobbles', 'scrobble')
      ->where('scrobble.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('album.name, album.artist')
      ->orderBy('count(scrobble.id)', 'DESC');

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $query
        ->andWhere('scrobble.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from->getTimestamp())
        ->setParameter('to', $dataSearchBar->to->getTimestamp());
    }

    return $query->getQuery();
  }
}
