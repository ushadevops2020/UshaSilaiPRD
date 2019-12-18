<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class blocksBulkImportContent {
  public static function blocksAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Blocks Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function blocksAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'silai_blocks']);
  $node->set('title', $item[0]);
  $node->set('field_silai_district', $item[1]);
  $node->set('field_silai_country', $item[2]);
  $node->set('field_silai_location', $item[3]);
  $node->set('field_silai_business_state', $item[4]);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 