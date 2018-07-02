<?php
#####################################################################################
#  Module AnyFeed PRO for Opencart 2.0.x From HostJars opencart.hostjars.com 		#
#####################################################################################

class ModelFeedAnyFeedPro extends Model {
	private $version = '0.1';

	public function createTable() {
		$this->log->write('createTable');
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "hj_any_feed_pro_feeds (id INT(11) AUTO_INCREMENT, name VARCHAR(256), settings MEDIUMBLOB, version VARCHAR(64), preset INT(1), fields MEDIUMBLOB, PRIMARY KEY (id))");
		
		$this->db->query("CREATE TABLE " . DB_PREFIX . "hj_any_feed_pro_feeds_product (id INT(11) AUTO_INCREMENT, product_id INT(11) NOT NULL, store_id INT(11) NOT NULL, ean VARCHAR(14) NOT NULL, mpn VARCHAR(64) NOT NULL, PRIMARY KEY (id))");
		
		$this->db->query("ALTER TABLE " . DB_PREFIX . "hj_any_feed_pro_feeds_product ADD KEY product_id (product_id), ADD KEY store_id (store_id)");
		
		$this->insertPresetFeeds();
	}

	public function deleteTable() {
		$this->log->write('deleteTable');
		$this->db->query("DROP TABLE " . DB_PREFIX . "hj_any_feed_pro_feeds");
		$this->db->query("DROP TABLE " . DB_PREFIX . "hj_any_feed_pro_feeds_product");
	}

	public function getProfile($profile, $preset=0) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "hj_any_feed_pro_feeds WHERE `name` = '". $this->db->escape($profile) ."' AND `preset` = '". $this->db->escape($preset). "'");
		return (isset($query->row['id'])) ?	$query->row : 0;
	}

	public function save($feeds) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "hj_any_feed_pro_feeds WHERE `preset` != 1");

		//build the sql and insert each preset feed
		foreach($feeds as $name => $feed) {
			$sql = 'INSERT INTO ' . DB_PREFIX . 'hj_any_feed_pro_feeds SET ';
			$values = array();
			foreach ($feed as $column => $value) {
				if($column == 'version' || $column == 'name' || $column == 'preset') {
					$values[] = '`' . $column . "`='" . $this->db->escape($value) . "'";
				} else {
					if ($column == 'settings' && !isset($value['enable'])){
						$value['enable'] = 0;
					}
					$values[] = '`' . $column . "`='" . $this->db->escape(json_encode($value)) . "'";
				}
			}
			$values[] = "`preset`='0'";
			$values[] = "`version`='" . $this->version . "'";
			$sql .= implode(',', $values);
			$this->db->query($sql);
		}
	}

	public function getProfiles() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "hj_any_feed_pro_feeds WHERE `preset` = '0'");
		return (isset($query->row['id'])) ?	$query->rows : array();
	}

	public function getPresets() {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "hj_any_feed_pro_feeds WHERE `preset` = 1");
		return (isset($query->row['id'])) ?	$query->rows : array();
	}

	public function deleteProfile($profile) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "hj_any_feed_pro_feeds WHERE `name` = '". $this->db->escape($profile) ."'");
	}

	private function insertPresetFeeds () {

		//get the preset fields
		$preset_feeds = $this->getPresetFeeds();
		//echo "<pre>"; print_r($preset_feeds); echo "</pre>";die();

		//build the sql and insert each preset feed
		foreach($preset_feeds as $name => $feed) {
			$sql = 'INSERT INTO ' . DB_PREFIX . 'hj_any_feed_pro_feeds SET ';
			$values = array();
			foreach ($feed as $column => $value) {
				if($column == 'version' || $column == 'name' || $column == 'preset') {
					$values[] = '`' . $column . "`='" . $this->db->escape($value) . "'";
				} else {
					$values[] = '`' . $column . "`='" . $this->db->escape(json_encode($value)) . "'";
				}
			}
			$values[] = "`preset`='1'";
			$sql .= implode(',', $values);
			$this->db->query($sql);
		};
	}

	public function getPresetFeeds () {
		//array format
		/*
			'Feed Name' => array(
				'version' => 'feed version',
				'settings' => array (
					'type' => 'feed type csv/xml',
					'delimiter' => 'feed delimiter (, | ^ etc),
					'cdata' => 'use cdata tags for xml'
				),
				'fields' => array (
					'Field Display Name' => array (
						'field setting id' => 'setting value',
					),
				)

			)
		 */
		$preset_feeds = array(
			'Empty' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => ',',
					'cdata' => '0',
					'url' => '',
					'enable' => 1,
				),
				'fields' => array (
				),
			),
			'Basic' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => ',',
					'cdata' => '0',
					'url' => '',
					'enable' => 1,
				),
				'fields' => array (
					'Name' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'product_name',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 0,
							),
						),
					),
					'Description' => array(
						'settings' => array (
							array(
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'description',
								),
								'Strip HTML' => array(
									'type'=>'checkbox',
									'name' => 'strip_html',
									'value' => '1',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 1,
							),
						),
					),
					'Model' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'model',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 2,
							),
						),
					),
					'Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 3,
							),
						),

					),
					'Special Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'special_price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 4,
							),
						),
					),
					'Image' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'image',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 5,
							),
						),
					),
					'Categories' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'categories',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 6,
							),
						),
					),
				),
			),
			'Bing' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => '\t',
					'cdata' => '0',
					'url' => '',
					'enable' => 1,
					'filename' => 'bingshopping',
				),
				'fields' => array (
					'SKU' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'MPID',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 0,
							),
						),
					),
					'Name' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Title',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 1,
							),
						),
					),
					'MPN' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'MPN',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 2,
							),
						),
					),
					'UPC' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'UPC',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 3,
							),
						),
					),
					'URL' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'UPC',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 4,
							),
						),
					),
					'Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 5,
							),
						),

					),
					'Description' => array(
						'settings' => array (
							array(
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'description',
								),
								'Strip HTML' => array(
									'type'=>'checkbox',
									'name' => 'strip_html',
									'value' => '1',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 6,
							),
						),
					),
					'Image' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'ImageURL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 7,
							),
						),
					),
					'Categories' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'MerchantCategory',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 8,
							),
						),
					),
				),
			),
			'PriceGrabber' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => ',',
					'cdata' => '0',
					'enable' => 1,
					'filename' => 'PriceGrabber',
				),
				'fields' => array (
					'SKU' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Retsku',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 0,
							),
						),
					),
					'Name' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Product Title',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 1,
							),
						),
					),
					'Description' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Detailed Description',
								),
								'Strip HTML' => array(
									'type'=>'checkbox',
									'name' => 'strip_html',
									'value' => '1',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 2,
							),
						),
					),
					'Categories' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Categorization',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 3,
							),
						),
					),
					'URL' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Product URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 4,
							),
						),

					),
					'Image' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Primary Image URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 5,
							),
						),
					),
					'Additional Images' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Additional Image URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 6,
							),
						),
					),
					'Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Selling Price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 7,
							),
						),
					),
					'Custom' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Condition',
								),
								'Field Value' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'New',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 8,
							),
						),
					),
					'Quantity' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Availability',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
									'rules' => array(
										array(
											'rule_comparator' => 'lt',
											'rule_comparator_value' => '1',
											'rule_export_type' => 'txt',
											'rule_export' => '',
										),
										array(
											'rule_comparator' => 'gt',
											'rule_comparator_value' => '0',
											'rule_export_type' => 'txt',
											'rule_export' => 'Yes',
										),
									),
								),
								'sort_order' => 9,
							),
						),
					),
					'Manufacturer' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Manufacturer Name',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 10,
							),
						),
					),
					'MPN' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Manufacturer Part Number',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 11,
							),
						),
					),
					'Options' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'options',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 12,
							),
						),
					),
				),
			),
			'ShopZilla' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => ',',
					'cdata' => '0',
					'enable' => 1,
					'filename' => 'ShopZilla',
				),
				'fields' => array (
					'SKU' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Unique ID',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 0,
							),
						),
					),
					'Name' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Title',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 1,
							),
						),
					),
					'Description' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Description',
								),
								'Strip HTML' => array(
									'type'=>'checkbox',
									'name' => 'strip_html',
									'value' => '1',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 2,
							),
						),
					),
					'Categories' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Categorization',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 3,
							),
						),
					),
					'URL' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Product URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 4,
							),
						),

					),
					'Image' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Image URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 5,
							),
						),
					),
					'Additional Images' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Additional Image URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 6,
							),
						),
					),
					'Custom' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Condition',
								),
								'Field Value' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'New',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 7,
							),
						),
					),
					'Quantity' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Availability',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
									'rules' => array(
										array(
											'rule_comparator' => 'lt',
											'rule_comparator_value' => '1',
											'rule_export_type' => 'txt',
											'rule_export' => 'Out of Stock',
										),
										array(
											'rule_comparator' => 'gt',
											'rule_comparator_value' => '0',
											'rule_export_type' => 'txt',
											'rule_export' => 'In Stock',
										),
									),
								),
								'sort_order' => 8,
							),
						),
					),
					'Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Current Price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 9,
							),
						),
					),
					'Manufacturer' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Brand',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 10,
							),
						),
					),
					'UPC' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'GTIN',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 11,
							),
						),
					),
					'MPN' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'MPN',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 12,
							),
						),
					),
					'Options' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'options',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 13,
							),
						),
					),

				),
			),
			'Google Shopping' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => ',',
					'cdata' => '0',
					'enable' => 1,
					'filename' => 'GoogleFeed',
				),
				'fields' => array (
					'SKU' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'id',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 0,
							),
						),
					),
					'Name' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'title',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 1,
							),
						),
					),
					'Description' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'description',
								),
								'Strip HTML' => array(
									'type'=>'checkbox',
									'name' => 'strip_html',
									'value' => '1',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 2,
							),
						),
					),
					'Categories' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'google_product_category',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 3,
							),
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'product_type',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 4,
							),
						),
					),
					'URL' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'link',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 5,
							),
						),

					),
					'Image' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'image_link',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 6,
							),
						),
					),
					'Additional Images' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'additional_image_link',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 7,
							),
						),
					),
					'Custom' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Condition',
								),
								'Field Value' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'New',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 8,
							),
						),
					),
					'Quantity' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'availability',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
									'rules' => array(
										array(
											'rule_comparator' => 'lt',
											'rule_comparator_value' => '1',
											'rule_export_type' => 'txt',
											'rule_export' => 'out of stock',
										),
										array(
											'rule_comparator' => 'gt',
											'rule_comparator_value' => '0',
											'rule_export_type' => 'txt',
											'rule_export' => 'in stock',
										),
									),
								),
								'sort_order' => 9,
							),
						),
					),
					'Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 10,
							),
						),
					),
					'Manufacturer' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'brand',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 11,
							),
						),
					),
					'UPC' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'gtin',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 12,
							),
						),
					),
					'MPN' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'mpn',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 13,
							),
						),
					),
				),
			),
			'Twenga' => array(
				'version' => '0.1',
				'settings' => array(
					'type' => 'CSV',
					'delimiter' => ',',
					'cdata' => '0',
					'enable' => 1,
					'filename' => 'Twenga',
				),
				'fields' => array (
					'SKU' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Product ID',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 0,
							),
						),
					),
					'Name' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Title',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 1,
							),
						),
					),
					'Description' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Long Description',
								),
								'Strip HTML' => array(
									'type'=>'checkbox',
									'name' => 'strip_html',
									'value' => '1',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 2,
							),
						),
					),
					'Categories' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Category',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'Category Names' => array(
									'type' => 'name_map',
									'name' => 'Categories',
								),
								'sort_order' => 3,
							),
						),
					),
					'URL' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Product URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 4,
							),
						),

					),
					'Image' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Big Image URL',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 5,
							),
						),
					),
					'Price' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Price',
								),
								'Unit' => array(
									'type'=>'unit',
									'name'=>'price_unit'
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 6,
							),
						),
					),
					'Manufacturer' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'Manufacturer',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 7,
							),
						),
					),
					'UPC' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'UPC',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 8,
							),
						),
					),
					'MPN' => array(
						'settings' => array (
							array (
								'Field Name' => array(
									'type'=>'text',
									'name' => 'name',
									'value' => 'MPN',
								),
								'Rules' => array (
									'type'=>'rule',
									'name'=>'rule',
								),
								'sort_order' => 9,
							),
						),
					),
				),
			),
		);

		foreach ($preset_feeds as $key=>$value) {
			$name = $this->makeStandardName($key);
			$preset_feeds[$key]['name'] = $name;
		}
		return $preset_feeds;
	}

	public function makeStandardName($name) {
		$name = strtolower($name);
		$name = str_replace(' ', '_', $name);
		return $name;
	}
	public function makeNiceName($name) {
		$name = ucwords($name);
		$name = str_replace('_', ' ', $name);
		return $name;
	}
	public function getVersion() {
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('sale/order');
		if(!method_exists($this->model_sale_order, 'getOrderOption'))	{
			return '1.5.1';
		}
		else{
			return '1.5.2';
		}
	}
	public function getProducts($data = array(), $lang_id = '') {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		if (!empty($data['filter_category_id'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
		}

		$sql .= " WHERE pd.language_id = '" . (!empty($lang_id) ? (int)$lang_id : (int)$this->config->get('config_language_id')) . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}


?>
