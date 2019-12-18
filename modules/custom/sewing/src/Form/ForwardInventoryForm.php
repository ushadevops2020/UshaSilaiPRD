<?php

namespace Drupal\sewing\Form;

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
 * ForwardInventoryForm class.
 */
class ForwardInventoryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'forward_inventory_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) 
  {
    
    $schoolListData = [];
    $ngoListData = [];
    $nid = $_REQUEST['nid'];
    
    
    $userId = ($_REQUEST[USERID]) ? $_REQUEST[USERID] : 0;
    $id = ($_REQUEST['id']) ? $_REQUEST['id'] : 0;

    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();
    $currentUserId = $user->id();
    $locationIds = [];
    $location = $user->get('field_user_location');
    foreach ($location as $key => $value) {
      $locationIds[] = $value->target_id;
    }
    
    $destinationData = drupal_get_destination();
    // Get a node storage object.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    // Load a single node.
    $sentInventoryData = $node_storage->load($nid);
    $forwardedItems = [];
    
    $sentItemQty = $sentInventoryData->field_sewing_inv_quantity->value;
    
    $database = \Drupal::database();
    $connection = Database::getConnection();
    $getAdminSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND, QTY_RECEIVED, TOTAL_FORWARDED))->condition('id', $id)->condition('nid', $nid)->condition('receiver_id', $currentUserId)->condition('sender_id', $userId);
    $getAdminSentItemData = $getAdminSentItemqry->execute();
    $adminSentItems = $getAdminSentItemData->fetchAll(\PDO::FETCH_OBJ);
    $itemsRemaiming = $adminSentItems[0]->qty_received - $adminSentItems[0]->total_forwarded;

    $ssiForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND, REF_ID, RECEIVER_ID, 'ssi_remarks'))->condition('nid', $nid)->condition(PARENT_REF_ID, $currentUserId)->condition(SENDER_ROLE, ROLE_SEWING_SSI);
    $ssiForwardedItemData = $ssiForwardedItemqry->execute();
    $ssiForwardedItems = $ssiForwardedItemData->fetchAll(\PDO::FETCH_OBJ);
    $forwardedItems = $ssiForwardedItems;
    
    $sentItem = $sentInventoryData->field_sewing_item_name->target_id;
    $location = $sentInventoryData->field_location->target_id;
    $receivedItems = $adminSentItems[0]->qty_received;

    // Get a node storage object.
    //$node_storage = \Drupal::entityManager()->getStorage('node');

    // Load a single node.
    $sentItemData = $node_storage->load($sentItem);
    $masterDataService = \Drupal::service('sewing.master_data');
    $schoolListData = $masterDataService->getSchoolListBylocationId($locationIds);
    

  //setting school list data and ngo list data as per condition
    $schoolList = ['' => SELECT_VALUE];
    if(!empty($schoolListData)) {
      $schoolList = $schoolList + $schoolListData;
    } else {
      $schoolList = $schoolList;
    }
    
    $form[HASH_PREFIX] = '<div id="wrapper_modal_forward_inventory_form">';
    $form[HASH_SUFFIX] = '</div>';

    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];

    $form[FIELD_HIDDEN_NID] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => ($nid) ? $nid : '',
      
    ];

    $form['field_hidden_action'] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => 'forward',
      
    ];

    $form['field_sewing_incoming_item_name'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Item Name'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($sentItemData) ? $sentItemData->title->value : '',
      HASH_MAXLENGTH => 50,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], DISABLED => true]
    ];
    
    $form[FIELD_SEWING_INCOMING_ITEM_QTY] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t(TEXT_QUANTITY),
      HASH_DEFAULT_VALUE => ($receivedItems) ? $receivedItems : '',
      HASH_MAXLENGTH => 5,
      HASH_REQUIRED => TRUE,
      HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE], DISABLED => true]
    ];

    $form[FIELD_SEWING_TRANSACTION_NUMBER] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Transaction Number'),
      HASH_DEFAULT_VALUE => ($sentInventoryData) ? $sentInventoryData->field_sewing_transaction_no->value : '',
      HASH_ATTRIBUTES => ['disabled' => true]
    ];

    $form[FIELD_SEWING_TRANSACTION_DATE] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Transaction Date'),
      HASH_DEFAULT_VALUE => ($sentInventoryData) ? $sentInventoryData->field_inventory_transaction_date->value : '',
      HASH_ATTRIBUTES => ['disabled' => true]
    ];

    $form[FIELD_SEWING_DOCKET_NUMBER] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Docket Number'),
      HASH_DEFAULT_VALUE => ($sentInventoryData) ? $sentInventoryData->field_sewing_docket_no->value : '',
      HASH_ATTRIBUTES => ['disabled' => true]
    ];

    $form[FIELD_SEWING_COURIER] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Courier'),
      HASH_DEFAULT_VALUE => ($sentInventoryData) ? $sentInventoryData->field_inventory_courier->value : '',
      HASH_ATTRIBUTES => ['disabled' => true]
    ];

    $form[FIELD_SEWING_COURIER][HASH_SUFFIX] = '<p>'.$itemsRemaiming.' items left</p><hr>';
    if(!empty($forwardedItems)) {
      $i = 0;
      foreach($forwardedItems as $forwardedItem) {
       $form['field_sewing_already_frd_to'.$i] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => $this->t('School Code'),
        HASH_OPTIONS => $schoolList,
        HASH_DEFAULT_VALUE => ($forwardedItem) ? $forwardedItem->ref_id : '',
        HASH_ATTRIBUTES => [DISABLED => true],
        HASH_PREFIX =>'<div id="field_sewing_already_frd_to">',
      ];
      $i++;
      $form['field_sewing_already_item_sent'.$i] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => $this->t(TEXT_QUANTITY),
        HASH_DEFAULT_VALUE => $forwardedItem->qty_send,
        HASH_MAXLENGTH => 10,
        HASH_ATTRIBUTES => [DISABLED => true],
        
      ];
      $i++;
	  $form['field_sewing_courier_number'.$i] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => $this->t('Remark'),
        HASH_DEFAULT_VALUE => $forwardedItem->ssi_remarks,
        HASH_MAXLENGTH => 10,
        HASH_REQUIRED => TRUE,
		'#description' => '*Courior No./Docket No./Other',
        HASH_ATTRIBUTES => [DISABLED => true],
		HASH_SUFFIX => '</div>',
      ];
	  $i++;
    }
  }
  if($itemsRemaiming > 0) {
    if(count($schoolList) > 1) {
      $locationId = implode(',', $locationIds);
      $form[FIELD_SEWING_FRD_TO][HASH_TREE]  = TRUE;
      $form[FIELD_SEWING_FRD_TO][HASH_PREFIX] = '<a href="#" class="addsewingschool-row" data-id="'.$locationId.'">Add</a><div id="sewingschool-wrapper-repater">';
      
      $form[FIELD_SEWING_FRD_TO][] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => $this->t('School Code'),
        HASH_OPTIONS => $schoolList,
        HASH_REQUIRED => TRUE,
        HASH_DEFAULT_VALUE => ($schoolId) ? $schoolId : '',
      ];
      $form[FIELD_SEWING_ITEM_SENT][HASH_TREE]  = TRUE;
      $form[FIELD_SEWING_ITEM_SENT][] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => $this->t(TEXT_QUANTITY),
        HASH_DEFAULT_VALUE => '',
        HASH_MAXLENGTH => 10,
        HASH_REQUIRED => TRUE,
        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
      ];
	  $form['field_sewing_courier_number'][HASH_TREE]  = TRUE;
	  $form['field_sewing_courier_number'][] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => $this->t('Remark'),
        HASH_DEFAULT_VALUE => '',
        HASH_MAXLENGTH => 10,
        HASH_REQUIRED => TRUE,
		'#description' => '*Courior No./Docket No./Other',
        //HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
      ];


      //$form[FIELD_SEWING_ITEM_SENT][HASH_SUFFIX] = '</div>';
      $form['field_sewing_courier_number'][HASH_SUFFIX] = '</div>';
    } else {
      $form[HASH_PREFIX] = '<p style="color:#de1f23">'.t('No School is associated with this NGO. Inventory can not be forwared.').'</p>';
    }
    
  }
  if($itemsRemaiming > 0 && count($schoolList) > 1) {
   $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
   $form[ACTIONS]['send'] = [
    HASH_TYPE => 'submit',
    HASH_VALUE => $this->t('Forward'),
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
}

$form[ACTIONS]['cancel'] = array(
  '#type' => 'button',
  '#value' => t('Cancel'),
  '#weight' => -1,
  '#attributes' => array('onClick' => 'window.location.href = "' . $destinationData['destination'].'"; event.preventDefault();'),
);

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
      $response->addCommand(new ReplaceCommand('#wrapper_modal_forward_inventory_form', $form));
      return $response;
    }
    else {
      $command = new CloseModalDialogCommand();
      $response->addCommand($command);
      drupal_set_message(t('Forwarded succesfully.'), STATUS);
      
      $response->addCommand(new RedirectCommand($form['sewing_silai_redirect']));
      return $response;
    } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $inventoryNid = $form_state->getValue(FIELD_HIDDEN_NID);
    
    $currentUserId = \Drupal::currentUser()->id();

    $database = \Drupal::database();
    $connection = Database::getConnection();
    $ssiItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND, 'qty_received', TOTAL_FORWARDED))->condition('nid', $inventoryNid)->condition(RECEIVER_ID, $currentUserId)->condition(RECEIVER_ROLE, ROLE_SEWING_SSI);

    $ssiItemData = $ssiItemqry->execute();
    $ssiItems = $ssiItemData->fetch(\PDO::FETCH_OBJ);

    $itemsSent = $_REQUEST[FIELD_SEWING_ITEM_SENT];
    
    $incomingItemQty = $form_state->getValue(FIELD_SEWING_INCOMING_ITEM_QTY);
    
    $totalReceived = ($ssiItems->qty_received) ? $ssiItems->qty_received : 0;
    $totalSent = ($ssiItems->total_forwarded) ? $ssiItems->total_forwarded : 0;
    
    $totalSent1 = 0;
    foreach($itemsSent as $row => $data) {
      $totalSent1 += $data;
    }
    $totalSent = $totalSent + $totalSent1;
    
    if($itemsSent[0] && ($totalSent1 <= 0 || $totalSent > $totalReceived)) {
      $form_state->setErrorByName(FIELD_SEWING_ITEM_SENT, t('Forward Quantity can not be 0 or greater than Total Received items')); 
    }
    
    
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();
    $connection = Database::getConnection();
    $incomingItemName = $form_state->getValue('field_sewing_incoming_item_name');
    $incomingItemQty = $form_state->getValue(FIELD_SEWING_INCOMING_ITEM_QTY);
    
    $itemSent = $_REQUEST[FIELD_SEWING_ITEM_SENT];

    
    $itemSentTo = $_REQUEST[FIELD_SEWING_FRD_TO];
    $itemCourierNo = $_REQUEST['field_sewing_courier_number'];

    $inventoryNid = $form_state->getValue(FIELD_HIDDEN_NID);
    $inventoryAction = $form_state->getValue('field_hidden_action');
    $inventoryRefId = ($form_state->getValue(FIELD_HIDDEN_REFID)) ? $form_state->getValue(FIELD_HIDDEN_REFID) : 0;

    $currentDomain = _get_current_domain();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();
    $currentUserid = $user->id();

    $receiverRole = ROLE_SEWING_SCHOOL_ADMIN;
    $updateTotalRole = ROLE_SEWING_SSI;
    $status = '3';
    $parantRefId = $user->id();

    $node_storage = \Drupal::entityManager()->getStorage('node');

    // Load a single node.
    $node = $node_storage->load($inventoryNid);

    $location = $node->get('field_location')->getValue()[0]['target_id'];
    $quantitySent = $node->get('field_sewing_inv_quantity')->getValue()[0]['value'];
    
    $masterDataService = \Drupal::service('silai.master_data');
    
    if($inventoryAction == 'forward') {

      if(!empty($inventoryNid)){
       
        $data_ary = array(
          'nid'                   => $inventoryNid,
          'domain_id'             => ($currentDomain == SILAI_DOAMIN) ? SILAI_DOAMIN : SEWING_DOMAIN,
          'sender_id'             => $user->id(),
          RECEIVER_ID           => 0,
          QTY_RECEIVED          => 0,
          STATUS                => '1',
          'sent_date'             => time(),
          'received_date'         => '',
          'location_id'           => $location,
          SENDER_ROLE           => $userRoles[1],
          RECEIVER_ROLE         => $receiverRole,
          PARENT_REF_ID         => $parantRefId 
          
        );
        
        foreach($itemSentTo as $key => $value) {
          $data_ary[REF_ID] = $value;
          $data_ary[QTY_SEND] = $itemSent[$key];
          $data_ary['ssi_remarks'] = $itemCourierNo[$key];
          $refArray[] = [
            'ref_id' => $value,
            'role'  => $receiverRole
          ];

          $query = $database->insert(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING)->fields($data_ary)->execute(); 
        }


                // For updating total forwarded items in multiple transaction 
        $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND))->condition('nid', $inventoryNid)->condition(RECEIVER_ROLE, $receiverRole)->condition('parent_ref_id', $currentUserid);
        
        $check_data = $check_qry->execute();
        $results = $check_data->fetchAll(\PDO::FETCH_OBJ);
        $totalSent = 0;

        foreach($results as $key => $value) {
          $totalSent += $value->qty_send;
        }
        
        $data_ary = array(
          TOTAL_FORWARDED          => $totalSent,
          STATUS                   => $status  
        );

        $qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array(QTY_SEND,'status'))->condition('nid', $inventoryNid)->condition(RECEIVER_ROLE, $updateTotalRole);
        
        $data = $qry->execute();
        $ssiData = $data->fetchAll(\PDO::FETCH_OBJ);
        $ssiInvExistingStatus = $ssiData[0]->status;
        if($ssiInvExistingStatus == PARTIALLY_FORWARDED_RECEIVED_STATUS) {
          $data_ary[STATUS] = PARTIALLY_FORWARDED_RECEIVED_STATUS;
        }


        $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING)->fields($data_ary)->condition('nid', $inventoryNid)->condition(RECEIVER_ROLE, $updateTotalRole); 
        $query->execute();
        
        
                #Fetching users for sending Notification
        foreach ($refArray as $key => $value) {
          $usersList[] = $masterDataService->getUsersByRefId($value['ref_id'], $value['role']);
        } 
        
        $users = [];
        foreach ($usersList as $key => $value) {
         $users = array_merge($users, $value);
       }
       
       $masterDataService = \Drupal::service('sewing.master_data');
       $targetUsers = [];
       if(in_array($userRoles[1], [ROLE_SEWING_SSI])) {
        $adminUsers = $masterDataService->getUsersByRoleSewing([ROLE_SEWING_HO_ADMIN, ROLE_SEWING_HO_USER]); 
        $targetUsers = array_merge($users, $adminUsers);
      } 
      
              #Send Notification
      $message = preg_replace('/{.*}/', $userRoles[1], INVENTORY_FORWARD_MESSAGE);
      $data = [
        'sender_role' => $userRoles[1],
        'receiver_id' => '',
        'receiver_role' => '',
        'message' => $message,
        'location' => $location,
        'created_by' => $currentUserid
      ];

      
      if(!empty($targetUsers)){
        $masterDataService->sewingNotificationAlert($data, $targetUsers);
      }
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
    return ['config.forward_inventory_form'];
  }
}