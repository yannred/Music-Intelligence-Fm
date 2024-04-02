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
      ->join('track.album', 'album')
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
      ->select('scrobble, track, user, lovedTrack, track_loved, track_loved.id as loved_track, image.url as image_url')
      ->join('scrobble.track', 'track')
      ->join('scrobble.user', 'user')
      ->leftJoin('user.lovedTracks', 'lovedTrack', 'WITH', 'lovedTrack.track = track.id')
      ->leftJoin('lovedTrack.track', 'track_loved')
      ->join('track.album', 'album')
      ->leftJoin('album.image', 'image')
      ->where('scrobble.user = :user')
      ->andWhere('image.size = 1')
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


  /**
   * Get total scrobbles for a user this week and last week
   * @param UserInterface $user
   * @return array ['thisWeek' => int, 'lastWeek' => int]
   */
  public function getTotalScrobblesThisWeek(UserInterface $user): array
  {
    $mondayLastWeek = strtotime('last monday', strtotime('tomorrow', strtotime('-1 week')));
    $sundayLastWeek = strtotime('last sunday', $mondayLastWeek);

    $resultLastWeek = $this->createQueryBuilder('scrobble')
      ->select('count(scrobble.id) as count')
      ->where('scrobble.user = :user')
      ->andWhere('scrobble.timestamp BETWEEN :lastMonday AND :lastSunday')
      ->setParameter('user', $user->getId())
      ->setParameter('lastMonday', $mondayLastWeek)
      ->setParameter('lastSunday', $sundayLastWeek)
      ->getQuery()
      ->getResult();

    $monday = strtotime('last monday', strtotime('tomorrow'));
    $sunday = strtotime('next sunday', $monday);

    $resultThisWeek = $this->createQueryBuilder('scrobble')
      ->select('count(scrobble.id) as count')
      ->where('scrobble.user = :user')
      ->andWhere('scrobble.timestamp BETWEEN :monday AND :sunday')
      ->setParameter('user', $user->getId())
      ->setParameter('monday', $monday)
      ->setParameter('sunday', $sunday)
      ->getQuery()
      ->getResult();

    return ['thisWeek' => $resultThisWeek[0]['count'], 'lastWeek' => $resultLastWeek[0]['count']];
  }


  /**
   * Get total scrobbles for a user this month and last month
   * @param UserInterface $user
   * @return array ['thisMonth' => int, 'lastMonth' => int]
   */
  public function getTotalScrobblesThisMonth(UserInterface $user): array
  {
    $firstDayLastMonth = strtotime('first day of last month');
    $lastDayLastMonth = strtotime('last day of last month');

    $resultLastMonth = $this->createQueryBuilder('scrobble')
      ->select('count(scrobble.id) as count')
      ->where('scrobble.user = :user')
      ->andWhere('scrobble.timestamp BETWEEN :firstDay AND :lastDay')
      ->setParameter('user', $user->getId())
      ->setParameter('firstDay', $firstDayLastMonth)
      ->setParameter('lastDay', $lastDayLastMonth)
      ->getQuery()
      ->getResult();

    $firstDay = strtotime('first day of this month');
    $lastDay = strtotime('last day of this month');

    $resultThisMonth = $this->createQueryBuilder('scrobble')
      ->select('count(scrobble.id) as count')
      ->where('scrobble.user = :user')
      ->andWhere('scrobble.timestamp BETWEEN :firstDay AND :lastDay')
      ->setParameter('user', $user->getId())
      ->setParameter('firstDay', $firstDay)
      ->setParameter('lastDay', $lastDay)
      ->getQuery()
      ->getResult();

    return ['thisMonth' => $resultThisMonth[0]['count'], 'lastMonth' => $resultLastMonth[0]['count']];
  }


  /**
   * Get total scrobbles for a user this year and last year
   * @param UserInterface $user
   * @return array ['thisYear' => int, 'lastYear' => int]
   */
  public function getTotalScrobblesThisYear(UserInterface $user): array
  {
    $firstDayLastYear = strtotime('first day of january last year');
    $lastDayLastYear = strtotime('last day of december last year');

    $resultLastYear = $this->createQueryBuilder('scrobble')
      ->select('count(scrobble.id) as count')
      ->where('scrobble.user = :user')
      ->andWhere('scrobble.timestamp BETWEEN :firstDay AND :lastDay')
      ->setParameter('user', $user->getId())
      ->setParameter('firstDay', $firstDayLastYear)
      ->setParameter('lastDay', $lastDayLastYear)
      ->getQuery()
      ->getResult();

    $firstDay = strtotime('first day of january');
    $lastDay = strtotime('last day of december');

    $resultThisYear = $this->createQueryBuilder('scrobble')
      ->select('count(scrobble.id) as count')
      ->where('scrobble.user = :user')
      ->andWhere('scrobble.timestamp BETWEEN :firstDay AND :lastDay')
      ->setParameter('user', $user->getId())
      ->setParameter('firstDay', $firstDay)
      ->setParameter('lastDay', $lastDay)
      ->getQuery()
      ->getResult();

    return ['thisYear' => $resultThisYear[0]['count'], 'lastYear' => $resultLastYear[0]['count']];
  }

}
