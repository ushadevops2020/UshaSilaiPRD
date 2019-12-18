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
    
   
   $nid = $_REQUEST['nid'];
   $id = $_REQUEST['id'];
   if(isset($_REQUEST[REFID])) {
    $refId = ($_REQUEST[REFID]) ? $_REQUEST[REFID] : 0;
  } else {
    $userId = $_REQUEST[USERID] ? $_REQUEST[USERID] : 0;
  }
    $destinationData = drupal_get_destination();
    // Get a node storage object.
    $node_storage = \Drupal::entityManager()->getStorage('node');
    // Load a single node.
    $sentInventoryData = $node_storage->load($nid);
    if($refId) {
      $database = \Drupal::database();
      $connection = Database::getConnection();
      $getPcSentItemqry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array('qty_send', 'ssi_remarks'))
      ->condition('id', $id);
      $getSsiSentItemData = $getPcSentItemqry->execute();
      $ssiSendItems = $getSsiSentItemData->fetchAll(\PDO::FETCH_OBJ);
      $sentItemQty = $ssiSendItems[0]->qty_send;
      $sentItemSSIRemark = $ssiSendItems[0]->ssi_remarks;
       
    } else {
      $sentItemQty = $sentInventoryData->field_sewing_inv_quantity->value;
    }

    $sentItem = $sentInventoryData->field_sewing_item_name->target_id;
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

    $form['field_sewing_incoming_item_name'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Item Name'),
      HASH_DEFAULT_VALUE => ($sentItemData) ? $sentItemData->title->value : '',
      HASH_MAXLENGTH => 50,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC], 'disabled' => true]
    ];
    
     $form[FIELD_SEWING_INCOMING_ITEM_QTY] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Sent Quantity'),
      HASH_DEFAULT_VALUE => ($sentItemQty) ? $sentItemQty : '',
      HASH_MAXLENGTH => 5,
      HASH_ATTRIBUTES => [CLASS_CONST => [NUMERIC], 'disabled' => true]
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
	$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();
	if(in_array($userRoles[1], [ROLE_SEWING_SSI])) {
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
		$form[FIELD_SEWING_COURIER][HASH_SUFFIX] = '<hr>';
	}else{
		$form['field_ssi_remarks'] = [
		HASH_TYPE => TEXTFIELD,
		HASH_TITLE => $this->t('Remark'),
		HASH_DEFAULT_VALUE => ($sentItemSSIRemark) ? $sentItemSSIRemark : '',
		HASH_ATTRIBUTES => ['disabled' => true]
    ];
	
    $form['field_ssi_remarks'][HASH_SUFFIX] = '<hr>';
	}
	

    $form[FIELD_SEWING_ITEM_RECEIVED] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Received Quantity'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => '',
      HASH_MAXLENGTH => 10,
      HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
    ];
    
     $form['field_sewing_remark'] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Remarks'),
      HASH_DEFAULT_VALUE => '',
      HASH_MAXLENGTH => 200,
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
        return $response;
      } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $TotalSentItems = $form_state->getValue(FIELD_SEWING_INCOMING_ITEM_QTY);
    $pcReceivedItems = $form_state->getValue(FIELD_SEWING_ITEM_RECEIVED);
    
    if(empty($pcReceivedItems) || $pcReceivedItems <= 0 || $pcReceivedItems > $TotalSentItems) {
      $form_state->setErrorByName(FIELD_SEWING_ITEM_RECEIVED, t('Received Quantity can not be 0 or greater than Total sent items')); 
    }
    
    
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $incomingItemName = $form_state->getValue('field_sewing_incoming_item_name');
    $incomingItemQty = $form_state->getValue(FIELD_SEWING_INCOMING_ITEM_QTY);
    $itemReceived = $form_state->getValue(FIELD_SEWING_ITEM_RECEIVED);
    $itemRemarks = $form_state->getValue('field_sewing_remark');
    $inventoryNid = $form_state->getValue('field_hidden_nid');
    $customTableId = $form_state->getValue('hidden_custom_table_id');
    $inventoryRefId = ($form_state->getValue(FIELD_HIDDEN_REFID)) ? $form_state->getValue(FIELD_HIDDEN_REFID) : 0;

    #get Current user
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $userRoles = $user->getRoles();
    $currentUserid = $user->id();
    $nameWithRole = $user->get('field_first_name')->value.' '. $user->get('field_last_name')->value.' ('.$userRoles[1].')' ;

     if(in_array($userRoles[1], [ROLE_SEWING_SSI])) {
        $data_ary = array(
          STATUS                   => '2',
        );
       
     } else {
        $data_ary = array(
          STATUS                   => '4',
         ); 
      }


    $database = \Drupal::database();
    #check feedback data by trainee id
    $connection = Database::getConnection();
    $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id', 'sender_id', 'location_id'))
    ->condition('id', $customTableId);
    $check_data = $check_qry->execute();
    $check_results = $check_data->fetchAll(\PDO::FETCH_OBJ);
    $targetUsers = [$check_results[0]->sender_id];
    $count = count($check_results);
     if($count >= 1){ 
            #update data
            if(!empty($inventoryNid)){
               
                $data_ary = array(
                    'qty_received'          => $itemReceived,
                    STATUS                => $data_ary[STATUS],
                    'received_date'         => time(),
                    'acc_remarks'           => $itemRemarks
                    
                );
                
                $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING)->fields($data_ary)->
                condition('id', $customTableId);
                
                $query->execute();


                if(in_array($userRoles[1], [ROLE_SEWING_SCHOOL_ADMIN])) {

                    $check_qry = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array('qty_received', 'parent_ref_id'))->condition('nid', $inventoryNid)->condition(RECEIVER_ROLE, ROLE_SEWING_SCHOOL_ADMIN);
                
                    $check_data = $check_qry->execute();
                    $results = $check_data->fetchAll(\PDO::FETCH_OBJ);
                    $totalReceived = 0;
                    
                    foreach($results as $key => $value) {
                      $totalReceived += $value->qty_received;
                    }

                    $queryInventoryParent = $connection->select(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING, 'n')->fields('n', array('nid', REF_ID, 'parent_ref_id', 'qty_received', 'total_forwarded'))->condition('nid', $inventoryNid)->condition(RECEIVER_ID, $results[0]->parent_ref_id);

                  #for updating fully received status
                  $inventoryParent = $queryInventoryParent->execute();
                  $inventoryParentData = $inventoryParent->fetchAll(\PDO::FETCH_OBJ);

                  $totalReceivedBySsi = $inventoryParentData[0]->qty_received;

                  if($totalReceived == $totalReceivedBySsi) {
                      $data_ary[STATUS] = '5'; 
                  }
                    
                   #update direct parent status
                   $query = $database->update(TABLE_CUSTOM_MANAGE_INVENTORY_SEWING)->fields([STATUS => $data_ary[STATUS], 'qty_received_school' => $totalReceived, 'qty_received_school_date' => time()])->condition('nid', $inventoryNid);

                  $query->condition(RECEIVER_ID, $results[0]->parent_ref_id);
                  $query->execute();
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

              $masterDataService = \Drupal::service('sewing.master_data');
              if(!empty($targetUsers)){
                $masterDataService->sewingNotificationAlert($data, $targetUsers);
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