<!-- nested collapse -->
<div class="panel-group" id="accordion1<?php echo $location_id; ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1<?php echo $location_id; ?>" href="#collapseThree<?php echo $location_id; ?>">
                  <?php echo $result['name']; ?>
                </a>
            </h4>
        </div>
        <div id="collapseThree<?php echo $location_id; ?>" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="panel-group" id="accordion2<?php echo $location_id; ?>">
                    <?php foreach ($result['stages'] as $stage_id => $stage_result) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion2<?php echo $location_id; ?>" href="#collapseThreeOne<?php echo $location_id . $stage_id; ?>"><?php echo $stage_result['name']; ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThreeOne<?php echo $location_id . $stage_id; ?>" class="panel-collapse collapse">
                            <div class="table-responsive">
                            <table class="table table-bordered" style="margin:0;">
                            <?php foreach ($stage_result['mice'] as $mouse_id => $mouse_result) { ?>
                                <tr>
                                    <td>
                                        <?php echo $mouse_result['name']; ?>
                                    </td>
                                    <td style="padding:0;">
                                        <table class="table table-bordered table-condensed table-striped" style="margin:0;">
                                          <th>
                                              <td>Attraction rate</td><td>Attracted Hunts</td><td>Total Hunts</td>
                                          </th>
                                        <?php foreach ($mouse_result['cheese'] as $cheese_result) { ?>
                                            <tr>
                                                <td>
                                                   <?php echo $cheese_result['name']; ?>
                                                </td>
                                                <td>
                                                   <?php echo $cheese_result['rate']; ?>&#37;
                                                 </td>
                                                 <td>
                                                   <?php echo $cheese_result['attracted_hunts']; ?>
                                                 </td>
                                                 <td>
                                                   <?php echo $cheese_result['total_hunts']; ?>
                                                </td>
                                             </tr>
                                        <?php } ?>
                                        </table>
                                    </td>
                                </tr>
                            <?php } ?>
                            </table>
                            </div>
                        </div>
                    </div>
                  <?php } ?>
                </div>

            </div>
        </div>
    </div>

</div>
