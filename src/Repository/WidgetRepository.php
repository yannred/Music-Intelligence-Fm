<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Widget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Widget>
 *
 * @method Widget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Widget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Widget[]    findAll()
 * @method Widget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Widget::class);
    $this->security = $security;
  }

  /**
   * Create DQL query for widget
   * @Deprecated SQL is used instead of DQL
   * @param array $parameters
   * @return Query
   */
  public function createWidgetQuery(array $parameters): Query
  {
    $user = $this->security->getUser();

    $queryBuilder = $this->getEntityManager()->getRepository($parameters['entity'])->createQueryBuilder($parameters['entityAlias']);

    if (isset($parameters['select'])) {
      $queryBuilder
        ->select($parameters['select']);
    } else {
      $queryBuilder
        ->select('scrobble');
    }

    if (isset($parameters['join'])) {
      foreach ($parameters['join'] as $join => $alias) {
        $queryBuilder
          ->join($join, $alias);
      }
    }

    $queryBuilder
      ->where('scrobble.user = ' . $user->getId());

    if (isset($parameters['where'])) {
      foreach ($parameters['where'] as $key => $value) {
        if ($key == 'and') {
          $queryBuilder
            ->andWhere($value['value']);

        } else {
          $queryBuilder
            ->orWhere($value['value']);
        }
      }
    }

    if (isset($parameters['groupby'])) {
      $queryBuilder
        ->groupBy($parameters['groupby']);
    }

    if (isset($parameters['orderby'])) {
      foreach ($parameters['orderby'] as $column => $order) {
        $queryBuilder
          ->orderBy($column, $order);
      }
    }

    return $queryBuilder->getQuery();
  }


  /**
   * Get the widgets from the user
   * @param int $widgetId
   * @param int $userId
   * @return Widget|null
   */
  public function getWidgetFromUser(int $widgetId, int $userId): ?Widget
  {
    $query = $this->createQueryBuilder('w')
      ->select('w')
      ->join('w.widgetGrid', 'grid')
      ->join('grid.user', 'user')
      ->where('w.id = :widgetId')
      ->setParameter('widgetId', $widgetId)
      ->andWhere('user.id = :userId')
      ->setParameter('userId', $userId)
      ->getQuery()
    ;

    $result = $query->getResult();

    return $result[0] ?? null;
  }

  /**
   * Get the number of scrobbles per month for the current year and the previous year
   * @param User $user
   * @return array
   */
  public function getScrobblesPerMonthAnnually(User $user): array
  {
    $sql = '
      SELECT
          YEAR(FROM_UNIXTIME(scrobble.timestamp)) as "year",
          MONTH(FROM_UNIXTIME(scrobble.timestamp)) as "month",
          COUNT(scrobble.id) as count
      FROM
          scrobble
      WHERE
          scrobble.user_id = :userId
          AND YEAR(FROM_UNIXTIME(scrobble.timestamp)) IN (YEAR(CURDATE()), YEAR(CURDATE()) - 1)
      GROUP BY
          year, month
      ORDER BY
          year, month;
    ';

    $query = $this->getEntityManager()->getConnection()->prepare($sql);
    $result = $query->executeQuery(['userId' => $user->getId()]);

    return $result->fetchAllAssociative();
  }

  /**
   * @param User $user
   * @return array[]
   * @throws \Doctrine\DBAL\Exception
   */
  public function getTotalScrobblesPerYear(User $user): array
  {
    $sql = '
      SELECT
          YEAR(FROM_UNIXTIME(scrobble.timestamp)) as "year",
          COUNT(scrobble.id) as count
      FROM
          scrobble
      WHERE
          scrobble.user_id = :userId
      GROUP BY
          year
      ORDER BY
          year;
    ';

    $query = $this->getEntityManager()->getConnection()->prepare($sql);
    $result = $query->executeQuery(['userId' => $user->getId()]);

    return $result->fetchAllAssociative();
  }

}
