<?php

namespace Drupal\thomas_more_ijsjes\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\thomas_more_ijsjes\IjsjesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Client;

class KeuzeForm extends FormBase {

  protected $state;
  protected $ijsjesManager;
  private $httpClient;
  const API_KEY= '47f5509d304ae752dcbfa70043775fe9';

  public function __construct(StateInterface $state, Client $httpClient, IjsjesManager $ijsjesManager) {
    $this->state = $state;
    $this->httpClient = $httpClient;
    $this->ijsjesManager = $ijsjesManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('http_client'),
      $container->get('thomas_more_ijsjes.ijsjes_manager')
    );
  }

  public function getFormId() {
    return 'thomas_more_ijsjes_keuze_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
   $smaken = array();
   $toppings = array();
   
   foreach($this->ijsjesManager->getSmaken() as $smaak){
     $smaken[$smaak->id] = $this->t($smaak->naam);
   }
   
   foreach($this->ijsjesManager->getToppings() as $topping){
     $toppings[$topping->id] = $this->t($topping->naam);
   }
   
   $form['naam'] = [
      '#type' => 'textfield',
      '#name' => 'naam',
      '#title' => 'Naam',
    ];
   
   $form['taal'] = [
      '#type' => 'textfield',
      '#name' => 'taal',
      '#title' => 'Taal',
    ];
   
   $form['geslacht'] = [
      '#type' => 'textfield',
      '#name' => 'geslacht',
      '#title' => 'Geslacht',
    ];
    
   $form['keuze'] = [
      '#type' => 'radios',
      '#options' => array(
        'Ijsje' => $this->t('Ijsje'),
        'Wafel' => $this->t('Wafel'),
      )
    ];
    
    $form['smaak'] = [
      '#type' => 'select',
      '#title' => 'Smaak van het ijsje',
      '#options' => $smaken,
      '#states' => array(
        'visible' => array(
          ':input[name="keuze"]' => array(
            'value' => 'Ijsje',
          ),
        ),
      ),
    ];
    $form['toppings'] = array(
      '#type' => 'checkboxes',
      '#name' => 'toppings',
      '#options' => $toppings,
      '#title' => $this->t('Toppings voor op de wafel'),
      '#states' => array(
        'visible' => array(
          ':input[name="keuze"]' => array(
            'value' => 'Wafel',
          ),
        ),
      ),
    );

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Opslaan',
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state){
    $steden = array
    (
      array("Geel","Belgium"),
      array("London","GREAT BRITAIN"),
    );
    $temperatuur = $this->getTemperatuur($steden[0][0])['main']['temp'];
    
    if($temperatuur >= 20){
      $keuze = $form_state->getValue('keuze');
      
      $id = $this->ijsjesManager->addKeuze($keuze, $form_state->getValue('smaak'), $form_state->getValue('naam'), $form_state->getValue('taal'), $form_state->getValue('geslacht'), Drupal::request()->getClientIp());
      
      foreach(array_filter($form_state->getValues()['toppings']) as $key => $value){
        $this->ijsjesManager->addKeuzeTopping($id, $key);
      }
      
      $i = $this->ijsjesManager->getCount($keuze);
      $i++;
      
      if($i >= $this->state->get('thomas_more_ijsjes.threshold_ijsje', 1) && $i >= $this->state->get('thomas_more_ijsjes.threshold_wafel', 1)){
        $this->ijsjesManager->sendEmail();
        
        $this->ijsjesManager->removeAllKeuzes($keuze);
        
        if($keuze === "Wafel"){
          $this->ijsjesManager->removeAllToppings();
        }
        
        drupal_set_message('Je keuzes zijn succesvol opgeslagen. De bestelling is verzonden!');
      }else{
        drupal_set_message('Je keuzes zijn succesvol opgeslagen.');
      }
    } else{
        drupal_set_message(t('De temperatuur is vandaag niet hoog genoeg voor een ijsje. Sorry!'), 'error');
    }
  }
  
  public function getTemperatuur($stad) {
        $request = $this->httpClient->get('http://api.openweathermap.org/data/2.5/weather',
            ['query' =>
                [
                    'appid' => self::API_KEY,
                    'q' => $stad . $steden[0][1],
                    'units' => 'metric',
                    'cnt' => 1
                ]
            ]
        );
        
        if ($request->getStatusCode() == 200) {
            return json_decode($request->getBody(), true);
        }
    }
}