<?php

// BRAINTREE PAYMENTS

defined('ABSPATH') or die('No script kiddies please!');

require_once 'lib/Braintree.php';
require_once 'tokens/BraintreeTokens.php';





function lifeframer_payment_form($atts)
{
    $a = shortcode_atts(array(
        'payment_amount' => 'NO_PAYMENT',
        'payment_description' => 'Life-Framer Payment',
        'after_payment' => '/',
        'promote_to_member' => false
    ), $atts);

    return print_braintree_payment_form($a);
}
add_shortcode('lifeframer_payment_form', 'lifeframer_payment_form');






function print_braintree_payment_form($a)
{
    ob_start();
    require_once('templates/payments-form.php');
    return ob_get_clean();

}






function make_braintree_payment(){
    global $wpdb;
    global $payment_table;
    global $transaction_table;

    $my_errors = new LFErrorCollection();

    if (isset($_POST['payment_method_nonce'])) {
        $payment_nonce = $_POST['payment_method_nonce'];
    }

    if (!isset($_POST['firstName']) || empty($_POST['firstName'])) {
        $my_errors->add_error(new LFError('firstName', 'First name is required'));
    }

    if (!isset($_POST['transactionId']) || empty($_POST['transactionId'])) {
        $my_errors->add_error(new LFError('transaction_id', 'LF-1133: Sorry, it look like we have encountered an internal error.'));
    }

    if (!isset($_POST['lastName']) || empty($_POST['lastName'])) {
        $my_errors->add_error(new LFError('lastName', 'Last name is required'));
    }

    if (!isset($_POST['email']) || empty($_POST['email'])) {
        $my_errors->add_error(new LFError('email', 'A valid email is required'));
    }

    if (!isset($_POST['country']) || empty($_POST['country'])) {
        $my_errors->add_error(new LFError('billing_country', 'Please select your billing country'));
    }

    $transaction_id = $_POST['transactionId'];
    $newsletter = $_POST['newsletter'];
    $email_address = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    $transaction = $wpdb->get_row($wpdb->prepare("SELECT * from $transaction_table WHERE transaction_id='%s' ORDER BY date DESC LIMIT 1", $transaction_id));

    $euCountries = array("at", "be", "bg", "hr", "cy", "cz", "dk", "ee", "fi", "fr", "de", "gr", "hu", "ie", "it", "lv", "lt", "lu", "mt", "nl", "pl", "pt", "sk", "si", "es", "se", "gb");

    $transactionAmount = $transaction->amount;
    $vat = 0;

    /* VAT Functionality */
    // if (in_array($_POST['country'], $euCountries)) {
    //     if (!isset($_POST['payment_amount']) || empty($_POST['payment_amount'])) {
    //         $my_errors->add_error(new LFError('payment_amount', 'Unable to read the payment amount. Please contact LifeFramer for support.'));
    //     }
    //     $vat = intval($transactionAmount) * 0.2;
    //     $transactionAmount = intval($transactionAmount) + $vat;
    // }

    // if ($_POST['payment_amount'] != $transactionAmount) {
    //     $my_errors->add_error(new LFError('payment_amount', 'Something seems to not be working as we expected. Please contact LifeFramer for support.'));
    // }

    if (!$my_errors->hasErrors()) {

        $result = Braintree\Transaction::sale([
            'amount' => $transactionAmount,
            'orderId' => $transaction->description . " " . $transaction_id,
            'paymentMethodNonce' => $payment_nonce,
            'customer' => [
                'firstName' => $_POST['firstName'],
                'lastName' => $_POST['lastName'],
                'email' => $_POST['email']
            ],
            'billing' => [
                'firstName' => $_POST['firstName'],
                'lastName' => $_POST['lastName'],
                'countryCodeAlpha2' => $_POST['country']
            ],
            'options' => [
                'submitForSettlement' => true,
                'three_d_secure' => [
                    "required" => false
                ]
            ]
        ]);

        if ($result->success) {

            $customer_name = $_POST['firstName'] . " " . $_POST['lastName'];
            $email = $_POST['email'];

            // Initialize de session to avoid ask for this info on the uploads
            initialize_session();
            $_SESSION["customer-name"] = $customer_name;
            $_SESSION["customer-email"] = $email;
            $_SESSION["payment_reference"] = $result->transaction->id;

            if (is_user_logged_in()){
                if($transaction->promote_to_member){
                    $current_user = wp_get_current_user();
                    $the_user_id = $current_user->ID;
                    // Change user role
                    UM()->roles()->set_role( $the_user_id, 'um_member' );
                }
                else{
                    $current_user = wp_get_current_user();
                    $the_user_id = $current_user->ID;
                    // Change user role
                    UM()->roles()->set_role( $the_user_id, 'um_entrant' );
                }
            }
            else{

                /* Check if we have user already registered */
                $payment_desc = trim($_POST['payment_description']);
                $user_exists = email_exists( $email );
                if ( $user_exists ) {
                    if( ($payment_desc =='Life-Framer - 1 entry')
                        || ($payment_desc =='Life-Framer - 3 entries')
                        || ($payment_desc =='Life-Framer - 6 entries')
                    ){
                        UM()->roles()->set_role( $user_exists, 'um_entrant' );
                    }
                    else{
                        if($payment_desc =='Life-Framer - membership'){
                            UM()->roles()->set_role( $user_exists, 'um_member' );
                        }
                    }
                }

            }


            if (!$wpdb->insert(
                $payment_table,
                array(
                    'date' => current_time('mysql'),
                    'name' => stripslashes($customer_name),
                    'wp_user' => is_user_logged_in() ? wp_get_current_user()->user_login : NULL,
                    'email_address' => trim($email),
                    'payment_ref' => $result->transaction->id,
                    'amount' => $result->transaction->amount,
                    'description' => $transaction->description,
                    'referer' => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : NULL,
                    'country' => countryCodeToName($_POST['country']),
                    'vat' => $vat,
                    'newsletter' => $newsletter
                )
            )
            ) {

                $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
                $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';
                $mail_headers[] = 'MIME-Version: 1.0" . "\n';
                $mail_headers[] = "Content-type:text/html;charset=UTF-8" . "\n";

                $subject = "Life-Framer ERROR when saving payment information!";
                $message = "Hi! The system has detected an error when saving the following payment:
                'transaction_id' => $transaction_id
                'date' => " . current_time('mysql') . ",
                'name' => " . $customer_name . ",
                'wp_user' => " . is_user_logged_in() ? wp_get_current_user()->user_login : NULL . ",
                'email_address' => " . $email . ",
                'payment_ref' => " . $result->transaction->id . ",
                'amount' => " . $result->transaction->amount . ",
                'country' =>" . $_POST['country'] . ",
                'vat' => " . $vat . ",
                'referer' => " . isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL . "
                'description' => " . $transaction->description;
                $to = "info@life-framer.com";
                wp_mail($to, $subject, $message, $mail_headers);

            } else {
                $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
                $mail_headers[] = 'Cc: Life Framer <info@life-framer.com>';
                $mail_headers[] = 'MIME-Version: 1.0" . "\n';
                $mail_headers[] = "Content-type:text/html;charset=UTF-8" . "\n";

                $subject = "Payment receipt and account creation";

                $site_url = get_bloginfo('url');
                if(strpos($site_url, 'staging') === false){
                    $htmlContent = file_get_contents(get_stylesheet_directory_uri()."/ultimate-member/templates/email/payment_email.html");
                }
                else{
                    $htmlContent = file_get_contents("https://life-framer:@Plmdevirusi10@staging.life-framer.com/wp-content/themes/Avada-Child-Theme/ultimate-member/templates/email/payment_email.html");
                }

                $message = $htmlContent;

                $payment_description = trim($_POST['payment_description']);

                if($payment_description =='Life-Framer - membership'){
                    $registration_link = '<a href="'.$site_url.'/register-member" style="color: #000;text-decoration: underline;" target="_blank">'.$site_url.'/register-member</a>';
                    $member_area = '<a href="'.$site_url.'/my-lf-member" style="color: #000;text-decoration: underline;" target="_blank">member</a>';
                }
                else{
                    $registration_link = '<a href="'.$site_url.'/register-entrant" style="color: #000;text-decoration: underline;" target="_blank">'.$site_url.'/register-entrant</a>';
                    $member_area = '<a href="'.$site_url.'/my-lf-entrant" style="color: #000;text-decoration: underline;" target="_blank">entrant</a>';
                }



                $find = array(
                    "{display_name}",
                    "{payment_id}",
                    "{description}",
                    "{total_amount_excluding_vat}",
                    "{rate_of_vat_charged}",
                    "{total_amount_of_vat}",
                    "{total_amount_including_vat}",
                    "{billed_to}",
                    "{billing_country}",
                    "{registration_link}",
                    "{year}",
                    "{member_area}"
                );

                $replace = array(
                    stripslashes($customer_name),
                    $result->transaction->id . " / " . $transaction_id,
                    $transaction->description,
                    $transaction->amount . " US$",
                    (($transaction->amount == $transactionAmount) ? "0%" : "20%"),
                    $vat ." US$",
                    $transactionAmount ." US$",
                    stripslashes($customer_name),
                    countryCodeToName($_POST['country']),
                    $registration_link,
                    date('Y'),
                    $member_area
                );

                $message = str_replace($find, $replace, $message);


                $to = $email;
                wp_mail($to, $subject, $message, $mail_headers);

                endTransaction($wpdb, $transaction_table, $transaction_id, $result->transaction->id);


            }


            wp_redirect($transaction->after_payment . '?transaction=' . $transaction_id.'&email='.$email_address);
            exit();
        }
    }

    $formated_errors = "";

    if($result->errors){

        if($result->message == 'Gateway Rejected: three_d_secure'){
            $my_errors->add_error(new LFError('threeds', "Authentication failed. You have not been charged. Please try again and make sure that you don't have a pop-up blocker installed which is restricting the additional authentication page from opening. Alternatively, you can change the payment method.
"));
            $formated_errors .= "Authentication failed. You have not been charged. Please try again and make sure that you don't have a pop-up blocker installed which is restricting the additional authentication page from opening. Alternatively, you can change the payment method.
" . "\n";
        }

        $errors = [];
        foreach ($result->errors->deepAll() AS $error) {
            $errors[] = $error->message;
        }

        $mail_headers = [];
        $mail_headers[] = 'From: Life Framer <info@life-framer.com>';
        $mail_headers[] = 'MIME-Version: 1.0" . "\n';
        $mail_headers[] = "Content-type:text/html;charset=UTF-8" . "\n";

        $subject = "Life-Framer payment error!";
        $message = "Hi! There was failed payment for the following transaction:
                'transaction_id' => $transaction_id
                'user_ip' => " . $_SERVER['REMOTE_ADDR'] . ",
                'date' => " . current_time('mysql') . ",
                'message' => " . $result->message . "
                'error' => " . join('.', $errors);
        $to = "info@life-framer.com";
        wp_mail($to, $subject, $message, $mail_headers);

        foreach ($result->errors->deepAll() AS $error) {
            $my_errors->add_error(new LFError($error->attribute, $error->message));
            $formated_errors .= $error->message . "\n";
        }
    }


    abortTransaction($wpdb, $transaction_table, $transaction_id, $formated_errors);

    $_REQUEST["errors"] = $my_errors;

}
add_action('admin_action_make_payment', 'make_braintree_payment');






function abortTransaction($wpdb, $transaction_table, $transaction_id, $formated_errors)
{
    $wpdb->update(
        $transaction_table,
        array(
            'errors' => $formated_errors,
            'in_flight' => false
        ),
        array('transaction_id' => $transaction_id),
        array(
            '%s'
        ),
        array('%s')
    );
}





function endTransaction($wpdb, $transaction_table, $transaction_id, $payment_reference)
{
    return $wpdb->update(
        $transaction_table,
        array(
            'in_flight' => false,
            'payment_reference' => $payment_reference
        ),
        array('transaction_id' => $transaction_id),
        array(
            '%s',
            '%s'
        ),
        array('%s')
    );
}

function countryCodeToName($countryCode)
{
    $countries = array("af" => "Afghanistan",
                       "ax" => "Åland Islands",
                       "al" => "Albania",
                       "dz" => "Algeria",
                       "as" => "American Samoa",
                       "ad" => "Andorra",
                       "ao" => "Angola",
                       "ai" => "Anguilla",
                       "ag" => "Antigua and Barbuda",
                       "ar" => "Argentina",
                       "am" => "Armenia",
                       "aw" => "Aruba",
                       "au" => "Australia",
                       "at" => "Austria",
                       "az" => "Azerbaijan",
                       "bs" => "Bahamas",
                       "bh" => "Bahrain",
                       "bd" => "Bangladesh",
                       "bb" => "Barbados",
                       "by" => "Belarus",
                       "be" => "Belgium",
                       "bz" => "Belize",
                       "bj" => "Benin",
                       "bm" => "Bermuda",
                       "bt" => "Bhutan",
                       "bo" => "Bolivia",
                       "ba" => "Bosnia and Herzegovina",
                       "bw" => "Botswana",
                       "bv" => "Bouvet Island",
                       "br" => "Brazil",
                       "io" => "British Indian Ocean Territory",
                       "bn" => "Brunei Darussalam",
                       "bg" => "Bulgaria",
                       "bf" => "Burkina Faso",
                       "bi" => "Burundi",
                       "kh" => "Cambodia",
                       "cm" => "Cameroon",
                       "ca" => "Canada",
                       "cv" => "Cape Verde",
                       "ky" => "Cayman Islands",
                       "cf" => "Central African Republic",
                       "td" => "Chad",
                       "cl" => "Chile",
                       "cn" => "China",
                       "cx" => "Christmas Island",
                       "co" => "Colombia",
                       "km" => "Comoros",
                       "cg" => "Congo",
                       "cd" => "Congo, The Democratic Republic of The",
                       "ck" => "Cook Islands",
                       "cr" => "Costa Rica",
                       "ci" => "Cote D'ivoire",
                       "hr" => "Croatia",
                       "cu" => "Cuba",
                       "cy" => "Cyprus",
                       "cz" => "Czechia",
                       "dk" => "Denmark",
                       "dj" => "Djibouti",
                       "dm" => "Dominica",
                       "do" => "Dominican Republic",
                       "ec" => "Ecuador",
                       "eg" => "Egypt",
                       "sv" => "El Salvador",
                       "gq" => "Equatorial Guinea",
                       "er" => "Eritrea",
                       "ee" => "Estonia",
                       "et" => "Ethiopia",
                       "fk" => "Falkland Islands (Malvinas)",
                       "fo" => "Faroe Islands",
                       "fj" => "Fiji",
                       "fi" => "Finland",
                       "fr" => "France",
                       "gf" => "French Guiana",
                       "pf" => "French Polynesia",
                       "tf" => "French Southern Territories",
                       "ga" => "Gabon",
                       "gm" => "Gambia",
                       "ge" => "Georgia",
                       "de" => "Germany",
                       "gh" => "Ghana",
                       "gi" => "Gibraltar",
                       "gr" => "Greece",
                       "gl" => "Greenland",
                       "gd" => "Grenada",
                       "gp" => "Guadeloupe",
                       "gu" => "Guam",
                       "gt" => "Guatemala",
                       "gg" => "Guernsey",
                       "gn" => "Guinea",
                       "gw" => "Guinea-bissau",
                       "gy" => "Guyana",
                       "ht" => "Haiti",
                       "hn" => "Honduras",
                       "hk" => "Hong Kong",
                       "hu" => "Hungary",
                       "is" => "Iceland",
                       "in" => "India",
                       "id" => "Indonesia",
                       "ir" => "Iran, Islamic Republic of",
                       "iq" => "Iraq",
                       "ie" => "Ireland",
                       "im" => "Isle of Man",
                       "il" => "Israel",
                       "it" => "Italy",
                       "jm" => "Jamaica",
                       "jp" => "Japan",
                       "je" => "Jersey",
                       "jo" => "Jordan",
                       "kz" => "Kazakhstan",
                       "ke" => "Kenya",
                       "kr" => "Korea",
                       "kw" => "Kuwait",
                       "kg" => "Kyrgyzstan",
                       "lv" => "Latvia",
                       "lb" => "Lebanon",
                       "ls" => "Lesotho",
                       "lr" => "Liberia",
                       "ly" => "Libyan Arab Jamahiriya",
                       "li" => "Liechtenstein",
                       "lt" => "Lithuania",
                       "lu" => "Luxembourg",
                       "mo" => "Macao",
                       "mk" => "Macedonia",
                       "mg" => "Madagascar",
                       "mw" => "Malawi",
                       "my" => "Malaysia",
                       "mv" => "Maldives",
                       "ml" => "Mali",
                       "mt" => "Malta",
                       "mh" => "Marshall Islands",
                       "mq" => "Martinique",
                       "mr" => "Mauritania",
                       "mu" => "Mauritius",
                       "yt" => "Mayotte",
                       "mx" => "Mexico",
                       "md" => "Moldova, Republic of",
                       "mc" => "Monaco",
                       "mn" => "Mongolia",
                       "me" => "Montenegro",
                       "ma" => "Morocco",
                       "mz" => "Mozambique",
                       "mm" => "Myanmar",
                       "na" => "Namibia",
                       "nr" => "Nauru",
                       "np" => "Nepal",
                       "nl" => "Netherlands",
                       "an" => "Netherlands Antilles",
                       "nc" => "New Caledonia",
                       "nz" => "New Zealand",
                       "ni" => "Nicaragua",
                       "ne" => "Niger",
                       "ng" => "Nigeria",
                       "nf" => "Norfolk Island",
                       "mp" => "Northern Mariana Islands",
                       "no" => "Norway",
                       "om" => "Oman",
                       "pk" => "Pakistan",
                       "pw" => "Palau",
                       "ps" => "Palestinian Territory, Occupied",
                       "pa" => "Panama",
                       "pg" => "Papua New Guinea",
                       "py" => "Paraguay",
                       "pe" => "Peru",
                       "ph" => "Philippines",
                       "pn" => "Pitcairn",
                       "pl" => "Poland",
                       "pt" => "Portugal",
                       "pr" => "Puerto Rico",
                       "qa" => "Qatar",
                       "re" => "Reunion",
                       "ro" => "Romania",
                       "ru" => "Russian Federation",
                       "rw" => "Rwanda",
                       "sh" => "Saint Helena",
                       "kn" => "Saint Kitts and Nevis",
                       "lc" => "Saint Lucia",
                       "pm" => "Saint Pierre and Miquelon",
                       "vc" => "Saint Vincent and The Grenadines",
                       "ws" => "Samoa",
                       "sm" => "San Marino",
                       "st" => "Sao Tome and Principe",
                       "sa" => "Saudi Arabia",
                       "sn" => "Senegal",
                       "rs" => "Serbia",
                       "sc" => "Seychelles",
                       "sl" => "Sierra Leone",
                       "sg" => "Singapore",
                       "sk" => "Slovakia",
                       "si" => "Slovenia",
                       "sb" => "Solomon Islands",
                       "so" => "Somalia",
                       "za" => "South Africa",
                       "es" => "Spain",
                       "lk" => "Sri Lanka",
                       "sd" => "Sudan",
                       "sr" => "Suriname",
                       "sj" => "Svalbard and Jan Mayen",
                       "sz" => "Swaziland",
                       "se" => "Sweden",
                       "ch" => "Switzerland",
                       "sy" => "Syrian Arab Republic",
                       "tw" => "Taiwan, Province of China",
                       "tj" => "Tajikistan",
                       "tz" => "Tanzania, United Republic of",
                       "th" => "Thailand",
                       "tl" => "Timor-leste",
                       "tg" => "Togo",
                       "tk" => "Tokelau",
                       "to" => "Tonga",
                       "tt" => "Trinidad and Tobago",
                       "tn" => "Tunisia",
                       "tr" => "Turkey",
                       "tm" => "Turkmenistan",
                       "tc" => "Turks and Caicos Islands",
                       "tv" => "Tuvalu",
                       "ug" => "Uganda",
                       "ua" => "Ukraine",
                       "ae" => "United Arab Emirates",
                       "gb" => "United Kingdom",
                       "us" => "United States",
                       "uy" => "Uruguay",
                       "uz" => "Uzbekistan",
                       "vu" => "Vanuatu",
                       "ve" => "Venezuela",
                       "vn" => "Viet Nam",
                       "vg" => "Virgin Islands, British",
                       "vi" => "Virgin Islands, U.S.",
                       "wf" => "Wallis and Futuna",
                       "eh" => "Western Sahara",
                       "ye" => "Yemen",
                       "zm" => "Zambia",
                       "zw" => "Zimbabwe");
    return $countries[$countryCode];
}

class LFErrorCollection
{
    private $_errors = [];

    public function __construct()
    {
    }

    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function add_error(LFError $e)
    {
        $this->_errors[] = $e;
    }

    public function deepAll()
    {
        return $this->_errors;
    }
}

class LFError
{
    public $attribute;
    public $message;

    public function __construct($_attribute, $_message)
    {
        $this->attribute = $_attribute;
        $this->message = $_message;
    }
}
