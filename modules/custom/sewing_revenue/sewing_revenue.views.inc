<?php
function sewing_revenue_views_data() {
	$data['usha_generate_fee_receipt']['table']['group'] = t('usha generate fee receipt');
    $data['usha_generate_fee_receipt']['table']['base'] = array(
    'field' => 'id',
    TITLE => t('Generate fee receipt table'),
    'weight' => -10,
    );

    $data['usha_generate_fee_receipt'][TABLE][JOIN] = array(
      TABLE_NODE_FIELD_DATA => [
        LEFT_FIELD => 'state_id',
        FIELD => NID,
      ],
    );
    $data['usha_generate_fee_receipt'][NID] = array(
      TITLE => t('state relationship title'),
      RELATIONSHIP => array(
        BASE => TABLE_NODE_FIELD_DATA, 
        TEXT_BASE_FIELD => NID,
        FIELD => 'state_id', 
        ID => STANDARD,
        HANDLER => 'views_handler_relationship',
        LABEL => t('state node'),
      ),
    );
    $data['usha_generate_fee_receipt'][TABLE][JOIN] = array(
      TABLE_NODE_FIELD_DATA => [
        LEFT_FIELD => 'town_id',
        FIELD => NID,
      ],
    );
    $data['usha_generate_fee_receipt'][NID] = array(
      TITLE => t('town relationship title'),
      RELATIONSHIP => array(
        BASE => TABLE_NODE_FIELD_DATA, 
        TEXT_BASE_FIELD => NID,
        FIELD => 'town_id', 
        ID => STANDARD,
        HANDLER => 'views_handler_relationship',
        LABEL => t('town node'),
      ),
    );
    $data['usha_generate_fee_receipt'][TABLE][JOIN] = array(
      TABLE_NODE_FIELD_DATA => [
        LEFT_FIELD => 'school_id',
        FIELD => NID,
      ],
    );
    $data['usha_generate_fee_receipt'][NID] = array(
      TITLE => t('school relationship title'),
      RELATIONSHIP => array(
        BASE => TABLE_NODE_FIELD_DATA, 
        TEXT_BASE_FIELD => NID,
        FIELD => 'school_id', 
        ID => STANDARD,
        HANDLER => 'views_handler_relationship',
        LABEL => t('school node'),
      ),
    );


  return $data;
}
