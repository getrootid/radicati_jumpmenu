<?php

namespace Drupal\radicati_jumpmenu\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Creates a field formatter that outputs a jump menu.
 * It can be applied to entity reference and entity reference revisions fields.
 * Generally this mean that it can be used on blocks and paragraphs.
 * The user can select a text field to use as the title in the jump menu, and
 * also select a content field.
 *
 * @FieldFormatter(
 *   id = "radicati_jumpmenu",
 *   label = @Translation("Jump Menu"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class JumpMenuFormatter extends FormatterBase {

  public static function defaultSettings() {
    return [
      'title_field' => '',
      'content_field' => '',
    ] + parent::defaultSettings();
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $title_field = $this->getSetting('title_field');
    $content_field = $this->getSetting('content_field');

    // Get the titles from the title_field and create an array of css id's that and the content
    // of the title field. The id will be used in the link to the content in the jump menu.
    $titles = [];
    $ids = [];
    foreach ($items as $delta => $item) {
      $entity = $item->entity;
      $title = $entity->get($title_field)->value;
      $titles[$delta] = $title;
      $ids[$delta] = Html::getUniqueId($title);
    }

    $menu = [
      '#theme' => 'radicati_jumpmenu__menu',
      '#menu_items' => $titles,
      '#ids' => $ids,
      '#attributes' => [
        'class' => ['radicati-jumpmenu__menu-wrapper']
      ]
    ];

    $output = [];

    foreach ($items as $delta => $item) {
      $entity = $item->entity;
      $content = $entity->get($content_field)->view();

      $output[] = [
        '#theme' => 'radicati_jumpmenu__item',
        '#attributes' => [
          'id' => $ids[$delta],
          'class' => ['radicati-jumpmenu__item']
        ],
        '#item' => $content
      ];
    }


    $elements = [
      '#theme' => 'radicati_jumpmenu',
      '#menu' => $menu,
      '#items' => $output,
      '#attached' => [
        'library' => [
          'radicati_jumpmenu/jumpmenu'
        ]
      ],
      '#attributes' => [
        'class' => ['radicati-jumpmenu']
      ]
    ];

//    foreach ($items as $delta => $item) {
//      $entity = $item->entity;
//
//      $title = $entity->get($title_field)->value;
//      $content = $entity->get($content_field)->value;
//
//      $elements[$delta] = [
//        '#theme' => 'radicati_jumpmenu',
//        '#title' => $title,
//        '#content' => $content,
//      ];
//    }

    return $elements;
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $title_field = $this->getSetting('title_field');
    $content_field = $this->getSetting('content_field');

    $form['title_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title field'),
      '#default_value' => $title_field,
      '#description' => $this->t('The field that will be used as the title in the jump menu.'),
    ];

    $form['content_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Content field'),
      '#default_value' => $content_field,
      '#description' => $this->t('The field that will be used as the content in the jump menu.'),
    ];

    return $form;
  }

  

  public function settingsSummary() {
    $summary = [];

    $title_field = $this->getSetting('title_field');
    $content_field = $this->getSetting('content_field');

    if (!empty($title_field)) {
      $summary[] = $this->t('Title field: @title_field', ['@title_field' => $title_field]);
    }

    if (!empty($content_field)) {
      $summary[] = $this->t('Content field: @content_field', ['@content_field' => $content_field]);
    }

    return $summary;
  }
}