<?php

namespace App\Controller;

use App\Data\GridstackItem;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use App\Service\StatisticsService;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WidgetController extends CustomAbsrtactController
{

  protected LoggerInterface $logger;
  protected Security $security;
  protected StatisticsService $statisticsService;
  protected UtilsService $utilsService;

  protected WidgetGrid $userWidgetGrid;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    Security $security,
    StatisticsService $statisticsService,
    UtilsService $utilsService
  )
  {
    parent::__construct($entityManager);

    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->security = $security;
    $this->statisticsService = $statisticsService;
    $this->utilsService = $utilsService;

    //create a new grid if no grid is found
    $grid = $this->entityManager->getRepository(WidgetGrid::class)->findOneBy(['user' => $this->security->getUser(), 'defaultGrid' => true]);
    if ($grid === null) {
      $this->userWidgetGrid = $this->statisticsService->createGrid();
    } else {
      $this->userWidgetGrid = $grid;
    }
  }

  /**
   * Return a JSON response with the gridstack items
   * @return Response
   */
  #[Route('/myPage/grid', name: 'app_widget_load_grid', methods: ['GET'])]
  public function loadGrid(): Response
  {
    $response = new Response();

    $gridStackItems = [];

    $widgetEntities = $this->userWidgetGrid->getWidgets();

    foreach ($widgetEntities as $widgetEntity) {
      $gridStackWidget = new GridstackItem($widgetEntity);
      $modelWidget = $widgetEntity->getTypeModel();

      $gridStackWidget->content = $this->statisticsService->generateContent($widgetEntity, $modelWidget->getContentParameters());

      $gridStackItems[] = $gridStackWidget;
    }

    $response->setStatusCode(Response::HTTP_OK);
    $response->setContent(json_encode($gridStackItems));

    return $response;
  }


  /**
   * Update chart datas (position, size)
   * @param Request $request
   * @return Response
   */
  #[Route('/myPage/widget/{id}', name: 'app_widget_update', methods: ['UPDATE'])]
  public function updateWidget(Request $request): Response
  {
    $response = new Response();

    $body = $request->getContent();
    $parameters = json_decode($body, true);

    $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $request->get('id'), 'widgetGrid' => $this->userWidgetGrid]);

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $widget->setWidth($parameters['w']);
      $widget->setHeight($parameters['h']);
      $widget->setPositionX($parameters['x']);
      $widget->setPositionY($parameters['y']);

      $this->entityManager->persist($widget);
      $this->entityManager->flush();

      $response->setStatusCode(Response::HTTP_CREATED);
      $response->setContent(json_encode(['id' => $widget->getId()]));
    }

    return $response;
  }


  /**
   * Delete a widget
   * @param Request $request
   * @return Response
   */
  #[Route('/myPage/widget/{id}', name: 'app_widget_delete', methods: ['DELETE'])]
  public function deleteWidget(Request $request, ): Response
  {
    $response = new Response();

    $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $request->get('id'), 'widgetGrid' => $this->userWidgetGrid]);

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $this->entityManager->remove($widget);
      $this->entityManager->flush();
      $response->setStatusCode(Response::HTTP_OK);
    }

    return $response;
  }

}
