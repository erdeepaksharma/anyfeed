<?php
############################################################################################
#  AnyFeed PRO Module for Opencart 2.0.x from HostJars http://opencart.hostjars.com    	   #
############################################################################################
class ModelFeedAnyFeedPro extends Model {

	public function getProductSpecials($product_id, $id_customer_group) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND ((`date_start` = '0000-00-00' OR `date_start` < NOW()) AND (`date_end` = '0000-00=00' OR `date_end` > NOW())) AND `customer_group_id` = '" . $this->db->escape($id_customer_group) . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function rulesMatch($prod_val, $rules=array(), $field_name) {

		$results = array('rules_match' => false, 'matching_rule' => array());
		foreach ($rules as $rule) {
			$match = $this->ruleMatch($prod_val, $rule, $field_name);
			if ($match) {
				$results['rules_match'] = true;
				$results['matching_rule'] = $rule;
			}
		}
		return $results;
	}

	public function ruleMatch($prod_val, $rule, $field_name, $case_insensitive=true) {
		$currency_fields = array('Price', 'Special Price');
		if (in_array($field_name, $currency_fields)) {
			$prod_val = (float)preg_replace("/([^0-9\\.])/i", "", $prod_val);
		}

		if ($case_insensitive) {
			$prod_val = strtolower($prod_val);
			$rule['rule_comparator_value'] = strtolower($rule['rule_comparator_value']);
		}

		$match = false;
		switch ($rule['rule_comparator']) {
			case 'noeq':
				$match = ($prod_val != html_entity_decode($rule['rule_comparator_value']));
				break;
			case 'lt':
				//Z code START
				if((is_numeric($prod_val) && is_numeric($rule['rule_comparator_value'])) && ((float)$prod_val < (float)$rule['rule_comparator_value'])){
					$match = true;
				}
				// Match fails if one if them isn't a number and length is not less than rule
				elseif(!is_numeric($prod_val) && strlen(trim($prod_val)) < $rule['rule_comparator_value']){
					$match = true;
				}
				//Z code END
				break;
			case 'gt':
				//Z code START
				if((is_numeric($prod_val) && is_numeric($rule['rule_comparator_value'])) && ((float)$prod_val > (float)$rule['rule_comparator_value'])){
					$match = true;
				}
				// Match fails if one if them isn't a number and length is not more than rule
				elseif(!is_numeric($prod_val) && strlen(trim($prod_val)) > $rule['rule_comparator_value']){
					$match = true;
				}
				//Z code END
				break;
			case 'eq':
				$match = ($prod_val == html_entity_decode($rule['rule_comparator_value']));
				break;
			case 'contain':
				$needle = ($rule['rule_comparator_value'] !== '') ? $rule['rule_comparator_value'] : ' ';
				$match = (strpos($prod_val, $needle) !== false);
				break;
			case 'nocontain':
				$needle = ($rule['rule_comparator_value'] !== '') ? $rule['rule_comparator_value'] : ' ';
				$match = (strpos($prod_val, $needle) === false);
				break;
		}
		return $match;
	}

	public function getStockStatus() {
		$query = $this->db->query('SELECT stock_status_id, name FROM ' . DB_PREFIX . "stock_status WHERE language_id='" . (int)$this->config->get('config_language_id') . "'");
		return $query->rows;
	}

	public function getProfile($profile) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "hj_any_feed_pro_feeds WHERE `name` = '". $this->db->escape($profile) ."' AND `preset` = '0'");
		return (isset($query->row['id'])) ?	$query->row : 0;
	}

	public function getSeoKeyword($product_id){
		$query = $this->db->query("SELECT `keyword` FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . $this->db->escape($product_id) . "'");
		return (isset($query->row['keyword'])) ? $query->row['keyword'] : '';
	}

	public function getProductMultiStoreIDs($prod_id) {
		$sql = "SELECT `store_id` from `" . DB_PREFIX . "product_to_store` WHERE `product_id`=" . $prod_id;
		$query = $this->db->query($sql);
		$store_ids = array();
		if (isset($query->rows)) {
			foreach ($query->rows as $row) {
				$store_ids[] = $row['store_id'];
			}
		}
		return $store_ids;
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

    public function getProductFilters($product_id) {
        $filters_ids = $this->db->query("
			SELECT f.`filter_id`, f.`filter_group_id`  FROM " . DB_PREFIX . "product_filter AS pf
			LEFT JOIN " . DB_PREFIX . "filter AS f
			    ON pf.`filter_id` = f.`filter_id`
			WHERE pf.`product_id` = '" . (int)$product_id. "'");

        $filters = array();
        foreach ($filters_ids->rows as $filters_id) {
            $filter_description = $this->db->query("
                SELECT fd.`name` FROM " . DB_PREFIX . "filter_description AS fd
                WHERE fd.`filter_id` = '" . (int)$filters_id['filter_id'] . "'
                AND fd.`language_id` = '" . (int)$this->config->get('config_language_id'). "'");
            $filter_group_description = $this->db->query("
                SELECT fgd.`name` FROM " . DB_PREFIX . "filter_group_description AS fgd
                WHERE fgd.`filter_group_id` = '" . (int)$filters_id['filter_group_id'] . "'
                AND fgd.`language_id` = '" . (int)$this->config->get('config_language_id'). "'");

            $filters[] = $filter_group_description->row['name'].' > '.$filter_description->row['name'];
        }
        return $filters;
    }

	public function getAttributes() { 
		$query = $this->db->query("
			SELECT ad.name
			FROM `" . DB_PREFIX . "product_attribute` AS pa
			INNER JOIN `" . DB_PREFIX . "attribute_description` AS ad
			ON pa.attribute_id = ad.attribute_id
			GROUP BY pa.attribute_id");
		return ($query->num_rows > 0) ? $query->rows : '';
	}

	public function getMaxCategories() {
		$query = $this->db->query("SELECT count(*) AS maximum FROM `" . DB_PREFIX . "product_to_category` GROUP BY `product_id` ORDER BY count(*) DESC LIMIT 0,1");
		return (isset($query->row['maximum'])) ? $query->row['maximum'] : '';
	}

	public function getProductOptionNames() {
		$query = $this->db->query("SELECT `" . DB_PREFIX . "product_option`.`option_id`, `" . DB_PREFIX . "option_description`.`name` FROM `" . DB_PREFIX . "product_option` JOIN `" . DB_PREFIX . "option_description` on `" . DB_PREFIX . "product_option`.`option_id` = `" . DB_PREFIX . "option_description`.`option_id` GROUP BY `" . DB_PREFIX . "product_option`.`option_id`");
		$names = array();
		foreach ($query->rows as $row) {
			$names[] = $row['name'];
		}
		return $names;
	}

	public function getMaxAttributes() {
		$query = $this->db->query("SELECT count(*) AS maximum FROM `" . DB_PREFIX . "product_attribute` GROUP BY `product_id` ORDER BY count(*) DESC LIMIT 0,1");
		return (isset($query->row['maximum'])) ? $query->row['maximum'] : '';
	}

    public function getMaxFilters() {
        $query = $this->db->query("SELECT count(*) AS maximum FROM `" . DB_PREFIX . "product_filter` GROUP BY `product_id` ORDER BY count(*) DESC LIMIT 0,1");
        return (isset($query->row['maximum'])) ? $query->row['maximum'] : '';
    }

	public function getMaxImages() {
		$query = $this->db->query("SELECT count(*) AS maximum FROM `" . DB_PREFIX . "product_image` GROUP BY `product_id` ORDER BY count(*) DESC LIMIT 0,1");
		return (isset($query->row['maximum'])) ? $query->row['maximum'] : '';
	}

    public function isMijo(){
        $query = $this->db->query("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'mijo_extensions'");
        return ($query->num_rows > 0) ? true : false;
    }
}
?>