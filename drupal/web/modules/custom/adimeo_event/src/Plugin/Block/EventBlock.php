<?php

namespace Drupal\adimeo_event\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\adimeo_event\EventService;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides an event block.
 *
 * @Block(
 *   id = "block_related_event",
 *   admin_label = @Translation("Événements liés"),
 *   category = @Translation("adimeo_related_event")
 * )
 */
class EventBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  protected EventService $eventService;
  protected RequestStack $requestStack;

  public function __construct(
      array $configuration,
      $pluginId,
      $pluginDefinition,
      RequestStack $request_stack,
      EventService $eventService
  )
  {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->requestStack = $request_stack;
    $this->eventService = $eventService;
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @return EventBlock
   */
  public static function create(
      ContainerInterface $container,
      array $configuration,
      $plugin_id,
      $plugin_definition
  ):EventBlock
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
      $container->get('adimeo_event_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array
  {
    $events = [];
    $otherEvents = [];
    $entity = $this->requestStack->getCurrentRequest()->get('node');
    if ($entity instanceof \Drupal\node\NodeInterface) {
      $nid = $entity->id();
      $tid = $entity->field_event_type->getValue();
      $tid = $tid[0]['target_id'];
      $events = $this->eventService->getRelatedEventByTerm($nid, $tid);
      if (count($events) < 3) {
        $otherEvents = $this->eventService->getRelatedEventByTerm($nid, $tid, '<>');
      }
    }
    return [
      '#theme' => 'adimeo__event',
      '#relatedEvents' => $events,
      '#otherEvents' => $otherEvents
    ];

  }

  /**
   * Cache per page
   */
  public function getCacheContexts(): array
  {
    return ['url.path'];
  }

}

