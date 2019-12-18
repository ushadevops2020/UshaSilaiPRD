<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class townBulkImportContent {
  public static function townAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Town Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }

  function townAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'manage_towns']);
  $node->set('title', $item[1]);
  $node->set('field_town_code', $item[0]);
  $node->set('field_district', $item[2]);
  //$node->set('field_town_pincode', $item[3]);
  $node->set('field_business_state', $item[4]);
  $node->set('field_location', $item[5]);
  $node->set('field_country', $item[6]);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 