<?php

namespace App\Data\Statisitc\TypeModel;

use App\Entity\Widget;

/**
 * Class for the type model of a widget
 * Can be extended to create different types
 * Mainly used to set the SQL query and the widget parameters (width etc) of a user statistic
 */
abstract class AbstractTypeModel
{
  protected string $code;
  protected string $wording;
  protected string $comment;
  protected int $typeWidget;
  protected int $subTypeWidget;
  protected string $query;
  protected float $width;
  protected float $height;
  protected float $positionX;
  protected float $positionY;
  protected string $fontColor;
  protected string $backgroundColor;

  protected array $contentParameters;

  public function __construct()
  {
    $this->code = '';
    $this->wording = '';
    $this->comment = '';
    $this->typeWidget = 0;
    $this->subTypeWidget = 0;
    $this->query = '';
    $this->width = 0.0;
    $this->height = 0.0;
    $this->positionX = 0.0;
    $this->positionY = 0.0;
    $this->fontColor = '';
    $this->backgroundColor = '';

    $this->contentParameters = [];
  }


  /**
   * Return the parameters for create the DQL query
   * @param Widget $widget
   * @return array
   */
  public function getQueryParameters(Widget $widget): array
  {
    $parameters = [
      'join' => 'JOIN user on (scrobble.user_id = user.id) ',
      'where' => 'WHERE user.id = ' . $widget->getWidgetGrid()->getUser()->getId() . ' ',
    ];

    return $parameters;
  }


  /**
   * @return string|null
   */
  public function getCode(): ?string
  {
    return $this->code;
  }

  /**
   * @param string|null $code
   */
  public function setCode(?string $code): void
  {
    $this->code = $code;
  }

  /**
   * @return string|null
   */
  public function getWording(): ?string
  {
    return $this->wording;
  }

  /**
   * @param string|null $wording
   */
  public function setWording(?string $wording): void
  {
    $this->wording = $wording;
  }

  /**
   * @return string|null
   */
  public function getComment(): ?string
  {
    return $this->comment;
  }

  /**
   * @param string|null $comment
   */
  public function setComment(?string $comment): void
  {
    $this->comment = $comment;
  }

  /**
   * @return int|null
   */
  public function getTypeWidget(): ?int
  {
    return $this->typeWidget;
  }

  /**
   * @param int|null $typeWidget
   */
  public function setTypeWidget(?int $typeWidget): void
  {
    $this->typeWidget = $typeWidget;
  }

  /**
   * @return int|null
   */
  public function getSubTypeWidget(): ?int
  {
    return $this->subTypeWidget;
  }

  /**
   * @param int|null $subTypeWidget
   */
  public function setSubTypeWidget(?int $subTypeWidget): void
  {
    $this->subTypeWidget = $subTypeWidget;
  }

  /**
   * @return string|null
   */
  public function getQuery(): ?string
  {
    return $this->query;
  }

  /**
   * @param string|null $query
   */
  public function setQuery(?string $query): void
  {
    $this->query = $query;
  }

  /**
   * @return float|null
   */
  public function getWidth(): ?float
  {
    return $this->width;
  }

  /**
   * @param float|null $width
   */
  public function setWidth(?float $width): void
  {
    $this->width = $width;
  }

  /**
   * @return float|null
   */
  public function getHeight(): ?float
  {
    return $this->height;
  }

  /**
   * @param float|null $height
   */
  public function setHeight(?float $height): void
  {
    $this->height = $height;
  }

  /**
   * @return float|null
   */
  public function getPositionX(): ?float
  {
    return $this->positionX;
  }

  /**
   * @param float|null $positionX
   */
  public function setPositionX(?float $positionX): void
  {
    $this->positionX = $positionX;
  }

  /**
   * @return float|null
   */
  public function getPositionY(): ?float
  {
    return $this->positionY;
  }

  /**
   * @param float|null $positionY
   */
  public function setPositionY(?float $positionY): void
  {
    $this->positionY = $positionY;
  }

  /**
   * @return string|null
   */
  public function getFontColor(): ?string
  {
    return $this->fontColor;
  }

  /**
   * @param string|null $fontColor
   */
  public function setFontColor(?string $fontColor): void
  {
    $this->fontColor = $fontColor;
  }

  /**
   * @return string|null
   */
  public function getBackgroundColor(): ?string
  {
    return $this->backgroundColor;
  }

  /**
   * @param string|null $backgroundColor
   */
  public function setBackgroundColor(?string $backgroundColor): void
  {
    $this->backgroundColor = $backgroundColor;
  }

  /**
   * @return array
   */
  public function getContentParameters(): array
  {
    return $this->contentParameters;
  }

  /**
   * @param array $contentParameters
   */
  public function setContentParameters(array $contentParameters): void
  {
    $this->contentParameters = $contentParameters;
  }



}