<?php

declare(strict_types=1);

namespace Drupal\helfi_linkedevents\Plugin\migrate\destination;

use Drupal\helfi_api_base\Plugin\migrate\destination\TranslatableEntityBase;
use Drupal\migrate\Row;

/**
 * Provides a destination plugin for Linked Events event entities.
 *
 * @MigrateDestination(
 *   id = "linkedevents_event",
 * )
 */
final class Event extends TranslatableEntityBase {

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'linkedevents_event';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTranslatableFields(): array {
    return [
      'name' => 'name',
      'description' => 'description',
      'short_description' => 'short_description',
      'provider' => 'provider',
      'info_url' => 'info_url',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity(Row $row, array $old_destination_id_values) {
    /** @var \Drupal\helfi_linkedevents\Entity\Event $entity */
    $entity = parent::getEntity($row, $old_destination_id_values);

    if (!$offers = $row->getSourceProperty('offers')) {
      return $entity;
    }

    foreach ($offers as $offer) {
      if (!isset($offer['is_free'])) {
        continue;
      }
      $entity->addOffer($offer);
    }

    return $entity;
  }

}
