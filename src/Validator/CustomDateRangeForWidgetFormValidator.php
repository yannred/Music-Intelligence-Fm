<?php

namespace App\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks if the date range is valid
 * Used only if the date type is set to custom
 */
class CustomDateRangeForWidgetFormValidator extends ConstraintValidator
{

  const DATE_RANGE_INVALID = "Date range invalid";

  public function validate(mixed $value, Constraint $constraint): void
  {
    if (!$constraint instanceof CustomDateRangeForWidgetForm) {
      throw new UnexpectedTypeException($constraint, CustomDateRangeForWidgetForm::class);
    }

    if ($value instanceof \DateTime) {
      $value = $value->format('Y-m-d');
    }

    //Control date is valid and less than tomorrow
    if (
      $value != "" &&
      (!strtotime($value) || strtotime($value) > strtotime("now"))
    ) {
      $this->context->buildViolation(self::DATE_RANGE_INVALID)->addViolation();
    } elseif (
      //Control the end date is greater than the start date
      $constraint->type == CustomDateRangeForWidgetForm::TO_FIELD &&
      $constraint->from != "" &&
      $constraint->to != "" &&
      strtotime($constraint->from) > strtotime($constraint->to)
    ) {
      $this->context->buildViolation(self::DATE_RANGE_INVALID)->addViolation();
    }

  }
}