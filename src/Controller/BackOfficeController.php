<?php

namespace Drupal\thomas_more_ijsjes\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\thomas_more_ijsjes\IjsjesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BackOfficeController extends ControllerBase {

  protected $ijsjesManager;

  public function __construct(IjsjesManager $ijsjesManager) {
    $this->ijsjesManager = $ijsjesManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('thomas_more_ijsjes.ijsjes_manager')
    );
  }

  public function buildKeuzes() {
    return [
      '#theme' => 'ijsjes',
      'keuzes' => $this->ijsjesManager->getKeuzes(),
    ];
  }

}
