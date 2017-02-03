<?php

namespace Transact\Admin\Settings\Shortcode;
/**
 * It will handle shortcode logic
 *
 * Class transactShortcode
 */
class transactShortcode
{
    /**
     * @var
     */
    protected $attributes;

    protected $options;

    function __construct($attributes)
    {
        $this->$attributes = $attributes;
        $this->options = get_option( 'transact-settings' );
    }

    function print_shortcode()
    {
        $options = $this->options;
        $button = '<button id="{{button_id}}" style="' .
            (isset($options['background_color']) ? 'background-color:' . esc_attr($options['background_color']) . ';' : '') .
            (isset($options['text_color']) ? 'color:' . esc_attr($options['text_color']) . ';' : '') .
            '" class="transact_purchase_button" onclick="transactApi.authorize(PurchasePopUpClosed);">{{button_text}}</button>';

        $a = shortcode_atts( array(
            'id'   => 'button_purchase',
            'text' => __('ttextex', 'transact'),
        ), $this->attributes );

        $button = str_replace(array('{{button_id}}', '{{button_text}}'), array($a['id'], $a['text']), $button);
        return $button;
    }


}