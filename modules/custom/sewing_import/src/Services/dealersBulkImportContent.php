<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class dealersBulkImportContent {
  public static function dealersAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Dealers Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  
  function dealersAddImportContentItemCallback($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One item processed.', '@count items successfully processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}

function create_node($item) {
  $node = Node::create(['type' => 'dealer']);
  $node->set('title', $item[0]);
  $node->set('field_dealer_code', $item[1]);
  $node->set('field_dealer_contact_no', $item[2]);
  //$node->set('field_district', $item[3]);
  //$node->set('field_town_city', $item[4]);
  $node->set('field_street', $item[5]);
  $node->set('field_dealer_pin_code', $item[6]);
  $node->set('field_locality', $item[7]);
  $node->set('field_dealer_latitude', $item[8]);
  $node->set('field_dealer_longitude', $item[9]);
  //$node->set('field_business_state', $item[10]);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 