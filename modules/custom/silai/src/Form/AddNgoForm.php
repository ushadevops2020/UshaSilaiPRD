<?php
/**
 * @file
 * Contains \Drupal\silai\Form\AddNgoForm.
 */
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

class AddNgoForm extends FormBase {
  /**
   * {@inheritdoc}
   */
	public function getFormId() {
		return 'modal_add_ngo_form';
	}

/**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) 
  {
    $userId = $_GET['data'];
    $currentDomain = _get_current_domain();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $location = [UNDERSCORE_NONE => SELECT_VALUE];
    $cancelRedirectURI = 'ngo-users-list';
  	$masterDataService = \Drupal::service('silai.master_data');
  	$locationArr = $masterDataService->getLocationByCountryId();

    if(!empty($locationArr)) {
        $location = $location + $locationArr;
    }   
    $userStatus = 1;
    if(!empty($userId)) {
     $account = \Drupal\user\Entity\User::load($userId);
     $userName = $account->getUsername();
     $accountRoles = $account->getRoles();
     $firstName =  ($account->field_first_name->value) ? $account->field_first_name->value : '';
     $lastName =  ($account->field_last_name->value) ? $account->field_last_name->value : '';
     $emailId =  ($account->getEmail()) ? $account->getEmail() : '';
     $contactNo =  ($account->field_user_contact_no->value) ? $account->field_user_contact_no->value : '';
     $profileId =  ($accountRoles[1]) ? $accountRoles[1] : '';
     $fieldUserId = $account->getUsername();
     $userLocation =  ($account->field_user_location->target_id) ? $account->field_user_location->target_id : '';
     $userStatus =  $account->status->value;
    }
    
    $form[HASH_PREFIX] = '<div id="wrapper_modal_add_user_form">';
    $form[HASH_SUFFIX] = '</div>';

    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];

    $form['field_hidden_user_id'] = [
      HASH_TYPE => 'hidden',
      HASH_VALUE => ($userId) ? $userId : '',
      
    ];

    $form[FIELD_FIRST_NAME] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('First Name'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($firstName) ? $firstName : '',
      HASH_MAXLENGTH => 30,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
    ];

     $form[FIELD_LAST_NAME] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Last Name'),
      HASH_DEFAULT_VALUE => ($lastName) ? $lastName : '',
      HASH_MAXLENGTH => 30,
      HASH_REQUIRED => TRUE,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
    ];

    $form[FIELD_USER_ID] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('User Name'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($fieldUserId) ? $fieldUserId : '',
      HASH_MAXLENGTH => 15,
      HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
    ];

     
	$form[FIELD_USER_PASSWORD] = [
		HASH_TYPE => 'password',
		HASH_TITLE => $this->t('Password'),
		HASH_REQUIRED => ($userId) ? FALSE : TRUE,
		HASH_MAXLENGTH => 20,
	];
    
    $form[FIELD_USER_EMAIL] = [
      HASH_TYPE => 'email',
      HASH_TITLE => $this->t('Email Id'),
      HASH_REQUIRED => TRUE,
      HASH_DEFAULT_VALUE => ($emailId) ? $emailId : '',
      HASH_MAXLENGTH => 100,
    ];

    $form[FILED_USER_CONTACT_NO] = [
      HASH_TYPE => TEXTFIELD,
      HASH_TITLE => $this->t('Contact No.'),
      HASH_DEFAULT_VALUE => ($contactNo) ? $contactNo : '',
      HASH_MAXLENGTH => 11,
      HASH_REQUIRED => TRUE,
      HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
    ]; 

    $form[FIELD_USER_STATUS] = [
      HASH_TYPE => 'radios',
      HASH_TITLE => $this->t('Status'),
      HASH_DEFAULT_VALUE => $userStatus,
      HASH_OPTIONS => [$this->t('Block'), $this->t('Active')],
      HASH_REQUIRED => TRUE,
    ];

     

    $form[ACTIONS] = array(HASH_TYPE => ACTIONS);
    $form[ACTIONS]['cancel'] = array(
    '#type' => 'button',
    '#value' => t('Cancel'),
    '#weight' => -1,
    '#attributes' => array('onClick' => 'window.location.href = "/'.$cancelRedirectURI.'"; event.preventDefault();'),
    );
    $form[ACTIONS]['send'] = [
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t('Save'),
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
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * AJAX callback handler for showing and hiding location dropdown based on user's profile.
  */
  public function checkProfile(array $form, FormStateInterface $form_state) {
    $profile = $form_state->getValue(FILED_PROFILE);

    $response = new AjaxResponse(); 
    if(in_array($profile, [ROLE_SEWING_SSI, ROLE_SILAI_PC])) {

      $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('#user_location_wrapper', 'removeClass', ['hide-user-location']));

    } else {
      $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('#user_location_wrapper', 'addClass', ['hide-user-location']));
      $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('#user_location', 'val', [UNDERSCORE_NONE]));
      
    }
    #$form_state->setRebuild();
    return $response;
  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function addUserAjax(array $form, FormStateInterface $form_state) {
      $doamin = _get_current_domain();
      $response = new AjaxResponse();
      
      if ($form_state->hasAnyErrors()) {
        $response->addCommand(new ReplaceCommand('#wrapper_modal_add_user_form', $form));
        return $response;
      }
      else {
        $command = new CloseModalDialogCommand();
        $response->addCommand($command);
        drupal_set_message(t('User information saved succesfully.'), STATUS);
        $response->addCommand(new RedirectCommand('/ngo-users-list'));
        return $response;
      } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $emailId = $form_state->getValue(FIELD_USER_EMAIL);
    $uniqueId = $form_state->getValue(FIELD_USER_ID);
    $password = $form_state->getValue(FIELD_USER_PASSWORD);

    if(!empty($uniqueId) && strlen($uniqueId) < 4) {
      $form_state->setErrorByName(FIELD_USER_ID, t('Username length must be atleast 4 characters')); 
    }
    if(!empty($password) && strlen($password) < 6) {
      $form_state->setErrorByName(FIELD_USER_PASSWORD, t('Password length must be atleast 6 characters')); 
    }
    $query = \Drupal::entityQuery('user');

    #For edit mode
    $userId = $_GET['data'];
    
    if ($userId) {
      $query->condition('uid', $userId, '!=');
    }

    $uids = $query->execute();
    $user_storage = \Drupal::entityManager()->getStorage('user');

    #Load multiple nodes
    $users = $user_storage->loadMultiple($uids);
    foreach ($users as $n) {
      $emailIdArr[] = strtolower($n->mail->value);
      $uniqueIdArr[] = strtolower($n->getUsername());
    }
    
    if( in_array(strtolower($emailId), $emailIdArr)) {
        $form_state->setErrorByName(FIELD_USER_EMAIL, t('Email Id already exist.'));
    } 

    if( in_array(strtolower($uniqueId), $uniqueIdArr)) {
        $form_state->setErrorByName(FIELD_USER_ID, t('User Name already exist.'));
    }
  }

  /**
   * Method to add and update user's information using custom form
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $firstName = $form_state->getValue(FIELD_FIRST_NAME);
    $lastName = $form_state->getValue(FIELD_LAST_NAME);
    $emailId = $form_state->getValue(FIELD_USER_EMAIL);
    $contactNo = $form_state->getValue(FILED_USER_CONTACT_NO);
    $userProfile = $form_state->getValue(FILED_PROFILE);
    $uniqueId = $form_state->getValue(FIELD_USER_ID);
    $password = $form_state->getValue(FIELD_USER_PASSWORD);
    $userId = $form_state->getValue('field_hidden_user_id');
    $userLocation = $form_state->getValue(FILED_USER_LOCATION);
    $userStatus = $form_state->getValue(FIELD_USER_STATUS);
    
    if(!empty($userId)) {
      $user = \Drupal\user\Entity\User::load($userId);
      $accountRoles = $user->getRoles();

      $user->setEmail($emailId);
      $user->setUsername($uniqueId);
       if(!empty($password)) {
        $user->setPassword($password);
      }

      $user->set(FIELD_FIRST_NAME, $firstName);
      $user->set(FIELD_LAST_NAME, $lastName);
      $user->set(FILED_USER_CONTACT_NO, $contactNo);
      $user->set(FILED_USER_LOCATION, $userLocation);
      $user->set(STATUS, $userStatus);
      $user->save();
    } else {
      $user = \Drupal\user\Entity\User::create();
       if(!empty($password)) {
        $user->setPassword($password);
      }
      
      
      $user->setEmail($emailId);
      $user->setUsername($uniqueId);
      $user->addRole(ROLE_SILAI_NGO_ADMIN);
      $user->set(FIELD_FIRST_NAME, $firstName);
      $user->set(FIELD_LAST_NAME, $lastName);
      $user->set(FILED_USER_CONTACT_NO, $contactNo);
      $user->set(FILED_USER_LOCATION, $userLocation);
      $user->set(STATUS, $userStatus);
      $user->enforceIsNew();
      #Save user
      $user->save();
      $uid = $user->id();
      
      #Mapping Of User with NGO
      $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      $userId = \Drupal::currentUser()->id();
      $masterDataService = \Drupal::service('silai.master_data');
      $ngoData = $masterDataService->getLinkedNgoForUser($userId);
      $database = Database::getConnection();   
      $dataArr = array(
          'nid' => $ngoData[$userId],
          'user_id' => $uid,
          'created' => time(),
          
      );        
      $query = $database->insert('silai_ngo_associated_user')->fields($dataArr)->execute();         
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
  	return ['config.modal_add_ngo_form'];
  }
}