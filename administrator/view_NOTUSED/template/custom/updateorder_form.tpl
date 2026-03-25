<?php echo $header; ?>
<?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>Update Order Quantities</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> ORDERS</h3>
            </div>
            <div class="panel-body">
                <form id="form_67341" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <a href="updateorderfromebay.php?imp=oui" class="btn btn-primary">Imprimer le package slip</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><button type="button" onclick="selectAll()" class="btn btn-default">Select All</button><br><button type="button" onclick="UnSelectAll()" class="btn btn-default">Unselect All</button></th>
                                    <th>Image</th>
                                    <th>SKU</th>
                                    <th>CLIENT</th>
                                    <th>Titre</th>
                                    <th>Prix</th>
                                    <th>QTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)) { ?>
                                    <?php foreach ($orders as $order) { ?>
                                        <tr>
                                            <td><input type="checkbox" name="vendu[]" value="<?php echo $order['order_id'] . ',' . $order['quantity'] . ',1'; ?>"/><?php echo $order['order_id']; ?></td>
                                            <td><img src="<?php echo $order['image']; ?>" height="50"/></td>
                                            <td><?php echo $order['sku']; ?></td>
                                            <td><?php echo $order['client']; ?></td>
                                            <td><?php echo $order['title']; ?></td>
                                            <td><?php echo $order['price']; ?></td>
                                            <td><?php echo $order['quantity']; ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No orders found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="panel-title">INVENTAIRE</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>SKU</th>
                                    <th>Titre</th>
                                    <th>ENTREPOT</th>
                                    <th>STOCK</th>
                                    <th>A PLACER</th>
                                    <th>STOCK</th>
                                    <th>VENDU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($inventory)) { ?>
                                    <?php foreach ($inventory as $item) { ?>
                                        <tr>
                                            <td><img src="<?php echo $item['image']; ?>" height="50"/></td>
                                            <td><?php echo $item['sku']; ?></td>
                                            <td><?php echo $item['title']; ?></td>
                                            <td><?php echo $item['warehouse']; ?></td>
                                            <td><?php echo $item['stock']; ?></td>
                                            <td></td>
                                            <td>0</td>
                                            <td><?php echo $item['sold']; ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No inventory items found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="form_id" value="67341"/>
                        <input type="hidden" name="new" value=""/>
                        <input type="hidden" name="ebayinputarbonum" value=""/>
                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    </div>

                    <div class="form-group">
                        <a href="interneusa.php" class="btn btn-default">Retour au MENU</a>
                        <a href="https://phoenixsupplies.ca/interne/updateorderfromebay.php" class="btn btn-default">Orders Business</a>
                        <a href="https://phoenixsupplies.ca/interne/updateordersite.php" class="btn btn-default">Orders sur SITE</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>

<script type="text/javascript">
    function selectAll() {
        var items = document.getElementsByName('vendu[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('vendu[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }
</script>
