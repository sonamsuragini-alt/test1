<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: settings_registration.php
| Author: PHP-Fusion Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once __DIR__.'/../maincore.php';
pageAccess('S4');
require_once THEMES.'templates/admin_header.php';
$locale = fusion_get_locale('', LOCALE.LOCALESET."admin/settings.php");
\PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => ADMIN.'settings_registration.php'.fusion_get_aidlink(), 'title' => $locale['register_settings']]);

$settings = fusion_get_settings();

$is_multilang = count(fusion_get_enabled_languages()) > 1 ? TRUE : FALSE;

if (isset($_POST['savesettings'])) {
    $inputData = [
        'login_method'        => form_sanitizer($_POST['login_method'], '0', 'login_method'),
        'license_agreement'   => form_sanitizer($_POST['license_agreement'], '', 'license_agreement', $is_multilang),
        'enable_registration' => form_sanitizer($_POST['enable_registration'], '0', 'enable_registration'),
        'email_verification'  => form_sanitizer($_POST['email_verification'], '0', 'email_verification'),
        'admin_activation'    => form_sanitizer($_POST['admin_activation'], '0', 'admin_activation'),
        'display_validation'  => form_sanitizer($_POST['display_validation'], '0', 'display_validation'),
        'enable_terms'        => form_sanitizer($_POST['enable_terms'], '0', 'enable_terms'),
        'license_lastupdate'  => ($_POST['license_agreement'] != fusion_get_settings('license_agreement') ? time() : fusion_get_settings('license_lastupdate'))
    ];

    if (\defender::safe()) {
        foreach ($inputData as $settings_name => $settings_value) {
            dbquery("UPDATE ".DB_SETTINGS." SET settings_value=:settings_value WHERE settings_name=:settings_name", [
                ':settings_value' => $settings_value,
                ':settings_name'  => $settings_name
            ]);
        }

        addNotice('success', $locale['900']);
        redirect(FUSION_REQUEST);
    }
}

opentable($locale['register_settings']);
echo openform('settingsform', 'post', FUSION_REQUEST);
$opts = ['1' => $locale['yes'], '0' => $locale['no']];
echo "<div class='well'>".$locale['register_description']."</div>\n";
echo "<div class='row'>\n";
echo "<div class='col-xs-12 col-sm-8'>\n";
openside('');
echo form_select('enable_terms', $locale['558'], $settings['enable_terms'], ['options' => $opts]);
if ($is_multilang == TRUE) {
    echo \PHPFusion\QuantumFields::quantum_multilocale_fields('license_agreement', $locale['559'], $settings['license_agreement'], [
        'form_name' => 'settingsform',
        'input_id'  => 'enable_license_agreement',
        'autosize'  => !fusion_get_settings('tinymce_enabled') ? FALSE : TRUE,
        'type'      => (fusion_get_settings('tinymce_enabled') ? 'tinymce' : 'html'),
        'function'  => 'form_textarea'
    ]);
} else {
    echo form_textarea('license_agreement', $locale['559'], $settings['license_agreement'], [
        'form_name' => 'settingsform',
        'autosize'  => !fusion_get_settings('tinymce_enabled') ? FALSE : TRUE,
        'html'      => !fusion_get_settings('tinymce_enabled') ? TRUE : FALSE
    ]);
}
closeside();
echo "</div><div class='col-xs-12 col-sm-4'>\n";
openside('');
echo form_select('enable_registration', $locale['551'], $settings['enable_registration'], ['options' => $opts]);
echo form_select('email_verification', $locale['552'], $settings['email_verification'], ['options' => $opts]);
echo form_select('admin_activation', $locale['557'], $settings['admin_activation'], ['options' => $opts]);
echo form_select('display_validation', $locale['553'], $settings['display_validation'], ['options' => $opts]);
$opts = ['0' => $locale['global_101'], '1' => $locale['699e'], '2' => $locale['699b']];
echo form_select('login_method', $locale['699'], $settings['login_method'], ['options' => $opts]);

closeside();
echo "</div>\n</div>\n";
echo form_button('savesettings', $locale['750'], $locale['750'], ['class' => 'btn-success']);
echo closeform();
closetable();
require_once THEMES.'templates/footer.php';
