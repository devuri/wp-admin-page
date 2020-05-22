<?php

namespace WPAdminPage;

/**
 * ----------------------------------------------------------------------------
 * @copyright 	Copyright © 2020 Uriel Wilson.
 * @package   	FormHelper
 * @version   	1.2.0
 * @license   	GPL-2.0
 * @author    	Uriel Wilson
 * @link      	https://github.com/devuri/wp-admin-page/
 *
 * ----------------------------------------------------------------------------
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('WPAdminPage\Admin\Form\FormHelper')) {
  final class FormHelper {


    /**
     * class version
     */
    const ADMINVERSION = '1.2.0';

    /**
     * processing
     * @var boolean
     */
    public $processing = false;

    /**
     * user_feedback
     *
     * give the user some feedback
     *
     * @param  string $class the css class (success | info | warning | error)
     * @param  string $message output message
     * @return string
     * @link https://developer.wordpress.org/reference/hooks/admin_notices/
     * @link https://developer.wordpress.org/reference/functions/__/
     */
    public function user_feedback($message = 'Options updated', $class = 'success'){

      $user_message  = '<div style="font-size: small; text-transform: capitalize;" id="user-feedback" class="notice notice-'.$class.' is-dismissible">';
      $user_message .= '<p>';
      $user_message .= __($message);
      $user_message .= '</p>';
      $user_message .= '</div>';
      return $user_message;
    }

    /**
     * [thickbox_link description]
     * @return [type] [description]
     */
    public function thickboxlink($linktext='click here',$id=''){
      $link = '<a href="#TB_inline?width=auto&inlineId='.$id.'" class="thickbox">';
      $link .= $linktext;
      $link .= '</a>';
      return $link;
    }

    /**
     * is_required
     *
     * set field as required, defaults to false
     *
     * @param  boolean $required
     * @return
     */
    public function is_required($required = false){
      if ($required) {
        $require = ' <span class="description">(required)</span>';
      } else {
        $require = '';
      }
      return $require;
    }

    /**
     * Input Field
     *
     * @param  string  $fieldname     the name of the field
     * @param  boolean $required set if this field is a required field
     * @param  string  $type     the field type
     * @return
     */
    public function input($fieldname='name',$val = '...', $required = false,$type='text'){
      $fieldname = strtolower($fieldname);
      // set reuired
      $require = $this->is_required($required);

      // lets build out the input
      $input  = '<!-- input field '.$fieldname.'_input -->';
      $input .= '<tr class="input">';
      $input .= '<th>';
      $input .= '<label for="'.str_replace(" ", "_", $fieldname).'">';
      $input .= ucwords(str_replace("_", " ", $fieldname));
      $input .= $require;
      $input .= '</label>';
      $input .= '</th>';
      $input .= '<td>';
      $input .= '<input type="'.$type.'" name="'.str_replace(" ", "_", $fieldname).'" id="'.str_replace(" ", "_", $fieldname).'" aria-describedby="'.str_replace(" ", "-", $fieldname).'-description" value="'.$val.'" class="uk-input">';
      $input .= '<p class="description" id="'.str_replace(" ", "-", $fieldname).'-description">';
      $input .= strtolower(str_replace("_", " ", $fieldname));
      $input .= '<strong>.</strong>';
      $input .= '</p>';
      $input .= '</td>';
      $input .= '</tr>';
      $input .= '<!-- input field '.$fieldname.'_input -->';
      return $input;
    }

    /**
     * hidden Input Field
     *
     * @param  string  $fieldname     the name of the field
     * @return
     */
    public function input_hidden($fieldname='name',$val = '...'){
    	$fieldname = strtolower($fieldname);

    	// lets build out the input
    	$input_hidden  = '<!-- input field '.$fieldname.'_input -->';
    	$input_hidden .= '<tr class="input">';
    	$input_hidden .= '<th>';
    	$input_hidden .= '</th>';
    	$input_hidden .= '<td>';
    	$input_hidden .= '<input type="hidden" name="'.str_replace(" ", "_", $fieldname).'" id="'.str_replace(" ", "_", $fieldname).'" aria-describedby="'.str_replace(" ", "-", $fieldname).'-description" value="'.$val.'" class="uk-input">';
    	$input_hidden .= '</td>';
    	$input_hidden .= '</tr>';
    	$input_hidden .= '<!-- input field '.$fieldname.'_input -->';
    	return $input_hidden;
    }

    public function upload($fieldname='upload_image_button',$val = 'Upload Image', $required = false,$type='button'){
      $fieldname = strtolower($fieldname);
      $upload_button  = '<tr class="input">';
      $upload_button .= '<th>';
      $upload_button .= '<label for="'.str_replace(" ", "_", $fieldname).'">';
      $upload_button .= ucwords(str_replace("_", " ", $fieldname));
      $upload_button .= '</label>';
      $upload_button .= '</th>';
      $upload_button .= '<td>';
      $upload_button .= '<!-- upload field '.$fieldname.'_input -->';
      $upload_button .= '<input id="'.str_replace(" ", "_", $fieldname).'"';
      $upload_button .= 'type="'.$type.'" class="button"';
      $upload_button .= 'value="'.$val.'" />';
      $upload_button .= '<p class="description" id="'.str_replace(" ", "-", $fieldname).'-description">';
      $upload_button .= strtolower(str_replace("_", " ", $fieldname));
      $upload_button .= '<strong>.</strong>';
      $upload_button .= '</p>';
      $upload_button .= '</td>';
      $upload_button .= '</tr>';
      $upload_button .= '<!-- input field '.$fieldname.'_input -->';

      return $upload_button;
    }

    /**
     * page_list building our own $pages array
     * @param  array  $arg [description]
     * @link https://developer.wordpress.org/reference/functions/get_pages/
     * @return array
     */
    public function page_list($arg = array()){
      $arg = array(
        'sort_column' => 'post_date',
        'sort_order' => 'desc'
      );
      // get the pages
      $pages = get_pages($arg);
      $page_list = array();
      foreach ($pages as $pkey => $page) {
        $page_list[$page->ID] = $page->post_title;
      }
      return $page_list;
    }

    /**
     * select field
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function select($options = array(),$fieldname = 'name', $js='do_some_js_action', $required = false){
      // set reuired
      $require = $this->is_required($required);
      $js_function = $js;
      $defualt_select = '<option selected="selected">Select an option</option>';

      // lets build out the select field
      $select  = '';
      $select .= '<tr class="input">';
      $select .= '<th>';
      $select .= '<label for="'.str_replace(" ", "_", $fieldname).'">';
      $select .= ucwords(str_replace("_", " ", $fieldname));
      $select .= $require;
      $select .= '</label>';
      $select .= '</th>';
      $select .= '<td>';
      $select .= '<select onchange="'.$js_function.'()" name="'.strtolower(str_replace(" ", "_", $fieldname)).'" id="'.strtolower(str_replace(" ", "_", $fieldname)).'" class="uk-select">';
      /**
       * Options list Output
       * @var array $options
       */
      if (is_array($options)) {
        foreach ($options as $optkey => $optvalue) {
          $select .= '<option value="'.$optkey.'">'.ucfirst($optvalue).'</option>';
        }
      }
      $select .= '</select>';
      $select .= '<p class="description" id="'.str_replace(" ", "-", $fieldname).'-description">';
      $select .= strtolower(str_replace("_", " ", $fieldname));
      $select .= '<strong>.</strong>';
      $select .= '</p>';
      $select .= '</td>';
      $select .= '</tr>';
      $select .= '<!-- select field '.$fieldname.'_input -->';
      return $select;
    }

    /**
     * Textarea
     *
     * @param  string  $fieldname     field name
     * @param  boolean $required set the filed to required
     * @return
     */
    public function textarea($fieldname='name',$required = false){
      $fieldname = strtolower($fieldname);
      // set reuired
      $require = $this->is_required($required);

      // lets build out the textarea
      $textarea  = '<!-- '.$fieldname.'_textarea -->';
      $textarea .= '<tr class="textarea">';
      $textarea .= '<th>';
      $textarea .= '<label for="'.str_replace(" ", "_", $fieldname).'">';
      $textarea .= ucwords(str_replace("_", " ", $fieldname));
      $textarea .= $require;
      $textarea .= '</label>';
      $textarea .= '</th>';
      $textarea .= '<td>';
      $textarea .= '<textarea class="uk-textarea" name="'.str_replace(" ", "_", $fieldname).'_textarea" rows="8" cols="50">';
      $textarea .= '</textarea>';
      $textarea .= '<p class="description" id="'.str_replace(" ", "-", $fieldname).'-description">';
      $textarea .= strtolower(str_replace("_", " ", $fieldname));
      $textarea .= '<strong>.</strong>';
      $textarea .= '</p>';
      $textarea .= '</td>';
      $textarea .= '</tr>';
      $textarea .= '<!-- '.$fieldname.'_textarea -->';
      return $textarea;
    }

    /**
     * Custom version of the WP Dropdown Category list
     *
     * @param  string $fieldname   field name
     * @param  array $args define custom arguments
     * @return
     * @link https://developer.wordpress.org/reference/functions/wp_dropdown_categories/
     */
    public function categorylist($fieldname=null,$args = array()){
      $require = $this->is_required($required);

      $catlist_args = array(
        'show_option_all'    => '',
        'show_option_none'   => '',
        'option_none_value'  => '-1',
        'orderby'            => 'ID',
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 1,
        'child_of'           => 0,
        'exclude'            => '',
        'echo'               => 0,
        'selected'           => 0,
        'hierarchical'       => 0,
        'name'               => strtolower(str_replace(" ", "_", $fieldname)).'set_category',
        'id'                 => '',
        'class'              => 'uk-select',
        'depth'              => 0,
        'tab_index'          => 0,
        'taxonomy'           => 'category',
        'hide_if_empty'      => false,
        'value_field'	     => 'term_id',
      );
      // ref https://developer.wordpress.org/reference/functions/wp_dropdown_categories/
      $categories = '<tr class="input-select">';
      $categories .= '<th><label for="select_dropdown">Select a Category</label></th>';
      $categories .= '<td>';
      $categories .= wp_dropdown_categories($catlist_args);
      $categories .= '</td>';
      $categories .= '</tr>';
      return $categories;
    }

    /**
     * Make Table
     *
     * Use this to create a table for the form
     * @param  string $tag decide to open or close table
     * @param  string $tbclass ad css class
     * @return
     */
    public function table($tag='close', $tbclass=''){
      if ($tag === 'open') {
        // lets open tags for the table
        $table  = '<table class="form-table '.$tbclass.'" role="presentation">';
        $table .= '<tbody>';
      } elseif ($tag === 'close') {
        // lets close the tags for the table
        $table  = '</tbody>';
        $table .= '</table>';
      }
      return $table;
    }

    /**
     * [submit_button description]
     * @param  string  $text             The text of the button. Default 'Save Changes'.
     * @param  string  $type             The type and CSS class(es) of the button. Core values include 'primary', 'small', and 'large'.
     * @param  string  $name             name of the submit button
     * @param  boolean $wrap             True if the output button should be wrapped in a paragraph tag, false otherwise.
     * @param  string  $other_attributes [description]
     * @return [type]                    [description]
     * @link https://developer.wordpress.org/reference/functions/get_submit_button/
     */
    public function submit_button($text = 'Save Changes', $type = 'primary large',$name ='submit',$wrap = ''){
      $submit_button = get_submit_button($text,$type,$name,$wrap);
      return $submit_button;
    }

    /**
     * input_val
     *
     * Get the input field $_POST data
     * @param  string $input_field input name
     * @return string
     */
    public function input_val($input_field=null){
      $input = sanitize_text_field($_POST[$input_field]);
      if ( isset( $input )) {
        return $input;
      }
    }

    /**
     * nonce field
     *
     * @param  string $fieldname nonce field name
     * @return
     * @link https://developer.wordpress.org/reference/functions/wp_nonce_field/
     */
    public function nonce($wpnonce = '_swa_page_wpnonce'){
      return wp_nonce_field( -1, $wpnonce, true , true);
    }

    /**
     * nonce_check
     *
     * @param  string $noncefield [description]
     * @return
     * @link https://developer.wordpress.org/reference/functions/wp_verify_nonce/
     */
    public function verify_nonce($noncefield='_swa_page_wpnonce'){
      /**
       * Lets verify this
       *
       * @return boolean
       */
      if ( ! isset( $_POST[$noncefield] ) || ! wp_verify_nonce( $_POST[$noncefield] )) {
        return false;
      } else {
        return true; // nonce is invalid
      }
    }

  }
}
