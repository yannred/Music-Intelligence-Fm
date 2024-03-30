<?php

namespace App\Data\Statisitc\SubTypeModel;

class ScrobblesPerMonthAnnualyModel extends AbstractSubTypeModel
{
  public function __construct(){
    parent::__construct();

    $this->chartType = 'bar';

//    $this->chartOptions->legendVisible = true;
    $this->chartOptions->aspectRatio = 2;
    $this->chartOptions->ticksVisibleY = false;
  }
}