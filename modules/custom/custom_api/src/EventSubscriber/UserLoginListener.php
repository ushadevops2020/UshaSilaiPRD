<?php

namespace Drupal\custom_api\EventSubscriber;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Xml;
use Drupal\Component\Serialization\Json;

/**
 * Class UserLoginListener.
 *
 * @package Drupal\custom_api
 */
class UserLoginListener implements EventSubscriberInterface {

  /**
   * @var path.current service
   */
  private $currentPath;
  /**
   * @var jwt.authentication.jwt service
   */
  private $jwtAuth;

  /**
   * Constructor with dependency injection
   */
  public function __construct($currentPath, $JwtAuth) {
    $this->currentPath = $currentPath;
    $this->jwtAuth = $JwtAuth;
  }  
  /**
   * Add JWT access token to user login API response
   */
  public function onHttpLoginResponse(FilterResponseEvent $event) {
    //Get response
    $response = $event->getResponse();
    // Get request
    $request = $event->getRequest();
    // Halt if not user login request
    $path = explode('/', $this->currentPath->getPath());
    if ($this->currentPath->getPath() == '/user/logout') {
      if($response->getStatusCode() === 403) {
        $content = $response->getContent();
        $decoded = array();
        $decoded = Json::decode($content);
        $decoded['isSuccess'] = 0;
        $decoded['responseCode'] = 0;
        $decoded['message'] = $decoded['message'];
        $response->setContent(Json::encode($decoded));
        $event->setResponse($response);
        return;
      }
    }

    // Ensure not error response
    // if ($response->getStatusCode() !== 200) {
    //   // print_r($response->getStatusCode());die;
    //     $content = $response->getContent();
    //     $decoded = array();
    //     $decoded = Json::decode($content);

    //     if(\Drupal::currentUser()->id()) {
    //       $decoded['isAlreadyLogin'] = 1;
    //     }
    //     $decoded['isSuccess'] = 0;
    //     $decoded['responseCode'] = 0;
    //     $decoded['statusCode'] = $response->getStatusCode();
    //     // $decoded['message'] = $content;
    //     $response->setContent(Json::encode($decoded));
    //     $event->setResponse($response);
    //     // user_logout();
    //     // session_destroy();
    //   return;
    // }
    // print_r($this->currentPath->getPath());die;
    // Just handle JSON format for now
    if ($request->query->get('_format') !== 'json') {
      return;
    }
    if ($this->currentPath->getPath() !== '/user/login') {
      if($path[1] == 'user' && is_numeric($path[2])) {
        $this->changeUserProfileResponse($event);
      }
      return;
    }
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    // print_r($user->getRoles());die;
    if (!in_array('sewing_school_teacher',$user->getRoles())) {
      user_logout();
      $content = $response->getContent();
      //$decoded = Json::decode($content);

      $decoded['isSuccess'] = true;
      $decoded['message'] = 'User is unauthorized for this application ';
      $decoded['responseCode'] = 1;
      $response->setContent(Json::encode($decoded));
      $event->setResponse($response);
      user_logout();
      session_destroy();
      return $response;
    }
    // Decode and add JWT token
    if ($content = $response->getContent()) {
      if ($decoded = Json::decode($content)) {
        // Add JWT access_token
        $access_token = $this->jwtAuth->generateToken();
        $decoded['access_token'] = $access_token;
        $value = $decoded;
        $decoded = array();
        $decoded['isSuccess'] = true;
        $decoded['message'] = 'Login succesfully';
        $decoded['responseCode'] = 1;
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

        $role=implode(',', $user->getRoles());
        $decoded['result'] = [
          "logout_token"  => $value['logout_token'],
          "csrf_token"    => $value['csrf_token'],
          "uid"           => \Drupal::currentUser()->id(),
          "name"          => $user->getUsername(),
          "mail"          => $user->getEmail(),
          "roleName"      => $role,
          "firstName"     => $user->field_first_name->value,
          "lastName"      => $user->field_last_name->value,
          "mobile"        => $user->field_user_contact_no->value,
          "location"      => $user->field_user_location->value
        ];
        // Set new response JSON
        $response->setContent(Json::encode($decoded));
        $event->setResponse($response);
      }
    }
  }

  /**
   * The subscribed events.
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::RESPONSE][] = ['onHttpLoginResponse'];
    return $events;
  }

  public function changeUserProfileResponse(FilterResponseEvent $event)  {
    $response = $event->getResponse();
    // Ensure not error response
    if ($response->getStatusCode() !== 200) {
      return;
    }
    // Get request
    $request = $event->getRequest();
    // Just handle JSON format for now
    if ($request->query->get('_format') !== 'json') {
      return;
    }
    if ($content = $response->getContent()) {
      if ($decoded = Json::decode($content)) {
        // Add JWT access_token
        $access_token = $this->jwtAuth->generateToken();
        $decoded['access_token'] = $access_token;
        $session = \Drupal::request()->getSession();
        $value = $decoded;
        $decoded = array();
        $decoded['isSuccess'] = true;
        $decoded['message'] = 'User Profile Details';
        $decoded['responseCode'] = 1;
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
        $masterDataService = \Drupal::service('silai.master_data');
        $schoolId = $masterDataService->getSchoolFromUserId(\Drupal::currentUser()->id());
        $nodes = \Drupal\node\Entity\Node::load($schoolId);
        $decoded['result'] = [
          "uid" => \Drupal::currentUser()->id(),
          "name" => $user->getUsername(),
          "mail" => $user->getEmail(),
          "firstName" => $user->field_first_name->value,
          "lastName" => $user->field_last_name->value,
          // "mobile" =>  $user->field_user_contact_no->value,
          "location" =>  $user->field_user_location->target_id,
          "school_code" => $nodes->field_school_code->value,
          "date_of_joining" => $nodes->field_date_open_of_silai_school->value,
          "total_learners" => 24,
          "currently_active" => 20,
          "aadhar_card" => "xxxxxxxxxxxxxxxxxxxxxxx"
        ];
        // Set new response JSON
        $response->setContent(Json::encode($decoded));
        $event->setResponse($response);
      }
    }
  }
}