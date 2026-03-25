<div class="alert alert-light bg-light">
    <?php echo $info_configure_warmup; ?>
</div>

<div class="form-group">
    <h5 class="mb-2"><?php echo $entry_warmup_language; ?></h5>
    <?php foreach ($languages as $language): ?>
    <div class="row">
        <div class="col-xs-8 col-form-label">
            <?php echo $language['text']; ?>
            <?php if ($language['default']) : ?>
                <span class="badge badge-secondary"><?php echo $text_default; ?></span>
            <?php endif; ?>
        </div>
        <div class="col-xs-4 text-right">
            <label class="switch" <?php if ($language['disabled']): ?> data-toggle="tooltip" title="<?php echo $text_disabled_default_language; ?>" <?php endif; ?>>
                <input type="checkbox" name="language[]" data-exclude-name="excluded_warmup_languages[]" value="<?php echo $language['code']; ?>" <?php echo $language['status'] ? 'checked' : ''; ?> <?php echo $language['disabled'] ? 'disabled' : ''; ?> />
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <?php endforeach; ?>
    <hr />
    <h5 class="mb-2"><?php echo $entry_warmup_currency; ?></h5>
    <?php foreach ($currencies as $currency): ?>
    <div class="row">
        <div class="col-xs-8 col-form-label">
            <?php echo $currency['text']; ?>
            <?php if ($currency['default']) : ?>
                <span class="badge badge-secondary"><?php echo $text_default; ?></span>
            <?php endif; ?>
        </div>
        <div class="col-xs-4 text-right">
            <label class="switch" <?php if ($currency['disabled']): ?> data-toggle="tooltip" title="<?php echo $text_disabled_default_currency; ?>" <?php endif; ?>>
                <input type="checkbox" name="currency[]" data-exclude-name="excluded_warmup_currencies[]" value="<?php echo $currency['code']; ?>" <?php echo $currency['status'] ? 'checked' : ''; ?> <?php echo $currency['disabled'] ? 'disabled' : ''; ?> />
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <?php endforeach; ?>
    <hr />
    <h5 class="mb-2"><?php echo $entry_warmup_route; ?></h5>
    <?php foreach ($routes as $route) : ?>
    <div class="row">
        <div class="col-xs-8 col-form-label">
            <?php echo $route['text']; ?>
        </div>
        <div class="col-xs-4 text-right">
            <label class="switch">
                <input type="checkbox" name="route[]" data-route-name="included_warmup_routes[]" value="<?php echo $route['value']; ?>" <?php echo $route['status'] ? 'checked' : ''; ?> />
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <?php endforeach; ?>
    <hr />
    <h5 class="mb-2"><?php echo $entry_warmup_misc; ?></h5>
    <div class="row">
        <div class="col-xs-8 col-form-label">
            <?php echo $text_product_categories_warmup; ?>
        </div>
        <div class="col-xs-4 text-right">
            <label class="switch">
                <input type="checkbox" name="product_categories_warmup" data-finetune-name="product_categories_warmup" value="1" <?php echo $product_categories_warmup ? 'checked' : ''; ?> />
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</div>
