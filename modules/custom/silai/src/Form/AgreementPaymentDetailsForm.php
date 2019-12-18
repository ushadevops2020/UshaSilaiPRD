<?php

namespace Drupal\silai\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;

use Drupal\node\Entity\Node;
use Drupal\Core\Url;

class AgreementPaymentDetailsForm extends FormBase {


	public function getFormId() {
		return 'silai_custom_agreement_payment_details_form';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$nid = $form_state->getBuildInfo()['args'][0];
		$destinationData = drupal_get_destination();
    	$conn = Database::getConnection();
	    $query = $conn->select(TABLE_SILAI_NGO_PAYMENT_DETAIL, 's')
	            ->condition('nid', $nid)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
		$node_storage = \Drupal::entityTypeManager()->getStorage('node');
		$node = $node_storage->load($nid);
		$Cararr_id = $node->get('field_cararr_id')->value;
		$agreement_amount = $node->get('field_agreement_amount')->value;
		$ngo_nid =  $node->get('field_agreement_ngo_name')->target_id;
		$ngo_node = $node_storage->load($ngo_nid);
		$ngo_name =  $ngo_node->get('title')->value;
		$balance_amount = $node->get('field_silai_agree_due_balance')->value;
		$agreement_id = $node->get('field_agreement_id')->value;
		$form[HASH_PREFIX] = '<div id="wrapper_modal_agreement_payment_details">';
	    $form[HASH_SUFFIX] = '</div>';
	    $form['status_messages'] = [
	      HASH_TYPE => 'status_messages',
	      '#weight' => -10,
	    ];
	    # Section for agreement details
	    $form['agreement_detail'] = array(
		        HASH_TYPE => 'details',
		        HASH_TITLE => t('Agreement Detail'),
		        '#open' => TRUE,
		    );
		$form['agreement_detail'][AGREEMENT] = [
		    HASH_TYPE => 'table',
		    '#header' => [
			    $this->t('Agreement Id'),
			    $this->t('NGO Name'),
			    $this->t('Agreement Amount'),
			    $this->t('Balance Amount'),
		  	],
		];
		$form['agreement_detail'][AGREEMENT][1]['agreement_id'] = [
		    HASH_PLAIN_TEXT => $agreement_id,
		];
		$form['agreement_detail'][AGREEMENT][1]['ngo_name'] = [
		    HASH_PLAIN_TEXT => $ngo_name,
		];
		$form['agreement_detail'][AGREEMENT][1][AGREEMENT_AMOUNT] = [
		    HASH_PLAIN_TEXT => $agreement_amount,
		];
		$form['agreement_detail'][AGREEMENT][1]['balance_amount'] = [
		    HASH_PLAIN_TEXT => $balance_amount,
		];
		# Section for Previous payment list.
		$row_count = count($payDatas);
		if($row_count >= 1){
			$header_table = array(
		       	'bank_drawn' => t('PO Number'),
		       	'cheque_no' => t('cheque_no'),
			    'amount' => t('Amount'),
			    PAYMENT_MODE => t('Payment Mode'),
			    'invoice_no' => t('Invoice No'),
			    'status' => t('Status'),
			    'opt' => t('Action'),
		    );
		    $form['pay_detail'] = array(
		        HASH_TYPE => 'details',
		        HASH_TITLE => t('Payment List'),
		        '#open' => TRUE,
		    );
		    $form['pay_detail']['pay_agreement'] = [
			    HASH_TYPE => 'table',
			    '#header' => [
				    $this->t('PO Number'),
				    $this->t('cheque_no'),
				    $this->t('Amount'),
				    $this->t('Payment Mode'),
				    $this->t('Invoice No'),
				    $this->t('Status'),
				    $this->t('Action'),
			  	],
			];
			foreach($payDatas as $payData){
				if($payData->payment_mode == 1){ $payment_mode = 'Cheque / DD';  }
				else{ $payment_mode = 'NEFT/RTGS';  }
			    if($payData->payment_status == 1){ $status = 'Received'; }
			    else{ $status = 'Pending'; }
			   	$form['pay_detail']['pay_agreement'][$payData->id]['bank_drawn'] = [
				    HASH_PLAIN_TEXT => $payData->bank_drawn,
				];
				// if($payData->payment_status == 0){
				// 	$form['pay_detail']['pay_agreement'][$payData->id]['cheque_no'] = [
				// 	    //HASH_PLAIN_TEXT => $payData->cheque_no,
				// 	    HASH_TYPE => TEXTFIELD,
				//       	//HASH_TITLE => $this->t('Installment'),
				//       	HASH_DEFAULT_VALUE => $payData->cheque_no,
				//       	HASH_MAXLENGTH => 10,
				//       	HASH_REQUIRED => FALSE,
				//       	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
				// 	];
				// 	$form['pay_detail']['pay_agreement'][$payData->id]['amount'] = [
				// 	    //HASH_PLAIN_TEXT => $payData->amount,
				// 	    HASH_TYPE => TEXTFIELD,
				//       	//HASH_TITLE => $this->t('Installment'),
				//       	HASH_DEFAULT_VALUE => $payData->amount,
				//       	HASH_MAXLENGTH => 10,
				//       	HASH_REQUIRED => FALSE,
				//       	HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
				// 	];
				// }else{
					$form['pay_detail']['pay_agreement'][$payData->id]['cheque_no'] = [
					    HASH_PLAIN_TEXT => $payData->cheque_no,
					];
					$form['pay_detail']['pay_agreement'][$payData->id]['amount'] = [
					    HASH_PLAIN_TEXT => $payData->amount,
					];
				// }
				
				$form['pay_detail']['pay_agreement'][$payData->id]['payment_mode'] = [
				    HASH_PLAIN_TEXT => $payment_mode,
				];
				$form['pay_detail']['pay_agreement'][$payData->id]['invoice_no'] = [
				    HASH_PLAIN_TEXT => $payData->invoice_no,
				];
				$form['pay_detail']['pay_agreement'][$payData->id]['status'] = [
				    HASH_PLAIN_TEXT => $status,
				];
				 if($payData->payment_status == 0){
					$form['pay_detail']['pay_agreement'][$payData->id]['btn_update'] = array(
				      '#markup' => '<a href="agreement-payment-amount-edit/'.$payData->id.'/'.$nid.'?destination=/agreements-listing" class="use-ajax" data-dialog-type="modal" data-dialog-options="{"width":"auto","height":"auto"}"">Edit</a>',
				    );
				 }else{
				 	$form['pay_detail']['pay_agreement'][$payData->id]['btn_update'] = [
					    HASH_PLAIN_TEXT => t(''),
					];
				 }
			}
		    // $rows=array();
		    // foreach($payDatas as $payData){
		    // 	if($payData->payment_mode == 1){
			   //  	$payment_mode = 'Cheque / DD';
			   //  }else{
			   //  	$payment_mode = 'NEFT/RTGS';
			   //  }
			   //  if($payData->payment_status == 1){
			   //  	$status = 'Received';
			   //  }else{
			   //  	//$a = '<a href="">hello</a>';
			   //  	$status = 'Pending';
			   //  }
			   //  $edit   = Url::fromUserInput('/mydata/form/mydata?num='.$data);
			   //  if($payData->payment_status == 1){
			   //  	$rows[] = array(
		    //          	'bank_drawn' => $payData->bank_drawn,
		    //          	'cheque_no' => $payData->cheque_no,
					 //    'amount' => $payData->amount,
					 //    PAYMENT_MODE => $payment_mode,
					 //    'invoice_no' => $payData->invoice_no,
					 //    'status' => $status,
					 //    'opt' => t(''),
		    //         );
			   //  }else{
			   //  	$rows[] = array(
		    //          	'bank_drawn' => $payData->bank_drawn,
		    //          	'cheque_no' => $payData->cheque_no,
					 //    'amount' => $payData->amount,
					 //    PAYMENT_MODE => $payment_mode,
					 //    'invoice_no' => $payData->invoice_no,
					 //    'status' => $status,
					 //    \Drupal::l('Edit', $edit),
		    //         );
			   //  }
			    
	            
		    // }
		    // $form['payment_list'] = array(
		    //     HASH_TYPE => 'details',
		    //     HASH_TITLE => t('Payment List'),
		    //     '#open' => TRUE,
		    // );
		    // $form['payment_list']['payment_data'] = [
	     //        '#type' => 'table',
	     //        '#header' => $header_table,
	     //        '#rows' => $rows,
	     //        '#empty' => t('No data found'),
	     //    ];
		}
		# Section for new payment custom form
		$form[INSTALLMENT] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Installment'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 10,
	      	HASH_REQUIRED => FALSE,
	      	//HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
		$form[PAYMENT_MODE] = [
				HASH_TYPE => RADIOSFIELD,
				HASH_TITLE => t('Payment Mode'),
				HASH_OPTIONS => array( 
							1 => t('CHEQUE/DD'),
							2 => t('NEFT/RTGS'),
						),
				HASH_REQUIRED => TRUE,
				HASH_DEFAULT_VALUE => 1,
		];
		$form[AMOUNT] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Amount'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
	    ];
	    $form[AGREEMENT_AMOUNT] = [
	     	HASH_TYPE => 'hidden',
	      	HASH_TITLE => $this->t('Agreement_amount'),
	      	HASH_DEFAULT_VALUE => ($agreement_amount) ? $agreement_amount : '',
	      	HASH_MAXLENGTH => 20,
	      	//HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['cheque_dd_no'] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Cheque/DD No.'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 20,
	      	//HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['bank_drawn'] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('PO Number'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['transaction_date'] = [
	     	HASH_TYPE => DATEFIELD,
	      	HASH_TITLE => $this->t('Transaction Date'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	    ];
	    $form['invoice_no'] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Invoice Number'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['invoice_date'] = [
	     	HASH_TYPE => DATEFIELD,
	      	HASH_TITLE => $this->t('Invoice Date'),
	      	HASH_DEFAULT_VALUE => '',
	      	HASH_MAXLENGTH => 30,
	      	HASH_REQUIRED => TRUE,
	    ];
		# Cancle and save button   - 
		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
	    $form[ACTIONS]['cancel'] = array(
	    HASH_TYPE => 'button',
	    HASH_VALUE => t('Cancel'),
	    '#weight' => -1,
	    HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "'.$destinationData['destination'].'"; event.preventDefault();'),
	    );
	    if($row_count >= 1){
	    	$submit_name = 'Pay Next Installment';
	    }else{
	    	$submit_name = 'Send';
	    }
		$form[ACTIONS]['submit'] = array(
			HASH_TYPE => 'submit',
			HASH_VALUE => $submit_name,
			'#attributes' => [
		        'class' => [
		          'use-ajax',
		        ],
		    ],
		    '#ajax' => [
		        'callback' => [$this, 'agreementPaymentAjax'],
		        'event' => 'click',
		    ],
		);
		$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
		return $form;
	}
	 /**
	   * AJAX callback handler that displays any errors or a success message.
	   */
	  public function agreementPaymentAjax(array $form, FormStateInterface $form_state) {
	      $doamin = _get_current_domain();
	      $response = new AjaxResponse();
	      
	      if ($form_state->hasAnyErrors()) {
	        $response->addCommand(new ReplaceCommand('#wrapper_modal_agreement_payment_details', $form));
	        return $response;
	      }
	      else {
	        $command = new CloseModalDialogCommand();
	        $response->addCommand($command);
	        drupal_set_message(t('Payment has been sucessfully done.'), STATUS);
	        $response->addCommand(new RedirectCommand('/agreements-listing'));
	        return $response;
	      } 
	  }
	public function validateForm(array &$form, FormStateInterface $form_state) {
		$field = $form_state->getValues();
		$nid = $form_state->getBuildInfo()['args'][0];
		$conn = Database::getConnection();
	    $query = $conn->select(TABLE_SILAI_NGO_PAYMENT_DETAIL, 's')
	            ->condition('nid', $nid)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $output = 0;
	    $total_pay_am = 0;
	    foreach ($payDatas as $payData) {
	    	$output = $payData->amount + $total_pay_am;
	    	$total_pay_am = $output;
	    }
	    $amount = $field['amount'];
	    $full_amount = $amount + $total_pay_am;
		$agreement_amount = $field[AGREEMENT_AMOUNT];
	    if($agreement_amount < $full_amount ){
	    	$message = 'Due Amount is : '.($agreement_amount - $total_pay_am);
	    	$form_state->setErrorByName(AMOUNT, $message);
	    }
	    $payment_mode = $field['payment_mode'];
	    if($payment_mode == 1){
	    	if(empty($field['cheque_dd_no'])){
	    		$message = 'CHEQUE/DD NO. field is required.';
	    		$form_state->setErrorByName('cheque_dd_no', $message);
	    	}
	    }
    }
    public function submitForm(array &$form, FormStateInterface $form_state) {	
		$nid = $form_state->getBuildInfo()['args'][0];	
		$field = $form_state->getValues();
		$cararr_id = $_GET['cararr_id'];
		$agreement_id = $_GET['agreement_id'];

		$dataArray = array(
            'nid'               => $nid,
            'agreement_id'      => $agreement_id,
            'cararr_id'         => $cararr_id,
            INSTALLMENT       => $field[INSTALLMENT],
            PAYMENT_MODE      => $field[PAYMENT_MODE],
            AMOUNT            => $field[AMOUNT],
            'cheque_no'         => $field['cheque_dd_no'],
            'bank_drawn'        => $field['bank_drawn'],
            'transaction_date'  => strtotime($field['transaction_date']),
            'invoice_no'        => $field['invoice_no'],
            'invoice_date'      => strtotime($field['invoice_date']),
            'payment_status'    => 0,
        );

    	$database = \Drupal::database();
    	$insert_query = $database->insert(TABLE_SILAI_NGO_PAYMENT_DETAIL)->fields($dataArray)->execute(); 

        # update recive balance and due balance in content type.
        $conn = Database::getConnection();
	    $query = $conn->select(TABLE_SILAI_NGO_PAYMENT_DETAIL, 's')
	            ->condition('nid', $nid)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $output = 0;
	    $total_pay_am = 0;
	    foreach ($payDatas as $payData) {
	    	$output = $payData->amount + $total_pay_am;
	    	$total_pay_am = $output;
	    }

	    $amount = $field['amount'];
	    $full_amount = $amount + $total_pay_am;
	    $agreement_ammount = $field[AGREEMENT_AMOUNT];
		$due_amount = $agreement_ammount - $total_pay_am;
		//print_r($due_amount);
		//die();
		$node = Node::load($nid);
		$node->set('field_silai_agre_received_amount', $total_pay_am); 
		$node->set('field_silai_agree_due_balance', $due_amount);
		$node->set('field_received_payment_status', '1'); 
		$node->save();
	} 
}



















