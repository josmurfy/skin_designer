<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?php echo $text_refund_details; ?></h4>
</div>
<div class="modal-body" id="squareup-refund-body">
    <div class="squareup-refund-step" id="squareup-refund-initial">
        <p><?php echo $text_select_refund_type; ?></p>
        <p>
            <label class="radio-inline">
            <input type="radio" name="refund_type" value="amount" checked />
                <?php echo $text_refund_only_amount; ?>
            </label>
        </p>
        <p>
            <label class="radio-inline">
            <input type="radio" name="refund_type" value="itemized" />
                <?php echo $text_refund_itemized; ?>
            </label>
        </p>
    </div>
    <div class="squareup-refund-step" id="squareup-refund-step-itemized-refund">
        <h3><?php echo $text_itemized_refund_heading; ?></h3>
        <p><?php echo $text_itemized_refund_intro; ?></p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?php echo $column_product_name; ?></th>
                        <th><?php echo $column_product_model; ?></th>
                        <th><?php echo $column_product_unit_price; ?></th>
                        <th><?php echo $column_product_quantity; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td class="itemized_name">
                                <div><?php echo $product['name']; ?></div>
                                <?php if (!empty($product['option']) && is_array($product['option'])) : ?>
                                    <?php foreach ($product['option'] as $option) : ?>
                                        <div><small>- <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="itemized_model">
                                <?php echo $product['model']; ?>
                            </td>
                            <td>
                                <?php echo $product['price']; ?>
                            </td>
                            <td>
                                <input type="number" data-price="<?php echo $product['price_raw']; ?>" class="form-control" name="itemized_refund[<?php echo $product['order_product_id']; ?>]" data-type="quantity_refund" data-order-product-id="<?php echo $product['order_product_id']; ?>" value="0" min="0" max="<?php echo $product['max_refund_quantity']; ?>" />
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <hr />
        <div class="form-group">
            <label class="control-label" id="squareup-refund-modal-content-itemized"><?php echo $text_insert_amount; ?></label>
            <input class="form-control" type="text" id="squareup-refund-itemized-insert" value="0" required data-max-allowed="<?php echo $max_allowed; ?>" data-price-prefix="<?php echo $price_prefix; ?>" data-price-suffix="<?php echo $price_suffix; ?>" />
        </div>
    </div>
    <div class="squareup-refund-step" id="squareup-refund-step-itemized-restock">
        <h3><?php echo $text_itemized_restock_heading; ?></h3>
        <p><?php echo $text_itemized_restock_intro; ?></p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?php echo $column_product_name; ?></th>
                        <th><?php echo $column_product_model; ?></th>
                        <th><?php echo $column_product_unit_price; ?></th>
                        <th><?php echo $column_product_quantity; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td class="itemized_name">
                                <div><?php echo $product['name']; ?></div>
                                <?php if (!empty($product['option']) && is_array($product['option'])) : ?>
                                    <?php foreach ($product['option'] as $option) : ?>
                                        <div><small>- <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="itemized_model">
                                <?php echo $product['model']; ?>
                            </td>
                            <td>
                                <?php echo $product['price']; ?>
                            </td>
                            <td>
                                <?php if ($product['is_ad_hoc_item']) : ?>
                                    <i class="fa fa-warning text-warning" data-toggle="tooltip" title="<?php $text_is_ad_hoc_item; ?>"></i>
                                <?php else : ?>
                                    <input type="number" data-price="<?php echo $product['price_raw']; ?>" class="form-control" name="itemized_restock[<?php echo $product['order_product_id']; ?>]" data-type="quantity_restock" data-order-product-id="<?php echo $product['order_product_id']; ?>" value="0" min="0" max="<?php echo $product['max_restock_quantity']; ?>" />
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="squareup-refund-step" id="squareup-refund-confirm-itemized">
        <h3><?php echo $text_itemized_refund_summary_heading; ?></h3>
        <div class="alert alert-info" id="itemized_refund_restock_total"></div>
        <hr />
        <h3><?php echo $text_itemized_restock_summary_heading; ?></h3>
        <div id="itemized_refund_restock_items"></div>
    </div>
    <div class="squareup-refund-step" id="squareup-refund-confirm-amount">
        <div class="form-group">
            <label class="control-label" id="squareup-refund-modal-content-reason"><?php echo $text_confirm_refund; ?></label>
            <textarea class="form-control" id="squareup-refund-reason-insert" required></textarea>
        </div>
        <div class="form-group">
            <label class="control-label" id="squareup-refund-modal-content-amount"><?php echo $text_insert_amount; ?></label>
            <input class="form-control" type="text" id="squareup-refund-amount-insert" required data-max-allowed="<?php echo $max_allowed; ?>" />
        </div>
    </div>
</div>
<div class="modal-footer">
    <button id="squareup-refund-back" type="button" class="btn btn-default"><?php echo $text_back; ?></button>
    <button id="squareup-refund-next" type="button" class="btn btn-primary"><?php echo $text_next; ?></button>
    <button id="squareup-refund-finish" type="button" class="btn btn-primary"><?php echo $text_ok; ?></button>
</div>