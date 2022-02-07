<?php

add_action('init', 'lifeframer_public_actions');

function lifeframer_public_actions()
{
    if (!is_admin()) {
        wp_register_style('lfpaymentcss', plugins_url('css/payments.css', __FILE__));
        wp_enqueue_style('lfpaymentcss');
    }

    if (isset($_POST['entry-process-submit'])) {
        entry_process();
    }if (isset($_POST['new-entry-process-submit'])) {
        new_entry_process();
    } else if (isset($_POST['make_payment'])) {
        make_braintree_payment();
    }
}
