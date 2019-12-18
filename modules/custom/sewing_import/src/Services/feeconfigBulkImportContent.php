<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class feeconfigBulkImportContent {
  public static function feeconfigAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Fee Configuration Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function feeconfigAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'grade_master']);
  $node->set('title', 'Node Fee Config');
  $node->set('field_grades_grade', $item[0]);  
  $node->set('field_school_type', $item[2]); 
  $node->set('field_affiliation_fees', $item[3]); 
  $node->set('field_renewal_fees', $item[4]); 
  $node->set('field_payable_to_uil', $item[5]); 
  $node->status = 1;  
  $node->enforceIsNew();
  $node->save();
} 