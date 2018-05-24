<?php

namespace Drupal\thomas_more_ijsjes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\thomas_more_ijsjes\IjsjesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BeheerForm extends FormBase {

  protected $state;

  public function __construct(StateInterface $state, IjsjesManager $ijsjesManager) {
    $this->state = $state;
    $this->ijsjesManager = $ijsjesManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('thomas_more_ijsjes.ijsjes_manager')
    );
  }
  public function getFormId() {
    return 'thomas_more_ijsjes_beheer_form';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
      
    foreach($this->ijsjesManager->getSmaken() as $smaak){
        $form['smaken'. $smaak->id] = [
          '#type' => 'textfield',
          '#title' => 'Smaken',
          '#default_value' => $smaken[$smaak->id] = $this->t($smaak->naam),
        ];
    }
    foreach($this->ijsjesManager->getToppings() as $topping){
        $form['toppings'. $topping->id] = [
          '#type' => 'textfield',
          '#title' => 'Toppings',
          '#default_value' => $toppings[$topping->id] = $this->t($topping->naam),
        ];
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Opslaan',
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
   foreach($this->ijsjesManager->getSmaken() as $smaak){
        $this->ijsjesManager->changeSmaken($form_state->getValue('smaken'.$smaak->id), $smaak->id);
    }
    foreach($this->ijsjesManager->getToppings() as $topping){
        $this->ijsjesManager->changeToppings($form_state->getValue('toppings'. $topping->id), $topping->id);
    }
    drupal_set_message('De keuzes zijn opgeslagen');
  }
}
