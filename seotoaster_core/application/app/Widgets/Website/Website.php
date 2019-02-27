<?php
/**
 * Description of Website
 *
 * @author iamne
 */
class Widgets_Website_Website extends Widgets_Abstract {

	const OPT_URL = 'url';

    /**
     * Return website host
     */
    const OPT_HOST = 'host';

	protected function  _load() {
		$content = '';
		$type    = $this->_options[0];
		switch ($type) {
			case self::OPT_URL:
				$content = $this->_toasterOptions['websiteUrl'];
			break;
            case self::OPT_HOST:
                $content = Tools_System_Tools::getUrlHost($this->_toasterOptions['websiteUrl']);
                break;
		}
		return $content;
	}

	public static function getAllowedOptions() {
		$translator = Zend_Registry::get('Zend_Translate');
		return array(
			array(
				'alias'   => $translator->translate('Website url'),
				'option' => 'website:url'
			)
		);
	}

}

