<?php

namespace App\Entity;

use App\Data\ChartOptions;
use App\Data\Statisitc\SubTypeModel\AbstractSubTypeModel;
use App\Data\Statisitc\SubTypeModel\BarModel;
use App\Data\Statisitc\SubTypeModel\ScrobblesPerMonthAnnualyModel;
use App\Data\Statisitc\SubTypeModel\TotalScrobblesPerYearModel;
use App\Data\Statisitc\TypeModel\AbstractTypeModel;
use App\Data\Statisitc\TypeModel\NativeTypeModel;
use App\Data\Statisitc\TypeModel\TopAlbumsModel;
use App\Data\Statisitc\TypeModel\TopArtistsModel;
use App\Data\Statisitc\TypeModel\TopTracksModel;
use App\Repository\WidgetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
class Widget
{

  const TYPE__NATIVE = 1; //type reserved for native widgets (not created by the user)
  const TYPE__TOP_ARTISTS = 2;
  const TYPE__TOP_ALBUMS = 3;
  const TYPE__TOP_TRACKS = 4;
  const TYPES = [
    'Top Artists' => self::TYPE__TOP_ARTISTS,
    'Top Albums' => self::TYPE__TOP_ALBUMS,
    'Top Tracks' => self::TYPE__TOP_TRACKS
  ];

  //Subtype reserved for NATIVE TYPE
  const SUB_TYPE_NATIVE__SCROBBLES_PER_MONTH_ANNUALY = 1;
  const SUB_TYPE_NATIVE__TOTAL_SCROBBLES_PER_YEAR = 2;

  const SUB_TYPE__BAR = 1;
  const SUB_TYPE__PIE = 2;
  const SUB_TYPE__DONUT = 3;
  const SUB_TYPES = [
    'Bar' => self::SUB_TYPE__BAR,
    'Pie' => self::SUB_TYPE__PIE,
    'Donut' => self::SUB_TYPE__DONUT
  ];

//  const WIDGET_DEFAULT_FONT_COLOR = '#ffffff';
  const WIDGET_DEFAULT_FONT_COLOR = 'black';
//  const WIDGET_DEFAULT_BACKGROUND_COLOR = '#d7d7f';
  const WIDGET_DEFAULT_BACKGROUND_COLOR = '#eaeaea';


  const DATE_TYPE__ALL_TIME = 0;
  const DATE_TYPE__CUSTOM = 1;
  const DATE_TYPE__LAST_WEEK = 2;
  const DATE_TYPE__LAST_MONTH = 3;
  const DATE_TYPE__LAST_3_MONTHS = 4;
  const DATE_TYPE__LAST_6_MONTHS = 5;
  const DATE_TYPE__LAST_YEAR = 6;
  const DATE_TYPES = [
    'All Time' => self::DATE_TYPE__ALL_TIME,
    'Custom period' => self::DATE_TYPE__CUSTOM,
    'Last Week' => self::DATE_TYPE__LAST_WEEK,
    'Last Month' => self::DATE_TYPE__LAST_MONTH,
    'Last 3 Months' => self::DATE_TYPE__LAST_3_MONTHS,
    'Last 6 Months' => self::DATE_TYPE__LAST_6_MONTHS,
    'Last Year' => self::DATE_TYPE__LAST_YEAR
  ];

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'widgets')]
  #[ORM\JoinColumn(nullable: false)]
  private ?WidgetGrid $widgetGrid = null;

  #[ORM\Column(length: 255)]
  private ?string $code = null;

  #[ORM\Column(length: 1020, nullable: true)]
  private ?string $wording = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $comment = null;

  #[ORM\Column]
  private ?int $typeWidget = null;

  #[ORM\Column(nullable: true)]
  private ?int $subTypeWidget = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $query = null;

  #[ORM\Column(nullable: true)]
  private ?float $width = null;

  #[ORM\Column(nullable: true)]
  private ?float $height = null;

  #[ORM\Column(nullable: true)]
  private ?float $positionX = null;

  #[ORM\Column(nullable: true)]
  private ?float $positionY = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $fontColor = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $backgroundColor = null;

  #[ORM\Column]
  private ?int $dateType = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $dateFrom = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $dateTo = null;

  private ?ChartOptions $option = null;



  public function getId(): ?int {return $this->id;}
  public function getWidgetGrid(): ?WidgetGrid {return $this->widgetGrid;}
  public function setWidgetGrid(?WidgetGrid $widgetGrid): static {$this->widgetGrid = $widgetGrid;return $this;}
  public function getCode(): ?string {return $this->code;}
  public function setCode(string $code): static {$this->code = $code;return $this;}
  public function getWording(): ?string {return $this->wording;}
  public function setWording(?string $wording): static {$this->wording = $wording;return $this;}
  public function getComment(): ?string {return $this->comment;}
  public function setComment(?string $comment): static {$this->comment = $comment;return $this;}
  public function getTypeWidget(): ?int {return $this->typeWidget;}
  public function setTypeWidget(int $typeWidget): static {$this->typeWidget = $typeWidget;return $this;}
  public function getSubTypeWidget(): ?int {return $this->subTypeWidget;}
  public function setSubTypeWidget(?int $subTypeWidget): static {$this->subTypeWidget = $subTypeWidget;return $this;}
  public function getQuery(): ?string {return $this->query;}
  public function setQuery(?string $query): static {$this->query = $query;return $this;}
  public function getWidth(): ?float {return $this->width;}
  public function setWidth(?float $width): static {$this->width = $width;return $this;}
  public function getHeight(): ?float {return $this->height;}
  public function setHeight(?float $height): static {$this->height = $height;return $this;}
  public function getPositionX(): ?float {return $this->positionX;}
  public function setPositionX(?float $positionX): static {$this->positionX = $positionX;return $this;}
  public function getPositionY(): ?float {return $this->positionY;}
  public function setPositionY(?float $positionY): static {$this->positionY = $positionY;return $this;}
  public function getFontColor(): ?string {return $this->fontColor;}
  public function setFontColor(?string $fontColor): static {$this->fontColor = $fontColor;return $this;}
  public function getBackgroundColor(): ?string {return $this->backgroundColor;}
  public function setBackgroundColor(?string $backgroundColor): static {$this->backgroundColor = $backgroundColor;return $this;}



  /**
   * Validate date range :
   * add 1 day to the end date if it's the same as the start date
   * check if the start date is before the end date
   * Complete the date range if one of the date is null (start date to 1970-01-01 and end date to today)
   * @return bool
   */
  public function validateDateRange(): bool
  {
    if ($this->getDateFrom() == $this->getDateTo()) {
      //add 1 day
      $this->setDateTo($this->getDateTo()->modify('+1 day'));
    }

    if ($this->getDateFrom() == null && $this->getDateTo() != null) {
      //set datefrom to january 1st 1970
      $this->setDateFrom(new \DateTime('1970-01-01'));
    }

    if ($this->getDateFrom() != null && $this->getDateTo() == null) {
      //set dateto to today
      $this->setDateTo(new \DateTime());
    }

    if ($this->getDateFrom() > $this->getDateTo()) {
      return false;
    }

    return true;
  }


  public function applyModel(mixed $widgetModel, bool $creating = true): void
  {
    if ($creating) {
      $this->setWidth($widgetModel->getWidth());
      $this->setHeight($widgetModel->getHeight());
    }
    $this->setCode($widgetModel->getCode());
    $this->setTypeWidget($widgetModel->getTypeWidget());
  }

  public function getDeleteButton($class = 'delete-widget'): string
  {
    $javascriptFunction = 'deleteWidget("' . $this->getId() . '")';
    return '<button onclick=' . $javascriptFunction . ' class="'.$class.'">X</button>';
  }

  public function getInfoButton($class = 'info-widget'): string
  {
    $infosContent = '<div class="widget-info-content">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias cornemo provident quam, vitae? Nam provident quaerat vel.</div>';
    return '<div class="info-block">     <button class="'.$class.'"><span>.i.</span></button>' . $infosContent . '</div>';
  }

  /**
   * Get the modify button for the widget
   * @param $class
   * @return string
   */
  public function getModifyButton($class = 'modify-widget'): string
  {
    $javascriptFunction = 'modifyWidget("' . $this->getId() . '")';
    return '<button onclick=' . $javascriptFunction . ' class="'.$class.'">M</button>';
  }

  /**
   * Get the Type Model (TopArtistsModel, TopAlbumsModel, TopTracksModel) from given typeWidget
   * @param int $typeWidget
   * @return AbstractTypeModel|null
   */
  public static function getTypeModelFrom(int $typeWidget): ?AbstractTypeModel
  {
    $model = null;
    switch ($typeWidget) {

      /** ********************* */
      /**    TOP ARTIST TYPE    */
      /** ********************* */
      case Widget::TYPE__TOP_ARTISTS:
        $model = new TopArtistsModel();
        break;

      /** ********************* */
      /**    TOP ARTIST TYPE    */
      /** ********************* */
      case Widget::TYPE__TOP_ALBUMS:
        $model = new TopAlbumsModel();
        break;

      /** ********************* */
      /**    TOP TRACKS TYPE    */
      /** ********************* */
      case Widget::TYPE__TOP_TRACKS:
        $model = new TopTracksModel();
        break;

      case Widget::TYPE__NATIVE:
        $model = new NativeTypeModel();
        break;

    }

    return $model;
  }

  /**
   * Get the Type Model (TopArtistsModel, TopAlbumsModel, TopTracksModel) from the instance
   * @return AbstractTypeModel|null
   */
  public function getTypeModel(): ?AbstractTypeModel
  {
    return self::getTypeModelFrom($this->getTypeWidget());
  }

  /**
   * Get the SubType Model (BarModel, PieModel, DonutModel) from given subTypeWidget
   * @param int $typeWidget
   * @return \App\Data\Statisitc\SubTypeModel\AbstractSubTypeModel
   */
  public static function getSubTypeModelFrom(int $subTypeWidget, int $typeWidget = 0): AbstractSubTypeModel
  {
    $model = null;

    if ($typeWidget == Widget::TYPE__NATIVE) {
      $model = self::getSubTypeModelForNativeWidget($subTypeWidget);
    } else {
      switch ($subTypeWidget) {

        case Widget::SUB_TYPE__BAR:
          $model = new BarModel();
          break;




      }
    }


    return $model;
  }

  /**
   * Get the SubType Model (ScrollblesPerMonthAnnualyModel, TotalScrobblesPerYearModel) from given subTypeWidget
   * Used only for native widgets
   * @param int $typeWidget
   * @return AbstractSubTypeModel
   */
  public static function getSubTypeModelForNativeWidget(int $typeWidget): AbstractSubTypeModel
  {
    $model = null;

    switch ($typeWidget) {

      case Widget::SUB_TYPE_NATIVE__SCROBBLES_PER_MONTH_ANNUALY :
        $model = new ScrobblesPerMonthAnnualyModel();
        break;

      case Widget::SUB_TYPE_NATIVE__TOTAL_SCROBBLES_PER_YEAR :
        $model = new TotalScrobblesPerYearModel();
        break;

    }

    return $model;
  }

  /**
   * Get the SubType Model (BarModel, PieModel, DonutModel) from the instance
   * @return AbstractSubTypeModel|null
   */
  public function getSubTypeModel(): ?AbstractSubTypeModel
  {
    return self::getSubTypeModelFrom($this->getSubTypeWidget(), $this->getTypeWidget());
  }


  /**
   * Get the Chart.js type from the widget Entity subtype
   * @param int $subTypeWidget
   * @return string
   */
  public static function getChartTypeFromWidgetSubType(int $subTypeWidget): string
  {
    $chartType = '';

    switch ($subTypeWidget) {
      case Widget::SUB_TYPE__BAR:
        $chartType = 'bar';
        break;

      case Widget::SUB_TYPE__PIE:
        $chartType = 'pie';
        break;

      case Widget::SUB_TYPE__DONUT:
        $chartType = 'doughnut';
        break;
    }

    return $chartType;
  }

  /**
   * Get the type for chart.js library
   * @return string
   */
  public function getChartType(): string
  {
    return self::getChartTypeFromWidgetSubType($this->subTypeWidget);
  }

  public function getDateType(): ?int
  {
      return $this->dateType;
  }

  public function setDateType(int $dateType): static
  {
      $this->dateType = $dateType;

      return $this;
  }

  public function getDateFrom(): ?\DateTimeInterface
  {
      return $this->dateFrom;
  }

  public function setDateFrom(?\DateTimeInterface $dateFrom): static
  {
      $this->dateFrom = $dateFrom;

      return $this;
  }

  public function getDateTo(): ?\DateTimeInterface
  {
      return $this->dateTo;
  }

  public function setDateTo(?\DateTimeInterface $dateTo): static
  {
      $this->dateTo = $dateTo;

      return $this;
  }



}

