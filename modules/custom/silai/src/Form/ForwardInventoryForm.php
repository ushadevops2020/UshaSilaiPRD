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
    $nid = $_GET['nid'];
     
    if(isset($_GET[REFID])) {
      $refId = ($_GET[REFID]) ? $_GET[REFID] : 0;
      $refId = filter_var($refId, FILTER_SANITIZE_NUMBER_INT);	
    } else {
      $userId = ($_GET[USERID]) ? $_GET[USERID] : 0;
    }
    $destinationData = drupal_get_destination();
    // Get a node storage object.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    // Load a single node.
    $sentInventoryData = $node_storage->load($nid);
    $forwardedItems = [];
    if($refId) {
      $database = \Drupal::database();
      $connection = Database::getConnection();
      $getPcSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, QTY_RECEIVED, TOTAL_FORWARDED))->condition('nid', $nid)->condition(REF_ID, $refId);
      $getPcSentItemData = $getPcSentItemqry->execute();
      $pcSendItems = $getPcSentItemData->fetchAll(\PDO::FETCH_OBJ);
      $sentItemQty = $pcSendItems[0]->qty_send;
      $itemsRemaiming = $pcSendItems[0]->qty_received - $pcSendItems[0]->total_forwarded;

      $ngoForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, REF_ID, RECEIVER_ID))->condition('nid', $nid)->condition(PARENT_REF_ID, $refId)->condition(SENDER_ROLE, ROLE_SILAI_NGO_ADMIN);
      $ngoForwardedItemData = $ngoForwardedItemqry->execute();
      $ngoForwardedItems = $ngoForwardedItemData->fetchAll(\PDO::FETCH_OBJ);
      $forwardedItems = $ngoForwardedItems;
      
    } else {
      $sentItemQty = $sentInventoryData->field_silai_quantity->value;

      $database = \Drupal::database();
      $connection = Database::getConnection();
      $getAdminSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, QTY_RECEIVED, TOTAL_FORWARDED))->condition('nid', $nid)->condition('sender_id', $userId);
      $getAdminSentItemData = $getAdminSentItemqry->execute();
      $adminSentItems = $getAdminSentItemData->fetchAll(\PDO::FETCH_OBJ);
      $itemsRemaiming = $adminSentItems[0]->qty_received - $adminSentItems[0]->total_forwarded;

      $currentUserId = \Drupal::currentUser()->id();
      $pcForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, REF_ID, RECEIVER_ID))->condition('nid', $nid)->condition(PARENT_REF_ID, $currentUserId)->condition(SENDER_ROLE, ROLE_SILAI_PC);
      $pcForwardedItemData = $pcForwardedItemqry->execute();
      $pcForwardedItems = $pcForwardedItemData->fetchAll(\PDO::FETCH_OBJ);
      $forwardedItems = $pcForwardedItems;
      
    }

    $sentItem = $sentInventoryData->field_silai_item_name->target_id;
    $location = $sentInventoryData->field_silai_location->target_id;

    // Get a node storage object.
    //$node_storage = \Drupal::entityManager()->getStorage('node');

    // Load a single node.
    $sentItemData = $node_storage->load($sentItem);
    $masterDataService = \Drupal::service('silai.master_data');
    if($refId) {
     $schoolListData = $masterDataService->getSchoolsByNgoId($refId);
     
     } else {
      $ngoListData = $masterDataService->getNgoByLocationId($location);
  }


  //setting school list data and ngo list data as per condition
  $schoolList = ['' => SELECT_VALUE];
  $ngoList = ['' => SELECT_VALUE];
  if(!empty($schoolListData)) {
        $schoolList = $schoolList + $schoolListData;
    } else {
        $schoolList = $schoolList;
    }
    if(!empty($ngoListData)) {
        $ngoList = $ngoList + $ngoListData;
    } else {
        $ngoList = $ngoList;
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

    if(!empty($refId)) {
      $form[FIELD_HIDDEN_REFID] = [
      HASH_TYPE => FIELD_HIDDEN,
      HASH_VALUE => ($refId) ? $refId : '',
      
    ];  
    }

    $form['field_silai_incoming_item_name'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Item Name'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($sentItemData) ? $sentItemData->title->value : '',
      HASH_MAXLENGTH => 50,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], DISABLED => true]
    ];
    
     $form[FIELD_SILAI_INCOMING_ITEM_QTY] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t(TEXT_QUANTITY),
      HASH_DEFAULT_VALUE => ($sentItemQty) ? $sentItemQty : '',
      HASH_MAXLENGTH => 5,
      HASH_REQUIRED => TRUE,
      HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE], DISABLED => true]
    ];
    $form[FIELD_SILAI_INCOMING_ITEM_QTY][HASH_SUFFIX] = '<p>'.$itemsRemaiming.' items left</p><hr>';
      if(!empty($forwardedItems)) {
      $i = 0;
      foreach($forwardedItems as $forwardedItem) {
         $form['field_silai_already_frd_to'.$i] = [
          HASH_TYPE => SELECTFIELD,
          HASH_TITLE => ($refId) ? $this->t('School Name') : $this->t('NGO Name'),
          HASH_OPTIONS => ($refId) ? $schoolList : $ngoList,
          HASH_DEFAULT_VALUE => ($forwardedItem) ? $forwardedItem->ref_id : '',
           HASH_ATTRIBUTES => [DISABLED => true],
           HASH_PREFIX => '<div id="inventory_section">',
        ];
        $i++;
        $form['field_silai_already_item_sent'.$i] = [
          HASH_TYPE => TEXTFIELD,
          HASH_TITLE => $this->t(TEXT_QUANTITY),
          HASH_DEFAULT_VALUE => $forwardedItem->qty_send,
          HASH_MAXLENGTH => 10,
          HASH_ATTRIBUTES => [DISABLED => true],
          HASH_SUFFIX => '</div>',
        ];
        $i++;
      }
    }
    if($itemsRemaiming > 0) {
      if($refId) {
        if(count($schoolList) > 1) {
        $form[FIELD_SILAI_FRD_TO][HASH_TREE]  = TRUE;
        $form[FIELD_SILAI_FRD_TO][HASH_PREFIX] = '<a href="#" class="addschool-row" data-id="'.$refId.'">Add</a><div id="school-wrapper-repater">';
        
        $form[FIELD_SILAI_FRD_TO][] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => $this->t('School Name'),
        HASH_OPTIONS => $schoolList,
        HASH_REQUIRED => TRUE,
        HASH_DEFAULT_VALUE => ($schoolId) ? $schoolId : '',
      ];
      $form[FIELD_SILAI_ITEM_SENT][HASH_TREE]  = TRUE;
      $form[FIELD_SILAI_ITEM_SENT][] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => $this->t(TEXT_QUANTITY),
        HASH_DEFAULT_VALUE => '',
        HASH_MAXLENGTH => 10,
        HASH_REQUIRED => TRUE,
        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
      ];


       $form[FIELD_SILAI_ITEM_SENT][HASH_SUFFIX] = '</div>';
     } else {
        $form[HASH_PREFIX] = '<p style="color:#de1f23">'.t('No School is associated with this NGO. Inventory can not be forwared.').'</p>';
     }
      } else {
        if(count($ngoList) >1) {
        $form[FIELD_SILAI_FRD_TO][HASH_TREE]  = TRUE;
        $form[FIELD_SILAI_FRD_TO][HASH_PREFIX] = '<a href="#" class="addCF" data-id="'.$location.'">Add</a><div id="wrapper-repater">';
        $form[FIELD_SILAI_FRD_TO][] = [
        HASH_TYPE => SELECTFIELD,
        HASH_TITLE => $this->t('NGO Name'),
        HASH_OPTIONS => $ngoList,
        HASH_REQUIRED => TRUE,
        HASH_DEFAULT_VALUE => ($ngoId) ? $ngoId : '',
      ];

      $form[FIELD_SILAI_ITEM_SENT][HASH_TREE]  = TRUE;
      $form[FIELD_SILAI_ITEM_SENT][] = [
        HASH_TYPE => TEXTFIELD,
        HASH_TITLE => $this->t(TEXT_QUANTITY),
        HASH_DEFAULT_VALUE => '',
        HASH_MAXLENGTH => 10,
        HASH_REQUIRED => TRUE,
        HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
      ];


       $form[FIELD_SILAI_ITEM_SENT][HASH_SUFFIX] = '</div>';
       } else {

        $form[HASH_PREFIX] = '<p style="color:#de1f23">'.t('No NGO is associated with this PC.Inventory can not be forwared.').'</p>';
        }
       }

    }
    if($itemsRemaiming > 0 && (count($schoolList) > 1 || count($ngoList) >1)) {
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
    $refId = ($form_state->getValue(FIELD_HIDDEN_REFID)) ? $form_state->getValue(FIELD_HIDDEN_REFID) : 0;

     $currentUserId = \Drupal::currentUser()->id();

    $database = \Drupal::database();
    $connection = Database::getConnection();
    $pcForwardedItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND, 'qty_received', TOTAL_FORWARDED))->condition('nid', $inventoryNid);
    if(!empty($refId)) {
      $pcForwardedItemqry->condition(REF_ID, $refId)->condition(RECEIVER_ROLE, ROLE_SILAI_NGO_ADMIN);
    } else {
      $pcForwardedItemqry->condition(RECEIVER_ID, $currentUserId)->condition(RECEIVER_ROLE, ROLE_SILAI_PC);
    }
    
    $pcForwardedItemData = $pcForwardedItemqry->execute();
    $pcForwardedItems = $pcForwardedItemData->fetch(\PDO::FETCH_OBJ);
    $itemsSent = $_REQUEST[FIELD_SILAI_ITEM_SENT];
    $incomingItemQty = $form_state->getValue(FIELD_SILAI_INCOMING_ITEM_QTY);
    
    $totalReceived = ($pcForwardedItems->qty_received) ? $pcForwardedItems->qty_received : 0;
    $totalSent = ($pcForwardedItems->total_forwarded) ? $pcForwardedItems->total_forwarded : 0;
     
     $totalSent1 = 0;
    foreach($itemsSent as $row => $data) {
      $totalSent1 += $data;
    }
    $totalSent = $totalSent + $totalSent1;
    if( $totalSent1 == 0 || $totalSent > $totalReceived) {
      $form_state->setErrorByName(FIELD_SILAI_ITEM_SENT, t('Forward Quantity can not be 0 or greater than Total Received items')); 
    }
    
    
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();
    $connection = Database::getConnection();
    $incomingItemName = $form_state->getValue('field_silai_incoming_item_name');
    $incomingItemQty = $form_state->getValue(FIELD_SILAI_INCOMING_ITEM_QTY);
    
    $itemSent = $_REQUEST[FIELD_SILAI_ITEM_SENT];

    
    $itemSentTo = $_REQUEST[FIELD_SILAI_FRD_TO];

    $inventoryNid = $form_state->getValue(FIELD_HIDDEN_NID);
    $inventoryAction = $form_state->getValue('field_hidden_action');
    $inventoryRefId = ($form_state->getValue(FIELD_HIDDEN_REFID)) ? $form_state->getValue(FIELD_HIDDEN_REFID) : 0;

    $currentDomain = _get_current_domain();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();
    $currentUserid = $user->id();

    if(in_array($userRoles[1], [ROLE_SILAI_PC])) {
      $receiverRole = ROLE_SILAI_NGO_ADMIN;
      $updateTotalRole = ROLE_SILAI_PC;
      $status = '3';

      $parantRefId = $user->id();
    } else {
      $receiverRole = ROLE_SILAI_SCHOOL_ADMIN;
      $updateTotalRole = ROLE_SILAI_NGO_ADMIN;
      $parantRefId = $inventoryRefId;
      $status = '5';
    }

    $node_storage = \Drupal::entityManager()->getStorage('node');

    // Load a single node.
    $node = $node_storage->load($inventoryNid);

    $location = $node->get('field_silai_location')->getValue()[0]['target_id'];
    $quantitySent = $node->get('field_silai_quantity')->getValue()[0]['value'];
    
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
                $refArray[] = [
                  'ref_id' => $value,
                  'role'  => $receiverRole
                ];

                $query = $database->insert(TABLE_CUSTOM_MANAGE_INVENTORY)->fields($data_ary)->execute(); 
                }


                // For updating total forwarded items in multiple transaction 
                $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array(QTY_SEND))->condition('nid', $inventoryNid)->condition(RECEIVER_ROLE, $receiverRole);
                if(!empty($inventoryRefId)) {
                 $check_qry->condition(PARENT_REF_ID, $inventoryRefId); 
                }
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


                $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY)->fields($data_ary)->condition('nid', $inventoryNid);
                if(!empty($inventoryRefId)) {
                 $query->condition(REF_ID, $inventoryRefId); 
                } else {
                  $query->condition(RECEIVER_ROLE, ROLE_SILAI_PC); 
                }
                $query->execute();
                if(!in_array($userRoles[1], [ROLE_SILAI_PC])) {
                  $database = \Drupal::database();
                    #check feedback data by trainee id
                    $connection = Database::getConnection();
                    $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY, 'n')->fields('n', array('nid', REF_ID, PARENT_REF_ID))->condition('nid', $inventoryNid)->condition(REF_ID, $inventoryRefId);
                    
                    $check_data = $check_qry->execute();
                    $check_results = $check_data->fetchAll(\PDO::FETCH_OBJ);

                    // update status for Receiver role PC
                    $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY)->fields([STATUS => $status])->condition('nid', $inventoryNid);

                    $query->condition(RECEIVER_ID, $check_results[0]->parent_ref_id);
                    $query->execute();
                }

                foreach ($refArray as $key => $value) {
                  $usersList[] = $masterDataService->getUsersByRefId($value['ref_id'], $value['role']);
                } 
                
                $users = [];
                foreach ($usersList as $key => $value) {
                   $users = array_merge($users, $value);
                }
                
                $targetUsers = [];
                if(in_array($userRoles[1], [ROLE_SILAI_PC])) {
                    $adminUsers = $masterDataService->getUsersByRole([ROLE_SILAI_HO_ADMIN, SILAI_HO_USER]); 
                    $targetUsers = array_merge($users, $adminUsers);
                } else {
                  $adminUsers = $masterDataService->getUsersByRole([ROLE_SILAI_HO_ADMIN, SILAI_HO_USER]);
                  $pcUsers = $masterDataService->getUsersByRole([ROLE_SILAI_PC], $location);
                  $targetUsers = array_merge($adminUsers, $pcUsers, $users);
                  
                }
                $message = preg_replace('/{.*}/', $userRoles[1], INVENTORY_FORWARD_MESSAGE);

                $data = [
                'sender_role' => $userRoles[1],
                'receiver_id' => '',
                'receiver_role' => '',
                'message' => $message,
                'location' => $location,
                'created_by' => $currentUserid
              ];

            $masterDataService = \Drupal::service('silai.master_data');
            if(!empty($targetUsers)){
              $masterDataService->notificationAlert($data, $targetUsers);
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