<?php
// बाल थीम विज़ार्ड स्क्रिप्ट की स्थापना रद्द करें
// प्लगइन हटा दिए जाने पर सभी डेटाबेस विकल्पों को हटा देता है

if ( !function_exists('add_action') ){
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}

if ( !current_user_can('manage_options') ){
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}

// अगर वर्डप्रेस से अनइंस्टॉल नहीं किया जाता है तो बाहर निकलें
if (!defined('WP_UNINSTALL_PLUGIN')) exit();

//  यह प्लगइन डेटाबेस में कोई विकल्प नहीं जोड़ता है
// यदि ऐसा होता है, तो उन्हें इस फ़ाइल कोड द्वारा हटा दिया जाएगा

?>