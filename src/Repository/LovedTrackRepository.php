<?php

namespace App\Repository;

use App\Entity\LovedTrack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LovedTrack>
 *
 * @method LovedTrack|null find($id, $lockMode = null, $lockVersion = null)
 * @method LovedTrack|null findOneBy(array $criteria, array $orderBy = null)
 * @method LovedTrack[]    findAll()
 * @method LovedTrack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LovedTrackRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, LovedTrack::class);
  }

  /**
   * @param $id
   * @param $getId
   * @return bool
   * @throws NonUniqueResultException
   */
  public function isLoved($id, $getId): bool
  {
    $result = $this->createQueryBuilder('loved_track')
      ->select('loved_track')
      ->where('loved_track.track = :id')
      ->andWhere('loved_track.user = :getId')
      ->setParameter('id', $id)
      ->setParameter('getId', $getId)
      ->getQuery()
      ->getOneOrNullResult();

    $result ? $result = true : $result = false;
    return $result;
  }
}
