<?php

namespace Drupal\adimeo_event\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Queue\QueueFactory;
use Drupal\adimeo_event\EventService;
use Symfony\Component\Console\Output\OutputInterface;

class UnpublishEventCommand extends DrushCommands
{

  protected EventService $eventService;

  protected QueueFactory $queueFactory;

  public function __construct(EventService $eventService, QueueFactory $queue)
  {
    $this->eventService = $eventService;
    $this->queueFactory = $queue;
  }

  /**
   * @command event:unpublish
   * @aliases eup
   * @return void
   */
  public function unPublishEvent(OutputInterface $output)
  {
    $events = $this->eventService->getOldEvent();
    if (empty($events)) {
      return $output->writeln('There is no event published event to add in the queue');
    }
    $queue = $this->queueFactory->get('unpublish_events_queue_worker');
    foreach ($events as $event) {
      $output->writeln('Adding event title : ' . $event->getTitle() . ' with nid : ' . $event->id() . ' to the queue');
      $queue->createItem($event);
    }
  }

}
