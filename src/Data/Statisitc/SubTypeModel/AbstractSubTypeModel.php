<?php

namespace App\Data\Statisitc\SubTypeModel;

use App\Data\ChartOptions;

/**
 * Class for the subtype model of a widget
 * Can be extended to create different subtypes
 * Mainly used to set the default options for the chart
 */
abstract class AbstractSubTypeModel
{

  protected string $chartType = '';
  protected ChartOptions $chartOptions;

  public function __construct(){
    $this->chartOptions = new ChartOptions();
  }

  public function getChartOptions(): ChartOptions
  {
    return $this->chartOptions;
  }

  public function getChartType(): string
  {
    return $this->chartType;
  }
}