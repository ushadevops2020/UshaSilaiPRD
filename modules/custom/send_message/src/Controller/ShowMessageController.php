<?php

namespace Drupal\send_message\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
/**
 * Defines HelloController class.
 */
class ShowMessageController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  protected $formBuilder;

  /**
   * 
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
  public function showMessage($message_id) { 
    $result = \Drupal::database()->select('send_message', 'n')
            ->fields('n', array('subject', 'message', 'filepath'))->condition('id', $message_id)
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
    $checkStatus = $_GET['sent'];
    if (!isset($checkStatus)) {
      $status_update = \Drupal::database()->update('send_message')->fields(['status' => 1, ])->condition('id', $message_id)->execute();
    }
    foreach ($result as $row => $content) {
      $subject = $content->subject;
      $message = $content->message;
      $file_id = $content->filepath;
    }
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

    return [
            '#title' => 'Message',
            '#theme' => 'read_message_board',
            '#subject' => $subject,
            '#message' => $message,
            '#filepath' =>$checked_file,
           ];
  }

  /**
   * Delete Message
   * @params: $id as integer
   * @return: Boolean value
  */
  public function deleteMessage() {
    $database = Database::getConnection();
    $results = array();
    $id = $_REQUEST['id'];
    $database->delete('send_message')->condition('id', $id)->execute();
    $return = ['data' => $results, STATUS => 1];
    drupal_set_message(t('Item has been deleted successfully.'), 'status');
    return new JsonResponse($return);
  }
}
