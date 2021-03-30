<?php

declare(strict_types=1);

namespace Drupal\helfi_linkedevents\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\helfi_api_base\Entity\RemoteEntityBase;

/**
 * Defines the linkedevents_event entity class.
 *
 * @ContentEntityType(
 *   id = "linkedevents_event",
 *   label = @Translation("Linked Events - Event"),
 *   label_collection = @Translation("Linked Events - Event"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "storage" = "Drupal\helfi_linkedevents\Entity\Storage\EventStorage",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\helfi_api_base\Entity\Access\RemoteEntityAccess",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\helfi_api_base\Entity\Routing\EntityRouteProvider",
 *     }
 *   },
 *   base_table = "linkedevents_event",
 *   data_table = "linkedevents_event_field_data",
 *   revision_table = "linkedevents_event_revision",
 *   revision_data_table = "linkedevents_event_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer remote entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "uid" = "uid",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   revision_metadata_keys = {
 *     "revision_created" = "revision_timestamp",
 *     "revision_user" = "revision_user",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/linkedevents-event/{linkedevents_event}",
 *     "edit-form" = "/admin/content/linkedevents-event/{linkedevents_event}/edit",
 *     "delete-form" = "/admin/content/linkedevents-event/{linkedevents_event}/delete",
 *     "collection" = "/admin/content/linkedevents-event"
 *   },
 *   field_ui_base_route = "linkedevents_event.settings"
 * )
 */
final class Event extends RemoteEntityBase
{

  use RevisionLogEntityTrait;

  /**
   * Adds the given data source.
   *
   * @param array $offer
   *   The values for a single offer
   *
   * @return $this
   *   The self.
   */
  public function addOffer(array $offer): self
  {
    // $offer['is_free'], $offer['info_url'], $offer['description'], $offer['price']
    $this->get('offers')->appendItem($offer['price'] . " " . $offer['info_url']);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Title'))
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setCardinality(1)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ]);

    $fields['location'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Location'))
      ->setDefaultValue('')
      ->setCardinality(1)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['provider'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Provider'))
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setCardinality(1)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['short_description'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Short description'))
      ->setReadOnly(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setCardinality(1)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(new TranslatableMarkup('Description'))
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'rows' => 6,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // info url
    $fields['info_url'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Info URL'))
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setCardinality(1)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // start time: datetime
    $fields['start_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start time'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // end time: datetime
    $fields['end_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End time'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // offers
    $fields['offers'] = BaseFieldDefinition::create('list_string')
      ->setLabel(new TranslatableMarkup('Offers'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // sub_events 1-n string
    $fields['sub_events'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Sub events'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDefaultValue('')
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // keywords 1-n
    $fields['keywords'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Keywords'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDefaultValue('')
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // images (links only)
    $fields['images'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Images'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDefaultValue('')
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // videos (links only)
    $fields['videos'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Videos'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDefaultValue('')
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // obsolete
    $fields['origins'] = BaseFieldDefinition::create('key_value')
      ->setLabel(new TranslatableMarkup('Origins'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }
}

/*
  name x
  location
    @id : "https://api.hel.fi/linkedevents/v1/place/tprek:21319/"
  provider x
  description x
  short_description x
  info_url x
  start_time
  end_time
  offers
    "is_free": false,
    "info_url": {
        "fi": "https://www.designmuseum.fi/",
        "sv": "https://www.designmuseum.fi/",
        "en": "https://www.designmuseum.fi/"
    },
    "description": {
        "fi": "",
        "sv": "",
        "en": ""
    },
    "price": {
        "fi": "https://www.designmuseum.fi/",
        "sv": "https://www.designmuseum.fi/",
        "en": "https://www.designmuseum.fi/"
    }
  images
    "id": 68000,
    "license": "event_only",
    "created_time": "2020-11-10T13:56:51.426605Z",
    "last_modified_time": "2020-11-10T13:56:51.426631Z",
    "name": "Iittala -kaleidoskooppi",
    "url": "https://api.hel.fi/linkedevents/media/images/Iittala_pressikuva.jpg",
    "cropping": "768,0,2067,1299",
    "photographer_name": "Designmuseo / Iittala",
    "alt_text": "Lasia ja lasinpuhaltajia",
    "data_source": "helsinki",
    "publisher": "ytj:0586977-6",
    "@id": "https://api.hel.fi/linkedevents/v1/image/68000/",
    "@context": "http://schema.org",
    "@type": "ImageObject"
  videos
    ...
  sub_events
    {
      "@id": "https://api.hel.fi/linkedevents/v1/event/helsinki:af4grg2osu/"
    },
    {
      "@id": "https://api.hel.fi/linkedevents/v1/event/helsinki:af4grg2od4/"
    }
  keywords
    {
      "@id": "https://api.hel.fi/linkedevents/v1/keyword/kulke:31/"
    },
    {
      "@id": "https://api.hel.fi/linkedevents/v1/keyword/kulke:49/"
    }
*/