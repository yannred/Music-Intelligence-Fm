<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CustomDateRangeForWidgetForm extends Constraint
{

  const FROM_FIELD = 0;
  const TO_FIELD = 1;

  public int $type;
  public string $from = '';
  public string $to = '';

  public function __construct(int $type, string $from, string $to, $groups = null, mixed $payload = null)
  {
    parent::__construct([], $groups, $payload);
    $this->type = $type;
    $this->from = $from;
    $this->to = $to;

  }


}