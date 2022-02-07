<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Register the menu item
 *
 * 1. Create a function that contains the menu-building code
 * 2. Register the above function using the admin_menu action hook. (If you are adding an admin menu for the Network, use network_admin_menu instead).
 * 3. Create the HTML output for the page (screen) displayed when the menu item is clicked
 *
 */
/** Step 2 (from text above). */
add_action('admin_menu', 'entry_process_menu');

/** Step 1. */
function entry_process_menu()
{
    add_menu_page('Life-Framer Entry Process', 'Life-Framer Entry Process', 'activate_plugins', 'life-framer-entry-process', 'entry_process_options');
}


/** Step 3. */
function entry_process_options()
{

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <h1>Life-framer Entry Process (<?= current_time('mysql'); ?>)</h1>
    <?php
    if (isset($_POST['theme_action']) && $_POST['theme_action'] == "edit_theme") {
        edit_lf_theme_form();
    } else if (isset($_POST['theme_action']) && $_POST['theme_action'] == "remove_theme") {
        delete_lf_theme();
    } else {
        lf_theme_list();
        create_lf_theme_form();
        print_delete_entry_form();
    }
}

function edit_lf_theme_form()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    global $wpdb;
    global $theme_table;
    $theme_id = $_POST['theme_id'];
    $theme = $wpdb->get_row("SELECT * FROM $theme_table WHERE id=$theme_id LIMIT 1");
    $redirect = urlencode($_SERVER['REQUEST_URI']);

    $theme_entries = $wpdb->get_results("SELECT * from {$wpdb->prefix}lf_theme_entries WHERE theme_id=$theme_id");


    ?>

    <div class="wrap">
        <form class="form" action="<?= admin_url('admin.php'); ?>" method="post">
            <div class="edit-theme">
                <h3>Edit theme</h3>
                <div class="clearfix"></div>
                <input type="hidden" name="action" value="upgrade_theme">
                <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
                <input type="hidden" name="theme_id" value="<?= $theme_id; ?>">

                <label>Name: </label>
                <input type="text" id="theme_name" name="theme_name" value="<?= $theme->name ?>">
                <div class="clearfix"></div>

                <label>Active from: </label>
                <input type="text" id="theme_start" name="theme_start" value="<?= $theme->start ?>">
                <div class="clearfix"></div>

                <label>Active to: </label>
                <input type="text" id="theme_end" name="theme_end" value="<?= $theme->end ?>">
                <div class="clearfix"></div>

                <label>Is series award </label>
                <input type="checkbox" name="is_series_award" <?php echo ($theme->is_series_award ? 'checked="checked"' : ''); ?> value="1" />
                <div class="clearfix"></div>
            </div>

            <div class="split-entries">


                <h3>Set deadline for entrants</h3>
                <div class="clearfix"></div>
                <label>Deadline</label>
                <input type="text" name="entry-deadline-entrant" value="<?php echo $theme_entries[0]->end_entrants;?>">
                <div class="clearfix"></div>


                <h3>Split deadline entries for entrants and members</h3>
                <div class="clearfix"></div>
                <!-- <a class="split-entries-btn button button-primary">Split entries</a> -->
                <?php
                if(!empty($theme_entries)){
                    foreach($theme_entries as $key => $entry){
                        echo '<label>Name: </label><input type="text" name="entry-name[]" value="'.$entry->name.'"><br>';
                        echo '<label>Deadline: </label><input type="text" name="entry-deadline[]" value="'.$entry->end_members.'"><br>';
                        echo '<input type="hidden" name="entry-id[]" value="'.$entry->id.'">';
                        echo '<hr>';
                    }
                }
                ?>


            </div>
            <div class="clearfix"></div>
            <div class="submit-holder">
                <input type="submit" name="edit-theme-submit" id="edit-theme-submit" class="button"
                   value="Edit theme">
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
    <?php
}

add_action('admin_action_upgrade_theme', 'lifeframer_upgrade_theme');

function lifeframer_upgrade_theme()
{
    global $wpdb;
    global $theme_table;


    // echo '<pre>'.print_r($_POST,1).'</pre>';
    $entry_names =  $_POST['entry-name'];
    $entry_deadlines =  $_POST['entry-deadline'];
    $entry_ids = $_POST['entry-id'];

    $entry_deadlines_entrant = $_POST['entry-deadline-entrant'];


    // echo '<pre>'.print_r($entry_ids,1).'</pre>';

    if(!empty($entry_ids)){
        $new_theme_name = implode(',', $entry_names);
        // echo $new_theme_name;
        // die();
        foreach($entry_ids as $key=>$entry_id){
            $wpdb->update(
                    "{$wpdb->prefix}lf_theme_entries",
                    array(
                        'name' => $entry_names[$key],
                        'start' => $_POST['theme_start'] ,
                        'end_members' => $entry_deadlines[$key],
                        'end_entrants' => $entry_deadlines_entrant

                    ),
                    /* Where statement */
                    array(
                        'id' => $entry_id,
                    ),
                    array(
                        '%s',   // value1
                        '%s',    // value2
                        '%s',    // value2
                        '%s'    // value2
                    ),
                    array(
                        '%d'
                    )
                );
        }
    }

    else{
        // echo 'empty';
        $new_theme_name = $_POST['theme_name'];
    }

    $wpdb->update(
        $theme_table,
        array(
            'name' => $new_theme_name,
            'start' => $_POST['theme_start'],
            'end' => $_POST['theme_end'],
        ),
        array('id' => $_POST['theme_id'])
    );

       // die();

    if (!isset ($_POST['_wp_http_referer']))
        die('Missing target.');

    $url = urldecode($_POST['_wp_http_referer']);

    wp_safe_redirect($url);

}

function delete_lf_theme()
{
    global $wpdb;
    global $theme_table;


    $wpdb->update(
        $theme_table,
        array(
            'deleted' => true
        ),
        array('id' => $_POST['theme_id'])
    );


    $wpdb->update(
            "{$wpdb->prefix}lf_theme_entries",
            array(
                'deleted' => '1',
            ),
            /* Where statement */
            array(
                'theme_id' => $_POST['theme_id']
            ),
            array(
                '%d',   // value1
            ),
            array(
                '%s'
            )
        );

    $url = $_SERVER['REQUEST_URI'];

    wp_safe_redirect($url);

}

function create_lf_theme_form()
{
    $redirect = urlencode($_SERVER['REQUEST_URI']);

    ?><br><br>
    <h2>Create new theme</h2>
    <div class="wrap">
        <?php
            $start_Date = date('Y-m-d 00:00:00');
            // echo $start_Date;
            $end_Date = date('Y-m-d 00:00:00',strtotime("+1 month", strtotime($start_Date)));
            // echo $end_Date;
            ?>
        <form class="form add-new-theme" action="<?= admin_url('admin.php'); ?>" method="post">
            <div class="new-theme">
                <h3>Theme Settings</h3>
                <div class="clearfix"></div>
                <input type="hidden" name="action" value="save_theme">
                <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
                <label>Name:</label>
                <input type="text" id="theme_name" name="theme_name">
                <div class="clearfix"></div>

                <label>Active from:</label>
                <input type="text" id="theme_start" name="theme_start" value="<?php echo $start_Date;?>">
                <div class="clearfix"></div>

                <label>Active to:</label>
                <input type="text" id="theme_end" name="theme_end" value="<?php echo $end_Date;?>">
                <div class="clearfix"></div>

                <label>
                    Is series award
                </label>
                <input type="checkbox" name="is_series_award" value="1" />
                <div class="clearfix"></div>

                <div class="split-entries-btn-holder">
                    <a class="split-entries-btn button button-primary">Split entries</a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="split-entries">
                <div class="split-entries-result"></div>

            </div>
            <div class="clearfix"></div>

            <br>


            <div class="submit-holder">
                <input type="submit" name="new-theme-submit" id="new-theme-submit" class="button"
                   value="Create new theme">
                <div class="clearfix"></div>
           </div>
        </form>
    </div>
    <?php
}

add_action('admin_action_save_theme', 'lifeframer_save_theme');

function lifeframer_save_theme()
{
    global $wpdb;
    global $theme_table;

    // echo '<pre>'.print_r($_POST,1).'</pre>';

    $entry_names = $_POST['entry-name'];
    $entry_deadline = $_POST['entry-deadline'];
    $entry_deadline_entrant = $_POST['entry-deadline-entrants'];

    $is_series_award = isset($_POST['is_series_award']) && $_POST['is_series_award'] == 1;

    if(empty($entry_names)){
        echo 'Split Entries first !';
        die();
    }



    $new_start = $_POST['theme_start'];
    $new_end = $_POST['theme_end'];

    check_overlapping_theme($wpdb, $theme_table, $new_start, $new_end, $is_series_award);

    $result_insert = $wpdb->insert(
        $theme_table,
        array(
            'name' => $_POST['theme_name'],
            'start' => $_POST['theme_start'],
            'end' => $_POST['theme_end'],
            'is_series_award' => $is_series_award ? 1 : 0,
        )
    );
    if($result_insert){
        $last_insert_id = $wpdb->insert_id;

        foreach($entry_names as $key=> $entry_name){
            $wpdb->insert(
                "{$wpdb->prefix}lf_theme_entries",
                    array(
                        'theme_id' => $last_insert_id,
                        'name' => $entry_name,
                        'start' => $new_start,
                        'end_members' => $entry_deadline[$key],
                        'end_entrants' => $entry_deadline_entrant[0],

                    ),
                    array(
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    )
                );
        }

    }


    // die();


    if (!isset ($_POST['_wp_http_referer']))
        die('Missing target.');

    $url = urldecode($_POST['_wp_http_referer']);

    wp_safe_redirect($url);

}

function print_delete_entry_form()
{
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    ?>
    <h2>Delete entry</h2>
    <?php
    if (isset($_GET['deleted_entry'])) {
        echo '<span style="color: red">The entry ' . $_GET['deleted_entry'] . ' has been deleted</span>';
    }
    ?>
    <form action="<?= admin_url('admin.php'); ?>" method="post"
          onsubmit="return confirm('Do you really want to delete the entry?')">
        <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
        <input type="hidden" name="action" value="delete_entry">
        <label for="entry-id">Entry ID:</label>
        <input type="number" name="entry-id" id="entry-id"/>
        <input type="submit" value="Delete entry"/>
    </form>
    <?php
}

// Register a new WP action
add_action('admin_action_delete_entry', 'delete_entry');

function delete_entry()
{

    global $wpdb;
    global $entry_table;

    // 1. Read the id of the entry that we want to remove
    $entry_to_delete = $_POST['entry-id'];

    // 2. Go to the WP database and modify the column entry for that entry
    $wpdb->update(
        $entry_table,
        array(
            'deleted' => true
        ),
        array('id' => $entry_to_delete)
    );

    // 3. Redirect back to the life-framer entry process page
    $url = urldecode($_POST['_wp_http_referer']);
    wp_safe_redirect($url . "&deleted_entry=" . $entry_to_delete);

}

function lf_theme_list()
{
    global $wpdb;
    global $theme_table;
    $result = $wpdb->get_results("SELECT * FROM $theme_table WHERE deleted = false");

    ?>
    <h2>Theme List</h2>
    <div class="wrap">
        <table class="wp-list-table widefat fixed unite_table_items">
            <thead>
            <tr>
                <th>Theme name</th>
                <th>Active?</th>
                <th>Start</th>
                <th>End</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($result as $entry) { ?>
                <tr>
                    <td><?= $entry->name ?></td>
                    <td><?php if (isThemeActive($entry)) {
                            ?>
                            <span style="color: green">Yes</span>
                        <?php } else { ?>
                            <span style="color: grey">No</span>
                        <?php } ?>
                    </td>
                    <td><?= $entry->start ?></td>
                    <td><?= $entry->end ?></td>
                    <td>
                     <!--    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
                            <input type="hidden" name="action" value="export_theme_entries_excel">
                            <input type="hidden" name="theme_id" value="<?= $entry->id ?>">
                            <input type="submit" value="Excel" class="button button-primary">
                        </form> -->
                        <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
                            <input type="hidden" name="action" value="export_theme_entries_excel_new">
                            <input type="hidden" name="theme_id" value="<?= $entry->id ?>">
                            <input type="submit" value="Excel Spreadsheet" class="button button-primary">
                        </form>
                        <div class="clearfix" style="height:5px;"></div>
                        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline">
                            <input type="hidden" name="theme_action" value="edit_theme">
                            <input type="hidden" name="theme_id" value="<?= $entry->id ?>">
                            <input type="submit" value="Edit" class="button button-primary">
                        </form>
                        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline">
                            <input type="hidden" name="theme_action" value="remove_theme">
                            <input type="hidden" name="theme_id" value="<?= $entry->id ?>">
                            <input type="submit" value="Remove" class="button button-primary">
                        </form>
                    </td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    </div>
<!--     <h2>Series Award</h2>
    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="export_series_excel">
        <input type="submit" value="Download Series Award Excel" class="button button-primary">
    </form> -->
    <br><br><br>
    <?php
    /* Start Date = 30 days from now */
    $end_date  = date('Y-m-d');
    $start_date  = date('Y-m-d', strtotime($end_date . " -30 days"));

    ?>


    <h2>Payments registry</h2>
    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="export_payments_excel">
        <label>Start date: </label><input type="date" name="start_date" value="<?php echo $start_date; ?>">
        <label>End date: </label><input type="date" name="end_date" value="<?php echo $end_date; ?>"><br><br>
        <input type="submit" value="Download Payments Registry Excel" class="button button-primary"><br><br>
    </form>

    <h2>Members registry</h2>
    <form action="<?= admin_url('admin.php'); ?>" method="post" style="display: inline">
        <input type="hidden" name="action" value="export_members_excel">
        <label>Start date: </label><input type="date" name="start_date" value="<?php echo $start_date; ?>">
        <label>End date: </label><input type="date" name="end_date" value="<?php echo $end_date; ?>"><br><br>
        <input type="submit" value="Download Members Registry Excel" class="button button-primary"><br><br>
    </form>


    <?php

    do_action('entry_submissions');

}

add_action('admin_action_export_theme_entries_excel_new', 'export_theme_entries_to_excel_new');


function export_theme_entries_to_excel_new(){

    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

    /** Include PHPExcel */
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

    global $wpdb;
    global $entry_table;
    global $payment_table;
    if (!isset ($_POST['theme_id']))
        die('Missing theme id.');

    $theme_id = $_POST['theme_id'];

    $membership_where_clause = "p.wp_user=wp_user AND LOWER(p.description) LIKE '%member%' ORDER BY p.date DESC LIMIT 1";


    $query = "SELECT  
        lf_entry.id,
        lf_entry.time,
        lf_entry.name,
        lf_entry.wp_user,
        lf_entry.email_address,
        lf_entry.date_time,
        lf_entry.additional_information,
        lf_entry.payment,
        lf_entry.theme_id,
        lf_entry.payment_reference,
        lf_entry.no_images,
        lf_entry.no_img_extra,
        lf_payments.description,
        lf_payments.date,
        lf_payments.newsletter,
        lf_photos.entry_id,
        lf_photos.path,
        lf_theme_entries.theme_id as themeid,
        lf_theme_entries.name as themename 

        FROM {$wpdb->prefix}lf_entry AS lf_entry

        INNER JOIN {$wpdb->prefix}lf_payments AS lf_payments
        ON lf_entry.payment_reference =  lf_payments.payment_ref 

        INNER JOIN {$wpdb->prefix}lf_photos AS lf_photos
        ON lf_entry.id = lf_photos.entry_id

        INNER JOIN {$wpdb->prefix}lf_theme_entries as lf_theme_entries
        ON lf_photos.theme_entry_id =  lf_theme_entries.id

        WHERE lf_entry.date_time>0 
        AND lf_entry.theme_id=$theme_id
        Group BY id, themename";


    $result = $wpdb->get_results($query, ARRAY_A );


    $mergeId = 'id';

    $result = array_reduce($result, function($c, $x) use ($mergeId)
    {
       $c[$x[$mergeId]] = isset($c[$x[$mergeId]])
          ?array_combine(
              $z=array_keys($c[$x[$mergeId]]), 
              array_map(function($y) use ($x, $c, $mergeId)
              {
                 return in_array($x[$y], (array)$c[$x[$mergeId]][$y])
                    ?$c[$x[$mergeId]][$y]
                    :array_merge((array)$c[$x[$mergeId]][$y], [$x[$y]]);
              }, $z)
           )
          :$x;
       return $c;
    }, []);



    if (!$result) die('Couldn\'t fetch records : ' . $query);

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Life-Framer")
        ->setLastModifiedBy("Life-Framer Entry Process")
        ->setTitle("Life-Framer Theme Entries")
        ->setSubject("Life-Framer Theme Entries")
        ->setDescription("Life-Framer Theme Entries")
        ->setKeywords("life-framer entry process")
        ->setCategory("Life-Framer");


    // Headers
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Entry Id')
        ->setCellValue('B1', 'Upload DateTime')
        ->setCellValue('C1', 'Name')
        ->setCellValue('D1', 'WP Username')
        ->setCellValue('E1', 'Email')
        ->setCellValue('F1', 'Website')
        ->setCellValue('G1', 'Instagram')
        ->setCellValue('H1', 'Facebook')
        ->setCellValue('I1', 'Additional information')
        ->setCellValue('J1', 'Payment amount (dollars)')
        ->setCellValue('K1', 'Payment description')
        ->setCellValue('L1', 'Payment date')
        ->setCellValue('M1', 'Payment reference')
        ->setCellValue('N1', 'Number of photos')
        ->setCellValue('O1', 'Newsletter')
        ->setCellValue('P1', 'Competitions Entered');


    $currentRow = 2;
    foreach ($result as $entry) {
        $input = $entry['additional_information']; // original text
        // $output = $input;
        //$output = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $input);
        // $output =  preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $output); // lets remove utf-8 special characters except blank spaces
        $additional_information = iconv("utf-8", "ascii//TRANSLIT", $input);

        $user_login = $entry['wp_user'];
        $user = get_user_by('login',$user_login);
        $portfolio = $instagram = $facebook = '';
        if($user){
            $user_id = $user->ID;
            $portfolio = get_user_meta($user_id,'social_website_custom',true);
            $instagram = get_user_meta($user_id,'social_instagram_username',true);
            $facebook = get_user_meta($user_id,'social_facebook_username',true);

            if($instagram!='' && strpos($instagram, 'instagram.com') === false) {
                $instagram = 'https://www.instagram.com/'.$instagram;
            }
     
            if($facebook!='' && strpos($facebook, 'facebook.com') === false) {
                $facebook = 'https://www.facebook.com/'.$facebook;
            }
        }

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $currentRow, $entry['id'])
            ->setCellValue('B' . $currentRow, date("Y-m-d H:i:s", $entry['date_time']))
            ->setCellValue('C' . $currentRow, stripslashes($entry['name']))
            ->setCellValue('D' . $currentRow, $entry['wp_user'])
            ->setCellValue('E' . $currentRow, $entry['email_address'])
            ->setCellValue('F' . $currentRow, $portfolio)
            ->setCellValue('G' . $currentRow, $instagram)
            ->setCellValue('H' . $currentRow, $facebook)
            // ->setCellValue('I' . $currentRow, preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $entry->additional_information))
            ->setCellValue('I' . $currentRow, stripslashes(str_replace('<br>',"\n",$additional_information)))
            ->setCellValue('J' . $currentRow, $entry['payment'])
            ->setCellValue('K' . $currentRow, $entry['description'])
            ->setCellValue('L' . $currentRow, $entry['date'])
            ->setCellValue('M' . $currentRow, $entry['payment_reference'])
            ->setCellValue('N' . $currentRow, ($entry['no_images'] + $entry['no_img_extra']))
            ->setCellValue('O' . $currentRow, $entry['newsletter'])
            ->setCellValue('P' . $currentRow, is_array($entry['themename'])?implode(', ', $entry['themename']): $entry['themename']) ;
        $currentRow++;
    }

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="life-framer-theme-entries.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;

}



add_action('admin_action_export_theme_entries_excel', 'export_theme_entries_to_excel');


function export_theme_entries_to_excel()
{

    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

    /** Include PHPExcel */
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

    global $wpdb;
    global $entry_table;
    global $payment_table;
    if (!isset ($_POST['theme_id']))
        die('Missing theme id. ERROR LF001. Please, contact support@cleanblocks.co.uk');

    $theme_id = $_POST['theme_id'];

    $membership_where_clause = "p.wp_user=wp_user AND LOWER(p.description) LIKE '%member%' ORDER BY p.date DESC LIMIT 1";

    /// SELECT `id`,`time`,`name`,`payment`,`no_images`,`email_address`,`portfolio`,`additional_information` FROM `wrd_lf_entry` WHERE theme_id=23
    $query = "
                SELECT id,time,name,wp_user,email_address,portfolio,instagram,additional_information,
                IF(payment != 0,
                    payment,
                    IF(payment_reference > '' ,
                      (SELECT p.amount
                       FROM $payment_table as p
                       WHERE p.payment_ref=payment_reference),
                       IF(wp_user > '',
                          (SELECT p.amount
                            FROM $payment_table as p
                            WHERE $membership_where_clause),
                          '---'))) as payment,
                IF(payment != 0,'', IF(payment_reference > '',
                    (SELECT p.description
                       FROM $payment_table as p
                       WHERE p.payment_ref=payment_reference),
                    IF(wp_user > '', (SELECT p.description
                      FROM $payment_table as p
                      WHERE $membership_where_clause ),
                          '---'))) as payment_description,
                IF(payment != 0,'', IF(payment_reference > '',
                    (SELECT p.date
                       FROM $payment_table as p
                       WHERE p.payment_ref=payment_reference),
                    IF(wp_user > '',(SELECT p.date
                      FROM $payment_table as p
                      WHERE $membership_where_clause ),
                          '---'))) as payment_date,
                IF(payment != 0,'', IF(payment_reference > '',
                    payment_reference,
                    IF(wp_user > '',(SELECT  p.payment_ref
                      FROM $payment_table as p
                      WHERE $membership_where_clause ),
                          '---'))) as payment_reference
                ,no_images
                FROM $entry_table
                WHERE theme_id=$theme_id AND deleted=FALSE";

    // echo $query;


    $result = $wpdb->get_results($query);
    if (!$result) die('Couldn\'t fetch records : ' . $query);

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

// Set document properties
    $objPHPExcel->getProperties()->setCreator("Life-Framer")
        ->setLastModifiedBy("Life-Framer Entry Process")
        ->setTitle("Life-Framer Theme Entries")
        ->setSubject("Life-Framer Theme Entries")
        ->setDescription("Life-Framer Theme Entries")
        ->setKeywords("life-framer entry process")
        ->setCategory("Life-Framer");


    // Headers
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Entry Id')
        ->setCellValue('B1', 'Upload DateTime')
        ->setCellValue('C1', 'Name')
        ->setCellValue('D1', 'WP Username')
        ->setCellValue('E1', 'Email')
        ->setCellValue('F1', 'Portfolio')
        ->setCellValue('G1', 'Instagram')
        ->setCellValue('H1', 'Additional information')
        ->setCellValue('I1', 'Payment amount (dollars)')
        ->setCellValue('J1', 'Payment description')
        ->setCellValue('K1', 'Payment date')
        ->setCellValue('L1', 'Payment reference')
        ->setCellValue('M1', 'Number of photos');

    $currentRow = 2;
    foreach ($result as $entry) {
        $input = $entry->additional_information; // original text
    $output = $input;
    //$output = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $input);
    $output =  preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $output); // lets remove utf-8 special characters except blank spaces

    // Values
        // "SELECT id,time,name,email_address,portfolio,instagram,additional_information,payment,no_images FROM $entry_table WHERE theme_id=$theme_id"
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $currentRow, $entry->id)
            ->setCellValue('B' . $currentRow, $entry->time)
            ->setCellValue('C' . $currentRow, $entry->name)
            ->setCellValue('D' . $currentRow, $entry->wp_user)
            ->setCellValue('E' . $currentRow, $entry->email_address)
            ->setCellValue('F' . $currentRow, $entry->portfolio)
            ->setCellValue('G' . $currentRow, $entry->instagram)
  //          ->setCellValue('H' . $currentRow, "Not available - Cleanblocks Maintenance")
// ->setCellValue('H' . $currentRow, $entry->additional_information)
            ->setCellValue('H' . $currentRow, $output)
            ->setCellValue('I' . $currentRow, $entry->payment)
            ->setCellValue('J' . $currentRow, $entry->payment_description)
            ->setCellValue('K' . $currentRow, $entry->payment_date)
            ->setCellValue('L' . $currentRow, $entry->payment_reference)
            ->setCellValue('M' . $currentRow, $entry->no_images);
        $currentRow++;
    }

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="life-framer-theme-entries.xls"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;

}




add_action('admin_action_export_series_excel', 'export_series_to_excel');

function export_series_to_excel()
{
    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

    /** Include PHPExcel */
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

    global $wpdb;
    global $entry_table;

    $result = $wpdb->get_results("SELECT id,time,name,email_address,portfolio,instagram,additional_information,no_images FROM $entry_table WHERE theme_id IS NULL and deleted=FALSE");
    if (!$result) die('Couldn\'t fetch records');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

// Set document properties
    $objPHPExcel->getProperties()->setCreator("Life-Framer")
        ->setLastModifiedBy("Life-Framer Entry Process")
        ->setTitle("Life-Framer Series Award Entries")
        ->setSubject("Life-Framer Series Award Entries")
        ->setDescription("Life-Framer Series Award Entries")
        ->setKeywords("life-framer series award entries")
        ->setCategory("Life-Framer");


    // Headers
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Entry Id')
        ->setCellValue('B1', 'Upload DateTime')
        ->setCellValue('C1', 'Name')
        ->setCellValue('D1', 'Email')
        ->setCellValue('E1', 'Portfolio')
        ->setCellValue('F1', 'Instagram')
        ->setCellValue('G1', 'Additional information')
        ->setCellValue('H1', 'Number of photos');

    $currentRow = 2;
    foreach ($result as $entry) {
        // Values
        // "SELECT id,time,name,email_address,portfolio,instagram,additional_information,payment,no_images FROM $entry_table WHERE theme_id=$theme_id"
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $currentRow, $entry->id)
            ->setCellValue('B' . $currentRow, $entry->time)
            ->setCellValue('C' . $currentRow, $entry->name)
            ->setCellValue('D' . $currentRow, $entry->email_address)
            ->setCellValue('E' . $currentRow, $entry->portfolio)
            ->setCellValue('F' . $currentRow, $entry->instagram)
            ->setCellValue('G' . $currentRow, $entry->additional_information)
            ->setCellValue('H' . $currentRow, $entry->no_images);
        $currentRow++;
    }

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="life-framer-series-entries.xls"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

add_action('admin_action_export_payments_excel', 'export_payments_to_excel');

function export_payments_to_excel()
{
    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

    /** Include PHPExcel */
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

    global $wpdb;
    global $payment_table;
    if(isset($_POST['start_date']) && $_POST['start_date'] != '' && isset($_POST['end_date']) && $_POST['end_date'] != '' ){
        $where = 'date BETWEEN "'.$_POST['start_date'].' 00:00:00.000000" AND "'.$_POST['end_date'].' 23:59:59.999999"';
        $xls_name = ' '.$_POST['start_date'].' - '. $_POST['end_date']. ' ';
    }
    else{
        $where = '1=1';
        $xls_name = '';
    }

    $result = $wpdb->get_results("SELECT id, payment_ref, date, description, amount, vat, country, name,email_address,wp_user, referer
                                    FROM $payment_table
                                    WHERE deleted=FALSE
                                    AND ".$where."
                                    ORDER BY date DESC");
    if (!$result) die('Couldn\'t fetch records');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

// Set document properties
    $objPHPExcel->getProperties()->setCreator("Life-Framer")
        ->setLastModifiedBy("Life-Framer Payments")
        ->setTitle("Life-Framer Payments Registry". $xls_name)
        ->setSubject("Life-Framer Payments Registry")
        ->setDescription("Life-Framer Payments Registry")
        ->setKeywords("life-framer payments")
        ->setCategory("Life-Framer");


    // Headers
    //id, payment_ref, date, description, amount, name,email_address,wp_user
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Payment Id')
        ->setCellValue('B1', 'Braintree Reference')
        ->setCellValue('C1', 'Date')
        ->setCellValue('D1', 'Description')
        ->setCellValue('E1', 'Amount')
        ->setCellValue('F1', 'VAT')
        ->setCellValue('G1', 'Billing Country')
        ->setCellValue('H1', 'Customer')
        ->setCellValue('I1', 'Email')
        ->setCellValue('J1', 'Wordpress Username')
        ->setCellValue('K1', 'Previous page (Referer)');

    $currentRow = 2;
    foreach ($result as $entry) {
        $country = $entry->country;
        // Values
        // id, payment_ref, date, description, amount, name,email_address,wp_user
        //iconv("utf-8", "ascii//TRANSLIT", $input)
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $currentRow, $entry->id)
            ->setCellValue('B' . $currentRow, $entry->payment_ref)
            ->setCellValue('C' . $currentRow, $entry->date)
            ->setCellValue('D' . $currentRow, $entry->description)
            ->setCellValue('E' . $currentRow, $entry->amount)
            ->setCellValue('F' . $currentRow, $entry->vat)
            ->setCellValue('G' . $currentRow, $entry->country)
            ->setCellValue('H' . $currentRow, $entry->name)
            ->setCellValue('I' . $currentRow, $entry->email_address)
            ->setCellValue('J' . $currentRow, $entry->wp_user)
            ->setCellValue('K' . $currentRow, $entry->referer);
        $currentRow++;
    }

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="life-framer-payments'.$xls_name.'.xls"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}


function export_members_to_excel(){

//    ini_set('max_execution_time', 60);
    if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');


    /** Include PHPExcel */
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';


    global $wpdb;

    if(isset($_POST['start_date']) && $_POST['start_date'] != '' && isset($_POST['end_date']) && $_POST['end_date'] != '' ){
        $where = 'payments.date BETWEEN "'.$_POST['start_date'].' 00:00:00.000000" AND "'.$_POST['end_date'].' 23:59:59.999999"';
        $xls_name = ' '.$_POST['start_date'].' - '. $_POST['end_date']. ' ';
    }
    else{
        $where = '1=1';
        $xls_name = '';
    }

    /* Get users WITH payment */

    $get_users_with_payment = $wpdb->get_results(
        "SELECT   
        COUNT(payments.id) AS theCount,
        users.ID,
        users.user_email,
        users.user_registered,
        usermeta.meta_value as role,
        usermeta2.meta_value as first_name,
        usermeta3.meta_value as last_name,
        max(payments.date) as last_payment,
        payments.date as date_payment,
        payments.amount as payment_amount       

        FROM {$wpdb->prefix}users AS users
        LEFT JOIN {$wpdb->prefix}usermeta AS usermeta
        ON (users.ID = usermeta.user_id AND usermeta.meta_key = 'role') 
        LEFT JOIN {$wpdb->prefix}usermeta AS usermeta2
        ON (users.ID = usermeta2.user_id AND usermeta2.meta_key='first_name')
        LEFT JOIN {$wpdb->prefix}usermeta AS usermeta3
        ON (users.ID = usermeta3.user_id AND usermeta3.meta_key='last_name')
        INNER JOIN  {$wpdb->prefix}lf_payments AS payments
        ON users.user_email = payments.email_address

        WHERE payments.id IN (SELECT max(payments2.id) from {$wpdb->prefix}lf_payments AS payments2 GROUP BY email_address)
        AND ".$where."
        GROUP BY users.user_email
        ORDER by last_payment DESC
        ", ARRAY_A);

    /* Get users WITHOUT payment */
    //     $get_users_without_payment = $wpdb->get_results(
    //         "
    //         SELECT
    //         users.ID,
    //         users.user_email,
    //         users.user_registered

    //         FROM {$wpdb->prefix}users AS users

    //         LEFT JOIN {$wpdb->prefix}lf_payments AS payments
    //         ON users.user_email = payments.email_address

    //         WHERE payments.email_address IS NULL
    //         GROUP BY users.user_email
    //         ", ARRAY_A);

    // foreach($get_users_without_payment as $key => $user_without_pay){
    //     $get_users_without_payment[$key]['role'] = get_user_meta($user_without_pay['ID'],'role',true);
    //     $get_users_without_payment[$key]['first_name'] = get_user_meta($user_without_pay['ID'],'first_name',true);
    //     $get_users_without_payment[$key]['last_name'] = get_user_meta($user_without_pay['ID'],'last_name',true);
    //     $get_users_without_payment[$key]['payment_amount'] = '-';
    //     $get_users_without_payment[$key]['last_payment'] = '-';
    // }


    // $users = array_merge($get_users_with_payment, $get_users_without_payment);
    $users = $get_users_with_payment;
    if (!$users) die('Couldn\'t fetch records');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Life-Framer")
        ->setLastModifiedBy("Life-Framer Members")
        ->setTitle("Life-Framer Members Registry". $xls_name)
        ->setSubject("Life-Framer Members Registry")
        ->setDescription("Life-Framer Members Registry")
        ->setKeywords("life-framer Members")
        ->setCategory("Life-Framer");


    // Headers
    //id, payment_ref, date, description, amount, name,email_address,wp_user
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'First Name')
        ->setCellValue('B1', 'Last Name')
        ->setCellValue('C1', 'E-mail')
        ->setCellValue('D1', 'Date Registered')
        ->setCellValue('E1', 'Community Role')
        ->setCellValue('F1', 'Date of last payment')
        ->setCellValue('G1', 'Payment amount');

    $currentRow = 2;
    foreach ($users as $user) {
        // $wp_user = $wpdb->get_results("SELECT * FROM wrd_users WHERE user_email = '$entry->email_address'");
        // if($entry->role == ''){
        //     $entry->role = 'past';
        // }
        // if(!isset($wp_user[0])){
        //     continue;
        // }
        // $wp_user_meta = get_user_meta($wp_user[0]->ID);


        // Values
        // id, payment_ref, date, description, amount, name,email_address,wp_user
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $currentRow, ($user['first_name'] ? $user['first_name'] : ''))
            ->setCellValue('B' . $currentRow, ($user['last_name'] ? $user['last_name'] : ''))
            ->setCellValue('C' . $currentRow, $user['user_email'])
            ->setCellValue('D' . $currentRow, $user['user_registered'])
            ->setCellValue('E' . $currentRow, ($user['role'] ? $user['role'] : 'no role currently'))
            ->setCellValue('F' . $currentRow, $user['last_payment'])
            ->setCellValue('G' . $currentRow, $user['payment_amount']);
        $currentRow++;
    }

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="life-framer-members'.$xls_name.'.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;

}
add_action('admin_action_export_members_excel', 'export_members_to_excel');


// SETTINGS

// Add settings link on plugin page
function your_plugin_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=lifeframer_options_plugin">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link');

add_action('admin_menu', 'wporg_custom_admin_menu');

function wporg_custom_admin_menu()
{
    add_options_page(
        'Life-Framer',
        'Life-Framer Settings',
        'manage_options',
        'lifeframer_options_plugin',
        'lifeframer_page'
    );
}

function lifeframer_page()
{
    global $dropbox;
    $redirect = urlencode($_SERVER['REQUEST_URI']);

    ?>
    <div class="wrap">
        <h2>Life-framer Settings</h2>
        <h3>Integration Dropbox-Lifeframer</h3>
        <p>In order to be able to upload user images to your Dropbox, you need to allow the LifeFramer Wordpress plugin
            to access the your account.
        </p>
        <p>Current state: </p>
        <?
        // $access_token = load_token("access");
        // if (!empty($access_token)) {
        //     $dropbox->SetAccessToken($access_token);
        // }
        // if (!$dropbox->IsAuthorized()) {
        //     // redirect user to dropbox auth page
        //     $return_url = admin_url('admin.php') . "?action=handle_dropbox_auth&auth_callback=1";
        //     $auth_url = $dropbox->BuildAuthorizeUrl($return_url);
        //     $request_token = $dropbox->GetRequestToken();
        //     store_token($request_token, $request_token['t']);
        //     print("<h3 style='color: red'>Authentication required. <a href='$auth_url'>Click here to request access to Life-framer Dropbox.</a></h3>");
        // } else {
        //     print("<h3 style='color: green'>Dropbox access has been configured correctly! You don't have anything else todo!</h3>");
        // }

        ?>
        <h2>Email configuration</h2>
        <h3>Theme entry - Customer Email Template</h3>
        <form action="<?= admin_url('admin.php'); ?>" method="post">
            <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
            <input type="hidden" name="action" value="update_theme_entry_customer_email_template">
            <textarea rows="10" cols="80" name="theme_entry_customer_email"
                      id="theme_entry_customer_email"><?= esc_textarea(get_theme_entry_customer_email_template()); ?></textarea><br/>
            <input type="submit" value="Update email template">
        </form>
        <h3>Series entry - Customer Email Template</h3>
        <form action="<?= admin_url('admin.php'); ?>" method="post">
            <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
            <input type="hidden" name="action" value="update_series_entry_customer_email_template">
            <textarea rows="10" cols="80" name="series_entry_customer_email"
                      id="series_entry_customer_email"><?= esc_textarea(get_series_entry_customer_email_template()); ?></textarea><br/>
            <input type="submit" value="Update email template">
        </form>
        <h3>Email notifications</h3>
        <form action="<?= admin_url('admin.php'); ?>" method="post">
            <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
            <input type="hidden" name="action" value="update_receive_email_settings">
            <p><input type="checkbox" id="receive_email_enabled"
                      name="receive_email_enabled" <?php if (receiveEmailSettingEnabled()) echo "checked" ?>/>Receive an
                email when a
                new customer upload images</p>
            <input type="submit" value="Update email notification settings">
        </form>

        <h3>Image quality of entrant uploaded images</h3>
        <form action="<?= admin_url('admin.php'); ?>" method="post">
            <input type="hidden" name="_wp_http_referer" value="<?= $redirect; ?>">
            <input type="hidden" name="action" value="update_image_quality_settings">
            <p>
            <?php
            ?>
                <input type="number" id="image_quality_settings" name="image_quality_settings" value="<? echo get_option( 'image_quality_settings' );?>" /> %

            </p>
            <input type="submit" value="Update image quality settings">
        </form>

    </div>
    <?php
}

add_action('admin_init', 'addExtraCreditsForEntrants');
function addExtraCreditsForEntrants()
{
    if (get_current_user_id() != 2) {
        return;
    }

    if (filter_input(INPUT_GET, 'update_extra_entries', FILTER_SANITIZE_NUMBER_INT) != 1) {
        return;
    }

    $emails = [

    ];
    foreach ($emails as $email) {
        $user = get_user_by('email', $email);
        if (!$user) {
            echo 'Could not find user by email: ' . $email . '<br />';
            continue;
        }

        $extraCredits = get_user_meta($user->ID, 'extra_entry_imgs', true);
        $credits = (is_int($extraCredits) && $extraCredits > 0) ? ($extraCredits) + 2 : 2;
        update_user_meta($user->ID, 'extra_entry_imgs', $credits);

        echo 'Awarded 2 credits to user with email ' . $email . '. Total credits now: ' . $credits . '<br />';
    }

    die('done');
}

add_action('admin_init', 'addExtraCreditsForMembers');
function addExtraCreditsForMembers()
{
    if (get_current_user_id() != 2) {
        return;
    }

    if (filter_input(INPUT_GET, 'update_extra_entries_for_members', FILTER_SANITIZE_NUMBER_INT) != 1) {
        return;
    }

    $emails = [

    ];
    foreach ($emails as $email) {
        $user = get_user_by('email', $email);
        if (!$user) {
            echo 'Could not find user by email: ' . $email . '<br />';
            continue;
        }

        $extraCredits = get_user_meta($user->ID, 'extra_img_1', true);
        $credits = (is_int($extraCredits) && $extraCredits > 0) ? ($extraCredits) + 5 : 5;
        update_user_meta($user->ID, 'extra_img_1', $credits);

        echo 'Awarded 5 credits to user with email ' . $email . '. Total credits now: ' . $credits . '<br />';
    }

    die('done');
}
