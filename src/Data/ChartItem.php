<?php

namespace App\Data;

class ChartItem
{
  const CALLBACK_OPTION__TRUNCATE_TICKS_X = 'truncateTickX';

  public int $id;

  public string $type;

  public string $title;

  /** @var array Contain raw data */
  public array $data;

  /** @var array contain the "options" attribute of the JSON chart definition */
  public array $optionsAttribute;

  /** @var array contain the "data" attribute of the JSON chart definition */
  public array $dataAttribute;

  /** @var array Contain constants for adding callback options to the chart in JavaScript statements */
  public array $callbackOptions;

  public function __construct()
  {
    $this->id = 0;
    $this->type = '';
    $this->title = '';
    $this->data = [];
    $this->optionsAttribute = [];
    $this->dataAttribute = [];
    $this->callbackOptions = [];
  }
}