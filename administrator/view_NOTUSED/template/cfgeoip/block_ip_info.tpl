<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="
					<?php echo $cancel; ?>" data-toggle="tooltip" title="
					<?php echo $button_cancel; ?>" class="btn btn-default">
					<i class="fa fa-reply"></i>
				</a>
			</div>
			<h1>
				<?php echo $heading_title; ?>
			</h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li>
					<a href="
						<?php echo $breadcrumb['href']; ?>">
						<?php echo $breadcrumb['text']; ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-info-circle"></i>
					<?php echo $text_detail; ?>
				</h3>
			</div>
			<div class="panel-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th><?php echo  $entry_user_ip ?></td>
							<td><?php echo  $user_ip ?></td>
						</tr>
						<?php if($error_msg == ''){ ?>
						<tr>
						<th><?php echo  $entry_country_iso_code ?></td>
						<td><?php echo  $country_iso_code ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_country_name ?></td>
						<td><?php echo  $country_name ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_subdivision_name ?></td>
						<td><?php echo  $subdivision_name ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_subdivision_iso_code ?></td>
						<td><?php echo  $subdivision_iso_code ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_postal_code ?></td>
						<td><?php echo  $postal_code ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_latitude ?></td>
						<td><?php echo  $latitude ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_longitude ?></td>
						<td><?php echo  $longitude ?></td>
						</tr>
						<?php } ?>
						<tr>
						<th><?php echo  $entry_access_page ?></td>
						<td><?php echo  $access_page ?></td>
						</tr>
						<tr>
						<th><?php echo  $entry_access_date ?></td>
						<td><?php echo  $access_date ?></td>
						</tr>
						<?php if($error_msg){ ?>
						<tr>
						<th><?php echo  $entry_error_msg ?></td>
						<td><?php echo  $error_msg ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php echo $footer; ?> 
