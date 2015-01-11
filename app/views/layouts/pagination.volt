<?php
    
    // build query string
    $currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queryString);
    unset($queryString['page']);
    $queryString = http_build_query($queryString);

?>
<div class="dt-row dt-bottom-row">
    <div class="row">
        <div class="col-sm-6">
            <div class="dataTables_info" id="datatable_tabletools_info">
                <?php
                    if ( $page->total_items && ($page->total_items > $itemsPerPage) ) {
                        echo $page->total_items, " entries. Showing page ", $page->current, " of ", $page->total_pages;
                    } else {
                        echo $page->total_items, " entries.";
                    }
                ?>
            </div>
        </div>
        <?php if ( $page->total_items && ($page->total_items > $itemsPerPage) ): ?>
        <div class="col-sm-6 text-right">
            <div class="dataTables_paginate paging_bootstrap">
            <ul class="pager" style="margin: 0; float: right;">
                <li>
                    <a href="<?php echo $currentUrl; ?><?php echo (!empty($queryString))? '?' . $queryString : ''; ?>">« First</a>
                </li>
                <li>
                    <a href="<?php echo $currentUrl; ?>?page=<?= $page->before; ?><?php echo (!empty($queryString))? '&amp;' . $queryString : ''; ?>">&larr; Previous</a>
                </li>
                
                <li>
                    <a href="<?php echo $currentUrl; ?>?page=<?= $page->next; ?><?php echo (!empty($queryString))? '&amp;' . $queryString : ''; ?>">Next &rarr;</a>
                </li>
                <li>
                    <a href="<?php echo $currentUrl; ?>?page=<?= $page->last; ?><?php echo (!empty($queryString))? '&amp;' . $queryString : ''; ?>">Last »</a>
                </li>
            </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>