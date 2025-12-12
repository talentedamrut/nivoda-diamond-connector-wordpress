<?php
/**
 * Public-facing functionality
 */

class Nivoda_Public {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            NIVODA_API_PLUGIN_URL . 'public/css/nivoda-public.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            NIVODA_API_PLUGIN_URL . 'public/js/nivoda-public.js',
            ['jquery'],
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'nivodaPublic', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('nivoda/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'checkoutUrl' => home_url('/checkout'),
            'cartUrl' => home_url('/cart')
        ]);
    }

    public function register_shortcodes() {
        add_shortcode('nivoda_search', [$this, 'search_shortcode']);
        add_shortcode('nivoda_cart', [$this, 'cart_shortcode']);
        add_shortcode('nivoda_checkout', [$this, 'checkout_shortcode']);
    }

    public function search_shortcode($atts) {
        $atts = shortcode_atts([
            'shapes' => '',
            'labgrown' => '',
            'limit' => '50',
            'has_image' => '',
            'has_v360' => '',
        ], $atts);

        ob_start();
        include NIVODA_API_PLUGIN_DIR . 'public/partials/search-form.php';
        return ob_get_clean();
    }

    public function cart_shortcode($atts) {
        ob_start();
        include NIVODA_API_PLUGIN_DIR . 'public/partials/cart-page.php';
        return ob_get_clean();
    }

    public function checkout_shortcode($atts) {
        ob_start();
        include NIVODA_API_PLUGIN_DIR . 'public/partials/checkout-page.php';
        return ob_get_clean();
    }

    public function register_rest_routes() {
        register_rest_route('nivoda/v1', '/search', [
            'methods' => 'GET',
            'callback' => [$this, 'rest_search_diamonds'],
            'permission_callback' => '__return_true',
            'args' => [
                'labgrown' => [
                    'type' => 'boolean',
                    'sanitize_callback' => 'rest_sanitize_boolean',
                ],
                'shapes' => [
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'size_from' => [
                    'type' => 'number',
                    'minimum' => 0,
                    'sanitize_callback' => 'floatval',
                ],
                'size_to' => [
                    'type' => 'number',
                    'minimum' => 0,
                    'sanitize_callback' => 'floatval',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 100,
                    'default' => 20,
                    'sanitize_callback' => 'absint',
                ],
            ]
        ]);

        register_rest_route('nivoda/v1', '/order', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_create_order'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'diamond_id' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'customer_info' => [
                    'required' => true,
                    'type' => 'object',
                ],
            ]
        ]);

        register_rest_route('nivoda/v1', '/hold', [
            'methods' => 'POST',
            'callback' => [$this, 'rest_create_hold'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'diamond_id' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);
    }

    public function check_permissions() {
        return current_user_can('manage_options');
    }

    public function rest_search_diamonds(WP_REST_Request $request) {
        $client = new Nivoda_API_Client();

        $params = [];

        if ($request->get_param('labgrown') !== null) {
            $params['labgrown'] = filter_var($request->get_param('labgrown'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->get_param('shapes')) {
            $params['shapes'] = explode(',', $request->get_param('shapes'));
        }

        if ($request->get_param('size_from')) {
            $params['size_from'] = floatval($request->get_param('size_from'));
        }

        if ($request->get_param('size_to')) {
            $params['size_to'] = floatval($request->get_param('size_to'));
        }

        if ($request->get_param('color')) {
            $params['color'] = explode(',', $request->get_param('color'));
        }

        if ($request->get_param('clarity')) {
            $params['clarity'] = explode(',', $request->get_param('clarity'));
        }

        if ($request->get_param('cut')) {
            $params['cut'] = explode(',', $request->get_param('cut'));
        }

        if ($request->get_param('has_v360') !== null) {
            $params['has_v360'] = filter_var($request->get_param('has_v360'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->get_param('has_image') !== null) {
            $params['has_image'] = filter_var($request->get_param('has_image'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->get_param('price_from')) {
            $params['price_from'] = floatval($request->get_param('price_from'));
        }

        if ($request->get_param('price_to')) {
            $params['price_to'] = floatval($request->get_param('price_to'));
        }

        if ($request->get_param('limit')) {
            $params['limit'] = intval($request->get_param('limit'));
        }

        if ($request->get_param('offset')) {
            $params['offset'] = intval($request->get_param('offset'));
        }

        if ($request->get_param('order_type')) {
            $params['order_type'] = $request->get_param('order_type');
        }

        if ($request->get_param('order_direction')) {
            $params['order_direction'] = $request->get_param('order_direction');
        }

        $result = $client->search_diamonds($params);

        if (is_wp_error($result)) {
            return new WP_Error(
                'search_failed',
                $result->get_error_message(),
                ['status' => 500]
            );
        }

        return rest_ensure_response($result);
    }

    public function rest_create_order(WP_REST_Request $request) {
        $client = new Nivoda_API_Client();
        $data = $request->get_json_params();
        
        $items = isset($data['items']) ? $data['items'] : [];
        $customer = isset($data['customer']) ? $data['customer'] : [];

        if (empty($items)) {
            return new WP_Error(
                'no_items',
                __('No items provided', 'nivoda-api-integration'),
                ['status' => 400]
            );
        }

        // Store customer details for email notification
        if (!empty($customer)) {
            update_option('nivoda_last_order_customer', $customer);
            
            // Send email notification to admin
            $this->send_order_notification($items, $customer);
        }

        $result = $client->create_order($items);

        if (is_wp_error($result)) {
            return new WP_Error(
                'order_failed',
                $result->get_error_message(),
                ['status' => 500]
            );
        }

        return rest_ensure_response($result);
    }

    private function send_order_notification($items, $customer) {
        $admin_email = get_option('admin_email');
        $subject = 'New Diamond Order Received';
        
        $message = "New order received:\n\n";
        $message .= "Customer Details:\n";
        $message .= "Name: " . $customer['name'] . "\n";
        $message .= "Email: " . $customer['email'] . "\n";
        $message .= "Phone: " . $customer['phone'] . "\n";
        $message .= "Address: " . $customer['address'] . "\n";
        $message .= "City: " . $customer['city'] . ", " . $customer['state'] . " " . $customer['zip'] . "\n";
        $message .= "Country: " . $customer['country'] . "\n\n";
        
        if (!empty($customer['comments'])) {
            $message .= "Comments: " . $customer['comments'] . "\n\n";
        }
        
        $message .= "Order Items: " . count($items) . "\n";
        $message .= "Reference: " . $items[0]['customer_order_number'] . "\n";
        
        wp_mail($admin_email, $subject, $message);

        // Also send confirmation to customer
        $customer_message = "Dear " . $customer['name'] . ",\n\n";
        $customer_message .= "Thank you for your order! We have received your request and will contact you shortly.\n\n";
        $customer_message .= "Order Reference: " . $items[0]['customer_order_number'] . "\n\n";
        $customer_message .= "Best regards,\nYour Diamond Store";
        
        wp_mail($customer['email'], 'Order Confirmation', $customer_message);
    }

    public function rest_create_hold(WP_REST_Request $request) {
        $client = new Nivoda_API_Client();
        $product_id = $request->get_param('product_id');
        $product_type = $request->get_param('product_type') ?? 'Diamond';

        if (empty($product_id)) {
            return new WP_Error(
                'no_product_id',
                __('Product ID is required', 'nivoda-api-integration'),
                ['status' => 400]
            );
        }

        $result = $client->create_hold($product_id, $product_type);

        if (is_wp_error($result)) {
            return new WP_Error(
                'hold_failed',
                $result->get_error_message(),
                ['status' => 500]
            );
        }

        return rest_ensure_response($result);
    }
}
