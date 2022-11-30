<?php

namespace Drupal\adimeo_event\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Processes Node Tasks.
 *
 * @QueueWorker(
 *   id = "unpublish_events_queue_worker",
 *   title = @Translation("Task Worker: Unpublish old nodes event"),
 * )
 */
class UnpublishOldEvents extends QueueWorkerBase implements ContainerFactoryPluginInterface
{

  /**
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected LoggerChannelFactoryInterface $loggerChannelFactory;

  public function __construct(
    $configuration,
    $plugin_id,
    $plugin_definition,
    LoggerChannelFactoryInterface $loggerChannelFactory
  )
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $container->get('logger.factory')
    );
  }

  public function processItem($node): void
  {
    try {
      $node->setUnpublished();
      $node->save();
      $this->loggerChannelFactory->get('debug')
        ->debug('Deleted event node @id with title %title from queue',
          [
            '@id' => $node->id(),
            '%title' => $node->getTitle(),
          ]);
    }
    catch (\Exception $exception) {
      $this->loggerChannelFactory->get('Warning')
          ->warning('Exception trow for queue @error',
          ['@error' => $exception->getMessage()]
        );
    }
  }
}
