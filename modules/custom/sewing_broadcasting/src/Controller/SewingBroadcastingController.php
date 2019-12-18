<?php
namespace Drupal\sewing_broadcasting\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\AccessResult; 
use Drupal\Core\Form\FormInterface;
use Drupal\user\Entity\User;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Class BroadcastingController.
 */
class SewingBroadcastingController extends ControllerBase {

  /**
   * Delete Notification
   * @params: $id as integer
   * @return: Boolean value
  */
  public function deleteNotification() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['id'];
    $database->delete('sewing_broadcasting')->condition('id', $id)->execute();
    $return = ['data' => $results, STATUS => 1];
    drupal_set_message(t('Item has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }

  public function readNotification($notification_id) { 
    $result = \Drupal::database()->select('sewing_broadcasting', 'n')
            ->fields('n', array('subject', 'message', 'filepath'))->condition('id', $notification_id)
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
    $checkStatus = $_GET['sent'];
    if (!isset($checkStatus)) {
      $status_update = \Drupal::database()->update('sewing_broadcasting')->fields(['status' => 1, ])->condition('id', $notification_id)->execute();
    }
    foreach ($result as $row => $content) {
      $subject = $content->subject;
      $message = $content->message;
      $file_id = $content->filepath;
    }
    // echo $file_id;die;
    //Loading file 
    $file_object = File::load($file_id);
    if($file_object) {
      $file_uri = $file_object->getFileUri();
      $file_url = Url::fromUri(file_create_url($file_uri))->toString();
      if (!empty($file_url)) {
        # code...
        $checked_file = $file_url;
      } else{
        $checked_file = '';
      }
    } else {
      $checked_file = '';
    }
        //echo $checked_file;die;
    return [
            '#title' => 'Notification',
            '#theme' => 'read_notification_board',
            '#subject' => $subject,
            '#message' => $message,
            '#filepath' =>$checked_file,
           ];
  }
}	