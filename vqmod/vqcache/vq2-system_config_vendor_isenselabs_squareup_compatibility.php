<?php

$_['squareup_imodule_version'] = '2.1.9';
$_['squareup_imodule_route_payment'] = 'extension/payment/squareup';
$_['squareup_imodule_route_credit_card'] = 'extension/credit_card/squareup';
$_['squareup_imodule_route_recurring'] = 'extension/recurring/squareup';
$_['squareup_imodule_extension_route'] = 'extension/extension';
$_['squareup_imodule_model_payment'] = 'model_extension_payment_squareup';
$_['squareup_imodule_model_credit_card'] = 'model_extension_credit_card_squareup';
$_['squareup_imodule_model_recurring'] = 'model_extension_recurring_squareup';
$_['squareup_imodule_event_model'] = 'model_extension_event';
$_['squareup_imodule_event_route'] = 'extension/event';
$_['squareup_imodule_events'] = array(
    'admin/controller/*/after' => 'extension/payment/squareup/setAdminURL',
    'admin/view/common/dashboard/before' => 'extension/payment/squareup/setAccessTokenAlert',
    'admin/view/common/column_left/before' => 'extension/payment/squareup/setAdminLink',
    'admin/view/catalog/product_form/before' => 'extension/payment/squareup/setProductWarning',
    'catalog/model/checkout/order/addOrderHistory/before' => 'extension/payment/squareup/beforeAddOrderHistory',
    'catalog/model/checkout/order/addOrderHistory/after' => 'extension/payment/squareup/afterAddOrderHistory'
);
$_['squareup_imodule_recurring_info_status_key'] = 'status';
$_['squareup_imodule_recurring_id_get_parameter'] = 'order_recurring_id';
$_['squareup_imodule_recurring_get_method_name'] = 'getRecurring';
$_['squareup_imodule_recurring_edit_model'] = 'model_account_recurring';
$_['squareup_imodule_add_order_history_method'] = 'addOrderHistory';
$_['squareup_imodule_update_order_history_method'] = 'addOrderHistory';
