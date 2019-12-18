<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class villageBulkImportContent {
  public static function villageAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Villages Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function villageAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'silai_villages']);
  $node->set('title', $item[0]);
  $node->set('field_silai_block', $item[1]);
  $node->set('field_silai_pin_code', $item[2]);
  $node->set('field_silai_vilage_poplation', $item[3]);
  $node->set('field_silai_post_office', $item[4]);
  $node->set('field_silai_no_of_households', $item[5]);
  $node->set('field_silai_electrification', $item[6]);
  $node->set('field_silai_near_by_place', $item[7]);
  $node->set('field_nearest_place_distance', $item[8]);
  $node->set('field_silai_vill_other_details', $item[9]);
  $node->set('field_silai_district', $item[10]);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 