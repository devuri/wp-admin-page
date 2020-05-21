<?php

namespace WPAdminPage;

/**
 *
 */
class Translate_Easy {

	/**
	 * $textdomain
	 *
	 * the textdomain for the current plugin
	 * @var string
	 */
	private $textdomain = 'defualt';

	/**
	 * Translate
	 *
	 * translate_text('some text')
	 * @param string $text_to_translate
	 * @link https://developer.wordpress.org/reference/functions/__/
	 * @link https://developer.wordpress.org/reference/functions/translate/
	 * @link https://wordpress.stackexchange.com/questions/147633/when-to-use-e-and-for-the-translation
	 */
	public function translate_text( string $text_to_translate='' ) {
			//$text = __( $text_to_translate, $this->textdomain );
			$text = __($text_to_translate);
			return $text;
	}

	/**
	 * text()
	 *
	 * easily call echo translate_text() on text values
	 * @param  string $text [description]
	 * @return string [translated text]
	 */
	public function text( string $text='' ){
		$e_text = $this->translate_text($text);
		echo $e_text;
	}

}
