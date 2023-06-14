<?php

declare(strict_types=1);

namespace Drupal\ucb_ck5_link_styles\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginElementsSubsetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Link Styles plugin.
 */
class LinkStyles extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface, CKEditor5PluginElementsSubsetInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function getElementsSubset(): array {
    if (empty($this->configuration['styles'])) {
      return [];
    }
    return ['<a class>'];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'styles' => [
        [
          'label' => 'Color: Blue Button',
          'element' => 'ucb-button ucb-button-blue',
          'enabled' => false,
        ],
        [
          'label' => 'Color: Gold Button',
          'element' => 'ucb-button ucb-button-gold',
          'enabled' => false,
        ],
        [
          'label' => 'Color: Black Button',
          'element' => 'ucb-button ucb-button-black',
          'enabled' => false,
        ],
        [
          'label' => 'Color: Grey Button',
          'element' => 'ucb-button ucb-button-grey',
          'enabled' => false,
        ],
        [
          'label' => 'Color: White Button',
          'element' => 'ucb-button ucb-button-white',
          'enabled' => false,
        ],
        [
          'label' => 'Size: Large Button',
          'element' => 'ucb-button ucb-button-large',
          'enabled' => false,
        ],
        [
          'label' => 'Size: Regular Button',
          'element' => 'ucb-button ucb-button-regular',
          'enabled' => false,
        ],
        [
          'label' => 'Size: Small Button',
          'element' => 'ucb-button ucb-button-small',
          'enabled' => false,
        ],
        [
          'label' => 'Style: Regular Button',
          'element' => 'ucb-button ucb-button-default',
          'enabled' => false,
        ],
        [
          'label' => 'Style: Full Button',
          'element' => 'ucb-button ucb-button-full',
          'enabled' => false,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['styles'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Link Styles'),
      '#description' => $this->t('Select one or more link styles.'),
    ];

    $configuredStyles = $this->configuration['styles'];

    // Generate checkboxes dynamically based on configured styles.
    foreach ($configuredStyles as $style) {
      $form['styles'][$style['element']] = [
        '#type' => 'checkbox',
        '#title' => $style['label'],
        '#default_value' => $style['enabled'],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $styles = [];
    foreach ($form_state->getValue('styles') as $selector => $value) {
      if ($value) {
        $styles[] = [
          'label' => $form['styles'][$selector]['#title'],
          'element' => $selector,
          'enabled' => true,
        ];
      }
    }
    $form_state->setValue('styles', $styles);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['styles'] = $form_state->getValue('styles');
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $config = parent::getDynamicPluginConfig($static_plugin_config, $editor);

    if (empty($this->configuration['styles'])) {
      return $config;
    }

    $config['link']['decorators'] = [];

    foreach ($this->configuration['styles'] as $style) {
      $config['link']['decorators'][] = [
        'mode' => 'manual',
        'label' => $style['label'],
        'attributes' => [
          'class' => $style['element'],
        ],
      ];
    }

    return $config;
  }

}
