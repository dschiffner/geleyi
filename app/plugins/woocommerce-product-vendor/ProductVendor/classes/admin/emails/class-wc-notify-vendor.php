<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class    WC_Email_Notify_Vendor
 * @version  2.0.0
 * @extends  WC_Email
 * @author   WooThemes
 * @package  WooCommerce/Classes/Emails
 */


class WC_Email_Notify_Vendor extends WC_Email
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->id          = 'vendor_new_order';
		$this->title       = __( 'Notify vendors', 'wc_product_vendor' );
		$this->description = __( 'New order emails are sent when an order is received/paid by a customer.', 'wc_product_vendor' );

		$this->heading = __( 'New customer order', 'wc_product_vendor' );
		$this->subject = __( '[{blogname}] New customer order ({order_number}) - {order_date}', 'wc_product_vendor' );

		$this->template_html  = 'emails/admin-new-order.php';
		$this->template_plain = 'emails/plain/admin-new-order.php';

		// Triggers for this email
		add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $this, 'trigger' ) );

		// Call parent constuctor
		parent::__construct();

		$this->recipient = get_option( 'admin_email' );
	}


	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param unknown $order_id
	 */
	function trigger( $order_id )
	{
		global $woocommerce;

		if ( $order_id ) {
			$this->object = new WC_Order( $order_id );

			$this->find[ ]    = '{order_date}';
			$this->replace[ ] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );

			$this->find[ ]    = '{order_number}';
			$this->replace[ ] = $this->object->get_order_number();
		}

		if ( !$this->is_enabled() ) return;

		$vendors = $this->get_vendors( $this->object );

		if ( empty( $vendors ) ) return;

		add_filter( 'woocommerce_order_get_items', array( $this, 'check_items' ), 10, 2 );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'check_order_totals' ), 10, 2 );
		foreach ( $vendors as $user_id => $user_email ) {

			$this->current_vendor = $user_id;
			$this->send( $user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		}
		remove_filter( 'woocommerce_get_order_item_totals', array( $this, 'check_order_totals' ), 10, 2 );
		remove_filter( 'woocommerce_order_get_items', array( $this, 'check_items' ), 10, 2 );

	}


	/**
	 *
	 *
	 * @param unknown $total_rows
	 * @param unknown $order
	 *
	 * @return unknown
	 */
	public function check_order_totals( $total_rows, $order )
	{
		$return[ 'cart_subtotal' ]            = $total_rows[ 'cart_subtotal' ];
		$return[ 'cart_subtotal' ][ 'label' ] = __( 'Commission Subtotal:', 'wc_product_vendor' );

		$prev_shipping_due = (array) get_post_meta( $order->id, '_vendor_shipping_due', true );
		foreach ( $prev_shipping_due as $vendor_id => $shipping_cost ) {
			if ( $this->current_vendor == $vendor_id ) {
				$return[ 'shipping' ]            = $total_rows[ 'shipping' ];
				$return[ 'shipping' ][ 'value' ] = woocommerce_price( $shipping_cost );
				break;
			}
		}

		return $return;
	}


	/**
	 *
	 *
	 * @param unknown $order
	 *
	 * @return unknown
	 */
	public function get_vendors( $order )
	{
		$items = $order->get_items();

		foreach ( $items as $key => $product ) {

			if ( empty( $product[ 'product_id' ] ) ) continue;
			$author = PV_Vendors::get_vendor_from_product( $product[ 'product_id' ] );

			// Only store the vendor authors
			if ( !PV_Vendors::is_vendor( $author ) ) {
				unset( $items[ $key ] );
				continue;
			}

			$vendors[ $author ] = get_userdata( $author )->user_email;
		}

		return $vendors;
	}


	/**
	 *
	 *
	 * @param unknown $items
	 * @param unknown $order
	 *
	 * @return unknown
	 */
	public function check_items( $items, $order )
	{
		foreach ( $items as $key => $product ) {

			if ( empty( $product[ 'product_id' ] ) ) {
				unset( $items[ $key ] );
				continue;
			}

			$author = PV_Vendors::get_vendor_from_product( $product[ 'product_id' ] );

			if ( $this->current_vendor != $author ) {
				unset( $items[ $key ] );
				continue;
			} else {
				$commission_due = PV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product[ 'product_id' ], $order );

				$items[ $key ][ 'line_subtotal' ] = $commission_due;
				$items[ $key ][ 'line_total' ]    = $commission_due;
				unset( $items[ $key ][ 'line_tax' ] );
			}

		}

		return $items;
	}


	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html()
	{
		ob_start();
		woocommerce_get_template( $this->template_html, array(
															 'order'         => $this->object,
															 'email_heading' => $this->get_heading()
														) );

		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain()
	{
		ob_start();
		woocommerce_get_template( $this->template_plain, array(
															  'order'         => $this->object,
															  'email_heading' => $this->get_heading()
														 ) );

		return ob_get_clean();
	}


	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'wc_product_vendor' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'wc_product_vendor' ),
				'default' => 'yes'
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'wc_product_vendor' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'wc_product_vendor' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'wc_product_vendor' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'wc_product_vendor' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'wc_product_vendor' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wc_product_vendor' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'wc_product_vendor' ),
					'html'      => __( 'HTML', 'wc_product_vendor' ),
					'multipart' => __( 'Multipart', 'wc_product_vendor' ),
				)
			)
		);
	}


}
