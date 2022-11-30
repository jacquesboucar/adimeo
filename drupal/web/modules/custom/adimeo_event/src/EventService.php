<?php

namespace Drupal\adimeo_event;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\Core\Entity\EntityStorageInterface;

class EventService
{
  protected EntityStorageInterface $entityManager;

  public function __construct(EntityTypeManagerInterface $entityManager)
  {
     $this->entityManager = $entityManager->getStorage('node');
  }

  public function getRelatedEventByTerm(string $nid, string $tid, string $operator = '='): array
  {
    $query = $this->entityManager->getQuery();
    $query->condition('type', 'event');
    $query->condition('status', 1);
    $query->condition('field_event_type', $tid, $operator);
    $query->condition('field_date_end', $this->getCurrentTime(), '>=');
    $query->condition('nid', $nid, '<>');
    $query->sort('field_date_start', 'ASC');
    $query->range(0, 3);
    $result = $query->execute();
    return $this->entityManager->loadMultiple(array_values($result));
  }

  public function getOldEvent(): array
  {
    $query = $this->entityManager->getQuery();
    $query->condition('type', 'event');
    $query->condition('status', 1);
    $query->condition('field_date_end', $this->getCurrentTime(), '<');
    $result = $query->execute();
    return $this->entityManager->loadMultiple(array_values($result));
  }
  private function getCurrentTime(): DrupalDateTime
  {
    $now = new DrupalDateTime();
    $now->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    return $now;
  }
}
