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

    echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #25D366;
        color: #ffffff;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        z-index: 1000;
        text-decoration: none;
        width: 60px;
        height: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    ">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
            <path d="M19.11 17.21c-.27-.13-1.59-.79-1.84-.88-.25-.09-.43-.13-.61.13-.18.27-.7.88-.86 1.06-.16.18-.31.2-.58.07-.27-.13-1.13-.42-2.15-1.34-.8-.71-1.34-1.59-1.49-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.31.4-.47.13-.16.18-.27.27-.45.09-.18.04-.34-.02-.47-.07-.13-.61-1.47-.84-2.01-.22-.53-.45-.46-.61-.47h-.52c-.18 0-.47.07-.72.34-.25.27-.95.93-.95 2.26 0 1.33.98 2.61 1.11 2.79.13.18 1.91 2.91 4.62 4.08.65.28 1.16.45 1.56.57.66.21 1.27.18 1.75.11.53-.08 1.59-.65 1.81-1.28.22-.63.22-1.17.16-1.28-.07-.11-.25-.18-.52-.31z"/>
            <path d="M16.03 3.2C8.95 3.2 3.2 8.95 3.2 16.03c0 2.28.6 4.5 1.73 6.45L3 29l6.71-1.76a12.78 12.78 0 0 0 6.32 1.71c7.08 0 12.83-5.75 12.83-12.83S23.11 3.2 16.03 3.2zm0 23.66a10.8 10.8 0 0 1-5.5-1.5l-.39-.23-3.98 1.04 1.06-3.88-.25-.4a10.78 10.78 0 1 1 9.06 4.97z"/>
        </svg>
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
