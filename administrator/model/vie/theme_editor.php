<?php
class ModelVieThemeEditor extends Model {
	const TABLE_THEME 		= 'vie_lte_theme';
	const TABLE_SKIN 		= 'vie_lte_skin';
	const TABLE_SKIN_OPTION = 'vie_lte_skin_option';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->model('setting/setting');
	}

	public function createTables() {
		$sqls = array(
			'
			CREATE TABLE `'. DB_PREFIX .'vie_lte_skin` (
			  `theme_id` varchar(64) NOT NULL,
			  `skin_id` varchar(64) NOT NULL,
			  `name` varchar(64) NOT NULL,
			  `root_skin_id` varchar(64) NOT NULL,
			  PRIMARY KEY (`theme_id`,`skin_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			',
			'
			CREATE TABLE `'. DB_PREFIX .'vie_lte_skin_option` (
			  `option` varchar(64) NOT NULL,
			  `theme_id` varchar(64) NOT NULL,
			  `skin_id` varchar(64) NOT NULL,
			  `value` text NOT NULL,
			  PRIMARY KEY (`option`,`theme_id`,`skin_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			',
			'
			CREATE TABLE `'. DB_PREFIX .'vie_lte_theme` (
			  `theme_id` varchar(64) NOT NULL,
			  `version` varchar(10) NOT NULL,
			  PRIMARY KEY (`theme_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			'
		);

		foreach ($sqls as $sql) {
			$this->db->query($sql);
		}

		return true;
	}

	public function dropTables() {
		$sqls = array(
			'DROP TABLE IF EXISTS `'. DB_PREFIX .'vie_lte_skin`',
			'DROP TABLE IF EXISTS `'. DB_PREFIX .'vie_lte_skin_option`',
			'DROP TABLE IF EXISTS `'. DB_PREFIX .'vie_lte_theme`',
		);

		foreach ($sqls as $sql) {
			$this->db->query($sql);
		}

		return true;
	}

	public function installTheme($store_id, $theme_id) {
		$theme_options = $this->loadThemeOptions($theme_id);

		if (!$this->isInstalledTheme($theme_id)) {
			$theme_id = $this->db->escape($theme_id);
			$version = $this->db->escape($theme_options['version']);

			$skin_file = $this->getThemeDataPath($theme_id) . 'data.txt';

			$this->importSkinsFromFile($theme_id, $skin_file);

			$this->db->query("INSERT INTO `". DB_PREFIX . self::TABLE_THEME ."` VALUES('$theme_id', '$version')");
		}

		// Choose skin for this store
		$skin_id = $theme_options['default_skin_id'];
		$this->setSkinForStore($store_id, $theme_id, $skin_id);
	}

	public function isInstalledTheme($theme_id) {
		$theme_id = $this->db->escape($theme_id);

		$query = $this->db->query("SELECT theme_id FROM `". DB_PREFIX . self::TABLE_THEME ."` WHERE theme_id = '$theme_id' LIMIT 0, 1");

		return $query->num_rows > 0 ? true : false;
	}

	public function upgradeTheme($theme_id) {
		$theme_id = $this->db->escape($theme_id);

/*		// Get all skins and all skin options by theme id
		$old_skins = array();
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id'");
		$old_skins = $query->rows;

		$old_skin_options = array();
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION . " WHERE theme_id = '$theme_id'");
		$old_skin_options = $query->rows;

		// Remove them by theme id
		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id'");
		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION . " WHERE theme_id = '$theme_id'");

		// Insert from theme skin files
		$skin_file = $this->getThemeDataPath($theme_id) . 'skins.json';
		$this->importSkinsFromFile($skin_file);

		$skins = json_decode(file_get_contents($skin_file), true);

		// Copy lost option
		foreach ($old_skins as $skin_row)
		{
			$skin_row['skin_id'] = $this->db->escape($skin_row['skin_id']);
			$skin_row['name'] = $this->db->escape($skin_row['name']);
			$skin_row['root_skin_id'] = $this->db->escape($skin_row['root_skin_id']);

			if (empty($skins[$theme_id . '|' . $skin_row['skin_id']]))
			{
				$this->db->query("
					INSERT INTO ". DB_PREFIX . self::TABLE_SKIN ." VALUES ('$theme_id', '{$skin_row['skin_id']}', '{$skin_row['name']}', '{$skin_row['root_skin_id']}')
				");
			}
			else
			{
				$this->db->query("
					UPDATE `". DB_PREFIX . self::TABLE_SKIN ."`
					SET `root_skin_id` = '{$skin_row['root_skin_id']}', `name` = '{$skin_row['name']}'
					WHERE `theme_id` = '$theme_id'
						AND `skin_id` = '{$skin_row['skin_id']}'
				");
			}
		}

		foreach ($old_skin_options as $skin_option_row)
		{
			$skin_option_row['option'] = $this->db->escape($skin_option_row['option']);
			$skin_option_row['theme_id'] = $this->db->escape($skin_option_row['theme_id']);
			$skin_option_row['skin_id'] = $this->db->escape($skin_option_row['skin_id']);
			$skin_option_row['value'] = $this->db->escape($skin_option_row['value']);

			$skin_code = $theme_id . '|' . $skin_row['skin_id'];

			if (empty($skins[$skin_code]) || empty($skins[$skin_code]['options'][$skin_option_row['option']]))
			{
				$this->db->query("
					INSERT INTO ". DB_PREFIX . self::TABLE_SKIN_OPTION . "
					SET `option` = '{$skin_option_row['option']}'
						, `theme_id` = '{$skin_option_row['theme_id']}'
						, `skin_id` = '{$skin_option_row['skin_id']}'
						, `value` = '{$skin_option_row['value']}'
					ON DUPLICATE KEY UPDATE `value` = '{$skin_option_row['value']}'
				");
			}
			else
			{
				$this->db->query("
					UPDATE ". DB_PREFIX . self::TABLE_SKIN_OPTION . "
					SET `value` = '{$skin_option_row['value']}'
					WHERE `theme_id` = '{$skin_option_row['theme_id']}'
						AND `skin_id` = '{$skin_option_row['skin_id']}'
						AND `option` = '{$skin_option_row['option']}'
				");
			}
		}
*/
		// Update theme version
		$theme_option = $this->loadThemeOptions($theme_id);
		$this->db->query("UPDATE `". DB_PREFIX . self::TABLE_THEME ."` SET version = '{$theme_option['version']}' WHERE theme_id = '$theme_id'");
	}

	public function getThemeId($store_id) {
		$settings = $this->model_setting_setting->getSetting('vie_theme_editor', $store_id);

		return is_array($settings) && isset($settings['vie_theme_editor_theme_id']) ? $settings['vie_theme_editor_theme_id'] : null;
	}

	public function getThemeVersion($store_id) {
		$theme_id = $this->db->escape($this->getThemeId($store_id));

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . self::TABLE_THEME . "` WHERE theme_id = '$theme_id'");

		return isset($query->row['version']) ? $query->row['version'] : null;
	}

	public function getConfigTheme($store_id) {
		return 'vie';
	}

	public function getThemeDataPath($theme_id) {
		return  DIR_SYSTEM . 'vie/';
	}

	public function getSkinId($store_id) {
		$settings = $this->model_setting_setting->getSetting('vie_theme_editor', $store_id);

		return is_array($settings) && isset($settings['vie_theme_editor_skin_id']) ? $settings['vie_theme_editor_skin_id'] : null;
	}

	/**
	 * Load theme options
	 * @param $theme_id
	 */
	public function loadThemeOptions($theme_id, $cache = true) {
		static $cache = array();

		// Get from cache if enabled
		if ($cache && isset($cache[$theme_id])) {
			return $cache[$theme_id];
		}

		// Get theme data path
		$theme_option_file = $this->getThemeDataPath($theme_id) . 'options.php';

		// Check where the theme option file is exist or not
		if (!file_exists($theme_option_file)) {
			$e = new Exception(Vie::_('error_theme_option_file_lost', 'Our theme is not set for this store! Please choose store which our theme is set.'), 4041);

			throw $e;
		}

		// Get file contents and decode
		$theme_options = include($theme_option_file);

		if (empty($theme_options['excluded_options'])) {
			$theme_options['excluded_options'] = array();
		}

		// Cache
		$cache[$theme_id] = $theme_options;

		return $theme_options;
	}

	public function getPositions() {
		$theme_options = $this->loadThemeOptions($this->config->get('config_template'));

		return isset($theme_options['positions']) ? $theme_options['positions'] : array();
	}

	public function setSkinForStore($store_id, $theme_id, $skin_id) {
		$this->model_setting_setting->editSetting('vie_theme_editor', array(
			'vie_theme_editor_theme_id' => $theme_id,
			'vie_theme_editor_skin_id' => $skin_id
		), $store_id);
	}

	public function setSkinCss($store_id, $skin_css) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = 'vie_theme_editor_skin_css'");

		$skin_css = $this->db->escape($skin_css);

		$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET `code` = 'vie_theme_editor', `key` = 'vie_theme_editor_skin_css', `value` = '". $skin_css ."', store_id = '" . (int)$store_id . "'");
	}

	public function deleteAllSkinData() {
		$this->db->query("DELETE FROM " . DB_PREFIX . self::TABLE_SKIN);
		$this->db->query("DELETE FROM " . DB_PREFIX . self::TABLE_SKIN_OPTION);
	}

	public function insertSkin(array $skin) {
		$skin['skin_id']        = $this->db->escape($skin['skin_id']);
		$skin['name']           = $this->db->escape($skin['name']);
		$skin['theme_id']       = $this->db->escape($skin['theme_id']);

		$this->db->query("INSERT INTO ". DB_PREFIX . self::TABLE_SKIN ."(skin_id, theme_id, root_skin_id, name) VALUES('{$skin['skin_id']}', '{$skin['theme_id']}', '{$skin['root_skin_id']}', '{$skin['name']}')");

		return true;
	}

	public function getSkinsByThemeId($theme_id) {
		$theme_id = $this->db->escape($theme_id);

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id'");

		return $query->rows;
	}

	public function getSkin($theme_id, $skin_id) {
		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id' LIMIT 0, 1    ");

		return $query->row;
	}

	public function getSkinOptions($theme_id, $skin_id) {
		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$query = $this->db->query("SELECT * FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");

		$options = array();

		foreach ($query->rows as $row) {
			$data = unserialize($row['value']);

			$options[$row['option']] = $this->unclean($data);
		}

		return $options;
	}

	public function updateSkinOptions($theme_id, $skin_id, array $options) {
		$theme_id   = $this->db->escape($theme_id);
		$skin_id    = $this->db->escape($skin_id);

		// Delete all skin options
		$this->db->query("DELETE FROM `". DB_PREFIX . self::TABLE_SKIN_OPTION ."` WHERE `theme_id` = '$theme_id' AND `skin_id` = '$skin_id'");

		// Insert new option
		foreach ($options as $key => $value) {
			$key = $this->db->escape($key);
			$value = $this->db->escape(serialize($value)); // TODO: Improve encoding better

			$this->db->query("INSERT INTO `". DB_PREFIX . self::TABLE_SKIN_OPTION ."`(`theme_id`, `skin_id`, `option`, `value`) VALUES ('$theme_id', '$skin_id', '$key', '$value')");
		}

		return true;
	}

	public function removeSkin($theme_id, $skin_id) {
		$theme_id = $this->db->escape($theme_id);
		$skin_id = $this->db->escape($skin_id);

		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");
		$this->db->query("DELETE FROM ". DB_PREFIX . self::TABLE_SKIN_OPTION ." WHERE theme_id = '$theme_id' AND skin_id = '$skin_id'");

		return true;
	}

	public function importSkinsFromFile($theme_id, $file) {
		if (!file_exists($file)) {
			throw new Exception(Vie::_('error_skin_file_lost', 'The skin file does not exist!'));
		}

		$contents = file_get_contents($file);

		$data = unserialize($contents);

		foreach ($data as $skin_id => $skin) {
			$skin_data = array(
				'skin_id'           => $skin_id,
				'theme_id'          => $theme_id,
				'root_skin_id'      => $skin['root_skin_id'],
				'name'              => $skin['name']
			);

			$this->insertSkin($skin_data);
			$this->updateSkinOptions($theme_id, $skin_id, $skin['options']);
		}

		return true;
	}

	public function unclean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[$this->unclean($key)] = $this->unclean($value);
			}
		} else {
			$data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
		}

		return $data;
	}
}
