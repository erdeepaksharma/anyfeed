<?php

####################################################################################
#  Any Feed PRO for Opencart 2.0.x From HostJars http://opencart.hostjars.com  	   #
####################################################################################

function ppp($var) {
    echo '<xmp style="text-align: left;">';
    print_r($var);
    echo '</xmp><br />';
}

class ControllerFeedAnyFeedPro extends Controller {

    private $error = array();

    public function install() {
        $this->load->model('feed/any_feed_pro');
        $this->model_feed_any_feed_pro->createTable();

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('any_feed_pro', array('any_feed_pro_status' => 1));
    }

    public function uninstall() {
        $this->load->model('feed/any_feed_pro');
        $this->model_feed_any_feed_pro->deleteTable();

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('any_feed_pro', array('any_feed_pro_status' => 0));
    }

    public function saveSettingsAjax() {
        if (!$this->user->hasPermission('modify', 'feed/any_feed_pro') && !$this->user->hasPermission('modify', 'extension/feed/any_feed_pro')) {
            $this->error['warning'] = $this->language->get('error_permission');
        } else {
            $this->load->model('setting/setting');
            $this->load->model('feed/any_feed_pro');
            $this->model_feed_any_feed_pro->save($this->request->post);
            $this->model_setting_setting->editSetting('any_feed_pro', $this->request->post);
            $this->response->setOutput(json_encode(true));
        }
    }

    public function saveSettingsFieldsAjax() {
        if (!$this->user->hasPermission('modify', 'feed/any_feed_pro') && !$this->user->hasPermission('modify', 'extension/feed/any_feed_pro')) {
            $this->error['warning'] = $this->language->get('error_permission');
        } else {
            $id = $this->request->get['id'];
            $this->load->model('setting/setting');
            $this->load->model('feed/any_feed_pro');
            $this->model_feed_any_feed_pro->updateProfileFields($id, $this->request->post);
            // $this->model_setting_setting->editSetting('any_feed_pro', $this->request->post);
            $this->response->setOutput(json_encode(true));
        }
    }

    public function isInstalled() {
        try {
            $this->load->model('feed/any_feed_pro');
            $this->model_feed_any_feed_pro->getProfiles();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function index() {
        if (!$this->isInstalled())
            $this->install();

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('any_feed_pro', array('any_feed_pro_status' => 1));
        //LOAD LANGUAGE
        $this->load->language('feed/any_feed_pro');
        //SET TITLE
        $this->document->setTitle($this->language->get('heading_title'));
        //LOAD SETTINGS
        $this->load->model('setting/setting');
        $this->load->model('feed/any_feed_pro');

        //SAVE SETTINGS (when form submitted)
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_feed_any_feed_pro->save($this->request->post);
            $this->model_setting_setting->editSetting('any_feed_pro', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'], 'SSL'));
        }

        //include jquery links if earlier than 1.5.1
        $OC_VERSION = $this->model_feed_any_feed_pro->getVersion();
        if (version_compare($OC_VERSION, '1.5.2', '<') || version_compare($OC_VERSION, '2', '<=')) {
            $data['jquery'] = '';

            if (version_compare($OC_VERSION, '1.5.2', '<'))
                $data['jquery'] .= '<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>';

            $data['jquery'] .= '
				<link type="text/css" href="view/javascript/jquery/ui/jquery-ui.min.css" rel="stylesheet" />
				<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui.min.js"></script>
			';
        }

        $data['css'] = '<link type="text/css" href="view/stylesheet/any_feed_pro.css" rel="stylesheet" />';


        $source_fields = array(
            'Product ID' => array(
                'prefix' => 'product_id',
            ),
            'URL' => array(
                'prefix' => 'url',
            ),
            'Name' => array(
                'prefix' => 'name',
            ),
            'Meta Tag Description' => array(
                'prefix' => 'meta_tag_description',
            ),
            'Meta Tag Keywords' => array(
                'prefix' => 'meta_tag_keywords',
            ),
            'Description' => array(
                'prefix' => 'description',
                'Strip HTML' => array(
                    'type' => 'checkbox',
                    'name' => 'description_strip_html',
                    'value' => '1',
                )
            ),
            'Product Tags' => array(
                'prefix' => 'product_tags',
            ),
            'Model' => array(
                'prefix' => 'model',
            ),
            'SKU' => array(
                'prefix' => 'sku',
            ),
            'UPC' => array(
                'prefix' => 'upc',
            ),
            'EAN' => array(
                'prefix' => 'ean',
            ),
            'JAN' => array(
                'prefix' => 'jan',
            ),
            'ISBN' => array(
                'prefix' => 'isbn',
            ),
            'MPN' => array(
                'prefix' => 'mpn',
            ),
            'Location' => array(
                'prefix' => 'location',
            ),
            'Price' => array(
                'prefix' => 'price',
//				'Unit' => array(
//					'type'=>'unit',
//					'name'=>'price_unit'
//				),
            ),
            'Status' => array(
                'prefix' => 'status',
            ),
            'Quantity' => array(
                'prefix' => 'quantity',
            ),
            'SEO Keyword' => array(
                'prefix' => 'name',
            ),
            'Image' => array(
                'prefix' => 'image',
            ),
            'Additional Images' => array(
                'prefix' => 'additional_images',
            ),
            'Length' => array(
                'prefix' => 'length',
            ),
            'Width' => array(
                'prefix' => 'width',
            ),
            'Height' => array(
                'prefix' => 'height',
            ),
            'Weight' => array(
                'prefix' => 'weight',
            ),
            'Manufacturer' => array(
                'prefix' => 'manufacturer',
            ),
            'Categories' => array(
                'prefix' => 'categories',
                'Category Names' => array(
                    'type' => 'name_map',
                    'name' => 'Categories',
                ),
            ),
            'Filters' => array(
                'prefix' => 'filters',
            ),
            'Attributes' => array(
                'prefix' => 'attribute',
                'Attribute Names' => array(
                    'type' => 'name_map',
                    'name' => 'Attribute',
                ),
            ),
            'Options' => array(
                'prefix' => 'options',
                'Option Names' => array(
                    'type' => 'name_map',
                    'name' => 'Options',
                ),
            ),
            'Special Price' => array(
                'prefix' => 'special_price',
                'Unit' => array(
                    'type' => 'unit',
                    'name' => 'price_unit'
                ),
            ),
            'Custom' => array(
                'prefix' => 'custom',
                'Field Value' => array(
                    'type' => 'text',
                    'name' => 'custom_value'
                ),
            ),
            'Date Modified' => array(
                'prefix' => 'date_modified',
            ),
            'Related Products' => array(
                'prefix' => 'related_products',
                'Identifying Field' => array(
                    'type' => 'select',
                    'name' => 'related_id_field',
                ),
            ),
            'Sort Order' => array(
                'prefix' => 'sort_order',
            ),
            'Viewed' => array(
                'prefix' => 'viewed'
            ),
            'Date Added' => array(
                'prefix' => 'date_added',
            ),
            'Date Available' => array(
                'prefix' => 'date_available',
            ),
            'Meta Title' => array(
                'prefix' => 'meta_title',
            ),
            'Points' => array(
                'prefix' => 'points',
            ),
        );

        //for common operations/fields like field name etc
        $common_settings = array(
            'Field Name' => array(
                'type' => 'text',
                'name' => 'name',
            ),
            'Rules' => array(
                'type' => 'rule',
                'name' => 'rule',
            ),
        );

        $data['select_options'] = array(
            'related_id_field' => array(
                'model' => 'Model',
                'sku' => 'SKU',
                'product_id' => 'Product ID',
                'upc' => 'UPC',
            ),
        );

        $data['rule_export_fields'] = array(
            'Product ID' => 'product_id',
            'URL' => 'url',
            'Name' => 'name',
            'Image' => 'image',
            'Price' => 'price',
            'Manufacturer' => 'manufacturer',
            'Model' => 'model',
            'SKU' => 'sku',
            'UPC' => 'upc',
            'EAN' => 'ean',
            'JAN' => 'jan',
            'ISBN' => 'isbn',
            'MPN' => 'mpn',
            'Quantity' => 'quantity',
            'Date Added' => 'date_added',
            'Viewed' => 'viewed',
            'Special Price' => 'special_price',
            'Length' => 'length',
            'Width' => 'width',
            'Height' => 'height',
            'Location' => 'location',
            'Points' => 'points',
            'Date Available' => 'date_available',
            'Weight' => 'weight',
            'Shipping' => 'shipping',
            'Status' => 'status',
            'Date Modified' => 'date_modified',
            'Product Tags' => 'tag',
            'Meta Tag Keywords' => 'meta_keyword',
            'Meta Tag Description' => 'meta_description',
            'SEO Keyword' => 'keyword',
            'Discount' => 'discount',
            'Special' => 'special',
            'Reward' => 'reward',
            'Rating' => 'rating',
        );

        $data['rule_export_types'] = array(
            array('name' => 'Export Custom Text', 'val' => 'txt'),
            array('name' => 'Export another Field', 'val' => 'slct'),
            array('name' => 'Skip this Product', 'val' => 'skip'),
            array('name' => 'Apply Math to field', 'val' => 'math'),
            array('name' => 'Append Text', 'val' => 'append'),
        );
        $data['rule_comparisons'] = array(
            array('name' => 'is Less than', 'val' => "lt"),
            array('name' => 'is Greater than', 'val' => "gt"),
            array('name' => 'is Equal to', 'val' => "eq"),
            array('name' => 'is Not Equal to', 'val' => "noeq"),
            array('name' => 'Contains', 'val' => "contain"),
            array('name' => 'Does Not Contain', 'val' => "nocontain"),
        );
        $data['math_operators'] = array(
            '+' => "Add",
            '-' => "Minus",
            '*' => "Multiply by",
            '/' => "Divide by",
        );

        ksort($source_fields);
        $data['source_fields'] = $source_fields;
        $data['common_field_settings'] = $common_settings;

        $profiles = $this->model_feed_any_feed_pro->getProfiles();

        $profile_list = array();
        foreach ($profiles as $profile) {
            foreach ($profile as $name => $value) {
                if ($name == 'name') {
                    $profile_list[$value] = $this->model_feed_any_feed_pro->makeNiceName($value);
                }
            }
        }

        $presets = $this->model_feed_any_feed_pro->getPresets();
        $preset_list = array();

        foreach ($presets as $preset) {
            foreach ($preset as $name => $value) {
                if ($name == 'name') {
                    $preset_list[$value] = $this->model_feed_any_feed_pro->makeNiceName($value);
                }
            }
        }

        // For Currency Setting
        $this->load->model('localisation/currency');
        $data['default_currency'] = $this->config->get('config_currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        // For Language Setting
        $this->load->model('localisation/language');
        $data['default_language'] = $this->config->get('config_language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // For Multi-Store settings
        $this->load->model('setting/store');
        $data['store_selections'] = $this->model_setting_store->getStores();
        
        $data['preset_feeds'] = $profile_list;
        $data['profiles'] = $preset_list;
        $data['token'] = $this->session->data['token'];

        // For Customer Groups
        if (version_compare(VERSION, '2.1', '>=')) {
            $this->load->model('customer/customer_group');
            $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
        } else {
            $this->load->model('sale/customer_group');
            $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
        }

        $this->load->model('catalog/option');
        $this->load->model('catalog/attribute');
        $this->load->model('catalog/category');
        $data['name_map']['Categories'] = $this->model_catalog_category->getCategories(array());
        $data['name_map']['Options'] = $this->model_catalog_option->getOptions(array());
        $data['name_map']['Attribute'] = $this->model_catalog_attribute->getAttributes(array());

        // Change X_id to id
        $name_map = array(
            'Categories' => 'category_',
            'Options' => 'option_',
            'Attribute' => 'attribute_'
        );

        foreach ($name_map as $name => $prefix) {
            foreach ($data['name_map'][$name] as $index => $map)
                $data['name_map'][$name][$index]['id'] = $map[$prefix . 'id'];
        }


        //LANGUAGE
        $text_strings = array(
            'heading_title',
            'entry_add_feed',
            'entry_exclude_fields',
            'button_save',
            'button_cancel',
            'button_add_module',
            'button_remove',
            'text_select_name',
            'text_feed_name',
            'text_feed_profile',
        );

        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }
        //END LANGUAGE
        //ERROR
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        //SUCCESS
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        //BREADCRUMB TRAIL
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_feed'),
            'href' => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

        //Choose which template file will be used to display this request.
        $this->template = 'feed/any_feed_pro.tpl';

        $data['header'] = $this->load->controller('common/header');
        $data['menu'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        //Send the output.
        $this->response->setOutput($this->load->view($this->template, $data));
    }
    
    public function editProfileFields() {
        if (!$this->isInstalled())
            $this->install();

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('any_feed_pro', array('any_feed_pro_status' => 1));
        //LOAD LANGUAGE
        $this->load->language('feed/any_feed_pro');
        //LOAD SETTINGS
        $this->load->model('setting/setting');
        $this->load->model('feed/any_feed_pro');
        
        $id = $this->request->get['id'];        
        $getProfileInfo = $this->model_feed_any_feed_pro->getProfileInfo($id);
        //SET TITLE
        $this->document->setTitle("Manage Fields: " . $getProfileInfo["name"]);

        //SAVE SETTINGS (when form submitted)
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_feed_any_feed_pro->save($this->request->post);
            $this->model_setting_setting->editSetting('any_feed_pro', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'], 'SSL'));
        }

        //include jquery links if earlier than 1.5.1
        $OC_VERSION = $this->model_feed_any_feed_pro->getVersion();
        if (version_compare($OC_VERSION, '1.5.2', '<') || version_compare($OC_VERSION, '2', '<=')) {
            $data['jquery'] = '';

            if (version_compare($OC_VERSION, '1.5.2', '<'))
                $data['jquery'] .= '<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>';

            $data['jquery'] .= '
				<link type="text/css" href="view/javascript/jquery/ui/jquery-ui.min.css" rel="stylesheet" />
				<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui.min.js"></script>
			';
        }

        $data['css'] = '<link type="text/css" href="view/stylesheet/any_feed_pro.css" rel="stylesheet" />';


        $source_fields = array(
            'Product ID' => array(
                'prefix' => 'product_id',
            ),
            'URL' => array(
                'prefix' => 'url',
            ),
            'Name' => array(
                'prefix' => 'name',
            ),
            'Meta Tag Description' => array(
                'prefix' => 'meta_tag_description',
            ),
            'Meta Tag Keywords' => array(
                'prefix' => 'meta_tag_keywords',
            ),
            'Description' => array(
                'prefix' => 'description',
                'Strip HTML' => array(
                    'type' => 'checkbox',
                    'name' => 'description_strip_html',
                    'value' => '1',
                )
            ),
            'Product Tags' => array(
                'prefix' => 'product_tags',
            ),
            'Model' => array(
                'prefix' => 'model',
            ),
            'SKU' => array(
                'prefix' => 'sku',
            ),
            'UPC' => array(
                'prefix' => 'upc',
            ),
            'EAN' => array(
                'prefix' => 'ean',
            ),
            'JAN' => array(
                'prefix' => 'jan',
            ),
            'ISBN' => array(
                'prefix' => 'isbn',
            ),
            'MPN' => array(
                'prefix' => 'mpn',
            ),
            'Location' => array(
                'prefix' => 'location',
            ),
            'Price' => array(
                'prefix' => 'price',
//				'Unit' => array(
//					'type'=>'unit',
//					'name'=>'price_unit'
//				),
            ),
            'Status' => array(
                'prefix' => 'status',
            ),
            'Quantity' => array(
                'prefix' => 'quantity',
            ),
            'SEO Keyword' => array(
                'prefix' => 'name',
            ),
            'Image' => array(
                'prefix' => 'image',
            ),
            'Additional Images' => array(
                'prefix' => 'additional_images',
            ),
            'Length' => array(
                'prefix' => 'length',
            ),
            'Width' => array(
                'prefix' => 'width',
            ),
            'Height' => array(
                'prefix' => 'height',
            ),
            'Weight' => array(
                'prefix' => 'weight',
            ),
            'Manufacturer' => array(
                'prefix' => 'manufacturer',
            ),
            'Categories' => array(
                'prefix' => 'categories',
                'Category Names' => array(
                    'type' => 'name_map',
                    'name' => 'Categories',
                ),
            ),
            'Filters' => array(
                'prefix' => 'filters',
            ),
            'Attributes' => array(
                'prefix' => 'attribute',
                'Attribute Names' => array(
                    'type' => 'name_map',
                    'name' => 'Attribute',
                ),
            ),
            'Options' => array(
                'prefix' => 'options',
                'Option Names' => array(
                    'type' => 'name_map',
                    'name' => 'Options',
                ),
            ),
            'Special Price' => array(
                'prefix' => 'special_price',
                'Unit' => array(
                    'type' => 'unit',
                    'name' => 'price_unit'
                ),
            ),
            'Custom' => array(
                'prefix' => 'custom',
                'Field Value' => array(
                    'type' => 'text',
                    'name' => 'custom_value'
                ),
            ),
            'Date Modified' => array(
                'prefix' => 'date_modified',
            ),
            'Related Products' => array(
                'prefix' => 'related_products',
                'Identifying Field' => array(
                    'type' => 'select',
                    'name' => 'related_id_field',
                ),
            ),
            'Sort Order' => array(
                'prefix' => 'sort_order',
            ),
            'Viewed' => array(
                'prefix' => 'viewed'
            ),
            'Date Added' => array(
                'prefix' => 'date_added',
            ),
            'Date Available' => array(
                'prefix' => 'date_available',
            ),
            'Meta Title' => array(
                'prefix' => 'meta_title',
            ),
            'Points' => array(
                'prefix' => 'points',
            ),
        );

        //for common operations/fields like field name etc
        $common_settings = array(
            'Field Name' => array(
                'type' => 'text',
                'name' => 'name',
            ),
            'Rules' => array(
                'type' => 'rule',
                'name' => 'rule',
            ),
        );

        $data['select_options'] = array(
            'related_id_field' => array(
                'model' => 'Model',
                'sku' => 'SKU',
                'product_id' => 'Product ID',
                'upc' => 'UPC',
            ),
        );

        $data['rule_export_fields'] = array(
            'Product ID' => 'product_id',
            'URL' => 'url',
            'Name' => 'name',
            'Image' => 'image',
            'Price' => 'price',
            'Manufacturer' => 'manufacturer',
            'Model' => 'model',
            'SKU' => 'sku',
            'UPC' => 'upc',
            'EAN' => 'ean',
            'JAN' => 'jan',
            'ISBN' => 'isbn',
            'MPN' => 'mpn',
            'Quantity' => 'quantity',
            'Date Added' => 'date_added',
            'Viewed' => 'viewed',
            'Special Price' => 'special_price',
            'Length' => 'length',
            'Width' => 'width',
            'Height' => 'height',
            'Location' => 'location',
            'Points' => 'points',
            'Date Available' => 'date_available',
            'Weight' => 'weight',
            'Shipping' => 'shipping',
            'Status' => 'status',
            'Date Modified' => 'date_modified',
            'Product Tags' => 'tag',
            'Meta Tag Keywords' => 'meta_keyword',
            'Meta Tag Description' => 'meta_description',
            'SEO Keyword' => 'keyword',
            'Discount' => 'discount',
            'Special' => 'special',
            'Reward' => 'reward',
            'Rating' => 'rating',
        );

        $data['rule_export_types'] = array(
            array('name' => 'Export Custom Text', 'val' => 'txt'),
            array('name' => 'Export another Field', 'val' => 'slct'),
            array('name' => 'Skip this Product', 'val' => 'skip'),
            array('name' => 'Apply Math to field', 'val' => 'math'),
            array('name' => 'Append Text', 'val' => 'append'),
        );
        $data['rule_comparisons'] = array(
            array('name' => 'is Less than', 'val' => "lt"),
            array('name' => 'is Greater than', 'val' => "gt"),
            array('name' => 'is Equal to', 'val' => "eq"),
            array('name' => 'is Not Equal to', 'val' => "noeq"),
            array('name' => 'Contains', 'val' => "contain"),
            array('name' => 'Does Not Contain', 'val' => "nocontain"),
        );
        $data['math_operators'] = array(
            '+' => "Add",
            '-' => "Minus",
            '*' => "Multiply by",
            '/' => "Divide by",
        );

        ksort($source_fields);
        $data['source_fields'] = $source_fields;
        $data['common_field_settings'] = $common_settings;

        $profiles = $this->model_feed_any_feed_pro->getPresets();
        
        $profile_list = array();
        foreach ($profiles as $profile) {
            if($profile["id"] != $id)
                continue;
            
            foreach ($profile as $name => $value) {
                if ($name == 'name') {
                    $profile_list[$value] = $this->model_feed_any_feed_pro->makeNiceName($value);
                }
            }
        }

        $presets = $this->model_feed_any_feed_pro->getPresets();
        $preset_list = array();

        foreach ($presets as $preset) {
            foreach ($preset as $name => $value) {
                if ($name == 'name') {
                    $preset_list[$value] = $this->model_feed_any_feed_pro->makeNiceName($value);
                }
            }
        }

        // For Currency Setting
        $this->load->model('localisation/currency');
        $data['default_currency'] = $this->config->get('config_currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        // For Language Setting
        $this->load->model('localisation/language');
        $data['default_language'] = $this->config->get('config_language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // For Multi-Store settings
        $this->load->model('setting/store');
        $data['store_selections'] = $this->model_setting_store->getStores();

        $data['preset_feeds'] = $profile_list;
        $data['profiles'] = $preset_list;
        $data['token'] = $this->session->data['token'];

        // For Customer Groups
        if (version_compare(VERSION, '2.1', '>=')) {
            $this->load->model('customer/customer_group');
            $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
        } else {
            $this->load->model('sale/customer_group');
            $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
        }

        $this->load->model('catalog/option');
        $this->load->model('catalog/attribute');
        $this->load->model('catalog/category');
        $data['name_map']['Categories'] = $this->model_catalog_category->getCategories(array());
        $data['name_map']['Options'] = $this->model_catalog_option->getOptions(array());
        $data['name_map']['Attribute'] = $this->model_catalog_attribute->getAttributes(array());

        // Change X_id to id
        $name_map = array(
            'Categories' => 'category_',
            'Options' => 'option_',
            'Attribute' => 'attribute_'
        );

        foreach ($name_map as $name => $prefix) {
            foreach ($data['name_map'][$name] as $index => $map)
                $data['name_map'][$name][$index]['id'] = $map[$prefix . 'id'];
        }


        //LANGUAGE
        $text_strings = array(
            'heading_title',
            'entry_add_feed',
            'entry_exclude_fields',
            'button_save',
            'button_cancel',
            'button_add_module',
            'button_remove',
            'text_select_name',
            'text_feed_name',
            'text_feed_profile',
        );

        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }
        
        $data["heading_title"] = "Manage Fields: " . $getProfileInfo["name"];
        
        //END LANGUAGE
        //ERROR
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        //SUCCESS
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        //BREADCRUMB TRAIL
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_feed'),
            'href' => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

        //Choose which template file will be used to display this request.
        $this->template = 'feed/edit_feed_field.tpl';

        $data['header'] = $this->load->controller('common/header');
        $data['menu'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['id'] = $id;

        //Send the output.
        $this->response->setOutput($this->load->view($this->template, $data));
    }
    
    public function manageProfile() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'code';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/event', 'token=' . $this->session->data['token'] . $url, true)
        );

        $data['events'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $event_total = $this->model_extension_event->getTotalEvents();

        $results = $this->model_extension_event->getEvents($filter_data);

        foreach ($results as $result) {
            $data['events'][] = array(
                'event_id' => $result['event_id'],
                'code' => $result['code'],
                'trigger' => $result['trigger'],
                'action' => $result['action'],
                'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'enable' => $this->url->link('extension/event/enable', 'token=' . $this->session->data['token'] . '&event_id=' . $result['event_id'], true),
                'disable' => $this->url->link('extension/event/disable', 'token=' . $this->session->data['token'] . '&event_id=' . $result['event_id'], true),
                'enabled' => $result['status']
            );
        }

        $data['heading_title'] = "Manage Profiles"; // $this->language->get('heading_title');

        $data['text_list'] = "Profile List"; // $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_event'] = $this->language->get('text_event');

        $data['column_code'] = $this->language->get('column_code');
        $data['column_trigger'] = $this->language->get('column_trigger');
        $data['column_action'] = $this->language->get('column_action');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_enable'] = $this->language->get('button_enable');
        $data['button_disable'] = $this->language->get('button_disable');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_code'] = $this->url->link('extension/event', 'token=' . $this->session->data['token'] . '&sort=code' . $url, true);
        $data['sort_trigger'] = $this->url->link('extension/event', 'token=' . $this->session->data['token'] . '&sort=trigger' . $url, true);
        $data['sort_action'] = $this->url->link('extension/event', 'token=' . $this->session->data['token'] . '&sort=action' . $url, true);
        $data['sort_status'] = $this->url->link('extension/event', 'token=' . $this->session->data['token'] . '&sort=status' . $url, true);
        $data['sort_date_added'] = $this->url->link('extension/event', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $event_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/event', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($event_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($event_total - $this->config->get('config_limit_admin'))) ? $event_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $event_total, ceil($event_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->load->model('feed/any_feed_pro');
        $presets = $this->model_feed_any_feed_pro->getPresets();

        $data['profileData'] = $presets;

        // For Currency Setting
        $this->load->model('localisation/currency');
        $data['default_currency'] = $this->config->get('config_currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        // For Language Setting
        $this->load->model('localisation/language');
        $data['token'] = $this->session->data['token'];
        $data['default_language'] = $this->config->get('config_language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $languageAccordingToId = array();
        foreach ($data['languages'] as $language) {
            $languageAccordingToId[$language["language_id"]] = $language;
        }
        $data['languages_by_id'] = $languageAccordingToId;
        // For Multi-Store settings
        $this->load->model('setting/store');
        $data['store_selections'] = $this->model_setting_store->getStores();

        //Send the output.
        $this->response->setOutput($this->load->view('feed/manage_profile.tpl', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'feed/any_feed_pro') && !$this->user->hasPermission('modify', 'extension/feed/any_feed_pro')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getProfile($profile_name = 'custom') {
        $this->load->model('feed/any_feed_pro');
        if (isset($this->request->get['feed'])) {
            $profile_name = $this->request->get['feed'];
        }
        if (isset($this->request->get['preset'])) {
            $preset = $this->request->get['preset'];
        }
        
        $getProfile = $this->model_feed_any_feed_pro->getProfile($profile_name, $preset);
        
        $profile = json_encode($getProfile);
        echo $profile;
        return;
    }

    public function deleteProfile() {
        $this->load->model('feed/any_feed_pro');
        if (isset($this->request->get['feed'])) {
            $profile_name = $this->request->get['feed'];
            $profile_name = $this->model_feed_any_feed_pro->makeStandardName($profile_name);
            $this->model_feed_any_feed_pro->deleteProfile($profile_name);
        }
        return;
    }
    
    public function duplicate() {
        $this->load->model('feed/any_feed_pro');
        if (isset($this->request->get['name'])) {
            $name = $this->request->get['name'];
            $profile_name = $this->model_feed_any_feed_pro->getProfileByName($name);
            if(!empty($profile_name)) {
                $sqlData = $profile_name;
                unset($sqlData["id"]);
                
                $sqlData["name"] = $sqlData["name"] . '_' . time();
                $sqlData["preset"] = 0;
                $this->model_feed_any_feed_pro->duplicateProfile($sqlData);
            }

            $this->response->redirect($this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'] . '&type=module', true));
        }
    }

    public function createProfile() {
        $this->load->language('feed/any_feed_pro');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('feed/any_feed_pro');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->profileValidate()) {            
                $isProfileExist = $this->model_feed_any_feed_pro->isProfileExist($this->request->post['name']);
                if(!empty($isProfileExist)) {
                    $this->error['name'] = "Please enter valid details. Profile name already exist!";
                }else {
                    $profiles = $this->model_feed_any_feed_pro->saveProfile($this->request->post);
                    $this->session->data['success'] = "Success: Profile successfully created!";
                    $this->response->redirect($this->url->link('feed/any_feed_pro/manageProfile', 'token=' . $this->session->data['token'] . '&type=module', true));            
                }
        }

        $data['heading_title'] = "Create Profile";

        $data['text_edit'] = $this->language->get('text_edit');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');


        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['filename'])) {
            $data['error_filename'] = $this->error['filename'];
        } else {
            $data['error_filename'] = '';
        }

        if (isset($this->error['store'])) {
            $data['error_store'] = $this->error['store'];
        } else {
            $data['error_store'] = '';
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('feed/any_feed_pro/createProfile', 'token=' . $this->session->data['token'], true);
        } else {
            $data['action'] = $this->url->link('feed/any_feed_pro/createProfile', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['token'] = $this->session->data['token'];        

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        
        // For Currency Setting
        $this->load->model('localisation/currency');
        $data['default_currency'] = $this->config->get('config_currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        
        // For Language Setting
        $this->load->model('localisation/language');
        $data['default_language'] = $this->config->get('config_language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // For Multi-Store settings
        $this->load->model('setting/store');
        $data['store_selections'] = $this->model_setting_store->getStores();

        $this->response->setOutput($this->load->view('feed/create_profile.tpl', $data));
    }


    public function deleteProfileUsingId() {
        $id = $this->request->get['id'];
        $this->load->model('feed/any_feed_pro');
        $getProfileInfo = $this->model_feed_any_feed_pro->deleteProfileUsingId($id);
        $this->session->data['success'] = "Success: Profile successfully deleted!";
        $this->response->redirect($this->url->link('feed/any_feed_pro/manageProfile', 'token=' . $this->session->data['token'] . '&type=module', true));            
    }
    
    public function editProfile() {
        $this->load->language('feed/any_feed_pro');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('feed/any_feed_pro');
        
        if(!empty($this->request->get['id']))
            $id = $this->request->get['id'];
        
        if(!empty($this->request->get['name']))
            $name = $this->request->get['name'];
        
        if(empty($id) && !empty($name)) {
            $getFeeds = $this->model_feed_any_feed_pro->getProfileByName($name);
            $id = $getFeeds['id'];
        }
        
        $getProfileInfo = $this->model_feed_any_feed_pro->getProfileInfo($id);

        if(isset($getProfileInfo['settings']) && !empty($getProfileInfo['settings'])) {
            $getProfileInfo['settings'] = json_decode($getProfileInfo['settings'], TRUE);
        }
  
        $data['profile_data'] = $getProfileInfo;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->profileValidate()) {
              $profiles = $this->model_feed_any_feed_pro->updateProfile($id, $this->request->post);
              $this->session->data['success'] = "Success: Profile successfully updated!";
              $this->response->redirect($this->url->link('feed/any_feed_pro/manageProfile', 'token=' . $this->session->data['token'] . '&type=module', true));            
        }

        $data['heading_title'] = "Create Profile";

        $data['text_edit'] = $this->language->get('text_edit');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['filename'])) {
            $data['error_filename'] = $this->error['filename'];
        } else {
            $data['error_filename'] = '';
        }

        if (isset($this->error['store'])) {
            $data['error_store'] = $this->error['store'];
        } else {
            $data['error_store'] = '';
        }

        if (empty($id)) {
            $data['action'] = $this->url->link('feed/any_feed_pro/editProfile', 'token=' . $this->session->data['token'], true);
        } else {
            $data['action'] = $this->url->link('feed/any_feed_pro/editProfile', 'token=' . $this->session->data['token'] . '&id=' . $id, true);
        }

        $data['token'] = $this->session->data['token'];        

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        
        // For Currency Setting
        $this->load->model('localisation/currency');
        $data['default_currency'] = $this->config->get('config_currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        
        // For Language Setting
        $this->load->model('localisation/language');
        $data['default_language'] = $this->config->get('config_language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // For Multi-Store settings
        $this->load->model('setting/store');
        $data['store_selections'] = $this->model_setting_store->getStores();

        $this->response->setOutput($this->load->view('feed/edit_profile.tpl', $data));
    }
    
    public function editFeedSettings() {
        $this->load->language('feed/any_feed_pro');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('feed/any_feed_pro');
        
        if(!empty($this->request->get['id']))
            $id = $this->request->get['id'];
        
        if(!empty($this->request->get['name']))
            $name = $this->request->get['name'];
        
        if(empty($id) && !empty($name)) {
            $getFeeds = $this->model_feed_any_feed_pro->getProfileSettingByName($name);
            $id = $getFeeds['id'];
        }
        
        $getProfileInfo = $this->model_feed_any_feed_pro->getProfileInfo($id);

        if(isset($getProfileInfo['settings']) && !empty($getProfileInfo['settings'])) {
            $getProfileInfo['settings'] = json_decode($getProfileInfo['settings'], TRUE);
        }
  
        $data['profile_data'] = $getProfileInfo;
        $data['cancel_return_data'] = $this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'] . '&type=module', true);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->profileValidate()) {
              $profiles = $this->model_feed_any_feed_pro->updateProfile($id, $this->request->post);
              $this->session->data['success'] = "Success: Profile successfully updated!";
              $this->response->redirect($this->url->link('feed/any_feed_pro', 'token=' . $this->session->data['token'] . '&type=module', true));            
        }

        $data['heading_title'] = "Edit Profile";

        $data['text_edit'] = $this->language->get('text_edit');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['filename'])) {
            $data['error_filename'] = $this->error['filename'];
        } else {
            $data['error_filename'] = '';
        }

        if (isset($this->error['store'])) {
            $data['error_store'] = $this->error['store'];
        } else {
            $data['error_store'] = '';
        }

        if (empty($id)) {
            $data['action'] = $this->url->link('feed/any_feed_pro/editFeedSettings', 'token=' . $this->session->data['token'], true);
        } else {
            $data['action'] = $this->url->link('feed/any_feed_pro/editFeedSettings', 'token=' . $this->session->data['token'] . '&id=' . $id, true);
        }

        $data['token'] = $this->session->data['token'];        

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        
        // For Currency Setting
        $this->load->model('localisation/currency');
        $data['default_currency'] = $this->config->get('config_currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        
        // For Language Setting
        $this->load->model('localisation/language');
        $data['default_language'] = $this->config->get('config_language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // For Multi-Store settings
        $this->load->model('setting/store');
        $data['store_selections'] = $this->model_setting_store->getStores();

        $this->response->setOutput($this->load->view('feed/edit_profile.tpl', $data));
    }
    
    protected function profileValidate() {
        if (empty($this->request->post['name'])) {
            $this->error['name'] = "Please enter valid details. It's required!"; // $this->language->get('error_name');
        }     
        
        if ((utf8_strlen($this->request->post['filename']) < 3) || (utf8_strlen($this->request->post['filename']) > 64)) {
            $this->error['filename'] = "Name must be between 3 and 64 characters!"; // $this->language->get('error_name');
        }
        
        if (empty($this->request->post['store'])) {
            $this->error['store'] = "Please select store. It's required"; // $this->language->get('error_name');
        }

        return !$this->error;
    }

}

?>
