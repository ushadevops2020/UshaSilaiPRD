<?php
/**
 * @file
 * Contains \Drupal\sewing_broadcasting\Form\SendNotificationForm.
 */
namespace Drupal\sewing_broadcasting\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Routing\TrustedRedirectResponse;

use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\file\Entity\File;

class SendNotificationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'send_notification_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $messageId = $_GET['data'];
    if($messageId) {
      $result = \Drupal::database()->select('sewing_broadcasting', 'n')
            ->fields('n', array('subject', 'message', 'filepath'))->condition('id', $messageId)
            ->execute()->fetchAll(\PDO::FETCH_OBJ);
      foreach ($result as $row => $content) {
        $subject = $content->subject;
        $message = $content->message;
        $file_id = $content->filepath;
      }
    }
    $masterDataService = \Drupal::service('sewing.master_data');
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $accountRoles = $user->getRoles();
    $locationArr = $masterDataService->getLocationByCountryId();
    $userLocation =  $user->field_user_location->target_id;
    $schoolbylocation = $masterDataService->getSchoolBylocationId($userLocation);
    $school = $masterDataService->getSchool();

    $form[HASH_PREFIX] = '<div id="wrapper_modal_send_notification_form">';
    $form[HASH_SUFFIX] = '</div>';

    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];
    $schoolbylocation[''] = '-Select-';
    asort($schoolbylocation);    
    $school[''] = '-Select-';
    asort($school);


    if (in_array(ROLE_SEWING_SSI, $accountRoles)) {
      $form['school'] = array (
          HASH_TYPE => SELECTFIELD,
          HASH_TITLE => ('School Code'),
          HASH_OPTIONS => $schoolbylocation,
          HASH_MULTIPLE => TRUE,
          HASH_STATES => [
            'disabled' => [':input[name="all_school[1]"]' => array('checked' => TRUE)]
          ],
      );
      if(!empty($messageId)) {
        $form['school'] = [
          HASH_TYPE => 'hidden',
        ];
      }
    }
    if (in_array(ROLE_SEWING_HO_ADMIN, $accountRoles) || in_array(ROLE_SEWING_HO_USER, $accountRoles)) {
      $form['location'] = array (
          HASH_TYPE => SELECTFIELD,
          HASH_TITLE => ('Location'),
          HASH_OPTIONS => $locationArr,
          HASH_MULTIPLE => TRUE,
      );
      if(!empty($messageId)) {
        $form['location'] = [
          HASH_TYPE => 'hidden',
        ];
      }
      
      $form['school'] = array (
          HASH_TYPE => SELECTFIELD,
          HASH_TITLE => ('School Code'),
          HASH_OPTIONS => $school,
          HASH_MULTIPLE => TRUE,
          HASH_STATES => [
            'disabled' => [':input[name="all_school[1]"]' => array('checked' => TRUE)]
          ],
      );
      if(!empty($messageId)) {
        $form['school'] = [
          HASH_TYPE => 'hidden',
        ];
      }
    }
    $form['all_school'] = [
        HASH_TYPE => 'checkboxes',
        HASH_OPTIONS => [
           '1' => t('Send to all School')
        ],
        HASH_TITLE => t(''),
        HASH_ATTRIBUTES => array('class' => array('send-all-school-class')),
    ];
    if(!empty($messageId)) {
      $form['all_school'] = [
        HASH_TYPE => 'hidden',
      ];
    }
    $form['subject'] = array(
      HASH_TYPE => TEXTFIELD,
      HASH_PREFIX => '<div id="message-section">',
      HASH_TITLE => t('Subject'),
      HASH_DEFAULT_VALUE => ($subject) ? $subject : '',
    );
    $form['message'] = array(
		HASH_TYPE => 'text_format',
      //HASH_TYPE => TEXTAREAFIELD,
		HASH_TITLE => t('Message'),
		'#format' => 'full_html',
		HASH_DEFAULT_VALUE => ($message) ? $message : '',
		HASH_PREFIX => '<div id="message-section-textarea">',
		HASH_SUFFIX => '</div>',
    );
    $form['notification_file'] = array(
      HASH_TYPE => FILEFIELD,
      HASH_TITLE => t('Upload Doc'),
      HASH_REQUIRED => FALSE,
      HASH_SUFFIX => '</div>', 
      HASH_UPLOAD_LOCATION => 'public://send_message/',
      HASH_DEFAULT_VALUE => ($file_id) ? array($file_id) : '',
      HASH_UPLOAD_VALIDATORS  => array(
        'file_validate_extensions' => array('jpg jpeg png doc docx pdf xls'),
      ),
      '#description' => t('Allowed file extensions: jpg jpeg png doc docx pdf xls.'),
    );

    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    if (in_array(ROLE_SEWING_SSI, $accountRoles)) {
      $form[ACTIONS]['cancel'] = array(
        HASH_TYPE => 'button',
        HASH_VALUE => t('Close'),
        HASH_WEIGHT => -1,
        HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "/sewing-broadcast-notification-ssi"; event.preventDefault();')
      );
    }    
    if (in_array(ROLE_SEWING_HO_ADMIN, $accountRoles) || in_array(ROLE_SEWING_HO_USER, $accountRoles)) {
      $form[ACTIONS]['cancel'] = array(
        HASH_TYPE => 'button',
        HASH_VALUE => t('Close'),
        HASH_WEIGHT => -1,
        HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "/sewing-broadcast-notification"; event.preventDefault();')
      );
    }
    if($messageId){
       $form[ACTIONS]['send'] = [
        HASH_TYPE => 'submit',
        HASH_VALUE => t('Update'),
        HASH_ATTRIBUTES => ['class' => ['use-ajax']],
        '#ajax' => ['callback' => [$this, 'SendNotificationAjax'],'event' => 'click'],
      ];
    }else{
      $form[ACTIONS]['send'] = [
        HASH_TYPE => 'submit',
        HASH_VALUE => t('Send'),
        HASH_ATTRIBUTES => ['class' => ['use-ajax']],
        '#ajax' => ['callback' => [$this, 'SendNotificationAjax'],'event' => 'click'],
      ];
    }
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    return $form;
  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function SendNotificationAjax(array $form, FormStateInterface $form_state) {
// echo "test";die('dieeee');
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $accountRoles = $user->getRoles();
    $response = new AjaxResponse();

    if ($form_state->hasAnyErrors()) {
      // echo "test has errors sdsd";die;
      $response->addCommand(new ReplaceCommand('#wrapper_modal_send_notification_form', $form));
      return $response;
    }
    else {
       // echo "test has No error";die;
      $command = new CloseModalDialogCommand();
      $response->addCommand($command);
      if (in_array(ROLE_SEWING_SSI, $accountRoles)) {
        $response->addCommand(new RedirectCommand('/sewing-broadcast-notification-ssi'));
      }else{
        $response->addCommand(new RedirectCommand('/sewing-broadcast-notification'));
      }
      return $response;
    } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $user = User::load(\Drupal::currentUser()->id());
    $accountRoles = $user->getRoles();
    if (in_array(ROLE_SEWING_SSI, $accountRoles)) {
      $schoolCodeArr  = $form_state->getValue('school');
      $finalschoolarr = array_values($schoolCodeArr);
      $allSchool      = $form_state->getValue('all_school');
      if($allSchool[1] == 0 && (count($finalschoolarr) == 1 && empty($finalschoolarr[0]))) {
        $form_state->setErrorByName('all_school', t('Select at least one of SEND TO ALL SCHOOL / SCHOOL CODE.')); 
        $form_state->setErrorByName('school', t('')); 
      }
    }
    if (in_array(ROLE_SEWING_HO_ADMIN, $accountRoles) || in_array(ROLE_SEWING_HO_USER, $accountRoles)) {
      $schoolCodeArr  = $form_state->getValue('school');
      $finalschoolarr = array_values($schoolCodeArr);
      $allSchool      = $form_state->getValue('all_school');
      $location       = $form_state->getValue('location');

      if($allSchool[1] == 0 && empty($location) && (count($finalschoolarr) == 1 && empty($finalschoolarr[0]))) {
        $form_state->setErrorByName('all_school', t('Select at least one of SEND TO ALL SCHOOL / LOCATION / SCHOOL CODE.'));
        $form_state->setErrorByName('school', t(''));
        $form_state->setErrorByName('location', t(''));
      }
    }
  }

  /**
   * Method to add and update broadcast notification using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $masterDataService = \Drupal::service('sewing.master_data');
    $user              = User::load(\Drupal::currentUser()->id());
    $accountRoles      = $user->getRoles();
    $uid               = $user->id();
    $filePath          = $form_state->getValue('notification_file');
    $subject           = $form_state->getValue('subject');
    $message           = $form_state->getValue('message');
    $location          = $form_state->getValue('location');
    $allSchool         = $form_state->getValue('all_school');
    $schoolCodeArr     = $form_state->getValue('school');
    $sent_date_time    = date("Y-m-d H:i:s");

    $messageId = $_GET['data'];
    $database = Database::getConnection(); 
    if (isset($messageId) && $messageId != '') {
      $dataArr['subject']       = $subject;
      $dataArr['message']       = $message['value'];
      $dataArr['status']        = '0';
      $dataArr['filepath']      = $filePath[0];
      $dataArr['modified_on']   = $sent_date_time;
      $query = $database->update('sewing_broadcasting')->fields($dataArr)->condition('id', $messageId)->execute();
      drupal_set_message("Notification has been successfully updated");
    }
    else {
      if (in_array(ROLE_SEWING_SSI, $accountRoles)) {
        $receiverIdArray = [];

        if (isset($allSchool[1]) && $allSchool[1] == 1) {
          $masterDataService   = \Drupal::service('sewing.master_data');
          $userLocation        =  $user->field_user_location->target_id;
          $schoolbylocationArr = $masterDataService->getSchoolBylocationId($userLocation);
          foreach ($schoolbylocationArr as $key => $value) {
            $node            = Node::load($key);
            $userId          = $node->field_sewing_user_id->target_id;
            $receiverIdArray[] = $userId;
          }
        }
        if (isset($allSchool[1]) && $allSchool[1] == 0) {
          if (isset($schoolCodeArr)) {
            foreach($schoolCodeArr as $key => $value) {
              $node            = Node::load($key);
              $userId          = $node->field_sewing_user_id->target_id;
              $receiverIdArray[] = $userId;
            }
          }
        }
        // print_r($receiverIdArray);die;
        $finalString   = ',' . implode (",", $receiverIdArray) .',';
        $commaSeperatedArray   =  implode (",", $receiverIdArray);
        $final_array = rtrim($commaSeperatedArray,', ');
        $explode_array = explode(",", $final_array);
        $numberOfNotifications = count($explode_array);

        // print_r($numberOfNotifications);
        // die();
        // echo "<br> " . $numberOfNotifications;
        // die('byeeeeeeee');
        $dataArr = array(
          'sender_id'          => $uid,
          'sender_role'        => $accountRoles[1],
          'receiver_id_arr'    => $finalString,
          'subject'            => $subject,
          'message'            => $message['value'],
          'sent_date'          => $sent_date_time,
          'status'             => '0',
          'filepath'           => $filePath[0],
          'count'              => $numberOfNotifications,
        );

        $database = Database::getConnection();
        if(empty($messageId)) {
          $query = $database->insert('sewing_broadcasting')->fields($dataArr)->execute();
          // print_r($query);die('diee');
          drupal_set_message('Notification sent successfully.');
        }
      }
      // send message to SSI user for the selected location
      if (in_array(ROLE_SEWING_HO_ADMIN, $accountRoles) || in_array(ROLE_SEWING_HO_USER, $accountRoles)) {
        $receiverIdArray = [];

        $finalLocationarr = array_values($location);
        if (count($finalLocationarr) != 0) {
          foreach($location as $key => $value){
            $receivedUserArr = $masterDataService->getSewingUsersByLocation($key, [ROLE_SEWING_SSI]);
            foreach($receivedUserArr as $key1 => $value1){
              $receivedUserId   = $value1;
              $receiverIdArray[] = $receivedUserId;
            }
          }
        }
        if (isset($allSchool[1]) && $allSchool[1] == 1) {
          $masterDataService = \Drupal::service('sewing.master_data');
          $schoolbylocationArr = $masterDataService->getSchool();
          foreach ($schoolbylocationArr as $key => $value) {
            $node              = Node::load($key);
            $userId            = $node->field_sewing_user_id->target_id;
            $receiverIdArray[] = $userId;
          }
        }
        if (isset($allSchool[1]) && $allSchool[1] == 0) {
          if (isset($schoolCodeArr)) {
            foreach($schoolCodeArr as $key => $value) {
              $node              = Node::load($key);
              $userId            = $node->field_sewing_user_id->target_id;
              $receiverIdArray[] = $userId;
            }
          }
        }
        $finalString   = ',' . implode (",", $receiverIdArray) .',';
        $commaSeperatedArray   =  implode (",", $receiverIdArray);
        $final_array = rtrim($commaSeperatedArray,', ');
        
        $explode_array = explode(",", $final_array);
        $numberOfNotifications = count($explode_array);
        // echo $numberOfNotifications;die;
        // $final_array_testtt = substr_replace( $commaSeperatedArray, 'testt', $numberOfNotifications, 0);
        // print_r($finalString);
        // die;
        // echo "<br> " . $numberOfNotifications;
        // die('byeeeeeeee');
        $dataArr = array(
          'sender_id'          => $uid,
          'sender_role'        => $accountRoles[1],
          'receiver_id_arr'    => $finalString,
          'subject'            => $subject,
          'message'            => $message['value'],
          'sent_date'          => $sent_date_time,
          'status'             => '0',
          'filepath'           => $filePath[0],
          'count'              => $numberOfNotifications,
        );

        $database = Database::getConnection();
        if(empty($messageId)) {
          $query = $database->insert('sewing_broadcasting')->fields($dataArr)->execute();
          drupal_set_message('Notification sent successfully.');
        }
      }
    }
  }
}