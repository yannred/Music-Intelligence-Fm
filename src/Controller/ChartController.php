<?php

namespace App\Controller;

use App\Data\ChartItem;
use App\Data\ChartOptions;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use App\Service\StatisticsService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{

  protected EntityManagerInterface $entityManager;
  protected LoggerInterface $logger;
  protected Security $security;
  protected StatisticsService $statisticsService;

  protected ChartItem $chartItem;

  protected WidgetGrid $userWidgetGrid;

  public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, Security $security, StatisticsService $statisticsService)
  {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->security = $security;
    $this->statisticsService = $statisticsService;

    //create a new grid if no grid is found
    $grid = $this->entityManager->getRepository(WidgetGrid::class)->findOneBy(['user' => $this->security->getUser(), 'defaultGrid' => true]);
    if ($grid === null) {
      $this->userWidgetGrid = $this->statisticsService->createGrid();
    } else {
      $this->userWidgetGrid = $grid;
    }
  }

  /**
   * Return the json data for build the chart
   * Used for the user statistic
   * @param int $id
   * @return Response
   * @throws \Exception
   */
  #[Route('/myPage/chart/{id}', name: 'app_chart', methods: ['GET'])]
  public function getChart(int $id): Response
  {
    $response = new Response();
    $chart = new ChartItem();

    $widget = $this->entityManager->getRepository(Widget::class)->getWidgetFromUser($id, $this->userWidgetGrid->getUser()->getId());

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $chart->id = $widget->getId();
      $chart->type = $widget->getChartType();
      $chart->title = $widget->getWording();

      $this->statisticsService->setWidget($widget);
      $this->statisticsService->setModels();

      $chart->dataAttribute = $this->statisticsService->getDataAttributeForChart();
      $chart->optionsAttribute = $this->statisticsService->getOptionsForChart();
      $chart->callbackOptions = $this->statisticsService->getCallbackOptionsForChart();

      $response->setStatusCode(Response::HTTP_OK);
      $response->setContent(json_encode($chart));
    }

    return $response;
  }


  /**
   * Return the json data for build the chart
   * Used for the native statistic (not saved in the database, not unique for a user)
   * @param int $subType The id of the native widget (ncanva-{id}) also used as the subType
   * @return Response
   * @throws \Exception
   */
  #[Route('/myPage/chart/native/{subType}', name: 'app_chart_native', methods: ['GET'])]
  public function getNativeChart(int $subType): Response
  {
    $response = new Response();
    $chart = new ChartItem();
    $widget = new Widget();
    $options = new ChartOptions();

    $widget->setTypeWidget(Widget::TYPE__NATIVE);
    $widget->setSubTypeWidget($subType);
    $this->statisticsService->setWidget($widget);
    $this->statisticsService->setModels();

    //the widget is defined by the subType and saved in the id attribute (ncanva-{id})
    $chart->id = $subType;

    //TODO : Case where no scorbbles are found ?

    $chart->type = $this->statisticsService->getChartTypeForNativeWidget();
    $chart->dataAttribute = $this->statisticsService->getDataAttributeForChart();

    $chart->optionsAttribute = $this->statisticsService->getOptionsForChart();

    $response->setStatusCode(Response::HTTP_OK);
    $response->setContent(json_encode($chart));

    return $response;
  }

}
