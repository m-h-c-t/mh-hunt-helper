<!-- Results -->
<h2 style="margin-top:0;"><i class="glyphicon glyphicon-globe"></i> Locations <span class="text-muted" style="font-size:small;">shortened url?</span></h2>
<!-- <br/>Mice found: <?php echo $results['found']['count']; ?><br/>-->

<?php if ($results['not_found']['count']) { ?>
    <br/><?php echo $results['not_found']['count']; ?> mice not found:<br/>
    <br/><?php foreach ($results['not_found']['mice'] as $mouse) { echo $mouse . "</br>"; }; ?><br/>
<?php } ?>
