<?php
namespace Drupal\silai_import\Services;

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class schoolBulkImportUpdate {
  public static function schoolUpdateItem($item, &$context){ 
    $context['sandbox']['current_item'] = $item;
    $message = 'School Data Importing.. ' . $item;
    $results = array();
    update_school_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function schoolUpdateItemCallback($success, $results, $operations) {
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

function update_school_node($item) {
  $node = \Drupal\node\Entity\Node::load($item[0]); 
  $node->set('field_silai_teacher_age', $item[1]);
  $node->save();
} 