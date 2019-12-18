<?php

/**
 * @file
 * Contains \Drupal\send_message\Form\SendMessageForm.
 */

namespace Drupal\send_message\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Database\Database;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;


class SendMessageForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'send_message_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $messageId = $_GET['data'];
    if($messageId) {
      $result = \Drupal::database()->select('send_message', 'n')
            ->fields('n', array('subject', 'message', 'filepath'))->condition('id', $messageId)
            ->execute()->fetchAll(\PDO::FETCH_OBJ);

      foreach ($result as $row => $content) {
        $subject = $content->subject;
        $message = $content->message;
        $file_id = $content->filepath;
      }
    }

    $masterDataService = \Drupal::service('silai.master_data');
    $locationArr = $masterDataService->getLocationByCountryId();
    $destinationData = drupal_get_destination();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $accountRoles = $user->getRoles();
    $userLocation =  $user->field_user_location->target_id;
    $statesByLocation = $masterDataService->getStatesByLocationId($userLocation);
    $ngoByLocation = $masterDataService->getNgoByLocationId($userLocation);

    $form[HASH_PREFIX] = '<div id="wrapper_send_message_form">';
    $form[HASH_SUFFIX] = '</div>';

    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];
	if (in_array('pc', $accountRoles)) {
	  $form['ngo'] = array (
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => ('NGO'),
        HASH_OPTIONS => $ngoByLocation,
        HASH_MULTIPLE => TRUE,
        HASH_REQUIRED => TRUE,
      );
    if(!empty($messageId)) {
      $form['ngo'] = [
        HASH_TYPE => 'hidden',
      ];
    }
  }
	if (in_array('administrator', $accountRoles) || in_array('silai_ho_user', $accountRoles) || in_array('silai_ho_admin', $accountRoles)) {
	  $form['location'] = array (
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => ('Location'),
        HASH_OPTIONS => $locationArr,
        HASH_MULTIPLE => TRUE,
        // HASH_REQUIRED => TRUE,
      );
    if(!empty($messageId)) {
      $form['location'] = [
        HASH_TYPE => 'hidden',
      ];
    }
      $form['ngo'] = array(
        HASH_TYPE => ENTITYAUTOCOMPLETEFIELD,
        '#target_type' => 'node',
        HASH_TITLE => t('NGO'),
        HASH_TAG => TRUE,
        '#selection_handler' => 'default',
        '#selection_settings' => [
          'target_bundles' =>['ngo' => 'ngo'],
        ],
      );
      if(!empty($messageId)) {
        $form['ngo'] = [
          HASH_TYPE => 'hidden',
        ];
      }
    }
    $form['editID'] = array(
      HASH_TYPE => 'hidden',
      HASH_TITLE => t('editID'),
      HASH_DEFAULT_VALUE => ($messageId) ? $messageId : '',
    );
    $form['subject'] = array(
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => t('Subject'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($subject) ? $subject : '',
    );
    $form['message'] = array(
      HASH_TYPE => TEXTAREAFIELD,
      HASH_TITLE => t('Message'),
      HASH_MAXLENGTH => 500,
      HASH_DEFAULT_VALUE => ($message) ? $message : '',
    );
    $form['fileUpload'] = array(
      HASH_TYPE => FILEFIELD,
      HASH_TITLE => t('Upload Doc'),
      HASH_REQUIRED => FALSE,
      HASH_DEFAULT_VALUE => '',
      HASH_UPLOAD_LOCATION => 'public://my_files/',
      HASH_DEFAULT_VALUE => ($file_id) ? array($file_id) : '',
    );
	
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t('Submit'),
      HASH_BUTTON_TYPE => 'primary',
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'SendMessageAjax'],
        'event' => 'click',
      ],
    );
    return $form;
  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function SendMessageAjax(array $form, FormStateInterface $form_state) {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $accountRoles = $user->getRoles();
    $response = new AjaxResponse();

    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#wrapper_send_message_form', $form));
      return $response;
    }
    else {
      $command = new CloseModalDialogCommand();
      $response->addCommand($command);
      if(in_array('pc', $accountRoles)) {
        $response->addCommand(new RedirectCommand('/sent-message-list-pc'));
      } else {
          $response->addCommand(new RedirectCommand('/sent-message-list'));
      }
      return $response;
    } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $location = $form_state->getValue('location');
    $ngo = $form_state->getValue('ngo');
    $editID = $form_state->getValue('editID');
    if(empty($editID)){
      if(empty($location) && empty($ngo)) {
        $form_state->setErrorByName('location', t('Select at least one value of Location/NGO.')); 
        $form_state->setErrorByName('ngo', t('')); 
      }
    }
    
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $accountRoles = $user->getRoles();
    $uid = $user->id();
    $subject = $form_state->getValue('subject');
    $message = $form_state->getValue('message');
    $filepath = $form_state->getValue('fileUpload');
    $location = $form_state->getValue('location');
    $ngo = $form_state->getValue('ngo');
    $sent_date = time();
    $masterDataService = \Drupal::service('silai.master_data');

    $messageId = $_GET['data'];
    $database = Database::getConnection(); 
    if (isset($messageId) && $messageId != '') {
      $dataArr['subject'] = $subject;
      $dataArr['message'] = $message;
      $dataArr['filepath'] = $filepath[0];
      $dataArr['status'] = '0';
      $query    = $database->update('send_message')->fields($dataArr)->condition('id', $messageId)->execute();
      drupal_set_message("Message has been successfully updated");
    }

    if (in_array('administrator', $accountRoles) || in_array('silai_ho_user', $accountRoles) || in_array('silai_ho_admin', $accountRoles)) {
      // If NGO is selected, send message to selected NGO user (for HO user)
      if(isset($ngo)){
        foreach($ngo as $var){
          foreach($var as $ngoId){
            $node = \Drupal\node\Entity\Node::load($ngoId);
            $userId = $node->field_ngo_user_id->target_id;
            if(isset($userId)){
              $receivedUser = \Drupal\user\Entity\User::load($userId);
              $receivedUserRole = $receivedUser->getRoles();
              $dataArr = array(
                'sender_id' => $uid,
                'sender_role' => $accountRoles[1],
                'receiver_id' => $userId,
                'receiver_role' => $receivedUserRole[1],
                'subject' => $subject,
                'message' => $message,
                'sent_date' => $sent_date,
                'status' => '0',
                'filepath' => $filepath[0],
                'ngo' => $ngoId,
              );
              $database = Database::getConnection();
              if(empty($messageId)) {
                $query    = $database->insert('send_message')->fields($dataArr)->execute();
              }
            }
          }
        }
      }
      // send message to PC user for the selected location
      foreach($location as $key => $value){
        // Get user based on location
        $receivedUserArr = $masterDataService->getUsersByLocation($key, [ROLE_SILAI_PC]);
        foreach($receivedUserArr as $val){
          $receivedUser = \Drupal\user\Entity\User::load($val);
          $receivedUserRole = $receivedUser->getRoles();
          $receivedUserId = $val;
          $dataArr = array(
            'sender_id' => $uid,
            'sender_role' => $accountRoles[1],
            'receiver_id' => $receivedUserId,
            'receiver_role' => $receivedUserRole[1],
            'subject' => $subject,
            'message' => $message,
            'sent_date' => $sent_date,
            'status' => '0',
            'filepath' => $filepath[0],
            'location' => $key,
          );
          $database = Database::getConnection();
          if(empty($messageId)) {
            $query    = $database->insert('send_message')->fields($dataArr)->execute();
          }
        }
      }
    }

    if (in_array('pc', $accountRoles)) {
      foreach($ngo as $ngoId){
        $node = \Drupal\node\Entity\Node::load($ngoId);
        $userId = $node->field_ngo_user_id->target_id;
        if(isset($userId)){
          $receivedUser = \Drupal\user\Entity\User::load($userId);
          $receivedUserRole = $receivedUser->getRoles();
          $dataArr = array(
            'sender_id' => $uid,
            'sender_role' => $accountRoles[1],
            'receiver_id' => $userId,
            'receiver_role' => $receivedUserRole[1],
            'subject' => $subject,
            'message' => $message,
            'sent_date' => $sent_date,
            'status' => '0',
            'filepath' => $filepath[0],
            'ngo' => $ngoId,
            // 'state' => $state,
          );
          $database = Database::getConnection();
          if(empty($messageId)) {
            $query    = $database->insert('send_message')->fields($dataArr)->execute();
          }
        }
      }
    }
    if(empty($messageId)) {
      drupal_set_message('Message sent successfully.');
    }
  }
}