<?php

namespace Drupal\thomas_more_ijsjes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsForm extends FormBase {

  protected $state;

  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }
  public function getFormId() {
    return 'thomas_more_ijsjes_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['threshold_ijsjes'] = [
      '#type' => 'number',
      '#title' => 'Threshold ijsjes',
      '#min' => '1', 
      '#default_value' => $this->state->get('thomas_more_ijsjes.threshold_ijsje', 1),
    ];
    
    $form['threshold_wafels'] = [
      '#type' => 'number',
      '#title' => 'Threshold wafels',
      '#min' => '1', 
      '#default_value' => $this->state->get('thomas_more_ijsjes.threshold_wafel', 1),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Opslaan',
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->state->set('thomas_more_ijsjes.threshold_ijsje', $form_state->getValue('threshold_ijsjes'));
    $this->state->set('thomas_more_ijsjes.threshold_wafel', $form_state->getValue('threshold_wafels'));
    drupal_set_message('De thresholds zijn opgeslagen');
  }
}
