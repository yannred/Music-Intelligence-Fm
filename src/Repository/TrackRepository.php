<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

  public function getTopTracks()
  {
    $user = $this->security->getUser();

    $topTrack = $this->createQueryBuilder('t')
      ->select('t, count(t.id) as count')
      ->join('t.scrobbles', 's')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('t.id')
      ->orderBy('count(t.id)', 'DESC')
      ->getQuery()
      ->getResult()
    ;

    return $topTrack;

  }


  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $query = $this->createQueryBuilder('t')
      ->select('t, count(t.id) as count')
      ->join('t.scrobbles', 's')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('t.id')
      ->orderBy('count(t.id)', 'DESC')
    ;

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $query
        ->andWhere('s.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from)
        ->setParameter('to', $dataSearchBar->to)
      ;
    }

    return $query->getQuery();
  }



}
