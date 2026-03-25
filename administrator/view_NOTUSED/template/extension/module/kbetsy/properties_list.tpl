<?php foreach($property_list as $property) { ?>
<div class="form-group <?php if ($property['required'] == "1") { ?> required <?php } ?>">
    <label class="control-label col-sm-2">
        <?php echo $property['name']; ?>
    </label>
    <div class="col-sm-10">
        <select name="property_attr[<?php echo $property['id']; ?>]<?php if ($property['multi'] == "1") { ?>[]<?php } ?>" class="form-control" id="property[<?php echo $property['id']; ?>]" <?php if ($property['multi'] == "1") { ?> multiple="multiple" <?php } ?>>
            <?php if ($property['multi'] != "1") { ?><option value="">Select Option</option><?php } ?>
            <?php foreach($property['values'] as $option) { ?>
               <?php if (in_array($option['id'],$property['selected'])) { ?>
                <option value="<?php echo $option['id']; ?>" selected="selected"><?php echo html_entity_decode($option['name'],1,'UTF-8'); ?></option>
            <?php } else { ?>
                <option value="<?php echo $option['id']; ?>"><?php echo html_entity_decode($option['name'],1,'UTF-8'); ?></option>
            <?php } ?>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>