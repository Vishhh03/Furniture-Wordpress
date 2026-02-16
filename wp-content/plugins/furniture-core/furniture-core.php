<?php
/*
Plugin Name: Furniture Core Functionality
Description: Handles Vendor Notifications, WhatsApp, and Tracking logic.
Version: 1.0
Author: AntiGravity
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 1. Vendor Notification (Email)
 * Hooks into 'woocommerce_order_status_processing'
 */
add_action( 'woocommerce_order_status_processing', 'furniture_notify_vendor_new_order', 10, 1 );

function furniture_notify_vendor_new_order( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $vendor_email = 'vendor@example.com'; // TO BE CONFIGURED
    $subject = 'New Order #' . $order_id . ' - Please Ship';
    
    // Simple HTML email body
    $message = '<h1>New Order Received</h1>';
    $message .= '<p>Please prepare the following order for shipment:</p>';
    $message .= '<ul>';
    foreach ( $order->get_items() as $item_id => $item ) {
        $product = $item->get_product();
        $message .= '<li>' . $item->get_name() . ' x ' . $item->get_quantity() . '</li>';
    }
    $message .= '</ul>';
    $message .= '<p><strong>Shipping Address:</strong><br>' . $order->get_formatted_shipping_address() . '</p>';
    
    // Send email
    wp_mail( $vendor_email, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

/**
 * 2. WhatsApp Button on Frontend
 */
add_action( 'wp_footer', 'furniture_whatsapp_floating_button' );

function furniture_whatsapp_floating_button() {
    // Replace with actual number
    $phone = '919876543210'; 
    $message = 'Hi, I have a question about the furniture.';
    $url = 'https://wa.me/' . $phone . '?text=' . urlencode( $message );
    
    echo '<a href="' . esc_url( $url ) . '" target="_blank" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #25D366;
        color: white;
        padding: 15px;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        z-index: 1000;
        text-decoration: none;
        font-size: 24px;
        width: 60px;
        height: 60px;
        text-align: center;
        line-height: 60px;
    ">
        <i class="dashicons dashicons-whatsapp"></i> DA
    </a>';
}

/**
 * 3. Tracking Number Logic
 * Add custom field to Order Edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'furniture_tracking_field_display' );

function furniture_tracking_field_display( $order ) {
    $tracking_number = $order->get_meta( '_tracking_number' );
    $courier_name = $order->get_meta( '_courier_name' );
    ?>
    <div class="order_data_column">
        <h3>Tracking Info</h3>
        <p class="form-field form-field-wide">
            <label for="tracking_number">Tracking Number:</label>
            <input type="text" class="short" name="tracking_number" id="tracking_number" value="<?php echo esc_attr( $tracking_number ); ?>" />
        </p>
        <p class="form-field form-field-wide">
            <label for="courier_name">Courier Name:</label>
            <input type="text" class="short" name="courier_name" id="courier_name" value="<?php echo esc_attr( $courier_name ); ?>" />
        </p>
    </div>
    <?php
}

add_action( 'woocommerce_process_shop_order_meta', 'furniture_save_tracking_info' );

function furniture_save_tracking_info( $order_id ) {
    if ( ! empty( $_POST['tracking_number'] ) ) {
        $order = wc_get_order( $order_id );
        $old_tracking = $order->get_meta( '_tracking_number' );
        $new_tracking = sanitize_text_field( $_POST['tracking_number'] );
        $courier = sanitize_text_field( $_POST['courier_name'] );

        $order->update_meta_data( '_tracking_number', $new_tracking );
        $order->update_meta_data( '_courier_name', $courier );
        $order->save();

        // If tracking changed, maybe trigger notification?
        if ( $old_tracking !== $new_tracking ) {
            // Logic to send "Shipped" email or WhatsApp here
            // furniture_send_tracking_update($order);
            $order->add_order_note( 'Tracking updated to: ' . $new_tracking );
        }
    }
}
