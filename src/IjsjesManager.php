<?php
namespace Drupal\thomas_more_ijsjes;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;

class IjsjesManager {

  protected $connection;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function addKeuze(string $keuze, int $smaak_keuze_id, string $naam, string $taal, string $geslacht, string $ip) {
    return $this->connection->insert('thomas_more_keuze')
      ->fields([
        'keuze' => $keuze,
        'smaak_keuze_id' => $smaak_keuze_id,
        'naam' => $naam,
        'taal' => $taal,
        'geslacht' => $geslacht,
        'ip_adres' => $ip,
      ])->execute();
  }

  public function addKeuzeTopping(int $wafel_id, int $topping_id) {
    return $this->connection->insert('thomas_more_wafel_relatie_topping')
      ->fields([
        'wafel_id' => $wafel_id,
        'topping_id' => $topping_id,
      ])->execute();
  }
  
  public function getCount(string $keuze){
    $query = $this->connection->select('thomas_more_keuze');
    $query->condition('keuze', $keuze);
    return (int) $query->countQuery()->execute()->fetchField();
  }
  
  public function getKeuzes(){
    $query = $this->connection->select('thomas_more_keuze', 't');
    return $query->execute()->fetchAll();
  }
  
  public function removeAllKeuzes(string $keuze){
    $query = $this->connection->delete('thomas_more_keuze');
    $query->condition('keuze', $keuze);
    $query->execute();
  }
  
  public function removeAllToppings(){
    $query = $this->connection->delete('thomas_more_wafel_relatie_topping');
    $query->execute();
  }
  
  public function getSmaken(){
    $query = $this->connection->select('thomas_more_ijsjes_smaak', 's');
    $query->fields('s', ['id', 'naam']);
    return $query->execute()->fetchAll();
  }
  
  public function getToppings(){
    $query = $this->connection->select('thomas_more_ijsjes_topping', 't');
    $query->fields('t', ['id', 'naam']);
    return $query->execute()->fetchAll();
  }
  
  public function getSmaakMetID($id){
    $query = $this->connection->select('thomas_more_ijsjes_smaak', 's');
    $query->condition('s.id', $id);
    $query->fields('s', ['naam']);
    return $query->execute()->fetchField();
  }
  
  public function getToppingsBijKeuze(){
    $query = $this->connection->select('thomas_more_ijsjes_topping', 't');
    $query->fields('t', ['id', 'naam']);
    return $query->execute()->fetchAll();
  }
  
  public function changeToppings(){
    return $this->connection->update('thomas_more_ijsjes_topping', 't')
      ->fields([
        'id' => $wafel_id,
        'topping_id' => $topping_id,
      ])->execute();
  }
    
  public function changeSmaken(){
    return $this->connection->update('thomas_more_ijsjes_smaak', 't')
      ->fields([
        'wafel_id' => $wafel_id,
        'topping_id' => $topping_id,
      ])->execute();
  }
  
  function sendEmail(){
    $msg = "Er is een bestelling geplaatst!";
    
    $params = array(
      'subject' => t('Nieuwe bestelling!'),
      'body' => check_markup(
        t($msg),
        'plain_text'
      ),
    );
    
    \Drupal::service('plugin.manager.mail')->mail('thomas_more_ijsjes', 'nieuwe_bestelling', "jeroen.tubex@intracto.com", "nl", $params, NULL, true);
  }
}