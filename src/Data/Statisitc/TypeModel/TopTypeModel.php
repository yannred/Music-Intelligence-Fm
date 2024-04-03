<?php

namespace App\Data\Statisitc\TypeModel;

use App\Entity\Widget;

abstract class TopTypeModel extends AbstractTypeModel
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Return the parameters for create the DQL query
   * @param Widget $widget
   * @return array
   */
  public function getQueryParameters(Widget $widget): array
  {
    $parameters = Parent::getQueryParameters($widget);

    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_WEEK) {
      $parameters['where'] .= 'AND scrobble.timestamp >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) '
          . 'AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()) '
      ;
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_MONTH) {
      $parameters['where'] .= 'AND scrobble.timestamp >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 MONTH) '
        . 'AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()) '
      ;
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_3_MONTHS) {
      $parameters['where'] .= 'AND scrobble.timestamp >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 MONTH) '
        . 'AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()) '
      ;
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_6_MONTHS) {
      $parameters['where'] .= 'AND scrobble.timestamp >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 6 MONTH) '
        . 'AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()) '
      ;
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_YEAR) {
      $parameters['where'] .= 'AND scrobble.timestamp >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 YEAR) '
        . 'AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()) '
      ;
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__CUSTOM) {
      $parameters['where'] .= 'AND scrobble.timestamp > ' . $widget->getDateFrom()->getTimestamp() . ' '
        . 'AND scrobble.timestamp < ' . $widget->getDateTo()->getTimestamp() . ' '
      ;
    }


    return $parameters;
  }


}