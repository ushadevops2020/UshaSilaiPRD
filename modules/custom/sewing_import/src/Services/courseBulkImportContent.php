<?php
namespace Drupal\sewing_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class courseBulkImportContent {
  public static function courseAddImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Course Data Importing.. ' . $item;
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  
  function courseAddImportContentItemCallback($success, $results, $operations) {
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
  $node = Node::create(['type' => 'course_master']);
  $node->set('title',  $item[1]);
  $node->set('field_course_code', $item[0]);  
  $node->set('field_course_duration', $item[3]); 
  $node->set('field_course_fee', $item[4]); 
  $node->set('field_grade', $item[2]); 
  $node->status = 1;  
  $node->enforceIsNew();
  $node->save();
} 