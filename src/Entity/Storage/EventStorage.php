<?php

declare(strict_types = 1);

namespace Drupal\helfi_linkedevents\Entity\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * The entity storage class for Linked Events event entities.
 */
final class EventStorage extends SqlContentEntityStorage implements ContentEntityStorageInterface {
}
