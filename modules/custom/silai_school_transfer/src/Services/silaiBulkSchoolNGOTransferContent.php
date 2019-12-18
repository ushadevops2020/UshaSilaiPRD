<?php
namespace Drupal\silai_school_transfer\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Database\Database;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;

class silaiBulkSchoolNGOTransferContent {
  public static function silaiBulkSchoolNGOTransferContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'School NGO Transfer Updating.. ' . $item;
    $results = array();
    update_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function silaiBulkSchoolNGOTransferContentItemCallback($success, $results, $operations) {
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

// This function actually creates each item as a node as type 'Page'
function update_node($item) {
	$node = \Drupal\node\Entity\Node::load($item[0]); 
	$node->set('field_name_of_ngo', $item[1]);
	$node->save();
} 