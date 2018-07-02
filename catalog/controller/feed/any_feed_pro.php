<?php
############################################################################################
#  AnyFeed PRO Module for Opencart 2.0.x from HostJars http://opencart.hostjars.com    	   #
############################################################################################
function ppp($var, $label='') {
	echo '<xmp style="text-align: left;">'.($label != '' ? $label.': ' : '');print_r($var);echo '</xmp><br />';
}
class ControllerFeedAnyFeedPro extends Controller {
	private $names = array();
	private $input_fields = array(
		'Product ID' => 'product_id',
		'URL' => 'url',
		'Name' => 'name',
		'Image' => 'image',
		//'additional_image' => 'additional_image',
		'Price' => 'price',
		'Description' => 'description',
		//'Categories' => 'categories',
		//'Filters' => 'filters',
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
		'any_feed_pro_field_stockstatus' => 'stock_status_id',
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
		'Reward' => 'reward',
		'Rating' => 'rating',
		//'Attributes' => 'attributes',
		//'Options' => 'options',
		'Custom' => 'custom',
		'Related Products' => 'related_products',
		'Sort Order' => 'sort_order',
		'Viewed' => 'viewed',
		'Date Added' => 'date_added',
		'Date Available' => 'date_available',
		'Meta Title' => 'meta_title',
		'Points' => 'points',
	);

	private function applyMath($value, $operator, $variable) {
		$return_value = $value;
		if (is_numeric($variable) && is_numeric($value)) {
			switch ($operator) {
				case 'Add':
					$return_value = $value + $variable;
					break;
				case 'Minus':
					$return_value = $value - $variable;
					break;
				case 'Multiply by':
					$return_value = $value * $variable;
					break;
				case 'Divide by':
					if ($variable != 0)
						$return_value = $value / $variable;
					break;
			}
		}

		return $return_value;
	}

	private function addFileHeaders($filename='', $file_type="XML") {
		if ($file_type == "XML"){
			$this->response->addHeader('Content-Type: application/xml');
		}elseif ($file_type == "CSV") {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			if (!empty($filename)) {
				$this->response->addheader('Content-Disposition: attachment; filename='.$filename.'.csv');
			} else {
				$this->response->addheader('Content-Disposition: attachment; filename=opencart_products.csv');
			}
			$this->response->addheader('Content-Transfer-Encoding: binary');
		}elseif ($file_type == "TXT") {
			$this->response->addheader('Content-Type: text/plain');
			$this->response->addheader('Content-Description: File Transfer');
			if (!empty($filename)) {
				$this->response->addheader('Content-Disposition: attachment; filename='.$filename.'.txt');
			} else {
				$this->response->addheader('Content-Disposition: attachment; filename=opencart_products.txt');
			}
		}

	}

	private function validateRootTag($root_tag) {
		$root_tag = html_entity_decode($root_tag);
		$root_tag = preg_replace('/[^A-Z0-9_:]/i', '', $root_tag);

		return $root_tag;
	}

	public function index() {
		$this->load->model('feed/any_feed_pro');
		$this->load->model('localisation/currency');
		if (defined('CLI_INITIATED')) {
			$profile_name = FEED_NAME;
			$this->cron = true;
		}
		elseif(isset($this->request->get['name'])){
			$profile_name = $this->request->get['name'];
		} else {
			echo "Profile name not set, please correct your url and try again";
			exit();
		}

		$profile = $this->model_feed_any_feed_pro->getProfile($profile_name);
		$settings = json_decode($profile['settings'], true);
		$fields =  json_decode(html_entity_decode($profile['fields']), true);

		$settings['root_tag'] = $this->validateRootTag($settings['root_tag']);

		$this->currency_id = $settings['currency'];

		if(empty($fields)) {
			$fields = array();
		}

		if(isset($settings['enable']) &&  $settings['enable'] == 1) {
			//check the cache
			if ($this->cron || (isset($settings['cache']) && $settings['cache'] === 'Yes')) {
				//cache on - check for the file
				$extension = ($settings['type'] === 'XML') ? '.xml' : '.csv';
				$filename = $profile_name . $extension;
				$feed_cache_dir = DIR_APPLICATION . '../feed_cache/';
				if (!file_exists($feed_cache_dir)) {
					mkdir($feed_cache_dir, 0755);
				}
				$feed_cache_dir = realpath($feed_cache_dir);
				$settings['cron_url'] = $feed_cache_dir . '/' . $filename;
				if (!$this->cron && file_exists($settings['cron_url'])) {
					$file = fopen($settings['cron_url'], 'r');
					$lastModified = filemtime($settings['cron_url']); //unix stamp
					$timeout = (int) $settings['timeout'] * 60; //min to seconds
					$timedOut = (time() - $lastModified) < $timeout;
					if ($timedOut) {
						//present the cached file
						$output = fread($file, filesize($settings['cron_url']));
						fclose($file);
						$this->addFileHeaders($settings['filename'], $settings['type']);

						$this->response->setOutput($output, 0);
						return; //dont regenerate the file
					}
				}
			}
			$this->createFeed($settings, $fields);
		} else {
			//$this->redirect($this->url->link('common/home'));
		}
	}

	private function createFeed($settings, $fields) {
		$this->load->model('feed/any_feed_pro');

		$output = '';

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		//Allow start and limit for DB query - a fix for large feeds.
		$data = array();
		if (isset($this->request->get['start'])) {
			$data['start'] = $this->request->get['start'];
			$data['limit'] = 50000;
		}
		if (isset($this->request->get['numResults'])) {
			$data['limit'] = $this->request->get['numResults'];
		}
		$this->stock_statuses = $this->model_feed_any_feed_pro->getStockStatus();
		$products = $this->model_feed_any_feed_pro->getProducts($data, $settings['language']);
		//$this->max_cat = $this->model_feed_any_feed_pro->getMaxCategories();
		$this->max_cat = 1;
		$this->max_opt_names = $this->model_feed_any_feed_pro->getProductOptionNames();
		$this->max_add = $this->model_feed_any_feed_pro->getMaxImages();
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$this->max_filters = $this->model_feed_any_feed_pro->getMaxFilters();
		}

		if($settings['type'] == 'CSV') {
			if (isset($settings['delimiter'])) {
				if ($settings['delimiter'] == '\t') {
					$settings['delimiter'] = "\t";
				}
			} else {
				$settings['delimiter'] = ',';
			}
		}


		foreach ($products as $product) {
			if (!(isset($settings['export_disabled_products']) && $settings['export_disabled_products'] == 0 && $product['status'] == 0))
				$output .= $this->getFeedRow($settings, $fields, $product);
		}
		//add all related headers to the front of the csv file
		if($settings['type'] == 'CSV' || $settings['type'] == 'TXT') {
			$output = join($settings['delimiter'], $this->names) . "\n" . $output;
		} else {
			$root_tag = 'any_feed_pro_product_list';
			if (!empty($settings['root_tag']))
				$root_tag = $settings['root_tag'];
			$output = "<?xml version=\"1.0\"?>\n<".$root_tag.">\n" . $output . "</" . $root_tag . ">";
		}

		$this->addFileHeaders($settings['filename'], $settings['type']);

		//save the feed if cache is on
		//wont make it this far if the timeout is low
		if ($this->cron || (isset($settings['cache']) && $settings['cache'] === 'Yes')) {
			$file = fopen($settings['cron_url'], 'w');
			fwrite($file, $output);
			fclose($file);
		}
		if (!$this->cron)
			$this->response->setOutput($output, 0);
	}

	private function getProdField($product, $field_name, $field_settings) {
		$prod_val = '';
		if ($field_name == 'Custom') {
			$prod_val = $field_settings['Field Value']['value'];
		}
		else if ($field_name == 'Manufacturer') {
			$manufacturer_name = '';
			$this->load->model('catalog/manufacturer');
			if (isset($product['manufacturer_id']))
			{
				$product_manufacturer = $this->model_catalog_manufacturer->getManufacturer($product['manufacturer_id']);
				if (isset($product_manufacturer['name']))
					$prod_val = $product_manufacturer['name'];

			}
		}
		else if (isset($product[$this->input_fields[$field_name]])) {
			$prod_val = $product[$this->input_fields[$field_name]];
		}
		return $prod_val;
	}
	
	private function getProdAnyFeedField($product_id, $field_name, $store) {
		$sql = "SELECT store_id, " . $field_name . " FROM " . DB_PREFIX . "hj_any_feed_pro_feeds_product WHERE product_id = '" . (int)$product_id . "' AND (store_id = " . $store;
		
		if($store > 0){
			$sql .= " OR store_id = 0";
		}
		
		$sql .= ")";
		
		$query = $this->db->query($sql);
		
		if($query->rows){
			$data = array();
			foreach($query->rows as $row){
				$data[$row['store_id']] = $row[$field_name];
			}
			
			if(isset($data[$store]) && $data[$store]){
				return $data[$store];
			}elseif(isset($data[0]) && $data[0]){
				return $data[0];
			}else{
				return '';
			}
		}else{
			return '';
		}
	}
	
	private function getStoreURL($product_id, $store) {
		$query = $this->db->query("SELECT url FROM " . DB_PREFIX . "store WHERE store_id = " . (int)$store);
		return isset($query->row['url']) ? $query->row['url'] : '';
	}

	private function setupProduct($product, $fields) {
		$this->load->model('catalog/product');

		//get the product URL
		if(isset($fields['URL'])) {
			$product['URL'] = HTTP_SERVER . 'index.php?route=product/product&product_id=' . $product['product_id'];
		}

		if (isset($fields['Related Products'])) {
				$related_prods = $this->model_catalog_product->getProductRelated($product['product_id']);
				$product['related_prods_array'] = array();
				foreach ($related_prods as $related_prod)
					$product['related_prods_array'][] = $related_prod;
		}

		//get additional images
		if(isset($fields['Additional Images'])) {
			$image_list = $this->model_catalog_product->getProductImages($product['product_id']);
			foreach($fields['Additional Images']['settings'] as $f) {
				$image_prefix = $f['Field Name']['value'];
				for($k = 0; $k < $this->max_add; $k++) {
					$index = $k+1;
					$fields[$image_prefix.$index] = array('settings'=> array(array('Field Name' => array('value' => $image_prefix.$index), 'sort_order' => $f['sort_order'])));
					$this->input_fields[$image_prefix.$index] = $image_prefix.$index;
					$product[$image_prefix.$index] = (isset($image_list[$k]['image'])) ? HTTP_SERVER . "image/" . $image_list[$k]['image'] : ' ';
				}
			}
		}

		//if filters are selected, export each product filter
		$this->load->model('feed/any_feed_pro');
		if (version_compare(VERSION, '1.5.5', '>=') && isset($fields['Filters'])) {
			$product_filters = $this->model_feed_any_feed_pro->getProductFilters($product['product_id']);
			foreach($fields['Filters']['settings'] as $f) {
				$filter_prefix = $f['Field Name']['value'];
				for($k = 0; $k < $this->max_filters; $k++) {
					$index = $k+1;
					$product[$filter_prefix.$index] = (isset($product_filters[$k])) ? $product_filters[$k] : ' ';
					$fields[$filter_prefix.$index] = array('settings'=> array(array('Field Name' => array('value' => $filter_prefix.$index), 'sort_order' => $f['sort_order'])));
					$this->input_fields[$filter_prefix.$index] = $filter_prefix.$index;
				}
			}
		}

		foreach ($this->stock_statuses as $ss) {
			if (isset($product['stock_status_id']) && isset($product['stock_status_id']) && $product['stock_status_id'] == $ss['stock_status_id']) {
				$product['stock_status_id'] = $ss['name'];
			}
		}

		$product['price'] = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

		$product['keyword'] = $this->model_feed_any_feed_pro->getSeoKeyword($product['product_id']);
		$product['url'] = html_entity_decode($this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'));

		if ($product['image']) {
			$product['image'] = HTTP_SERVER . "image/" . str_replace(' ', '%20', $product['image']);
		} else {
			$product['image'] = '';
		}
		return array('product' => $product, 'fields' => $fields);
	}

	private function getFeedRow($settings, $fields, $product) {
		$row_output = '';

		if (isset($settings['store'])) {
			$prod_store_ids = $this->model_feed_any_feed_pro->getProductMultiStoreIDs($product['product_id']);
			$found = false;
			foreach ($prod_store_ids as $store_id) {
				if (in_array($store_id, $settings['store']))
					$found = true;
			}
			if (!$found)
				return '';
		}

		$result = $this->setupProduct($product, $fields);
		$product = $result['product'];
		$fields = $result['fields'];

		$prod = array();

		//skip the special fields that have their own field lists
		$special_fields = array('Categories', 'Options', 'Attributes', 'Related Products', 'Special Price');

		$sorted_fields = array();
		foreach ($fields as $key => $value) {
			foreach ($value['settings'] as $k => $s) {
				$new_field_array = array('settings' => array());
				$new_field_array['settings'] = array($k => $s);
				array_splice($sorted_fields, (int)$s["sort_order"], 0, array(array($key => $new_field_array)));
			}
		}
		//compare all the product fields, to the fields in the AnyFeed settings. Add appropriate ones
		$headings = array();
		foreach($sorted_fields as $key => $value)
		{
			
			$key = array_keys($value)[0];
			$value = $value[$key];

			if (!in_array($key, $special_fields) && isset($this->input_fields[$key]))
			{
				foreach($value['settings'] as $field_settings)
				{
					if($key == 'EAN' || $key == 'MPN'){
						$fieldName = strtolower($key);
						$any_feed_val = $this->getProdAnyFeedField($product['product_id'], $fieldName, array_values($settings['store'])[0]);
						$prod_val = $any_feed_val ? $any_feed_val : $product[$this->input_fields[$key]];
					}elseif($key == 'URL' && array_keys($settings['store'])[0] !== 'Default'){
						$new_url = $this->getStoreURL($product['product_id'], array_values($settings['store'])[0]);
						$prod_val =  $new_url ? $new_url . 'index.php?route=product/product&product_id=' . $product['product_id'] : '';
					}elseif($key == 'Image' && array_keys($settings['store'])[0] !== 'Default'){
						$new_url = $this->getStoreURL($product['product_id'], array_values($settings['store'])[0]);
						$prod_val = str_replace(HTTP_SERVER, $new_url, $product['image']);
					}else{
						$prod_val = $this->getProdField($product, $key, $field_settings);
						$prod_val = str_replace('&#039;', "'", $prod_val);
					}

					if (isset($field_settings['Strip HTML']['value'])) {
						$prod_val = strip_tags(html_entity_decode($prod_val));
					}

					if (!empty($field_settings['Rules']['rules'])) {
						foreach ($field_settings['Rules']['rules'] as $rule) {
							if ($this->model_feed_any_feed_pro->ruleMatch($prod_val, $rule, $key)) {
								if ($rule['rule_export_type'] == 'skip') {
									return '';
								} else {
									$prod_val = $this->processRule($rule, $prod_val, $product, $key, $settings);
								}
							}
						}
					}

					$valid_heading = $this->validateHeader($field_settings['Field Name']['value'], $key);
					$headings[] = $valid_heading;

					if (isset($field_settings['Unit']) && is_float($prod_val)) {
						$pre = '';
						$post = '';
						$prod_val = preg_replace('/[^0-9\.,]/', '', $this->currency->format($prod_val, $settings['currency']));
						if (!empty($field_settings['Unit']['value'])) {
							$unit_val = $field_settings['Unit']['value'];
						} else {
							$currency = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
							$unit_val = ($currency['symbol_left'] !== "") ? $currency['symbol_left'] : $currency['symbol_right'];
						}
						if (isset($field_settings['Unit']['location'])) {
							if ($field_settings['Unit']['location'] === 'after') {
								$post = $unit_val;
							} else if ($field_settings['Unit']['location'] === 'before') {
								$pre = $unit_val;
							}
						}
						$prod_val = $pre.(String)$prod_val.$post;
					}

					//Z code START
					$prod_val = htmlspecialchars_decode(trim(preg_replace('/\s+/', ' ', $prod_val)));
					$prod_val = htmlspecialchars_decode($prod_val);
					//Z code END
					$prod[$valid_heading] = $prod_val;
				}
			}
			else
			{
				if ($key == 'Special Price' && isset($fields['Special Price'])) {
					foreach ($value['settings'] as $field_settings) {
						$prod_val = '';

						$id_customer_group = $this->config->get('config_customer_group_id');
						if (isset($field_settings['Customer Group']['value'])) {
							$id_customer_group = $field_settings['Customer Group']['value'];
						}

						$product_specials = $this->model_feed_any_feed_pro->getProductSpecials($product['product_id'], $id_customer_group);

						if (!empty($product_specials) && isset($product_specials[0]['price'])) {
							$special_price = $product_specials[0]['price'];
							$product['raw_special_price'] = $this->currency->format($this->tax->calculate($special_price, $product['tax_class_id'], $this->config->get('config_tax')), $this->currency_id, '', false);

							$prod_val = $this->currency->format($this->tax->calculate($special_price, $product['tax_class_id'], $this->config->get('config_tax')), $this->currency_id);

							if (!empty($field_settings['Rules']['rules'])) {
								// Loop every rule
								foreach ($field_settings['Rules']['rules'] as $rule) {
									if ($this->model_feed_any_feed_pro->ruleMatch($prod_val, $rule, $key)) {
										if ($rule['rule_export_type'] == 'skip') {
											return '';
										} else {
											$prod_val = $this->processRule($rule, $prod_val, $product, $key, $settings);
										}
									}
								}
							}
						}


						$valid_heading = $this->validateHeader($field_settings['Field Name']['value'], $key);
						$headings[] = $valid_heading;

						if (isset($field_settings['Unit']) && is_float($prod_val)) {
							$pre = '';
							$post = '';
							$prod_val = preg_replace('/[^0-9\.,]/', '', $this->currency->format($prod_val, $settings['currency']));
							if (!empty($field_settings['Unit']['value'])) {
								$unit_val = $field_settings['Unit']['value'];
							} else {
								$currency = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
								$unit_val = ($currency['symbol_left'] !== "") ? $currency['symbol_left'] : $currency['symbol_right'];
							}
							if (isset($field_settings['Unit']['location'])) {
								if ($field_settings['Unit']['location'] === 'after') {
									$post = $unit_val;
								} else if ($field_settings['Unit']['location'] === 'before') {
									$pre = $unit_val;
								}
							}
							$prod_val = $pre.(String)$prod_val.$post;
						}

						$prod[$valid_heading] = $prod_val;
					}
				}
				if ($key == 'Related Products' && isset($fields['Related Products'])) {
					foreach ($value['settings'] as $field_settings) {
						$prod_val = '';
						if (!empty($product['related_prods_array'])) {
							$related_ids = array();
							foreach ($product['related_prods_array'] as $related_prod) {
								$related_ids[] = $related_prod[$field_settings['Identifying Field']['value']];
							}
							$prod_val = implode(':', $related_ids);
						}
						$valid_heading = $this->validateHeader($field_settings['Field Name']['value'], $key);
						$headings[] = $valid_heading;

						$prod[$valid_heading] = $prod_val;
					}
				}
				//if categories are to export get all categories
				if($key == 'Categories' && isset($fields['Categories'])) {
					// get product categories
					$category_list = $this->getCategoryList($product['product_id']);
					foreach($value['settings'] as $f) {
						// Create copy of the array in case they have multiple categories in one feed
						$tmp_category_list = array_values($category_list);

						// Custom Name Mapping
						if (isset($f['Category Names']['enabled']) && $f['Category Names']['enabled'] == 1) {
							foreach ($tmp_category_list as $cat_key => $cat) {
								// If this isn't set it probably means that some categories have been deleted
								if (isset($f['Category Names']['names'][$cat['id']])) {
									if (!empty($f['Category Names']['names'][$cat['id']]['new'])){
										$tmp_category_list[$cat_key]['name'] = $f['Category Names']['names'][$cat['id']]['new'];
									}
								}
							}
						}

						// Column name for category
						$category_prefix = !empty($f['Field Name']['value']) ? $f['Field Name']['value'] : 'Category';
						$filtered_categories = array();

						if (!empty($f['Rules']['rules'])) {
							// Loop every rule
							foreach ($f['Rules']['rules'] as $rule) {
								// Loop every category for each rule
								$matchcount = 0;
								for($cat_index = 0; $cat_index < count($category_list); $cat_index++) {
									if (isset($tmp_category_list[$cat_index])) {
										// Rule is contain category or nocontain category AND type is SKIP
										if($rule['rule_export_type'] == 'skip' && ($rule['rule_comparator'] == 'nocontain' || $rule['rule_comparator'] == 'contain')){
											$tempRule = $rule['rule_comparator'];
											$rule['rule_comparator'] = 'eq'; //Change rule to eq, so it compares the categories
											// Compare current category with the one in the rule
											$response = $this->model_feed_any_feed_pro->ruleMatch($tmp_category_list[$cat_index]['name'], $rule, $key) ? 1 : 0;
											
											if($response){
												$matchcount ++; //Increase matches if rule returns true
												$filtered_categories[] = $rule['rule_comparator_value'];
											}
											$rule['rule_comparator'] = $tempRule;
										}else{
											if ($this->model_feed_any_feed_pro->ruleMatch($tmp_category_list[$cat_index]['name'], $rule, $key)) {
												if ($rule['rule_export_type'] == 'skip') {
													return '';
												} else {
													$tmp_category_list[$cat_index]['name'] = $this->processRule($rule, $tmp_category_list[$cat_index]['name'], $product, $key, $settings);
												}
											}
										}
									}
								}

								// Rule is contain category or nocontain category AND type is SKIP
								// Check matchcount and skip product if needed
								if($rule['rule_export_type'] == 'skip' && (($rule['rule_comparator'] == 'nocontain' && !$matchcount) || ($rule['rule_comparator'] == 'contain' && $matchcount))){
									return '';
								}
							}
						}
						
						$prod_cats = array();
						for($cat_index = 0; $cat_index < count($category_list); $cat_index++) {
							$readable_index = $cat_index + 1;
							if($filtered_categories){
								if(in_array($tmp_category_list[$cat_index]['name'], $filtered_categories)){
									$prod_cats[] = $tmp_category_list[$cat_index]['name'];
								}
							}else{
								$prod_cats[] = (isset($tmp_category_list[$cat_index]['name'])) ? $tmp_category_list[$cat_index]['name'] : ' ';
							}
						}
						$prod['Category'] = implode(' > ', $prod_cats);
					}
					$headings[] =  $category_prefix;
				} // END Categories

				//if attributes are selected, export each product attribute
				if($key == 'Attributes' && isset($fields['Attributes'])) {

					$attribute_groups = $this->model_catalog_product->getProductAttributes($product['product_id']);
					$total_attributes = $this->model_feed_any_feed_pro->getAttributes();

					foreach($value['settings'] as $f) {

						$attribute_list = array();
						foreach($attribute_groups as $attribute) {
							foreach($attribute['attribute'] as $values) {
								foreach($total_attributes as $total_attribute) {

									$original_name = $total_attribute['name'];

									//tip compatibility: add blank values to attributes that exist but not assigned to product
									if ($values['name'] == $total_attribute['name']){
										$attribute_name = $values['name'];


										if (isset($f['Attribute Names']['enabled']) && $f['Attribute Names']['enabled'] == 1) {
											if (isset($f['Attribute Names']['names'])) {
												foreach($f['Attribute Names']['names'] as $custom_attribute_names) {
													if (!empty($custom_attribute_names['new']) && ($custom_attribute_names['original'] == $original_name)){
														$attribute_name = $custom_attribute_names['new'];
														break;
													}
												}
											}
										}

										$attribute_list[$attribute_name] = $values['text'];
									} else if (!isset($attribute_list[$total_attribute['name']])) {
										$attribute_list[$total_attribute['name']] = ' ';
									}
								}
							}
						}

						//Z_code START
						if(empty($attribute_groups)){
							foreach($total_attributes as $total_attribute) {
								$attribute_list[$total_attribute['name']] = ' ';
							}
						}
						//Z_code END

						if (!empty($f['Rules']['rules'])) {
							// Loop every rule
							foreach ($f['Rules']['rules'] as $rule) {
								foreach ($attribute_list as $key => $attribute_val) {
									if ($this->model_feed_any_feed_pro->ruleMatch($attribute_val, $rule, $key)) {
										if ($rule['rule_export_type'] == 'skip') {
											return '';
										} else {
											$attribute_list[$key] = $this->processRule($rule, $attribute_val, $product, $key, $settings);
										}
									}
								}
							}
						}

						foreach($attribute_list as $key=>$value) {
							$headings[] = $key;
							$prod[$key] = $value;
						}
					}
				} // END Attributes

				//if options are selected, export each product option
				if($key == 'Options' && isset($fields['Options'])) {
					$options = $this->model_catalog_product->getProductOptions($product['product_id']);
					$option_list = array();
					foreach($value['settings'] as $f) {
						foreach($options as $option) {
							$option_types = array('checkbox', 'select', 'radio');
							if (in_array($option['type'], $option_types)) {
								foreach($option['product_option_value'] as $values) {

									//set prefixes to blank for +
									($values['price_prefix'] != '-') ? $values['price_prefix'] = '' : $values['price_prefix'] = '-';
									($values['weight_prefix'] != '-') ? $values['weight_prefix'] = '' : $values['weight_prefix'] = '-';

									if(isset($option_list[$option['name']])) {

										$option_list[$option['name']] .= '|' . $values['name'].":".$values['quantity'].":".$values['price_prefix'].number_format($values['price'], 2, '.', '').":".$values['weight_prefix'].number_format($values['weight'], 2, '.', '').":".$option['required'].":".$option['type'];
									} else {
										$option_list[$option['name']] = $values['name'].":".$values['quantity'].":".$values['price_prefix'].number_format($values['price'], 2, '.', '').":".$values['weight_prefix'].number_format($values['weight'], 2, '.', '').":".$option['required'].":".$option['type'];
									}
								}
							}
						}

						foreach($this->max_opt_names as $opt_name) {
							$original_name = $opt_name;

							if (isset($f['Option Names']['enabled']) && $f['Option Names']['enabled'] == 1) {
								if (isset($f['Option Names']['names'])) {
									foreach($f['Option Names']['names'] as $custom_option_names) {
										if (!empty($custom_option_names['new']) && ($custom_option_names['original'] == $original_name)){
											$opt_name = $custom_option_names['new'];
											break;
										}
									}
								}
							}

							/*
							Do rules for options make sense?
							if (!empty($f['Rules']['rules'])) {
								foreach ($f['Rules']['rules'] as $rule) {
									if ($this->model_feed_any_feed_pro->ruleMatch($opt_name, $rule, $key)) {
										if ($rule['rule_export_type'] == 'skip') {
											return '';
										} else {
											$opt_name = $this->processRule($rule, $opt_name, $product, $key, $settings);
										}
									}
								}
							}*/

							$headings[] = $opt_name;

							$option_exists_for_prod = false;
							foreach($option_list as $key=>$value) {

								if ($key == $original_name) {
									$prod[$opt_name] = $value;
									$option_exists_for_prod = true;
									break;
								}
							}
							if (!$option_exists_for_prod) {
								$prod[$opt_name] = '';
							}
						}
					}
				} // END Options

			} // END Special Fields
		} // END Fields

		foreach($headings as $key) {
			$this->names[$key] = $key;
		}

		if ($settings['type'] == 'CSV') {
			$row_output .= $this->outputCSV($prod, $settings['delimiter']);
		}
		elseif ($settings['type'] == 'XML') {
			$row_output .= $this->outputXML($prod, $settings);
		}
		else {
			$row_output .= $this->outputTXT($prod, $settings['delimiter']);
		}
		return $row_output;
	}

	private function getCategoryList($product_id) {
		$categories = $this->model_catalog_product->getCategories($product_id);
		$thiscat = '';
		$category_list = array();
		foreach ($categories as $category) {

			$catpath = $this->getCatInfo($category['category_id']);
			if ($catpath) {
				$thiscat = '';
				foreach (explode('_', $catpath) as $pathid) {
					$cat = $this->model_catalog_category->getCategory($pathid);
					if ($cat) {
						if (!$thiscat) {
							$thiscat = $cat['name'];
						} else {
							$thiscat .= ' > ' . $cat['name'];
						}
					}
				}
			}
			$category_list[] = array('name' => $thiscat, 'id' => $category['category_id']);
		}

		//remove all categories that are sub sets of the main category path
		for($i=0; $i <= count($category_list);$i++){
			if(!empty($category_list[$i+1]) && $category_list[$i+1]['name'] != '' && !empty($category_list[$i]) && $category_list[$i]['name'] != '') {
				if(strstr($category_list[$i+1]['name'], $category_list[$i]['name'])) {
					unset($category_list[$i]);
				}
			}
		}

		return $category_list;
	}

	private function processRule($rule, $prod_val, $product, $key, $settings) {
		switch ($rule['rule_export_type'])
		{
			case 'txt':
				$prod_val = $rule['rule_export'];
				break;
			case 'slct':
				$prod_val = $product[$rule['rule_export']];
				break;
			case 'math':
				if (in_array($this->input_fields[$key], array('price', 'special_price'))) {
					$prod_val = $this->applyMath($product['raw_'.$this->input_fields[$key]], $rule['math_operator'], $rule['rule_export']);
					$prod_val = $this->currency->format($prod_val, $settings['currency']);
				}
				else
					$prod_val = $this->applyMath($prod_val, $rule['math_operator'], $rule['rule_export']);
				break;
			case 'append':
				$prod_val = $prod_val . $rule['rule_export'];
				break;
		}

		return $prod_val;
	}

	protected function outputXML($product, $settings) {
		$result = "<product>\n";
		foreach ($product as $key => &$value) {
			if($settings['cdata'] != '1') {
				$value = str_replace('&', '&amp;', $value);
				$value = str_replace('>', '&gt;', $value);
				$value = str_replace('<', '&lt;', $value);
			} else {
				$value = "<![CDATA[" . $value . "]]>";
			}

			if(is_numeric($key)) {
				$key = 'No_' . $key;
			}

			$valid_key = str_replace(' ', '_', $key);
			$valid_key = preg_replace('/[^A-Z0-9_:]/i', '', $valid_key);
			$value = "<" . $valid_key . ">" . $value . "</" . $valid_key . ">";
		}
		$result .= join("\n", $product);
		$result .= "\n</product>\n";
		return $result;
	}

	protected function outputCSV($product, $delim) {
		foreach ($product as $key => $value) {
			$value = str_replace('"', '""', $value);
			$product[$key] = '"' . $value . '"';
		}
		$result = join($delim, $product);
		$result .= "\n";
		return $result;
	}

	protected function outputTXT($product, $delim) {
		$result = join($delim, $product);
		$result .= "\n";
		return $result;
	}

	protected function getCatInfo($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id);

		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}

			$path = $this->getCatInfo($category_info['parent_id'], $new_path);

			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}

	public function generateFeed($profile_name='custom') {
		$this->load->model('feed/any_feed_pro');
		if(isset($this->request->get['profile'])){
			$profile_name = $this->request->get['profile'];
		}
		$profile = json_encode($this->model_feed_any_feed_pro->getProfile($profile_name));
		echo $profile;
		return;
	}

	private function validateHeader($header_value, $header) {
		//Checking to see if the Name choosen by the Client is not a space

		if($header_value === '') {
			$header_value = $header;
		}
		return $header_value;
	}
}
?>
