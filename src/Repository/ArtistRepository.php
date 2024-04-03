<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Artist>
 *
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Artist::class);
    $this->security = $security;
  }


  /**
   * return an array of top artists
   * @return array
   * @throws NonUniqueResultException
   */
  public function getTopArtists(): array
  {
    $user = $this->security->getUser();

    $artists = $this->createQueryBuilder('artist')
      ->select('artist.id, count(scrobble.id) as count')
      ->join('artist.tracks', 'track')
      ->join('track.scrobbles', 'scrobble')
      ->where('scrobble.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('artist.id')
      ->orderBy('count(scrobble.id)', 'DESC')
      ->getQuery()
      ->getResult();

    $topResults = array_slice($artists, 0, Artist::LIMIT_TOP_ARTIST);

    $artists = [];
    foreach ($topResults as $result) {
      $artist = $this->createQueryBuilder('artist')
        ->select('artist, count(scrobble.id) as count, count(distinct track.id) as totalTrack, count(distinct album.id) as totalAlbum')
        ->join('artist.tracks', 'track')
        ->join('track.scrobbles', 'scrobble')
        ->join('track.album', 'album')
        ->where('artist.id = :artistId')
        ->andWhere('scrobble.user = :user')
        ->setParameter('artistId', $result['id'])
        ->setParameter('user', $user->getId())
        ->getQuery()
        ->getOneOrNullResult()
      ;
      $artists[] = $artist;
    }

    return $artists;
  }


  /**
   * Get the query for pagination
   * @param SearchBarData $dataSearchBar
   * @return Query
   */
  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $query = $this->createQueryBuilder('a')
      ->select('a, count(s.id) as count, count(distinct t.id) as totalTrack, count(distinct al.id) as totalAlbum')
      ->join('a.tracks', 't')
      ->join('t.scrobbles', 's')
      ->join('t.album', 'al')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('a.name')
      ->orderBy('count(a.name)', 'DESC')
    ;

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $query
        ->andWhere('s.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from->getTimestamp())
        ->setParameter('to', $dataSearchBar->to->getTimestamp())
      ;
    }

    return $query->getQuery();
  }

}
