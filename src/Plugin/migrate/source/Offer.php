<?php

declare(strict_types = 1);

namespace Drupal\helfi_linkedevents\Plugin\migrate\source;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for retrieving data from Linked Events.
 *
 * @MigrateSource(
 *   id = "linkedevents_offer",
 * )
 */
class Offer extends SourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, EntityTypeManagerInterface $entity_type_manager) {
    $this->storage = $entity_type_manager->getStorage('linkedevents_event');

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['id' => ['type' => 'string']];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'LinkedEventsOffer';
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator() {
    /** @var \Drupal\helfi_linkedevents\Entity\Event $entity */
    foreach ($this->storage->loadMultiple() ?? [] as $entity) {
      $data = $entity->getData('offer');
      if ($data) {
        $data['id'] = hash('sha256', json_encode($data));
        $data['langcode'] = $entity->language()->getId();
        $data['parent_id'] = $entity->id();
        yield $data;
      }
    }
  }

}
