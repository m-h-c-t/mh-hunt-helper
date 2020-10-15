<!-- helper Form -->
<form novalidate name="item_list_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="form-group row">
        <h2><i class="glyphicon glyphicon-pencil"></i> <?php echo ucfirst($name); ?></h2>
        <textarea rows="10" class="form-control input-lg" placeholder="Copy map <?php echo $name; ?> list here..." required name="<?php echo $name; ?>" id="<?php echo $name; ?>"
          ><?php if (isset($results['original_items']) && !empty($results['original_items'])) { echo htmlspecialchars(implode("\n", $results['original_items'])); } ?></textarea>
    </div>
    <div class="form-group row">
        <button type="submit" class="btn btn-lg btn-primary col-xs-4 col-xs-offset-1 col-sm-2 col-sm-offset-3">Search</button>
        <button type="button" id="reset" class="btn btn-lg btn-danger col-xs-4 col-xs-offset-2 col-sm-2 col-sm-offset-2">Reset</button>
    </div>
</form>
