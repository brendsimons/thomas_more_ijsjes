<?php

namespace Drupal\thomas_more_ijsjes\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a ijsjes menu block.
 *
 * @Block(
 *  id = "thomas_more_ijsjes_block",
 *  admin_label = @Translation("Ijsjes"),
 * )
 */
class IjsjesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $state;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, StateInterface $state ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->state = $state;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      'form' => \Drupal::formBuilder()->getForm('Drupal\thomas_more_ijsjes\Form\KeuzeForm'), 
      
    ];
  }
}
