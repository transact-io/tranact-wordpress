<?php

namespace Transact\FrontEnd\Controllers\Buttons;
/**
 * It will take care of print different buttons
 *
 * Class transactHandleButtons
 */
class transactHandleButtons
{
    /**
     * text to be included on the button
     */
    const BUTTON_TEXT = 'Purchase with Transact for';
    const TOKENS_TEXT = 'tokens';
    const TOKEN_TEXT = 'token';
    const SUBSCRIBE_TEXT = 'Subscribe';

    /**
     * Keys for buttons options, by default PURCHASE_AND_SUBSCRIPTION
     */
    const PURCHASE_AND_SUBSCRIPTION = 1;
    const ONLY_PURCHASE = 2;
    const ONLY_SUBSCRIBE = 3;

    /**
     * @var int post_id
     */
    protected $post_id;

    /**
     * @var Transact\FrontEnd\Controllers\Api\TransactApi
     */
    protected $transact_api;

    protected $options;


    public function __construct($post_id, $transact_api)
    {
        $this->post_id = $post_id;
        $this->transact_api = $transact_api;
        $this->options = get_option( 'transact-settings' );
    }


    /**
     * Will check if the user have a subscription and which kind of button want to use (given on post settings)
     *
     * @return string
     */
    public function print_buttons()
    {
        $type_of_button = get_post_meta( $this->post_id, 'transact_display_button' , true );
        switch($type_of_button) {
            case (self::PURCHASE_AND_SUBSCRIPTION):
                return $this->print_purchase_and_subscription($this->options, $this->transact_api);
                break;
            case (self::ONLY_PURCHASE):
                return $this->print_single_button($this->options, $this->transact_api, $type_of_button);
                break;
            case (self::ONLY_SUBSCRIBE):
                return $this->print_single_button($this->options, $this->transact_api, $type_of_button);
                break;
            default:
                return $this->print_single_button($this->options, $this->transact_api, self::ONLY_PURCHASE);
                break;
        }
    }

    /**
     * It prints purchase button and subscription button together
     *
     * @param $options
     * @param $transact_api
     * @return string
     */
    protected function print_purchase_and_subscription($options, $transact_api)
    {
        $content = $this->print_single_button($options, $transact_api, self::ONLY_PURCHASE);
        $content .= $this->print_single_button($options, $transact_api, self::ONLY_SUBSCRIBE);
        //var_dump($content);die;

        return $content;
    }

    /**
     * It prints a single button, either subscription or purchase
     *
     * @param $options
     * @param $transact_api
     * @param $button_type button type to print subscription or purchase
     * @return string html button
     */
    protected function print_single_button($options, $transact_api, $button_type)
    {
        $button_text = $this->get_button_text($button_type);

        $button_background_color_style = (isset($options['background_color']) ? 'background-color:' . esc_attr($options['background_color']) . ';' : '');
        $button_text_color_style = (isset($options['text_color']) ? 'color:' . esc_attr($options['text_color']) . ';' : '');
        $background_fade_color_style = '';
        if(isset($options['page_background_color'])) {
            list($r, $g, $b) = sscanf($options['page_background_color'], "#%02x%02x%02x");
            $background_fade_color_style = "background:linear-gradient(to bottom, rgba($r,$g,$b,0), rgba($r,$g,$b,1) 68%, rgba($r,$g,$b,1))";
        }
        $onclick = 'transactApi.authorize(PurchasePopUpClosed)';

        $button = printf(
            '<div class="transact_purchase_button fade" style="%s"><button style="%s" id="button_purchase" onclick="%s">%s</button></div>',
            $background_fade_color_style,
            $button_background_color_style . $button_text_color_style,
            $onclick,
            $button_text
        );

        /**$button = '<div class="transact_purchase_button fade" style="' .
            $background_fade_color_style . '">' .
            '<button style="' . $button_background_color_style . $button_text_color_style .
            '" id="button_purchase" onclick="transactApi.authorize(PurchasePopUpClosed);">' .
            $button_text .
            '</button>
                    </div>';**/

        return $button;
    }

    /**
     * Getting button text depending button type
     * @param $button_type
     * @return string|void
     */
    private function get_button_text($button_type)
    {
        if ($button_type == self::ONLY_PURCHASE) {
            $price = $this->transact_api->get_price();
            if ($price == 1) {
                $token_text = __(self::TOKEN_TEXT, 'transact');
            } else {
                $token_text = __(self::TOKENS_TEXT, 'transact');
            }
            $button_text = __(self::BUTTON_TEXT, 'transact') . ' '.  $price . ' ' . $token_text;
        } else {
            $button_text = __(self::SUBSCRIBE_TEXT, 'transact');
        }
        return $button_text;
    }

}