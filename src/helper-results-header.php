<!-- Results -->
<h2 style="margin-top:0;">Results<span class="text-muted"> (<?php echo $results['found']['count']; ?>)</span></h2>

<?php if ($results['not_found']['count']) { ?>
<div class="row">
    <div class="panel-group col-md-6 col-md-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading"><?php echo $results['not_found']['count'] . " " . $name; ?> not found!</div>
            <?php foreach ($results['not_found']['items'] as $item) { ?>
            <div class="panel-body"><?php echo $item; ?></div>
            <?php }; ?>
        </div>
    </div>
</div>
<?php } ?>
