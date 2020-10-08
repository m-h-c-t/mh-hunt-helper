<!-- Results -->
<h2 style="margin-top:0;">Results<span class="text-muted"> (<?php echo $results['found']['count']; ?>)</span></h2>

<?php if ($results['not_found']['count']) { ?>
    <div class="panel-group">
        <div class="panel panel-danger">
        <div class="panel-heading"><?php echo $results['not_found']['count']; ?> mice not found!</div>
        <?php foreach ($results['not_found']['mice'] as $mouse) { ?>
        <div class="panel-body"><?php echo $mouse; ?></div>
        <?php }; ?>
    </div></div>
<?php } ?>
