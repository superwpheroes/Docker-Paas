<?php

defined('ABSPATH') or die('No script kiddies please!');

function get_theme_name($theme_id)
{
    global $wpdb;
    global $theme_table;
    $theme_name = $wpdb->get_var("SELECT name FROM $theme_table WHERE id = $theme_id LIMIT 1");
    return $theme_name;
}

function get_current_lifeframer_theme($is_series_award = false)
{
    global $wpdb;
    global $theme_table;
    $is_series_award = $is_series_award ? 1 : 0;
    $current_time = current_time('mysql');
    $current_theme = $wpdb->get_row("SELECT * FROM $theme_table WHERE start < '$current_time' and end > '$current_time' and deleted = false AND is_series_award = {$is_series_award} LIMIT 1");
    return $current_theme;
}


function check_overlapping_theme($wpdb, $theme_table, $new_start, $new_end, $is_series_award = false)
{
    $is_series_award = $is_series_award ? 1 : 0;
    $query = "SELECT COUNT(*) FROM $theme_table WHERE '$new_start' <= end AND '$new_end' >= start AND deleted = false AND is_series_award = {$is_series_award} LIMIT 1";
    $overlapping_theme = $wpdb->get_var($query);
    if ($overlapping_theme > 0) {
        wp_die(__('ERROR: Sorry, you are trying to create a theme that overlaps with an existing one. Review your start and end dates.'));
    }
}


function isThemeActive($theme)
{
    $start = new DateTime($theme->start);
    $end = new DateTime($theme->end);
    $current = new DateTime(current_time('mysql'));

    return ($current > $start && $current < $end);

}
