<?php

namespace Drupal\silai\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;

/**
 * AcceptInventoryForm class.
 */
class AcceptInventoryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'accept_inventory_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) 
  {
    
   
   $nid = $_GET['nid'];
   $id = $_GET['id'];
   if(isset($_GET[REFID])) {
    $refId = ($_GET[REFID]) ? $_GET[REFID] : 0;
  } else {
    $userId = $_GET[USERID] ? $_GET[USERID] : 0;
  }
    $destinationData = drupal_get_destination();
    // Get a node storage object.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    // Load a single node.
    $sentInventoryData = $node_storage->load($nid);
    if($refId) {
      $database = \Drupal::database();
      $connection = Database::getConnection();
      $getPcSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('qty_send'))
      ->condition('id', $id);
      //->condition('nid', $nid)->condition(REF_ID, $refId);
      $getPcSentItemData = $getPcSentItemqry->execute();
      $pcSendItems = $getPcSentItemData->fetchAll(\PDO::FETCH_OBJ);
      $sentItemQty = $pcSendItems[0]->qty_send;
       
    } else {
      $sentItemQty = $sentInventoryData->field_silai_quantity->value;
    }

    $sentItem = $sentInventoryData->field_silai_item_name->target_id;

    // Get a node storage object.
    //$node_storage = \Drupal::entityManager()->getStorage('node');

    // Load a single node.
   $sentItemData = $node_storage->load($sentItem);

   
    $form[HASH_PREFIX] = '<div id="wrapper_modal_accept_inventory_form">';
    $form[HASH_SUFFIX] = '</div>';

    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];

    $form['field_hidden_nid'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => ($nid) ? $nid : '',
      
    ];

    $form['hidden_custom_table_id'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => ($id) ? $id : '',
      
    ];
    if(!empty($refId)) {
      $form[FIELD_HIDDEN_REFID] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => ($refId) ? $refId : '',
      
    ];  
    }
    $form['field_hidden_action'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => 'accept',
      
    ];

    $form['field_silai_incoming_item_name'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Item Name'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($sentItemData) ? $sentItemData->title->value : '',
      HASH_MAXLENGTH => 50,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], 'disabled' => true]
    ];
    
     $form[FIELD_SILAI_INCOMING_ITEM_QTY] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Sent Quantity'),
      HASH_DEFAULT_VALUE => ($sentItemQty) ? $sentItemQty : '',
      HASH_MAXLENGTH => 5,
      HASH_REQUIRED => TRUE,
      HASH_ATTRIBUTES => [CLASS_CONST => [NUMERIC], 'disabled' => true]
    ];

    $form[FIELD_SILAI_ITEM_RECEIVED] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Received Quantity'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => '',
      HASH_MAXLENGTH => 10,
      HASH_ATTRIBUTES => [CLASS_CONST => [NUMERIC]]
    ];
    
     $form['field_silai_remark'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Remarks'),
      HASH_DEFAULT_VALUE => '',
      HASH_MAXLENGTH => 200,
      HASH_ATTRIBUTES => [CLASS_CONST => [NUMERIC]]
    ];


    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
    '#type' => 'button',
    '#value' => t('Cancel'),
    '#weight' => -1,
    '#attributes' => array('onClick' => 'window.location.href = "' . $destinationData['destination'].'"; event.preventDefault();'),
    );
    $form[ACTIONS]['send'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t('Accept'),
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'addUserAjax'],
        'event' => 'click',
      ],
    ];
    $form['sewing_silai_redirect'] = $destinationData['destination'];
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }


  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function addUserAjax(array $form, FormStateInterface $form_state) {
      $doamin = _get_current_domain();
      $response = new AjaxResponse();
      
      if ($form_state->hasAnyErrors()) {
        $response->addCommand(new ReplaceCommand('#wrapper_modal_accept_inventory_form', $form));
        return $response;
      }
      else {
        $command = new CloseModalDialogCommand();
        $response->addCommand($command);
        drupal_set_message(t('Received succesfully.'), STATUS);
        $response->addCommand(new RedirectCommand($form['sewing_silai_redirect']));
        // if($doamin == SEWING_DOMAIN) {
        //   $response->addCommand(new RedirectCommand('/manage-inventory_pc'));
        // } else if($doamin == SILAI_DOAMIN) {
        //     $response->addCommand(new RedirectCommand('/manage-inventory_pc'));
        // }
        return $response;
      } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $TotalSentItems = $form_state->getValue(FIELD_SILAI_INCOMING_ITEM_QTY);
    $pcReceivedItems = $form_state->getValue(FIELD_SILAI_ITEM_RECEIVED);
    
    if(empty($pcReceivedItems) || $pcReceivedItems > $TotalSentItems) {
      $form_state->setErrorByName(FIELD_SILAI_ITEM_RECEIVED, t('Received Quantity can not be 0 or greater than Total sent items')); 
    }
    
    
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $incomingItemName = $form_state->getValue('field_silai_incoming_item_name');
    $incomingItemQty = $form_state->getValue(FIELD_SILAI_INCOMING_ITEM_QTY);
    $itemReceived = $form_state->getValue(FIELD_SILAI_ITEM_RECEIVED);
    $itemRemarks = $form_state->getValue('field_silai_remark');
    $inventoryNid = $form_state->getValue('field_hidden_nid');
    $customTableId = $form_state->getValue('hidden_custom_table_id');
    $inventoryRefId = ($form_state->getValue(FIELD_HIDDEN_REFID)) ? $form_state->getValue(FIELD_HIDDEN_REFID) : 0;

    //get Current user
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();
    $currentUserid = $user->id();
    $nameWithRole = $user->get('field_first_name')->value.' '. $user->get('field_last_name')->value.' ('.$userRoles[1].')' ;

     if(in_array($userRoles[1], [ROLE_SILAI_PC])) {
        $data_ary = array(
          STATUS                   => '2',
        );
       
     } elseif(in_array($userRoles[1], [ROLE_SILAI_NGO_ADMIN])) {
        $data_ary = array(
          STATUS                   => '4',
        );
     } else {
        $data_ary = array(
          STATUS                   => '6',
         ); 
      }


    $database = \Drupal::database();
    #check feedback data by trainee id
    $connection = Database::getConnection();
    $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id', 'sender_id', 'location_id'))
    ->condition('id', $customTableId);
    // ->condition('nid', $inventoryNid);
    // if(!empty($inventoryRefId)) {
    //   $check_qry->condition(REF_ID, $inventoryRefId);
    // }
    $check_data = $check_qry->execute();
    $check_results = $check_data->fetchAll(\PDO::FETCH_OBJ);
    $targetUsers = [$check_results[0]->sender_id];
    $count = count($check_results);
     if($count >= 1){ #update data
            //$nid = $node->id();
            if(!empty($inventoryNid)){
               
                $data_ary = array(
                    'qty_received'          => $itemReceived,
                    STATUS                => $data_ary[STATUS],
                    'received_date'         => time(),
                    'acc_remarks'           => $itemRemarks
                    
                );
                
                $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY)->fields($data_ary)->
                condition('id', $customTableId);
                // condition('nid', $inventoryNid);
                // if(!empty($inventoryRefId)) {
                //   $query->condition(REF_ID, $inventoryRefId); 
                // } 
                $query->execute();


                if(in_array($userRoles[1], [ROLE_SILAI_NGO_ADMIN])) {
                  $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY)->fields([STATUS => $data_ary[STATUS]])->condition('nid', $inventoryNid);

                  $query->condition(RECEIVER_ID, $check_results[0]->parent_ref_id);
                  $query->execute();

                  // get Inventory parent data of accepter to send notification
                  $queryInventoryParent = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id', 'sender_id'))->condition('nid', $inventoryNid)->condition(RECEIVER_ID, $check_results[0]->sender_id);
                  
                  $inventoryParent = $queryInventoryParent->execute();
                  $inventoryParentData = $inventoryParent->fetchAll(\PDO::FETCH_OBJ);
                  
                  //add indirect parent for notification
                  $targetUsers = [$check_results[0]->sender_id, $inventoryParentData[0]->sender_id];
                } elseif(in_array($userRoles[1], [ROLE_SILAI_SCHOOL_ADMIN])) {

                   // update direct parent status
                   $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY)->fields([STATUS => $data_ary[STATUS]])->condition('nid', $inventoryNid);

                  $query->condition(RECEIVER_ID, $check_results[0]->parent_ref_id);
                  $query->execute();

                   
                    $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id'))->condition('nid', $inventoryNid)->condition(REF_ID, $inventoryRefId);
                    
                    $check_data = $check_qry->execute();
                    $check_results = $check_data->fetchAll(\PDO::FETCH_OBJ);

                    // update in-direct parent status for School admin
                    $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY)->fields([STATUS => $data_ary[STATUS]])->condition('nid', $inventoryNid);

                    $query->condition(RECEIVER_ID, $check_results[0]->parent_ref_id);
                    $query->execute();

                    // get Inventory parent data of accepter to send notification
                    $queryInventoryParent = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id', 'sender_id'))->condition('nid', $inventoryNid)->condition(REF_ID, $check_results[0]->parent_ref_id);
                  
                  $inventoryParent = $queryInventoryParent->execute();
                  $inventoryParentData = $inventoryParent->fetchAll(\PDO::FETCH_OBJ);

                  // get Inventory parent data of accepter to send notification
                    $qryInvNextParent = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id', 'sender_id'))->condition('nid', $inventoryNid)->condition(RECEIVER_ID, $inventoryParentData[0]->parent_ref_id);
                  
                  $invNextParent = $qryInvNextParent->execute();
                  $invNextParentData = $invNextParent->fetchAll(\PDO::FETCH_OBJ);


                  //add indirect parent for notification
                  $targetUsers = [$check_results[0]->sender_id, $inventoryParentData[0]->parent_ref_id, $invNextParentData[0]->sender_id];
                
                  }

            }
            $message = preg_replace('/{.*}/', $nameWithRole, INVENTORY_ACCEPT_MESSAGE);
             $data = [
                'sender_role' => $userRoles[1],
                'receiver_id' => '',
                'receiver_role' => '',
                'message' => $message,
                'location' => $check_results[0]->location_id,
                'created_by' => $currentUserid
              ];

          $masterDataService = \Drupal::service('silai.master_data');
          if(!empty($targetUsers)){
            $masterDataService->notificationAlert($data, $targetUsers);
          }
        }
        return;

  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['config.accept_inventory_form'];
  }


}