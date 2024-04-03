<?php

namespace App\Data\Statisitc\TypeModel;

use App\Entity\Widget;

class TopArtistsModel extends TopTypeModel
{

  public function __construct()
  {

    parent::__construct();

    $this->setCode('TOPARTIST - ' . date('Y-m-d H:i:s'));
    $this->setTypeWidget(Widget::TYPE__TOP_ARTISTS);
    $this->setWidth(4);
    $this->setHeight(4);
  }

  /**
   * Return the parameters for create the DQL query
   * @param Widget $widget
   * @return array
   */
  public function getQueryParameters(Widget $widget): array
  {
    $parameters = Parent::getQueryParameters($widget);

    $parameters['select'] = 'SELECT artist.name, count(scrobble.id) as count ';

    $parameters['from'] = 'FROM scrobble ';

    $parameters['join'] .= 'JOIN track on (scrobble.track_id = track.id) ';
//    $parameters['join'] .= 'JOIN album on (track.album_id = album.id) ';
    $parameters['join'] .= 'JOIN artist on (track.artist_id = artist.id) ';

    $parameters['groupby'] = 'GROUP BY artist.name ';

    $parameters['orderby'] = 'ORDER BY count(scrobble.id) DESC ';

    return $parameters;
  }

}