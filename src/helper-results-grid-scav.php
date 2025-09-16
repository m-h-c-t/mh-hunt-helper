<?php

$lootDefinition = [];
    $lootDropRate = [];

    foreach($results['results'] as $location) {
        foreach($location['stages'] as $stage) {
            foreach($stage['items'] as $item_id=>$item) {
                $lootDefinition[$item_id] = $item['name'];

                if(!array_key_exists($item_id, $lootDropRate)) {
                    $lootDropRate[$item_id] = [];
                }

                foreach($item['cheese'] as $cheese) {
                    $lootDropRate[$item_id][] = [
                        'drop_rate' => $cheese['drop_rate'],
                        'location' => $location['name'],
                        'stage' => $stage['name'] !== '---'? "({$stage['name']})": '',
                        'cheese' => $cheese['name']
                    ];
                }
                usort($lootDropRate[$item_id], function($a, $b){
                    return $b['drop_rate'] <=> $a['drop_rate'];
                });

            }
        }
    }

    echo <<<HTML
    <style>
        /** bootstrap **/
        .alert-info td {
            color: #31708f !important;
            background-color: #d9edf7 !important;
            border-color: #bce8f1 !important;
        }

        .alert-warning td {
            color: #8a6d3b!important;
            background-color: #fcf8e3!important;
            border-color: #faebcc!important;
        }

        /** tablesorter-custom **/
        table.tablesorter thead tr th, table.tablesorter tfoot tr th {
        background-color: #e6EEEE;
        border: 2px solid #e7e7e7;
        padding: 4px;
            padding-left: 4px;
        }

        /* table.tablesorter th {
            width: 50%;
        } */


        table.tablesorter tbody td {
            color: #3D3D3D;
            padding: 4px;
            background-color: #FFF;
            vertical-align: middle;
            border: 2px solid #e7e7e7;
        }

        /** tablesorter-reflow **/
        /* REQUIRED CSS: change your reflow breakpoint here (35em below) */
        @media ( max-width: 35em ) {

        /* uncomment out the line below if you don't want the sortable headers to show */
        /* table.ui-table-reflow thead { display: none; } */

        /* css for reflow & reflow2 widgets */
        .ui-table-reflow td,
        .ui-table-reflow th {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        float: right;
        /* if not using the stickyHeaders widget (not the css3 version)
        * the "!important" flag, and "height: auto" can be removed */
        width: 100% !important;
        height: auto !important;
        }

        /* reflow widget only */
        .ui-table-reflow tbody td[data-title]:before {
        color: #469;
        font-size: 1.1em;
        content: attr(data-title);
        float: left;
        width: 50%;
        white-space: pre-wrap;
        text-align: start;
        display: inline-block;
        margin-bottom: 10px;
        }

        /* reflow2 widget only */
        table.ui-table-reflow .ui-table-cell-label.ui-table-cell-label-top {
        display: block;
        padding: .4em 0;
        margin: .4em 0;
        text-transform: uppercase;
        font-size: .9em;
        font-weight: 400;
        }
        table.ui-table-reflow .ui-table-cell-label {
        padding: .4em;
        min-width: 30%;
        display: inline-block;
        margin: -.4em 1em -.4em -.4em;
        }

        } /* end media query */

        /* reflow2 widget */
        .ui-table-reflow .ui-table-cell-label {
        display: none;
        }

        .scav-helper--other-location.select2-selection--single {
            height: auto!important;
        }

        .scav-helper--other-location.select2-selection--single .select2-selection__rendered {
            line-height: unset;
            padding: 10px;
        }

        .scav-helper--other-location .select2-results__option {
            text-align: left;
            border-bottom: 1px #aaa solid;
        }

        .scav-helper--other-location .select2-results > .select2-results__options {
            max-height: 257.55px;
        }

        .scav-helper--other-location p {
            margin: 0;
        }

        #drop-rate--table-view-sticky th,
        .tablesorter-filter-row.tablesorter-ignoreRow td {
            width: unset!important;
            min-width: unset!important;
            max-width: unset!important;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
                <table id="drop-rate--table-view">
                    <colgroup>
                        <col style="width: 50%;">
                        <col style="width: 50%;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Loot</th>
                            <th>Location / Cheese</th>
                        </tr>
                    </thead>
                    <tbody>
    HTML;
    foreach($lootDropRate as $item_id=>$dropRates) {
        $search_name = urlencode($lootDefinition[$item_id]);

        echo <<<HTML
        <tr class="alert">
            <td style="font-size: 16px; padding: 10px">
                <div style="float:left; width:100%;">
                    <a href="/loot.php?item={$item_id}" target="_blank">{$lootDefinition[$item_id]}</a>
                    (<a href="https://mhwiki.hitgrab.com/wiki/index.php?search={$search_name}" target="_blank">wiki</a>)
                </div>
            </td>
        HTML;

        echo <<<HTML
        <td style="font-size: 14px; padding: 10px;">
        HTML;
            if(count($dropRates)>0) {
                echo '<select>';
                foreach($dropRates as $dropRateIdx=>$dropRate) {
                    $fmtDropRate = number_format($dropRate['drop_rate'], 2);
                    echo <<<HTML
                    <option value="{$fmtDropRate}"
                            data-rate="{$fmtDropRate}"
                            data-location="{$dropRate['location']}"
                            data-stage="{$dropRate['stage']}"
                            data-cheese="{$dropRate['cheese']}"
                            >
                            {$fmtDropRate}
                    </option>
                    HTML;
                }
                echo '</select>';
            }
        echo <<<HTML
        </td>
    </tr>
    HTML;

    }
    echo <<<HTML
                    </tbody>
                </table>
                <script>
                    //select2
                    var formatSelect2Result = function(state) {
                        if (!state.id) {
                            return state.text;
                        }
                        var optionData = $(state.element).data();
                        var html = '';

                        // if(!!optionData.rate) html+=`<p style="font-size: 16px">\${optionData.rate}%&nbsp;<br class="visible-xs-inline hidden-sm hidden-md hidden-lg"/>`;
                        // if(!!optionData.location) html+=`<b>\${optionData.location}</b>&nbsp;<br class="visible-xs-inline hidden-sm hidden-md hidden-lg"/>`
                        // if(!!optionData.stage) html+=`<b>\${optionData.stage}</b>&nbsp;<br class="visible-xs-inline hidden-sm hidden-md hidden-lg"/>`
                        // if(!!optionData.cheese) html+= `<span class="hidden-xs visible-sm-inline visible-md-inline visible-lg-inline">&sol;&nbsp;</span>\${optionData.cheese}</p>`;

                        if(!!optionData.rate) html+=`<p style="font-size: 16px">\${optionData.rate}%&nbsp;<br/>`;
                        if(!!optionData.location) html+=`<b>\${optionData.location}</b>&nbsp;<br/>`
                        if(!!optionData.stage) html+=`<b>\${optionData.stage}</b>&nbsp;<br/>`
                        if(!!optionData.cheese) html+= `\${optionData.cheese}</p>`;

                        // html += state.text;
                        console.log(html);
                        return jQuery(html);
                    },
                    highlightRow = function(e){
                        var tr = jQuery(e.currentTarget).parentsUntil('tbody').toArray().pop();
                        jQuery(tr).toggleClass('alert-warning');
                    };

                    jQuery("select").select2({
                        templateResult: formatSelect2Result,
                        templateSelection: formatSelect2Result,
                        dropdownAutoWidth: true,
                        width: '100%',
                        minimumResultsForSearch: Infinity,
                        selectionCssClass: "scav-helper--other-location",
                        dropdownCssClass: "scav-helper--other-location"
                    })
                    .on('select2:open', highlightRow)
                    .on('select2:close', highlightRow)
                    .on('select2:selecting', function(e){
                        e.preventDefault();
                        return false;
                    });

                    //tablesorter
                    jQuery('#drop-rate--table-view').tablesorter({
                        theme: 'bootstrap',
                        headers: { 1: { sorter:'select', filter: false } },
                        widthFixed: false,
                        widgets : [ 'filter', 'reflow', 'stickyHeaders' ],
                        sortList: [[1,0]],
                        widgetOptions : {
                            // class name added to make it responsive (class name within media query)
                            reflow_className    : 'ui-table-reflow',
                            // header attribute containing modified header name
                            reflow_headerAttrib : 'data-name',
                            // data attribute added to each tbody cell
                            // it contains the header cell text, visible upon reflow
                            reflow_dataAttrib   : 'data-title'
                        }
                    });

                    (function($, document, window, viewport){
                        $(window).resize(
                            viewport.changed(function(){
                                \$tbl = $("#drop-rate--table-view");
                                if(viewport.current() == 'xs') {
                                    //hide headers
                                    \$tbl.find('thead').hide();
                                    \$('.tablesorter-sticky-wrapper').find('thead').hide();
                                }
                                else {
                                    //show headers
                                    \$tbl.find('thead').show();
                                    \$('.tablesorter-sticky-wrapper').find('thead').show();
                                }



                            })
                        ).trigger('resize');

                    })(jQuery, document, window, ResponsiveBootstrapToolkit);
                </script>
            </div>
        </div>
    </div>
    HTML;
