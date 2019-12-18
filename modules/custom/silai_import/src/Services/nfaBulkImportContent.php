<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

class nfaBulkImportContent {
  public static function nfaAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'NFA Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function nfaAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'nfa']);
  $node->set('field_fiscal_year', $item[0]);
  $node->set('field_date_of_saction', $item[1]);
  $node->set('field_budget_head', $item[2]);
  $node->set('field_sub_head_budgeted', $item[3]);
  $node->set('title', $item[4]);
  $node->set('field_sactioned_amount', $item[5]);
  $node->set('field_silai_location', $item[6]);
  $node->set('field_silai_business_state', $item[7]);
  $node->status = 1;
  $node->enforceIsNew();
  $node->save();
} 