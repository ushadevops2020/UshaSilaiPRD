<?php
namespace Drupal\mis;

use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;


class addImportContent {

  public static function addImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Creating ' . $item['title'];
    $results = array();
    upload_weekly_mis($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function addImportContentItemCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One item processed.', '@count items processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}

// This function actually creates each item as a node as type 'Page'
function upload_weekly_mis($item) {
  $userId = \Drupal::currentUser()->id();
  $database = Database::getConnection(); 
  $i= 0; $dataArr = array();
  foreach(WEEKLY_MIS_UPLOAD_FIELDS as $key=>$value) {
      if($i==1 || $i==2) {
        $item[$i] = time();
      } elseif($i==35 || $i ==36) {
        $item[$i] = $item[$i] == 'Yes'?1:0;
      }
      $dataArr[$value] = $item[$i];
      $dataArr['created_by'] = $userId;
      $dataArr['created_date'] = time();
      $i++;
  }
  $database->insert('usha_weekly_mis')->fields($dataArr)->execute();
} 