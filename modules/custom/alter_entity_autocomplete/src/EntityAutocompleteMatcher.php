<?php

namespace Drupal\alter_entity_autocomplete;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\user\Entity\User;

class EntityAutocompleteMatcher extends \Drupal\Core\Entity\EntityAutocompleteMatcher {

  /**
   * Gets matched labels based on a given search string. 
   */
  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '') {
    $matches = [];
      $nodeType = array_keys($selection_settings['target_bundles']);
      if($nodeType[0] == 'manage_towns') {
          $options = $selection_settings + [
            'target_type' => $target_type,
            'handler' => $selection_handler,
          ];
        $handler = $this->selectionManager->getInstance($options);
        if (isset($string)) {
          $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
          $entity_labels = $this->getReferenceableEntities($string, $match_operator, 'manage_towns', $target_type, 25);

          $matches = $this->createAutoSearchOutPut($entity_labels, $target_type, 'field_town_code');

          // foreach ($entity_labels as $row => $values) {
          //     foreach ($values as $entity_id => $label) {
          //       $entity = \Drupal::entityTypeManager()->getStorage($target_type)->load($entity_id);
          //       $entity = \Drupal::entityManager()->getTranslationFromContext($entity);
          //       $type = !empty($entity->type->entity) ? $entity->type->entity->label() : $entity->bundle();
          //       $d_type = !empty($entity->type->entity) ? $entity->title->value : $entity->title->value;

          //       $townCode = !empty($entity->type->entity) ? $entity->get('field_town_code')->getValue()[0]['value'] : $entity->get('field_town_code')->getValue()[0]['value'];
          //       $key = $d_type.' ('.$entity_id.')';
          //       $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
          //       $key = Tags::encode($key);
          //       $label = $d_type.' ('.$townCode.')';
          //       $matches[] = ['value' => $key, 'label' => $label];
          //     }            
          // }
        }
       
      }

      if(in_array('sewing_school', $nodeType)) {
         $options = [
          'target_type'      => $target_type,
          'handler'          => $selection_handler,
          'handler_settings' => $selection_settings,
        ];

        if (isset($string)) {
          $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
          $entity_labels = $this->getReferenceableEntities($string, $match_operator, 'sewing_school', $target_type, 25);

           $matches = $this->createAutoSearchOutPut($entity_labels, $target_type, 'field_sewing_school_code');

        }

        
      } else if(in_array('silai_school', $nodeType)) {
         $options = [
          'target_type'      => $target_type,
          'handler'          => $selection_handler,
          'handler_settings' => $selection_settings,
        ];

        if (isset($string)) {
          $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
          $entity_labels = $this->getReferenceableEntities($string, $match_operator, 'silai_school', $target_type, 25);

          $matches = $this->createAutoSearchOutPut($entity_labels, $target_type, 'field_school_code');

        }

        
      } else {
          $options = $selection_settings + [
            'target_type' => $target_type,
            'handler' => $selection_handler,
          ];
          $handler = $this->selectionManager->getInstance($options);

          if (isset($string)) {
            // Get an array of matching entities.
            $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
            $entity_labels = $handler->getReferenceableEntities($string, $match_operator, 10);

            // Loop through the entities and convert them into autocomplete output.
            foreach ($entity_labels as $values) {
              foreach ($values as $entity_id => $label) {
                $key = "$label ($entity_id)";
                // Strip things like starting/trailing white spaces, line breaks and
                // tags.
                $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
                // Names containing commas or quotes must be wrapped in quotes.
                $key = Tags::encode($key);
                $matches[] = ['value' => $key, 'label' => $label];
              }
            }
          }        
      }
      return $matches;
  }

  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $type = 'sewing_school', $target_type='node',  $limit = 0)
  {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $userData = User::load($current_user->id());
    $masterDataService = \Drupal::service('silai.master_data');
    $locationIds = [];
    $ngoId = [];
    if($roles[1] == ROLE_SEWING_SSI){
        //$loactionId = $userData->field_user_location->target_id;
        $location = $userData->get('field_user_location');
        foreach ($location as $key => $value) {
            $locationIds[] = $value->target_id;
        }
    } else if ($roles[1] == ROLE_SILAI_PC) {
      //$loactionId = $userData->field_user_location->target_id;
      $location = $userData->get('field_user_location');
        foreach ($location as $key => $value) {
            $locationIds[] = $value->target_id;
        }
    } else if($roles[1] == ROLE_SILAI_NGO_ADMIN) {
      $locationIds = [];
      $ngoData = $masterDataService->getLinkedNgoForUser($current_user->id());
      $ngoId = [$ngoData[$current_user->id()]];
    }
    
    $type = $type;
    if($type == 'sewing_school') {
      $field = 'field_sewing_school_code';
      $query =\Drupal::entityQuery($target_type)
        ->condition('type', $type)
        ->condition(STATUS, 1);
        if(!empty($locationIds)) {
          $query->condition('field_location', $locationIds, 'IN');
        }
        $query->condition('field_sew_school_approval_status', 1)
        ->condition($field, $match, $match_operator)
        ->range(0, $limit);
    } else if($type == 'silai_school') {
        $field = 'field_school_code';
        $query =\Drupal::entityQuery($target_type)
          ->condition('type', $type)
          ->condition(STATUS, 1);
        
        if(!empty($locationIds)) {
          $query->condition('field_silai_location', $locationIds, 'IN');
        } 
        if(!empty($ngoId)) {
          $query->condition('field_name_of_ngo', $ngoId, 'IN');
        }
        $query->condition('field_sil_school_approval_status', 1)
        ->condition($field, $match, $match_operator)
        ->range(0, $limit);
    } else if($type == 'manage_towns') {
      $query =\Drupal::entityQuery($target_type)
        ->condition('type', $type)
        ->condition(STATUS, 1);
        if(!empty($locationIds)) {
          $query->condition('field_location', $locationIds, 'IN');
        }
        $query->condition('title', $match, $match_operator);
        $query->range(0, $limit);      
     } else {

     }
     
    $result = $query->execute();
    if (empty($result)) {
      return [];
    }
    
    $options = array();
    $entities = \Drupal::entityManager()->getStorage($target_type)->loadMultiple($result);
    
    foreach ($entities as $entity_id => $entity) {
      $bundle = $entity->bundle();
       $options[$bundle][$entity_id] = Html::escape(\Drupal::entityManager()
      ->getTranslationFromContext($entity)
      ->label());
    }
    return $options;
  }


  public function createAutoSearchOutPut($data, $target_type, $field) {

    // Loop through the entities and convert them into autocomplete output.
          foreach ($data as $row => $values) {
              foreach ($values as $entity_id => $label) {
                $entity = \Drupal::entityTypeManager()->getStorage($target_type)->load($entity_id);
                $entity = \Drupal::entityManager()->getTranslationFromContext($entity);
                $type = !empty($entity->type->entity) ? $entity->type->entity->label() : $entity->bundle();
                $d_type = !empty($entity->type->entity) ? $entity->$field->value : $entity->$field->value;
                $title = !empty($entity->type->entity) ? $entity->title->value : $entity->title->value;
                
                $key = $d_type.' ('.$entity_id.')';
                // Strip things like starting/trailing white spaces, line breaks and tags.
                $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
                // Names containing commas or quotes must be wrapped in quotes.
                $key = Tags::encode($key);
                
               // $label = $d_type;
                $label = $d_type.' ('.$title.')';
                $matches[] = ['value' => $key, 'label' => $label];
              }
             
          }

      return $matches;
  }
  
}
