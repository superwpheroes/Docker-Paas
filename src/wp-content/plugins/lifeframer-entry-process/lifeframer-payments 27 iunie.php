<?php

// BRAINTREE PAYMENTS

defined('ABSPATH') or die('No script kiddies please!');

require_once 'lib/Braintree.php';
require_once 'tokens/BraintreeTokens.php';

add_shortcode('lifeframer_payment_form', 'lifeframer_payment_form');

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

function print_braintree_payment_form($a)
{
    global $wpdb;
    global $transaction_table;

    nocache_headers();


    if (!isset($a['payment_amount']) || $a['payment_amount'] === 'NO_PAYMENT') {
        $output = 'ERROR LF-2391: Please specify payment_amount on the shortcode';
        return $output;
    } else {

        $transaction_id = "LFRAM-" . rand(1000000, 9999999);

        if (!$wpdb->insert(
            $transaction_table,
            array(
                'date' => current_time('mysql'),
                'transaction_id' => $transaction_id,
                'wp_user' => is_user_logged_in() ? wp_get_current_user()->user_login : NULL,
                'amount' => $a['payment_amount'],
                'description' => $a['payment_description'],
                'promote_to_member' => (strcasecmp($a['promote_to_member'], "true") == 0),
                'after_payment' => $a['after_payment'],
                'in_flight' => true,
                'errors' => NULL,
                'referer' => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : NULL
            )
        )
        ) {
            $output = 'ERROR LF-2393: Sorry, we are experiencing problems and our payment system is not available at the moment. Try again refreshing this page in a few seconds or please contact our support team support@life-framer.com. We’ll respond within 24 hours.';
            return $output;
        }

        $clientToken = Braintree_ClientToken::generate([
//        "customerId" => $aCustomerId->ID
        ]);

        $clientTokenPaypal = Braintree_ClientToken::generate([
//        "customerId" => $aCustomerId->ID
        ]);


        $output = '<script src="https://js.braintreegateway.com/js/braintree-2.21.0.min.js"
                xmlns="http://www.w3.org/1999/html"></script>
        <script type="text/javascript">

            jQuery(window).unload(function() {
                jQuery("#cover").remove();
            });

            jQuery(document).ready(function () {

                jQuery(".card-input").hide();

                function copy_values_and_call_braintree() {
                    updatePersonalDetails();
                    jQuery("#braintree_card_number").val(jQuery("#card_number").val());
                    var expiration = jQuery("#expiration_date_month").val() + "/" + jQuery("#expiration_date_year").val();
                    jQuery("#braintree_expiration_date").val(expiration);
                    jQuery("#braintree_cvv").val(jQuery("#cvv").val());
                    jQuery("#braintree_cardholder_name").val(jQuery("#cardholder_name").val());
                    jQuery("#braintree_postal_code").val(jQuery("#postal_code").val());
                    jQuery("#payment_card_submit").click();
                }

                function validateEmail($email) {
                  var emailReg = /.+@.+\..+/;
                  return emailReg.test( $email );
                }

                function validate_form(extended) {

                    jQuery("label").css("color", "rgb(85, 85 ,85)");
                    jQuery("#form-errors").empty();

                    errors = [];
                    if (!jQuery("#firstName_details").val()) {
                        errors.push({
                            field: "firstName_details",
                            message: "Your first name is required"
                        });
                    }

                    if (!jQuery("#lastName_details").val()) {
                        errors.push({
                            field: "lastName_details",
                            message: "Your last name is required"
                        });
                    }

                    if (!jQuery("#billing_country").val()) {
                        errors.push({
                            field: "billing_country",
                            message: "Your billing country is required"
                        });
                    }

                    if (!jQuery("#email_details").val() || !validateEmail(jQuery("#email_details").val())) {
                        errors.push({
                            field: "email_details",
                            message: "A valid email is required"
                        });
                    }
                    if(extended)
                    {
                        month = jQuery("#expiration_date_month").val();
                        if (month == "--") {
                            errors.push({
                                field: "expiration_date_month",
                                message: "Please select a month (expiration date)"
                            });
                        }
                        year = jQuery("#expiration_date_year").val();
                        if (year == "--") {
                            // only one label for month and year...
                            errors.push({
                                field: "expiration_date_month",
                                message: "Please select a year (expiration date)"
                            });
                        }

                        if (!jQuery("#card_number").val()) {
                            errors.push({
                                field: "card_number",
                                message: "Enter a valid credit or debit card number"
                            });
                        }


                        if (!jQuery("#cvv").val()) {
                            errors.push({
                                field: "cvv",
                                message: "Please specify the security code (CVV) for your card."
                            });
                        }

                        if (!jQuery("#cardholder_name").val()) {
                            errors.push({
                                field: "cardholder_name",
                                message: "Please specify the card holder name for your card."
                            });
                        }

                        if (!jQuery("#postal_code").val()) {
                            errors.push({
                                field: "postal_code",
                                message: "Please specify your postcode."
                            });
                        }
                    }

                    return errors;
                }

                jQuery("#call_paypal").click(function(){
                    errors = validate_form(false);
                    if (errors.length == 0) {
                        jQuery("#paypal-container button").click();
                    } else {
                        jQuery("html, body").animate({
                                scrollTop: 0
                            }, 600);
                        jQuery("#cover").remove();
                        jQuery("#form-errors").append("<p class=\"error-title\">Please fix the following problems before proceeding with the PayPal payment:</p>");
                        jQuery("#form-errors").append("<ul>");

                        for (i = 0; i < errors.length; i++) {
                            jQuery("#form-errors").append("<li class=\'payment-error\'>" + errors[i].message + "</li>");
                            jQuery(\'label[for="\' + errors[i].field + \'"]\').css("color", "red");
                        }
                        jQuery("#form-errors").append("</ul>");
                    }
                });

                jQuery("#back_to_paypal").click(function(){
                    jQuery(".card-input").hide();
                    jQuery("html, body").animate({
                            scrollTop: 0
                        }, 600);
                    jQuery(".hide_when_pay_by_card").show();
                    jQuery("#call_paypal").click();
                });


                jQuery("#pay_by_card").click(function () {

                    jQuery(".card-input").show();
                    jQuery("html, body").animate({
                            scrollTop: (jQuery("#card_number").offset().top - 230)
                        }, 600);
                    jQuery(".hide_when_pay_by_card").hide();
                });

                    jQuery("#card_payment_button").click(function () {
                        jQuery("body").prepend("<div id=\"cover\"></div>");
                        jQuery("html, body").animate({
                                scrollTop: 0
                            }, 600);

                        jQuery("#card_payment_button").prop("disabled", true);
                        errors = validate_form(true);
                        if (errors.length == 0) {
                            copy_values_and_call_braintree();
                        } else {
                            jQuery("#card_payment_button").prop("disabled", false);
                            jQuery("#cover").remove();
                            jQuery("#form-errors").append("<p class=\"error-title\">Sorry, something went wrong when processing your payment. Please fix the following problems:</p>");
                            jQuery("#form-errors").append("<ul>");

                            for (i = 0; i < errors.length; i++) {
                                jQuery("#form-errors").append("<li class=\'payment-error\'>" + errors[i].message + "</li>");
                                jQuery(\'label[for="\' + errors[i].field + \'"]\').css("color", "red");
                            }
                            jQuery("#form-errors").append("</ul>");
                        }


                });

                var checkoutPaypal;
                initialisePaypalIntegration();

                var checkoutCreditCard;
                initialiseCreditCardIntegration();

                jQuery("#encrypted-warning").hide();
                jQuery("#encrypted-warning-link").mouseover(function () {
                    jQuery("#encrypted-warning").show();
                });
                jQuery("#encrypted-warning-link").mouseout(function () {
                    jQuery("#encrypted-warning").hide();
                });

                jQuery("#cvv-warning").hide();
                jQuery("#cvv-warning-link").mouseover(function () {
                    jQuery("#cvv-warning").show();
                });
                jQuery("#cvv-warning-link").mouseout(function () {
                    jQuery("#cvv-warning").hide();
                });

            });

            function initialiseCreditCardIntegration(){
                braintree.setup("' . $clientToken . '", "custom", {
                    id: "checkout",
                    onReady: function (integration) {
                            checkoutCreditCard = integration;
                    }
                });
            }

            function initialisePaypalIntegration(){
                braintree.setup("' . $clientTokenPaypal . '", "custom", {
                        paypal: {
                            container: "paypal-container",
                            singleUse: true, // Required
                            amount: jQuery("#paypal-payment > .payment_amount").val(), // Required
                            currency: "USD", // Required
                            locale: "en_us" // TODO : UK locale
                        },
                        onPaymentMethodReceived: function (obj) {
                            jQuery("body").prepend("<div id=\"cover\"></div>");
                            jQuery("html, body").animate({
                                scrollTop: 0
                            }, 600);

                            jQuery(".firstName-i").val(jQuery("#firstName_details").val());
                            jQuery(".lastName-i").val(jQuery("#lastName_details").val());
                            jQuery(".billing_country-i").val(jQuery("#billing_country").val());
                            jQuery(".email-i").val(jQuery("#email_details").val());
                            jQuery(".payment_method_nonce").val(obj.nonce);
                            jQuery("#paypal-payment").submit();

                        },
                        onReady: function (integration) {
                            checkoutPaypal = integration;
                        }
                });
            }

            function updatePersonalDetails() {
                jQuery(".firstName-i").val(jQuery("#firstName_details").val());
                jQuery(".lastName-i").val(jQuery("#lastName_details").val());
                jQuery(".email-i").val(jQuery("#email_details").val());
            }

            function addVat() {
                var country = jQuery("#billing_country").val();
                jQuery(".billing_country-i").val(country);
                if(checkoutPaypal != null)
                {
                    checkoutPaypal.teardown(function () {
                      checkoutPaypal = null;
                    });
                }
                if (country.match("at|be|bg|hr|cy|cz|dk|ee|fi|fr|de|gr|hu|ie|it|lv|lt|lu|mt|nl|pl|pt|sk|si|es|se|gb")) {
                    jQuery("#vat_notice").show();
                    jQuery("#vat_amount").show();
                    jQuery(".payment_amount").val(' . (intval($a['payment_amount']) + (intval($a['payment_amount']) * 0.2)) . ');
                    initialisePaypalIntegration();
                } else {
                    jQuery("#vat_notice").hide();
                    jQuery("#vat_amount").hide();
                    jQuery(".payment_amount").val(' . (intval($a['payment_amount'])) . ');
                    initialisePaypalIntegration();
                }
            }

        </script>';

        $output .= '<div class="um-form" class="payment-form">
            <div id="personal-info">
                <div><h2 class="payment-title">Payment amount: $' . $a['payment_amount'] . ' <span id="vat_amount" style="display:none">+$' . (intval($a['payment_amount']) * 0.2) . ' VAT</span>(USD)</h2></div>
                <div id="form-errors">
                    ';
        if (isset($_REQUEST['errors'])) {
            $errors = $_REQUEST['errors'];
            if (count($errors->deepAll()) == 0 || (count($errors->deepAll()) == 1 && $errors->deepAll()[0]->attribute == "paymentMethodNonce")) {
                $output .= '<p class="error-title">Sorry, something went wrong when processing your payment - either your Card expiry date or CSV was incorrect. Please try again</p>';
            } else {
                $output .= '<p class="error-title">Sorry, something went wrong when processing your payment. Please fix the following problems:</p>';
                $output .= '<ul>';
                foreach ($errors->deepAll() AS $error) {
                    if ($error->attribute != "paymentMethodNonce") {
                        $output .= "<li class='payment-error'>$error->message</li>";
                    } else {
                        if (count($errors->deepAll()) == 1) {
                            $output .= "<li class='payment-error'>The server can't process your payment. Try again and if the problem persist, write us: support@life-framer.com. We’ll respond within 24 hours.</li>";
                        }
                    }
                }
                $output .= '</ul>';
            }
        }

        $user_fname = $user_lname = $user_email='';
        if(is_user_logged_in()){
            $current_user = wp_get_current_user();
            $user_fname = $current_user->first_name;
            $user_lname = $current_user->last_name;
            $user_email = $current_user->user_email;
        }
       


        $output .= '</div >
                <div id = "card-images" >
                    <div class="comodo-trust">
                        <script language="JavaScript" type="text/javascript">
                            TrustLogo("'.get_bloginfo('url').'/wp-content/uploads/payments/comodo-secure.png", "CL1", "none");
                        </script>
                        <a  href="https://ssl.comodo.com" id="comodoTL">Comodo SSL</a>
                    </div>
                    <img src = "' . content_url("uploads/payments/visa.png") . '" width = "45px;" />
                    <img src = "' . content_url("uploads/payments/mastercard.png") . '" width = "45px;" />
                    <img src = "' . content_url("uploads/payments/paypal.jpg") . '" width = "45px;" />
                </div >
                <div class="um-clear" >
                    <div class="um-field-label" ><label for="firstName_details" > First
                            name </label >
                        <div class="um-clear" ></div >
                    </div >
                    <input name = "firstName_details" id = "firstName_details" onchange = "updatePersonalDetails()"';
        $output .= isset($user_fname) ? 'value="' . $user_fname . '"' : 'value=""';
        $output .= ' size="40" type="text">
                    <div class="um-field-label"><label for="lastName_details">Last name
                        </label>
                        <div class="um-clear"></div>
                    </div>
                    <input name="lastName_details" id="lastName_details" onchange="updatePersonalDetails()"';
        $output .= isset($user_lname) ? 'value="' . $user_lname . '"' : 'value=""';
        $output .= ' size="40" type="text">
                    <div class="um-field-label"><label for="email_details">Email address
                        </label>
                        <div class="um-clear"></div>
                    </div>
                    <input name="email_details" id="email_details" onchange="updatePersonalDetails()"';
        // $output .= isset($_POST['email']) ? 'value="' . $_POST['email'] . '"' : 'value=""';

        $output .= (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""';
        // VAT amount essage based on Payment amount
        switch ($a['payment_amount']) {
            case 20:
                $varvat = 4;
                break;
            case 30:
                $varvat = 6;
                break;
            case 40:
                $varvat = 8;
                break;
            case 60:
                $varvat = 12;
                break;
            case 80:
                $varvat = 16;
                break;
        }

        $output .= ' size="40" type="text" required>' . '
                    <div class="um-field-label"><label for="billing_country">Billing Country
                        </label>
                        <div class="um-clear"></div>
                    </div>
                    <select id="billing_country" name="billing_country" onchange="addVat()">
                        <option value="">Please select one</option>
                        <option value="af">Afghanistan</option>
                        <option value="ax">Åland Islands</option>
                        <option value="al">Albania</option>
                        <option value="dz">Algeria</option>
                        <option value="as">American Samoa</option>
                        <option value="ad">Andorra</option>
                        <option value="ao">Angola</option>
                        <option value="ai">Anguilla</option>
                        <option value="ag">Antigua and Barbuda</option>
                        <option value="ar">Argentina</option>
                        <option value="am">Armenia</option>
                        <option value="aw">Aruba</option>
                        <option value="au">Australia</option>
                        <option value="at">Austria</option>
                        <option value="az">Azerbaijan</option>
                        <option value="bs">Bahamas</option>
                        <option value="bh">Bahrain</option>
                        <option value="bd">Bangladesh</option>
                        <option value="bb">Barbados</option>
                        <option value="by">Belarus</option>
                        <option value="be">Belgium</option>
                        <option value="bz">Belize</option>
                        <option value="bj">Benin</option>
                        <option value="bm">Bermuda</option>
                        <option value="bt">Bhutan</option>
                        <option value="bo">Bolivia</option>
                        <option value="ba">Bosnia and Herzegovina</option>
                        <option value="bw">Botswana</option>
                        <option value="bv">Bouvet Island</option>
                        <option value="br">Brazil</option>
                        <option value="io">British Indian Ocean Territory</option>
                        <option value="bn">Brunei Darussalam</option>
                        <option value="bg">Bulgaria</option>
                        <option value="bf">Burkina Faso</option>
                        <option value="bi">Burundi</option>
                        <option value="kh">Cambodia</option>
                        <option value="cm">Cameroon</option>
                        <option value="ca">Canada</option>
                        <option value="cv">Cape Verde</option>
                        <option value="ky">Cayman Islands</option>
                        <option value="cf">Central African Republic</option>
                        <option value="td">Chad</option>
                        <option value="cl">Chile</option>
                        <option value="cn">China</option>
                        <option value="cx">Christmas Island</option>
                        <option value="co">Colombia</option>
                        <option value="km">Comoros</option>
                        <option value="cg">Congo</option>
                        <option value="cd">Congo, The Democratic Republic of The</option>
                        <option value="ck">Cook Islands</option>
                        <option value="cr">Costa Rica</option>
                        <option value="ci">Cote D\'ivoire</option>
                        <option value="hr">Croatia</option>
                        <option value="cu">Cuba</option>
                        <option value="cy">Cyprus</option>
                        <option value="cz">Czechia</option>
                        <option value="dk">Denmark</option>
                        <option value="dj">Djibouti</option>
                        <option value="dm">Dominica</option>
                        <option value="do">Dominican Republic</option>
                        <option value="ec">Ecuador</option>
                        <option value="eg">Egypt</option>
                        <option value="sv">El Salvador</option>
                        <option value="gq">Equatorial Guinea</option>
                        <option value="er">Eritrea</option>
                        <option value="ee">Estonia</option>
                        <option value="et">Ethiopia</option>
                        <option value="fk">Falkland Islands (Malvinas)</option>
                        <option value="fo">Faroe Islands</option>
                        <option value="fj">Fiji</option>
                        <option value="fi">Finland</option>
                        <option value="fr">France</option>
                        <option value="gf">French Guiana</option>
                        <option value="pf">French Polynesia</option>
                        <option value="tf">French Southern Territories</option>
                        <option value="ga">Gabon</option>
                        <option value="gm">Gambia</option>
                        <option value="ge">Georgia</option>
                        <option value="de">Germany</option>
                        <option value="gh">Ghana</option>
                        <option value="gi">Gibraltar</option>
                        <option value="gr">Greece</option>
                        <option value="gl">Greenland</option>
                        <option value="gd">Grenada</option>
                        <option value="gp">Guadeloupe</option>
                        <option value="gu">Guam</option>
                        <option value="gt">Guatemala</option>
                        <option value="gg">Guernsey</option>
                        <option value="gn">Guinea</option>
                        <option value="gw">Guinea-bissau</option>
                        <option value="gy">Guyana</option>
                        <option value="ht">Haiti</option>
                        <option value="hn">Honduras</option>
                        <option value="hk">Hong Kong</option>
                        <option value="hu">Hungary</option>
                        <option value="is">Iceland</option>
                        <option value="in">India</option>
                        <option value="id">Indonesia</option>
                        <option value="ir">Iran, Islamic Republic of</option>
                        <option value="iq">Iraq</option>
                        <option value="ie">Ireland</option>
                        <option value="im">Isle of Man</option>
                        <option value="il">Israel</option>
                        <option value="it">Italy</option>
                        <option value="jm">Jamaica</option>
                        <option value="jp">Japan</option>
                        <option value="je">Jersey</option>
                        <option value="jo">Jordan</option>
                        <option value="kz">Kazakhstan</option>
                        <option value="ke">Kenya</option>
                        <option value="kr">Korea</option>
                        <option value="kw">Kuwait</option>
                        <option value="kg">Kyrgyzstan</option>
                        <option value="lv">Latvia</option>
                        <option value="lb">Lebanon</option>
                        <option value="ls">Lesotho</option>
                        <option value="lr">Liberia</option>
                        <option value="ly">Libyan Arab Jamahiriya</option>
                        <option value="li">Liechtenstein</option>
                        <option value="lt">Lithuania</option>
                        <option value="lu">Luxembourg</option>
                        <option value="mo">Macao</option>
                        <option value="mk">Macedonia</option>
                        <option value="mg">Madagascar</option>
                        <option value="mw">Malawi</option>
                        <option value="my">Malaysia</option>
                        <option value="mv">Maldives</option>
                        <option value="ml">Mali</option>
                        <option value="mt">Malta</option>
                        <option value="mh">Marshall Islands</option>
                        <option value="mq">Martinique</option>
                        <option value="mr">Mauritania</option>
                        <option value="mu">Mauritius</option>
                        <option value="yt">Mayotte</option>
                        <option value="mx">Mexico</option>
                        <option value="md">Moldova, Republic of</option>
                        <option value="mc">Monaco</option>
                        <option value="mn">Mongolia</option>
                        <option value="me">Montenegro</option>
                        <option value="ma">Morocco</option>
                        <option value="mz">Mozambique</option>
                        <option value="mm">Myanmar</option>
                        <option value="na">Namibia</option>
                        <option value="nr">Nauru</option>
                        <option value="np">Nepal</option>
                        <option value="nl">Netherlands</option>
                        <option value="an">Netherlands Antilles</option>
                        <option value="nc">New Caledonia</option>
                        <option value="nz">New Zealand</option>
                        <option value="ni">Nicaragua</option>
                        <option value="ne">Niger</option>
                        <option value="ng">Nigeria</option>
                        <option value="nf">Norfolk Island</option>
                        <option value="mp">Northern Mariana Islands</option>
                        <option value="no">Norway</option>
                        <option value="om">Oman</option>
                        <option value="pk">Pakistan</option>
                        <option value="pw">Palau</option>
                        <option value="ps">Palestinian Territory, Occupied</option>
                        <option value="pa">Panama</option>
                        <option value="pg">Papua New Guinea</option>
                        <option value="py">Paraguay</option>
                        <option value="pe">Peru</option>
                        <option value="ph">Philippines</option>
                        <option value="pn">Pitcairn</option>
                        <option value="pl">Poland</option>
                        <option value="pt">Portugal</option>
                        <option value="pr">Puerto Rico</option>
                        <option value="qa">Qatar</option>
                        <option value="re">Reunion</option>
                        <option value="ro">Romania</option>
                        <option value="ru">Russian Federation</option>
                        <option value="rw">Rwanda</option>
                        <option value="sh">Saint Helena</option>
                        <option value="kn">Saint Kitts and Nevis</option>
                        <option value="lc">Saint Lucia</option>
                        <option value="pm">Saint Pierre and Miquelon</option>
                        <option value="vc">Saint Vincent and The Grenadines</option>
                        <option value="ws">Samoa</option>
                        <option value="sm">San Marino</option>
                        <option value="st">Sao Tome and Principe</option>
                        <option value="sa">Saudi Arabia</option>
                        <option value="sn">Senegal</option>
                        <option value="rs">Serbia</option>
                        <option value="sc">Seychelles</option>
                        <option value="sl">Sierra Leone</option>
                        <option value="sg">Singapore</option>
                        <option value="sk">Slovakia</option>
                        <option value="si">Slovenia</option>
                        <option value="sb">Solomon Islands</option>
                        <option value="so">Somalia</option>
                        <option value="za">South Africa</option>
                        <option value="es">Spain</option>
                        <option value="lk">Sri Lanka</option>
                        <option value="sd">Sudan</option>
                        <option value="sr">Suriname</option>
                        <option value="sj">Svalbard and Jan Mayen</option>
                        <option value="sz">Swaziland</option>
                        <option value="se">Sweden</option>
                        <option value="ch">Switzerland</option>
                        <option value="sy">Syrian Arab Republic</option>
                        <option value="tw">Taiwan, Province of China</option>
                        <option value="tj">Tajikistan</option>
                        <option value="tz">Tanzania, United Republic of</option>
                        <option value="th">Thailand</option>
                        <option value="tl">Timor-leste</option>
                        <option value="tg">Togo</option>
                        <option value="tk">Tokelau</option>
                        <option value="to">Tonga</option>
                        <option value="tt">Trinidad and Tobago</option>
                        <option value="tn">Tunisia</option>
                        <option value="tr">Turkey</option>
                        <option value="tm">Turkmenistan</option>
                        <option value="tc">Turks and Caicos Islands</option>
                        <option value="tv">Tuvalu</option>
                        <option value="ug">Uganda</option>
                        <option value="ua">Ukraine</option>
                        <option value="ae">United Arab Emirates</option>
                        <option value="gb">United Kingdom</option>
                        <option value="us">United States</option>
                        <option value="uy">Uruguay</option>
                        <option value="uz">Uzbekistan</option>
                        <option value="vu">Vanuatu</option>
                        <option value="ve">Venezuela</option>
                        <option value="vn">Viet Nam</option>
                        <option value="vg">Virgin Islands, British</option>
                        <option value="vi">Virgin Islands, U.S.</option>
                        <option value="wf">Wallis and Futuna</option>
                        <option value="eh">Western Sahara</option>
                        <option value="ye">Yemen</option>
                        <option value="zm">Zambia</option>
                        <option value="zw">Zimbabwe</option>
                    </select>
                    <div id="vat_notice" style="display: none" class="um-field-label">
                    <p>We have to charge VAT to photographers based in the EU according to current tax legislations. Payment amount: $' . $a['payment_amount'] . ' +$' . $varvat . ' VAT(USD)</p>
                    <div class="um-clear"></div>
                    </div>
                    <div class="card-input">
                    <div class="um-field-label"><label for="card_number">Card number
                        </label>
                        <div class="um-clear"></div>
                    </div>
                    <div class="warning">
                        <a id="encrypted-warning-link">
                            <img src="' . content_url("uploads/payments/lock.png") . '" width="20px" id="lock-icon">
                        </a>
                        <p id="encrypted-warning">
                            Your credit card information is encrypted via our secure payment system</p>
                        <input name="card_number" id="card_number" type="text" maxlength="16">
                    </div>
                    <div id="expiry-security-row">
                        <div id="expiration-date">
                            <div class="um-field-label">
                                <label for="expiration_date_month">Expires on </label>
                            </div>
                            <select id="expiration_date_month">
                                <option value="--">MM</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            <select id="expiration_date_year">
                                <option value="--">YYYY</option>
                                <option value="18">2018</option>
                                <option value="19">2019</option>
                                <option value="20">2020</option>
                                <option value="21">2021</option>
                                <option value="22">2022</option>
                                <option value="23">2023</option>
                                <option value="24">2024</option>
                                <option value="25">2025</option>
                                <option value="26">2026</option>
                                <option value="27">2027</option>
                                <option value="28">2028</option>
                            </select>
                        </div>
                        <div id="security-code">
                            <div class="um-field-label">
                                <label for="cvv">Security code </label>
                            </div>
                            <div class="warning">
                                <a id="cvv-warning-link">
                                    <img src="' . content_url("uploads/payments/question.png") . '" width="15px"
                                         id="lock-icon">
                                </a>
                            </div>
                        </div>
                        <input id="cvv" type="text" maxlength="4">
                        <img id="cvv-warning" src="' . content_url("uploads/payments/cvv.png") . '">
                    </div>
                    <div class="um-field-label"><label for="postal_code">Postcode / Zip
                            Code </label>
                        <div class="um-clear"></div>
                    </div>
                    <input type="text" id="postal_code">
                    <div class="um-field-label"><label for="cardholder_name">Cardholder
                            name </label>
                        <div class="um-clear"></div>
                    </div>
                    <input type="text" id="cardholder_name">
                    <div class="um-clear"></div>
                    <div class="um-center"><input type="submit" id="card_payment_button" class="um-button"
                                                  value="Pay and continue"/></div>
                    <div class="um-center" id="back_to_paypal">
                         <a href="#">or pay with Paypal</a>
                    </div>
                    </div>
                    <div class="um-center hide_when_pay_by_card"><input type="submit" id="pay_by_card" class="um-button"
                                                  value="Pay by card"/>
                    </div>
                    <div class="um-center hide_when_pay_by_card" id="or-section"><p id="or_phrase">OR</p>
                    </div>
                    <div class="um-center hide_when_pay_by_card">
                    <div id="call_paypal">
                        <img id="call_paypal_btn" src="' . content_url("uploads/payments/pay_with_paypal.png") . '" />
                    </div>
                    </div>
                    <div class="um-center hide_when_pay_by_card" id="paypal-section" style="display:none; visibility:hidden">
                        <div id="paypal-div">
                            <form id="paypal-payment" action="' . esc_attr($_SERVER['REQUEST_URI']) . '"
                                  method="post">
                                <input type="hidden" name="transactionId" id="transactionId"  value="' . $transaction_id . '">
                                <input type="hidden" name="firstName" id="firstName" class="firstName-i" value="">
                                <input type="hidden" name="lastName" id="lastName" class="lastName-i" value="">
                                <input type="hidden" name="country" id="country" class="billing_country-i" value="">
                                <input type="hidden" name="email" id="email" class="email-i" value="">
                                <input type="hidden" name="make_payment" value="paypal">
                                <input type="hidden" name="payment_amount" class="payment_amount" value="' . $a['payment_amount'] . '">
                                <input type="hidden" name="payment_description" class="payment_description" value="' . $a['payment_description'] . '">
                                <input type="hidden" class="payment_method_nonce" name="payment_method_nonce"
                                       id="payment_method_nonce" value="">
                                <div id="paypal-container"></div>
                            </form>
                        </div>
                    </div>
                    <div id="card-section">
                        <form id="checkout" action="' . esc_attr($_SERVER['REQUEST_URI']) . '" method="post">
                            <input type="hidden" name="transactionId" id="transactionId"  value="' . $transaction_id . '">
                            <input type="hidden" name="firstName" id="firstName" class="firstName-i" value="">
                            <input type="hidden" name="lastName" id="lastName" class="lastName-i" value="">
                            <input type="hidden" name="country" id="country" class="billing_country-i" value="">
                            <input type="hidden" name="email" class="email-i" value="">
                            <input type="hidden" name="make_payment" value="card">
                            <input type="hidden" name="payment_amount" class="payment_amount" value="' . $a['payment_amount'] . '">
                            <input type="hidden" name="payment_description" class="payment_description" value="' . $a['payment_description'] . '">
                            <input data-braintree-name="number" id="braintree_card_number">
                            <input data-braintree-name="cvv" id="braintree_cvv">
                            <input data-braintree-name="expiration_date" id="braintree_expiration_date">
                            <input data-braintree-name="postal_code" id="braintree_postal_code">
                            <input data-braintree-name="cardholder_name" id="braintree_cardholder_name">
                            <input type="submit" id="payment_card_submit" value="Pay">
                        </form>
                    </div>
                </div>
            </div>
            <br/>
            <br/>';

    }
    return $output;

}

add_action('admin_action_make_payment', 'make_braintree_payment');




function make_braintree_payment()
{
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
    $email_address = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    $transaction = $wpdb->get_row($wpdb->prepare("SELECT * from $transaction_table WHERE transaction_id='%s' ORDER BY date DESC LIMIT 1", $transaction_id));

    $euCountries = array("at", "be", "bg", "hr", "cy", "cz", "dk", "ee", "fi", "fr", "de", "gr", "hu", "ie", "it", "lv", "lt", "lu", "mt", "nl", "pl", "pt", "sk", "si", "es", "se", "gb");

    $transactionAmount = $transaction->amount;
    $vat = 0;

    if (in_array($_POST['country'], $euCountries)) {
        if (!isset($_POST['payment_amount']) || empty($_POST['payment_amount'])) {
            $my_errors->add_error(new LFError('payment_amount', 'Unable to read the payment amount. Please contact LifeFramer for support.'));
        }
        $vat = intval($transactionAmount) * 0.2;
        $transactionAmount = intval($transactionAmount) + $vat;
    }

    if ($_POST['payment_amount'] != $transactionAmount) {
        $my_errors->add_error(new LFError('payment_amount', 'Something seems to not be working as we expected. Please contact LifeFramer for support.'));
    }

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
            'options' => ['submitForSettlement' => true]
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
                    global $ultimatemember;
                    $current_user = wp_get_current_user();
                    um_fetch_user($current_user->ID);
                    // Change user role
                    $ultimatemember->user->set_role('member');
                }
                else{
                    global $ultimatemember;
                    $current_user = wp_get_current_user();
                    um_fetch_user($current_user->ID);
                    // Change user role
                    $ultimatemember->user->set_role('entrant');
                }
            }
            else{

                /* Check if we have user already registered */
                $payment_desc = trim($_POST['payment_description']);
                $user_exists = email_exists( $email );
                if ( $user_exists ) {
                    global $ultimatemember;
                    if( ($payment_desc =='Life-Framer - 1 entry')
                     || ($payment_desc =='Life-Framer - 3 entries')
                     || ($payment_desc =='Life-Framer - 6 entries')
                    ){
                        um_fetch_user($user_exists);
                        $ultimatemember->user->set_role('entrant');
                    }
                    else{
                        if($payment_desc =='Life-Framer - membership'){
                            um_fetch_user($user_exists);
                            $ultimatemember->user->set_role('member');
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
                }
                else{
                    $registration_link = '<a href="'.$site_url.'/register-entrant" style="color: #000;text-decoration: underline;" target="_blank">'.$site_url.'/register-entrant</a>';
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
                    "{year}"
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
                    date('Y')
                );

                $message = str_replace($find, $replace, $message);


                // $message =
                //     "Hello " . $customer_name .
                //     "\n\nThank you for joining the Life Framer award, we have received your payment. It may take a few moments for this transaction to appear in your account. "
                //     . "\n\n\nPayment ID: " . $result->transaction->id . " / " . $transaction_id
                //     . "\nDescription: " . $transaction->description
                //     . "\nTotal amount excluding VAT: " . $transaction->amount . " US$"
                //     . "\nRate of VAT charged per item: " . (($transaction->amount == $transactionAmount) ? "0%" : "20%")
                //     . "\nTotal amount of VAT: " . $vat ." US$"
                //     . "\nTotal amount including VAT: " . $transactionAmount ." US$"
                //     . "\nBilled to: " . $customer_name
                //     . "\nBilling Country: " . countryCodeToName($_POST['country'])
                //     . "\n\n\nSupport "
                //     . "\n-------"
                //     . "\nIf you have any questions regarding your payment, the submission process or your “my LF” account, please don’t hesitate to get in touch us with us at the following address: support@life-framer.com. We will get back in touch with you within 24 hours. Alternatively you might want to check out our FAQs and rules."
                //     . "\n\nCopyright 2013-2017 Life Framer (G & W media LLP). All rights reserved.";


                $to = $email;
                wp_mail($to, $subject, $message, $mail_headers);

                endTransaction($wpdb, $transaction_table, $transaction_id, $result->transaction->id);


            }


            wp_redirect($transaction->after_payment . '?transaction=' . $transaction_id.'&email='.$email_address);
            exit();
        }
    }

    $formated_errors = "";

    foreach ($result->errors->deepAll() AS $error) {
        $my_errors->add_error(new LFError($error->attribute, $error->message));
        $formated_errors .= $error->message . "\n";
    }

    abortTransaction($wpdb, $transaction_table, $transaction_id, $formated_errors);

    $_REQUEST["errors"] = $my_errors;

}


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