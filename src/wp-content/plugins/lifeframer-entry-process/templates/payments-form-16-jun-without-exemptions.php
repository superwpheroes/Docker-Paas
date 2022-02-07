<?php
global $wpdb;
global $transaction_table;

nocache_headers();



//return if no payment amount specified in shortcode
if( !isset($a['payment_amount']) || $a['payment_amount'] === 'NO_PAYMENT' || empty($a['payment_amount']) ){
    echo 'ERROR LF-2391: Please specify payment_amount on the shortcode';
    return;
}




//add transaction to db
$transaction_id = "LFRAM-" . rand(1000000, 9999999);
if(!$wpdb->insert(
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
)){
    echo  'ERROR LF-2393: Sorry, we are experiencing problems and our payment system is not available at the moment. Try again refreshing this page in a few seconds or please contact our support team support@life-framer.com. We’ll respond within 24 hours.';
    return;
}




//initialize braintree tokens - used for brainstree sdk
$clientToken = Braintree_ClientToken::generate([
    //"customerId" => $aCustomerId->ID
]);

$clientTokenPaypal = Braintree_ClientToken::generate([
    //"customerId" => $aCustomerId->ID
]);



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
$legal = get_page_by_title('legal');
$legal = get_permalink($legal);


$user_fname = $user_lname = $user_email='';
if(is_user_logged_in()){
    $current_user = wp_get_current_user();
    $user_fname = $current_user->first_name;
    $user_lname = $current_user->last_name;
    $user_email = $current_user->user_email;
}
?>
<div class="um-form" class="payment-form">
    <div id="personal-info">
        <div><h2 class="payment-title">Payment amount: $<?php echo $a['payment_amount']; ?>  <span id="vat_amount" style="display:none">+$ <?php echo (intval($a['payment_amount']) * 0.2); ?> VAT</span>(USD)</h2></div>
        <div id="form-errors">
            <?php if(isset($_REQUEST['errors'])) : ?>
                <?php $errors = $_REQUEST['errors']; ?>
                <?php if (count($errors->deepAll()) == 0 || (count($errors->deepAll()) == 1 && $errors->deepAll()[0]->attribute == "paymentMethodNonce")) : ?>
                    <p class="error-title">Sorry, something went wrong when processing your payment - either your Card expiry date or CSV was incorrect. Please try again</p>
                <?php else: ?>
                    <p class="error-title">Sorry, something went wrong when processing your payment. Please fix the following problems:</p>
                    <ul>
                        <?php foreach ($errors->deepAll() AS $error): ?>
                            <?php if($error->attribute != "paymentMethodNonce"): ?>
                                <?php if($error->attribute != "paymentMethodNonce"): ?>
                                    <li class='payment-error'><?php echo $error->message; ?></li>
                                <?php else: ?>
                                    <?php if (count($errors->deepAll()) == 1): ?>
                                        <li class='payment-error'>The server can't process your payment. Try again and if the problem persist, write us: support@life-framer.com. We’ll respond within 24 hours.</li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div id="modal" class="hidden">
            <div class="bt-mask"></div>
            <div class="bt-modal-frame">
                <div class="bt-modal-header">
                    <div class="header-text">Authentication</div>
                </div>
                <div class="bt-modal-body"></div>
                <div class="bt-modal-footer"><a id="text-close" href="#">Cancel</a></div>
            </div>
        </div>
        <div id = "card-images" >
            <img src = "<?php echo content_url("uploads/payments/visa.png"); ?>" width = "45px;" />
            <img src = "<?php echo content_url("uploads/payments/vbv.png"); ?>" width = "45px;" />
            <img src = "<?php echo content_url("uploads/payments/mastercard.png"); ?>" width = "45px;" />
            <img src = "<?php echo content_url("uploads/payments/msc.png"); ?>" width = "45px;" />
            <img src = "<?php echo content_url("uploads/payments/paypal.png") ;?>" width = "45px;" />
        </div>
        <div class="um-clear" >

            <!--first name-->
            <div class="um-field-label">
                <label for="firstName_details">First name</label>
                <div class="um-clear"></div>
            </div>
            <input name="firstName_details" id="firstName_details" onchange="updatePersonalDetails()" <?php echo (isset($user_fname)) ? 'value="' . $user_fname . '"' : 'value=""'; ?> size="40" type="text">

            <!--last name-->
            <div class="um-field-label">
                <label for="lastName_details">Last name</label>
                <div class="um-clear"></div>
            </div>
            <input name="lastName_details" id="lastName_details" onchange="updatePersonalDetails()" <?php echo (isset($user_lname)) ? 'value="' . $user_lname . '"' : 'value=""'; ?>  size="40" type="text">

            <!--email address-->
            <div class="um-field-label">
                <label for="email_details">Email address</label>
                <div class="um-clear"></div>
            </div>
            <input name="email_details" id="email_details" onchange="updatePersonalDetails()" <?php echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?> size="40" type="text" required>

            <!--country-->
            <div class="um-field-label">
                <label for="billing_country">Billing Country</label>
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
                <option value="ci">Cote D'ivoire</option>
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
                <p>We have to charge VAT to photographers based in the EU according to current tax legislations. Payment amount: $ <?php echo $a['payment_amount'];  ?> + $ <?php echo $varvat; ?>  VAT(USD)</p>
                <div class="um-clear"></div>
            </div>


            <!--card input fields-->
            <div class="card-input">


                <!--card number-->
                <div class="um-field-label">
                    <label for="card_number">Card number </label>
                    <div class="um-clear"></div>
                </div>

                <div class="warning">
                    <a id="encrypted-warning-link">
                        <img src="<?php echo content_url("uploads/payments/lock.png"); ?>" width="20px" id="lock-icon">
                    </a>

                    <p id="encrypted-warning">Your credit card information is encrypted via our secure payment system</p>
                    <div id="hf-number"></div>

                    <div class="input-group nonce-group hidden">
                        <span class="input-group-addon">nonce</span>
                        <input readonly name="nonce" class="form-control">
                    </div>
                </div>
                <!--card number-->

                <div id="expiry-security-row">
                    <div id="expiration-date">
                        <div class="um-field-label">
                            <label for="expiration_date_month">Expires on </label>
                        </div>
                        <div id="hf-date"></div>
                    </div>
                    <div id="security-code">
                        <div class="um-field-label">
                            <label for="cvv">Security code </label>
                        </div>
                        <div class="warning">
                            <a id="cvv-warning-link">
                                <img src="<?php echo content_url("uploads/payments/question.png"); ?> " width="15px" id="lock-icon">
                            </a>
                        </div>
                        <div id="hf-cvv"></div>
                        <img id="cvv-warning" src="<?php echo content_url("uploads/payments/cvv.png"); ?>" style="display:none;">
                    </div>
                </div>

                <div class="um-clear"></div>

                <!--<div class="um-field-label"><label for="postal_code">Postcode / Zip
                        Code </label>
                    <div class="um-clear"></div>
                </div>
                <input type="text" id="postal_code">-->

                <div class="um-field-label">
                    <label for="cardholder_name">Cardholder name </label>
                    <div class="um-clear"></div>
                </div>
                <input type="text" id="cardholder_name">
                <div class="um-clear"></div>

                <!--phone number-->
                <!--            <div class="um-field-label">-->
                <!--                <label for="email_details">Phone number</label>-->
                <!--                <div class="um-clear"></div>-->
                <!--            </div>-->
                <!--            <input name="billing_phone" id="billing_phone" onchange="updatePersonalDetails()" --><?php ////echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?><!-- type="tel" required>-->

                <!--address-->
                <div class="um-field-label">
                    <label for="address">Address</label>
                    <div class="um-clear"></div>
                </div>
                <input name="address" id="address" onchange="updatePersonalDetails()" <?php //echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?> type="text" required>

                <!--address 2-->
                <!--            <div class="um-field-label">-->
                <!--                <label for="address2">Address Line 2 (Optional)</label>-->
                <!--                <div class="um-clear"></div>-->
                <!--            </div>-->
                <!--            <input name="address2" id="address2" onchange="updatePersonalDetails()" --><?php ////echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?><!-- type="text" required>-->

                <!--locality-->
                <div class="um-field-label">
                    <label for="locality">City </label>
                    <div class="um-clear"></div>
                </div>
                <input name="locality" id="locality" onchange="updatePersonalDetails()" <?php //echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?> type="text" required>

                <!--region-->
                <!--            <div class="um-field-label">-->
                <!--                <label for="region">State / Province / Region </label>-->
                <!--                <div class="um-clear"></div>-->
                <!--            </div>-->
                <!--            <input name="region" id="region" onchange="updatePersonalDetails()" --><?php ////echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?><!-- type="text" required>-->

                <!--postal code-->
                <div class="um-field-label">
                    <label for="postal_code">Post Code / Zip Code</label>
                    <div class="um-clear"></div>
                </div>
                <input name="postal_code" id="postal_code" onchange="updatePersonalDetails()" <?php //echo (isset($user_email) && (trim($user_email)!='')) ? 'value="' . $user_email . '" disabled="disabled" class="payment_email_filled" ' : 'value=""'; ?> type="text" required>


                <div class="um-center">
                    <input type="submit" id="card_payment_button" class="um-button" value="Pay and continue"/>
                </div>

                <div class="um-center" id="back_to_paypal">
                    <a href="#">or pay with Paypal</a>
                </div>
            </div>
            <!--end card input fields-->

            <div>
                <label for="terms" style="margin-top: 15px;">
                    <input id="terms" style="float:left; margin-left:15px;" type="checkbox" name="terms" required>
                    <span style="display: block; margin-left: 46px;">By paying you agree to Life Framer's
                    <a href="<?php echo $legal; ?> #terms" target="_blank" style="text-decoration: underline;">Terms &amp; Conditions</a>,
                    <a href="<?php echo $legal; ?> #privacy" target="_blank" style="text-decoration: underline;">Privacy Policy</a> and award
                    <a href="<?php echo $legal; ?> #rules" target="_blank" style="text-decoration: underline;">Rules</a>
                    </span>
                </label>
                <label style="margin-top: 5px;"><input id="newsletter" style="float:left; margin-left:15px;" type="checkbox" name="newsletter"><span style="display: block; margin-left: 46px;">Receive our newsletter to be notified of the award winners and exhibitions news</span></label>
            </div>

            <div class="um-center hide_when_pay_by_card">
                <input type="submit" id="pay_by_card" class="um-button" value="Pay by card"/>
            </div>

            <div class="um-center hide_when_pay_by_card" id="or-section">
                <p id="or_phrase">OR</p>
            </div>

            <div class="um-center hide_when_pay_by_card">
                <div id="call_paypal">
                    <img id="call_paypal_btn" src="<?php echo content_url("uploads/payments/pay_with_paypal.png"); ?> " />
                </div>
            </div>

            <div id="paypal-button"></div>

            <!--paypal hidden form-->
            <div class="um-center hide_when_pay_by_card" id="paypal-section" style="display:none; visibility:hidden">
                <div id="paypal-div">
                    <form id="paypal-payment" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post">
                        <input type="hidden" name="transactionId" id="transactionId"  value="<?php echo $transaction_id; ?>">
                        <input type="hidden" name="firstName" id="firstName" class="firstName-i" value="">
                        <input type="hidden" name="lastName" id="lastName" class="lastName-i" value="">
                        <input type="hidden" name="country" id="country" class="billing_country-i" value="">
                        <input type="hidden" name="email" id="email" class="email-i" value="">
                        <input type="hidden" name="billing-address-1" class="billing-address-1" value="">
                        <input type="hidden" name="billing-locality" class="billing-locality" value="">
                        <input type="hidden" name="billing-postal-code" class="billing-postal-code" value="">
                        <input type="hidden" name="make_payment" value="paypal">
                        <input type="hidden" name="payment_amount" class="payment_amount" value="<?php echo $a['payment_amount']; ?>">
                        <input type="hidden" name="payment_description" class="payment_description" value="<?php  echo $a['payment_description']; ?> ">
                        <input type="hidden" class="payment_method_nonce" name="payment_method_nonce" id="payment_method_nonce" value="">
                        <input type="hidden" name="newsletter" class="newsletter-i" value="yes">
                        <div id="paypal-container"></div>
                    </form>
                </div>
            </div>

            <!--card hidden form-->
            <div id="card-section">
                <form id="checkout" action="<?php  echo esc_attr($_SERVER['REQUEST_URI']); ?> " method="post">
                    <input type="hidden" name="transactionId" id="transactionId"  value="<?php echo $transaction_id; ?>">
                    <input type="hidden" name="firstName" id="firstName" class="firstName-i" value="">
                    <input type="hidden" name="lastName" id="lastName" class="lastName-i" value="">
                    <input type="hidden" name="country" id="country" class="billing_country-i" value="">
                    <input type="hidden" name="email" class="email-i" value="">
                    <input type="hidden" name="billing-address-1" class="billing-address-1" value="">
                    <input type="hidden" name="billing-locality" class="billing-locality" value="">
                    <input type="hidden" name="billing-postal-code" class="billing-postal-code" value="">
                    <input type="hidden" name="make_payment" value="card">
                    <input type="hidden" name="payment_amount" class="payment_amount" id="payment_amount" value="<?php echo $a['payment_amount']; ?>">
                    <input type="hidden" name="payment_description" class="payment_description" value="<?php  echo $a['payment_description']; ?>">
                    <input data-braintree-name="number" id="braintree_card_number">
                    <input data-braintree-name="cvv" id="braintree_cvv">
                    <input data-braintree-name="expiration_date" id="braintree_expiration_date">
                    <!--<input data-braintree-name="postal_code" id="braintree_postal_code">-->
                    <input data-braintree-name="cardholder_name" id="braintree_cardholder_name">
                    <input type="hidden" name="newsletter" class="newsletter-i" value="yes">
                    <input type="hidden" class="card_payment_method_nonce" name="payment_method_nonce" id="card_payment_method_nonce" value="">
                    <input type="submit" id="payment_card_submit" value="Pay">
                </form>
            </div>
        </div>
    </div>
    <br/>
    <br/>
</div>











<script src="https://js.braintreegateway.com/js/braintree-2.21.0.min.js" xmlns="http://www.w3.org/1999/html"></script>

<script src="https://js.braintreegateway.com/web/3.58.0/js/client.min.js"></script>

<script src="https://js.braintreegateway.com/web/3.58.0/js/hosted-fields.min.js"></script>

<script src="https://js.braintreegateway.com/web/3.58.0/js/three-d-secure.min.js"></script>

<script src="https://www.paypalobjects.com/api/checkout.js" data-version-4 log-level="warn"></script>

<script src="https://js.braintreegateway.com/web/3.58.0/js/paypal-checkout.min.js"></script>


<script type="text/javascript">

    jQuery(window).unload(function() {
        jQuery("#cover").remove();
    });

    jQuery(document).ready(function () {


        var payBtn = document.getElementById("card_payment_button");
        var nonceGroup = document.querySelector(".nonce-group");
        var nonceInput = document.querySelector(".card_payment_method_nonce");
//    var payGroup = document.querySelector(".pay-group");
        var modal = document.getElementById("modal");
        var bankFrame = document.querySelector(".bt-modal-body");
        var closeFrame = document.getElementById("text-close");
        var amountInput = document.getElementById("payment_amount");
        var clientTokenScript = document.getElementById("client-token");
        var xhr = new XMLHttpRequest();
        var components = {
            client: null,
            threeDSecure: null,
            hostedFields: null,
        };




        onFetchClientToken();




        function onFetchClientToken( ) {
            braintree.client.create({
                authorization: "<?php echo $clientToken; ?>"
            }, onClientCreate);
        }








        function onClientCreate(err, client) {
            if (err) {
                console.log("client error:", err);
                return;
            }

            components.client = client;

            braintree.hostedFields.create({
                client: client,
                styles: {
                    input: {
                        "font-size": "14px",
                        "font-family": "monospace"
                    },
                    '.invalid': {
                        'border-color': 'red'
                    }
                },
                fields: {
                    number: {
                        selector: "#hf-number",
                        placeholder: ""
                    },
                    cvv: {
                        selector: "#hf-cvv",
                        placeholder: "123"
                    },
                    expirationDate: {
                        selector: "#hf-date",
                        placeholder: "MM / YY"
                    }
                }
            }, onComponent("hostedFields"));

            braintree.threeDSecure.create({
                client: client,
                version:2
            }, onComponent("threeDSecure"));
        }






        function onComponent(name) {
            return function(err, component) {
                if (err) {
                    console.log("component error:", err);
                    return;
                }

                components[name] = component;

                if(name == 'hostedFields'){
                    component.on('validityChange', function (event) {
                        var field = event.fields[event.emittedBy];

                        if (field.isValid) {
                            console.log(event.emittedBy, 'is fully valid');
                        } else if (field.isPotentiallyValid) {
                            console.log(event.emittedBy, 'is potentially valid');
                        } else {
                            console.log(event.emittedBy, 'is not valid');
                        }
                    });
                }

                if (components.threeDSecure && components.hostedFields) {
                    setupForm();
                }
            }
        }





        function setupForm() {
            enablePayNow();
        }





        function addFrame(err, iframe) {
            bankFrame.appendChild(iframe);
            modal.classList.remove("hidden");
        }




        function removeFrame() {
            var iframe = bankFrame.querySelector("iframe");
            modal.classList.add("hidden");
            iframe.parentNode.removeChild(iframe);
        }





        function enablePayNow() {
            payBtn.value = "Pay Now";
            payBtn.removeAttribute("disabled");
        }




        closeFrame.addEventListener("click", function () {
            components.threeDSecure.cancelVerifyCard(removeFrame());
            enablePayNow();
        });





        payBtn.addEventListener("click", function(event) {

            errors = validate_form(true);
            if (errors.length != 0) {
                jQuery("html, body").animate({
                    scrollTop: 0
                }, 600);
                jQuery("#card_payment_button").prop("disabled", false);
                jQuery("#form-errors").append("<p class=\"error-title\">Sorry, something went wrong when processing your payment. Please fix the following problems:</p>");
                jQuery("#form-errors").append("<ul>");

                for (i = 0; i < errors.length; i++) {
                    jQuery("#form-errors").append("<li class=\'payment-error\'>" + errors[i].message + "</li>");
                    jQuery("label[for=" + errors[i].field + "]").css("color", "red");
                }
                jQuery("#form-errors").append("</ul>");
                return;
            }

            payBtn.setAttribute("disabled", "disabled");
            payBtn.value = "Processing...";

            components.hostedFields.tokenize(function(err, payload) {
                if (err) {
                    console.log("tokenization error:", err);
                    enablePayNow();
                    return;
                } else {
                    jQuery("#cover").remove();
                    console.log("tokenization success:", payload);
                    console.log("tokenization success:", jQuery("#billing_country").val());
                }

                components.threeDSecure.verifyCard({
                    amount:<?php echo $a['payment_amount']; ?>,
                    nonce: payload.nonce,
                    addFrame: addFrame,
                    removeFrame: removeFrame,
                    bin: payload.details.bin, // Example: hostedFieldsTokenizationPayload.details.bin
                    billingAddress: {
                        givenName: jQuery("#firstName_details").val(),
                        surname: jQuery("#lastName_details").val(),
                        streetAddress: jQuery("#address").val(),
                        locality: jQuery("#locality").val(),
                        postalCode: jQuery("#postal_code").val(),
                        countryCodeAlpha2: jQuery("#billing_country").val()
                    },
                    additionalInformation: {
                        shippingGivenName: jQuery("#firstName_details").val(),
                        shippingSurname: jQuery("#lastName_details").val(),
                        shippingAddress: {
                            streetAddress: jQuery("#address").val(),
                            locality: jQuery("#locality").val(),
                            postalCode: jQuery("#postal_code").val(),
                            countryCodeAlpha2: jQuery("#billing_country").val()
                        },
                        email: jQuery("#email_details").val()
                    },onLookupComplete: function (data, next) {
                        console.log(data);
                        next();
                    }
                }, function(err, verification) {
                    if (err) {

                        console.log("verification error:", err);
                        jQuery('#form-errors').append('<p class="error-title">Sorry, something went wrong when processing your payment. Please fix the following problems:</p>');
                        jQuery('#form-errors').append("<ul><li class='payment-error'>Authentication failed. You have not been charged. Please try again and make sure that you don't have a pop-up blocker installed which is restricting the additional authentication page from opening. Alternatively, you can change the payment method.</li></ul>");

                        jQuery("html, body").animate({
                            scrollTop: 0
                        }, 600);

                        enablePayNow();
                        return;
                    }

                    console.log("verification success:", verification);
                    console.log(JSON.stringify(verification.details));
                    console.log(JSON.stringify(verification.binData));
                    nonceInput.value = verification.nonce;

                    copy_values_and_call_braintree();
                });
            });
        });





        jQuery(".card-input").hide();





        function copy_values_and_call_braintree() {
            updatePersonalDetails();
            jQuery("#braintree_card_number").val(jQuery("#card_number").val());
            var expiration = jQuery("#expiration_date_month").val() + "/" + jQuery("#expiration_date_year").val();
            jQuery("#braintree_expiration_date").val(expiration);
            jQuery("#braintree_cvv").val(jQuery("#cvv").val());
            jQuery("#braintree_cardholder_name").val(jQuery("#cardholder_name").val());
            // jQuery("#braintree_postal_code").val(jQuery("#postal_code").val());
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
            if (!jQuery("#terms").is(":checked")) {
                errors.push({
                    field: "terms",
                    message: "Please agree to our Terms & Conditions, Privacy Policy and Rules"
                });
            }
            if(extended) {
                if (!jQuery("#hf-date").hasClass('braintree-hosted-fields-valid')) {
                    errors.push({
                        field: "expiration_date_month",
                        message: "Please select an expiration date"
                    });
                }
                if (!jQuery("#hf-number").hasClass('braintree-hosted-fields-valid')) {
                    errors.push({
                        field: "card_number",
                        message: "Enter a valid credit or debit card number"
                    });
                }
                if (!jQuery("#hf-cvv").hasClass('braintree-hosted-fields-valid')) {
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
                if (!jQuery("#address").val()) {
                    errors.push({
                        field: "address",
                        message: "Your billing address is required"
                    });
                }
                if (!jQuery("#locality").val()) {
                    errors.push({
                        field: "locality",
                        message: "Your billing City / Town is required"
                    });
                }

                if (!jQuery("#postal_code").val()) {
                    errors.push({
                        field: "postal_code",
                        message: "Your billing Postal Code is required"
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
                    jQuery("label[for=" + errors[i].field + "]").css("color", "red");
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
                scrollTop: (jQuery("#hf-number").offset().top - 230)
            }, 600);
            jQuery(".hide_when_pay_by_card").hide();
        });

        var checkoutPaypal;
        initialisePaypalIntegration();

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









    function initialisePaypalIntegration(){
        braintree.setup(" <?php echo $clientTokenPaypal; ?> ", "custom", {
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
                if(jQuery("#newsletter").is(":checked")) {
                    jQuery(".newsletter-i").val("yes");
                }
                else {
                    jQuery(".newsletter-i").val("no");
                }

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
        jQuery(".billing-phone").val(jQuery("#billing_phone").val());
        jQuery(".billing-address-1").val(jQuery("#address").val());
        jQuery(".billing-address-2").val(jQuery("#address2").val());
        jQuery(".billing-locality").val(jQuery("#locality").val());
        jQuery(".billing-region").val(jQuery("#region").val());
        jQuery(".billing-postal-code").val(jQuery("#postal_code").val());
        if(jQuery("#newsletter").is(":checked")) {
            jQuery(".newsletter-i").val("yes");
        }
        else {
            jQuery(".newsletter-i").val("no");
        }
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

        // jQuery("#vat_notice").hide();
        // jQuery("#vat_amount").hide();
        jQuery(".payment_amount").val('<?php echo (intval($a['payment_amount'])); ?> ');
        initialisePaypalIntegration();


        // if (country.match("at|be|bg|hr|cy|cz|dk|ee|fi|fr|de|gr|hu|ie|it|lv|lt|lu|mt|nl|pl|pt|sk|si|es|se|gb")) {
        //     jQuery("#vat_notice").show();
        //     jQuery("#vat_amount").show();
        //     jQuery(".payment_amount").val(' . (intval($a['payment_amount']) + (intval($a['payment_amount']) * 0.2)) . ');
        //     initialisePaypalIntegration();
        // } else {
        //     jQuery("#vat_notice").hide();
        //     jQuery("#vat_amount").hide();
        //     jQuery(".payment_amount").val(' . (intval($a['payment_amount'])) . ');
        //     initialisePaypalIntegration();
        // }
    }

</script>