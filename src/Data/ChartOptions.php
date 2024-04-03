<?php

namespace App\Data;

/**
 * Used to store the options of a chart
 */
class ChartOptions
{
  /** @var int Ratio : 1 for square, 2 for rectangle */
  public int $aspectRatio;

  /** @var bool Set visibility of legend chart */
  public bool $legendVisible;

  /** @var string Contain index axis of chart (for bar chart)*/
  public string $indexAxis;

  /** @var bool Set visibility of ticks on x axis */
  public bool $ticksVisibleX;
  /** @var bool Set visibility of ticks on y axis */
  public bool $ticksVisibleY;

  /** @var int Set font size of ticks on x axis */
  public int $ticksFontSizeX;
  /** @var int Set font size of ticks on y axis */
  public int $ticksFontSizeY;

  public function __construct()
  {
    $this->aspectRatio = 1;
    $this->legendVisible = true;
    $this->indexAxis = 'x';
    $this->ticksVisibleX = true;
    $this->ticksVisibleY = true;
    $this->ticksFontSizeX = 14;
    $this->ticksFontSizeY = 14;
  }
}