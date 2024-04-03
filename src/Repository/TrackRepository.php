<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Track>
 *
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Track::class);
    $this->security = $security;
  }

  /**
   * return an array of top tracks
   * @return array
   * @throws Exception
   */
  public function getTopTracks()
  {
    $user = $this->security->getUser();
    $connexion = $this->getEntityManager()->getConnection();

    $sql = '
      SELECT track.id        as track_id,
             track.name      as track_name,
             artist.name     as artist_name,
             album.name      as album_name,
             image.url       as image_url,
             count(track.id) as count,
             user_loved_track.id   as loved_track
      FROM track
               JOIN scrobble ON track.id = scrobble.track_id
               JOIN album ON track.album_id = album.id
               JOIN artist ON track.artist_id = artist.id
               JOIN user ON scrobble.user_id = user.id
               LEFT JOIN track_image ON track_image.track_id = track.id
               LEFT JOIN image ON track_image.image_id = image.id
               LEFT JOIN loved_track ON loved_track.user_id = user.id AND loved_track.track_id = track.id
               LEFT JOIN track as user_loved_track ON loved_track.track_id = user_loved_track.id
      WHERE scrobble.user_id = :user
        AND image.size = 2
      GROUP BY track.id
      ORDER BY count DESC
      LIMIT 5
    ';

    $resultSet = $connexion->executeQuery($sql, ['user' => $user->getId()]);
    $result = $resultSet->fetchAllAssociative();
    return $result;
  }


  /**
   * Get query for pagination
   * @param SearchBarData $dataSearchBar
   * @return Query
   */
  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $query = $this->createQueryBuilder('track')
      ->select('track, artist, album, image, scrobble, user, user_loved, track_loved, track.id as track_id, track.name as track_name, artist.name as artist_name, album.name as album_name, count(track.id) as count, image.url as image_url, track_loved.id as loved_track')
      ->join('track.scrobbles', 'scrobble')
      ->join('track.album', 'album')
      ->join('track.artist', 'artist')
      ->join('scrobble.user', 'user')
      ->leftJoin('track.image', 'image')
      ->leftJoin('user.lovedTracks', 'user_loved', 'WITH', 'user_loved.track = track.id')
      ->leftJoin('user_loved.track', 'track_loved')
      ->where('scrobble.user = :user')
      ->andWhere('image.size = 1')
      ->setParameter('user', $user->getId())
      ->groupBy('track.id')
      ->orderBy('count(track.id)', 'DESC')
    ;

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $query
        ->andWhere('scrobble.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from->getTimestamp())
        ->setParameter('to', $dataSearchBar->to->getTimestamp());
    }

    return $query->getQuery();
  }


}
