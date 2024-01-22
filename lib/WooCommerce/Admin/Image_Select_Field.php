<?php
/**
 * Image_Select_Field class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Admin
 */

namespace Oblak\NPG\WooCommerce\Admin;

use Oblak\WP\Abstracts\Hook_Runner;
use Oblak\WP\Decorators\Hookable;

/**
 * Outputs the image select field in WooCommerce settings API forms
 */
#[Hookable( 'init', 999, 'is_admin' )]
class Image_Select_Field extends Hook_Runner {
    /**
     * Whether filters are added
     *
     * @var bool
     */
    protected static bool $filters_added = false;

    /**
     * Renders image select field
     *
     * @param  string           $html  Empty string.
     * @param  string           $key   Field key.
     * @param  array            $data Field data.
     * @param  \WC_Settings_API $obj   Settings API object.
     * @return string
     *
     * @hook woocommerce_generate_image_select_html
     * @type filter
     */
    public function render_image_select_field( $html, string $key, array $data, \WC_Settings_API $obj ): string {
        $field_key = $obj->get_field_key( $key );

        $defaults = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'selector_width'    => '50px',
            'options'           => array(),
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array(),
        );
        $data     = wp_parse_args( $data, $defaults );

        ob_start();
        ?>
        <tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $obj->get_tooltip_html( $data ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
			</th>
			<td class="forminp">
                <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                <div data-input-name="<?php echo esc_attr( $field_key ); ?>" class="image-select-field">
                <?php foreach ( $data['options'] as $option_value => $option_data ) : ?>
                    <?php
                    /**
                     * Filters the image option URL
                     *
                     * @param  string $image_url Image URL.
                     * @param  string $key       Option key.
                     * @return string            Modified image URL
                     *
                     * @since 2.2.2
                     */
                    $image_url = apply_filters( 'woocommerce_image_select_option_image_url', $option_data['image'], $field_key );
                    $classes   = array( 'image-select-option' );

                    if ( $obj->get_option( $key, '' ) === $option_value ) {
                        $classes[] = 'selected';
                    }

                    if ( $option_data['disabled'] ?? false ) {
                        $classes[] = 'disabled';
                    }

                    ?>
                    <div
                        style="width: <?php echo esc_attr( $data['selector_width'] ); ?>; height: auto"
                        class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
                        data-option="<?php echo esc_attr( $option_value ); ?>"
                        data-tip="<?php echo esc_attr( $option_data['title'] ); ?>"
                    >
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $option_data['title'] ); ?>">
                        </span>
                    </div>
                <?php endforeach; ?>
                <input id="<?php echo esc_attr( $field_key ); ?>" name="<?php echo esc_attr( $field_key ); ?>" value="<?php echo esc_attr( $obj->get_option( $key, '' ) ); ?>" type="hidden">
                </div>
                <?php echo $obj->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </td>
        </tr>
        <?php

        if ( ! self::$filters_added ) {
            add_filter( 'admin_footer', array( $this, 'add_custom_styles' ), 99 );
            add_filter( 'admin_footer', array( $this, 'add_custom_scripts' ), 100 );

            self::$filters_added = true;
        }

        return ob_get_clean();
    }

    /**
     * Adds custom styles
     */
    public function add_custom_styles() {
        ?>
        <style>
            .image-select-field {
                display:flex;
                gap: 10px;
            }

            .image-select-field .image-select-option {
                height:auto;
                cursor: pointer;
                padding: 5px;
                background-color: #fff;
                border: 1px solid #ddd;
            }

            .image-select-field .image-select-option.selected {
                border: 1px solid #007cba;
            }

            .image-select-field .image-select-option.disabled {
                cursor: not-allowed;
                opacity: 0.9;
            }

            .image-select-option img {
                width: 100%;
                height: auto;
                display: block;
                margin: 0 auto;
            }

            .image-select-field .image-select-option.disabled img {
                filter: grayscale(0.75);
            }
        </style>
        <?php
    }

    /**
     * Adds custom scripts
     */
    public function add_custom_scripts() {
        echo '<' .'script>'; // phpcs:ignore
        echo <<<JS
        jQuery(function($){
            $('.image-select-option').tipTip({
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200,
                'keepAlive': true
            });

            $('.image-select-option').click(function() {
                if ($(this).hasClass('disabled')) {
                    return;
                }

                $(this).siblings().removeClass('selected');
                $(this).addClass('selected');
                $(this).closest('.image-select-field').find('input').val($(this).attr('data-option'));
            });
        });
        JS;
        echo '</script>';
    }
}
