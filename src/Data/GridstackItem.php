<?php

namespace App\Data;

use App\Entity\Widget;

class GridstackItem
{
  public int $id;
  public int $x;
  public int $y;
  public int $w;
  public int $h;
  public string $content;

  public function __construct(?Widget $widget = null)
  {
    if ($widget) {
      $this->id = $widget->getId();
      $this->x = $widget->getPositionX();
      $this->y = $widget->getPositionY();
      $this->w = $widget->getWidth();
      $this->h = $widget->getHeight();
      $this->content = $widget->getCode();
    } else {
      $this->id = 0;
      $this->x = 0;
      $this->y = 0;
      $this->w = 0;
      $this->h = 0;
      $this->content = '';
    }


  }
}