<?php
/**
 * Checkout Page Template
 * Shortcode: [nivoda_checkout]
 */
?>

<div class="nivoda-checkout-wrapper">
    <h1>Checkout</h1>

    <div id="nivoda-checkout-empty" style="display:none;">
        <p>Your cart is empty. Please add items before checking out.</p>
        <a href="<?php echo esc_url(home_url('/diamonds')); ?>" class="nivoda-btn nivoda-btn-primary">Browse Diamonds</a>
    </div>

    <div id="nivoda-checkout-container" style="display:none;">
        <div class="nivoda-checkout-grid">
            <!-- Customer Information Form -->
            <div class="nivoda-checkout-form">
                <h2>Customer Information</h2>
                
                <form id="nivoda-checkout-form">
                    <div class="nivoda-form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>

                    <div class="nivoda-form-group">
                        <label for="customer_email">Email Address *</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>

                    <div class="nivoda-form-group">
                        <label for="customer_phone">Phone Number *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required>
                    </div>

                    <div class="nivoda-form-group">
                        <label for="customer_address">Address *</label>
                        <textarea id="customer_address" name="customer_address" rows="3" required></textarea>
                    </div>

                    <div class="nivoda-form-row">
                        <div class="nivoda-form-group">
                            <label for="customer_city">City *</label>
                            <input type="text" id="customer_city" name="customer_city" required>
                        </div>

                        <div class="nivoda-form-group">
                            <label for="customer_state">State/Province *</label>
                            <input type="text" id="customer_state" name="customer_state" required>
                        </div>
                    </div>

                    <div class="nivoda-form-row">
                        <div class="nivoda-form-group">
                            <label for="customer_zip">ZIP/Postal Code *</label>
                            <input type="text" id="customer_zip" name="customer_zip" required>
                        </div>

                        <div class="nivoda-form-group">
                            <label for="customer_country">Country *</label>
                            <input type="text" id="customer_country" name="customer_country" required>
                        </div>
                    </div>

                    <div class="nivoda-form-group">
                        <label for="customer_comments">Order Notes (Optional)</label>
                        <textarea id="customer_comments" name="customer_comments" rows="3" placeholder="Any special instructions or comments..."></textarea>
                    </div>

                    <div class="nivoda-form-group">
                        <label for="customer_order_number">Your Reference Number (Optional)</label>
                        <input type="text" id="customer_order_number" name="customer_order_number" placeholder="Internal reference or PO number">
                    </div>

                    <div class="nivoda-form-group">
                        <label class="nivoda-checkbox-label">
                            <input type="checkbox" id="return_option" name="return_option" value="1">
                            Request return option (if available)
                        </label>
                    </div>

                    <div id="nivoda-checkout-error" class="nivoda-error" style="display:none;"></div>
                    <div id="nivoda-checkout-success" class="nivoda-success" style="display:none;"></div>

                    <button type="submit" id="nivoda-place-order" class="nivoda-btn nivoda-btn-primary nivoda-btn-large">
                        Place Order
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="nivoda-checkout-summary">
                <h2>Order Summary</h2>
                <div id="nivoda-order-items"></div>
                
                <div class="nivoda-order-totals">
                    <table>
                        <tr>
                            <td>Subtotal:</td>
                            <td class="nivoda-amount" id="nivoda-order-subtotal">$0</td>
                        </tr>
                        <tr class="nivoda-total-row">
                            <td><strong>Total:</strong></td>
                            <td class="nivoda-amount"><strong id="nivoda-order-total">$0</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let cart = [];

    // Load cart
    function loadCart() {
        const savedCart = localStorage.getItem('nivoda_cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
        }
        displayCheckout();
    }

    // Display checkout
    function displayCheckout() {
        if (cart.length === 0) {
            $('#nivoda-checkout-empty').show();
            $('#nivoda-checkout-container').hide();
            return;
        }

        $('#nivoda-checkout-empty').hide();
        $('#nivoda-checkout-container').show();

        // Display order items
        let html = '';
        let total = 0;

        cart.forEach(function(item) {
            const cert = item.diamond.certificate;
            const price = parseFloat(item.price);
            total += price;

            html += '<div class="nivoda-order-item">';
            if (item.diamond.image) {
                html += '<img src="' + item.diamond.image + '" alt="Diamond">';
            }
            html += '<div class="nivoda-order-item-details">';
            html += '<h4>' + cert.carats + ' Carat ' + cert.shape + '</h4>';
            html += '<p>' + cert.color + ' | ' + cert.clarity + ' | ' + cert.cut + '</p>';
            html += '<p class="nivoda-order-item-price">$' + price.toLocaleString() + '</p>';
            html += '</div>';
            html += '</div>';
        });

        $('#nivoda-order-items').html(html);
        $('#nivoda-order-subtotal').text('$' + total.toLocaleString());
        $('#nivoda-order-total').text('$' + total.toLocaleString());
    }

    // Handle form submission
    $('#nivoda-checkout-form').on('submit', function(e) {
        e.preventDefault();

        const button = $('#nivoda-place-order');
        const originalText = button.text();

        // Disable button and show loading
        button.prop('disabled', true).text('Processing...');
        $('#nivoda-checkout-error').hide();
        $('#nivoda-checkout-success').hide();

        // Prepare order data
        const orderItems = cart.map(function(item) {
            return {
                offerId: item.offerId,
                customer_comment: $('#customer_comments').val() || '',
                customer_order_number: $('#customer_order_number').val() || 'ORDER-' + Date.now(),
                return_option: $('#return_option').is(':checked')
            };
        });

        // Customer details for email/storage
        const customerDetails = {
            name: $('#customer_name').val(),
            email: $('#customer_email').val(),
            phone: $('#customer_phone').val(),
            address: $('#customer_address').val(),
            city: $('#customer_city').val(),
            state: $('#customer_state').val(),
            zip: $('#customer_zip').val(),
            country: $('#customer_country').val(),
            comments: $('#customer_comments').val()
        };

        // Submit order
        $.ajax({
            url: nivodaPublic.restUrl + 'order',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                items: orderItems,
                customer: customerDetails
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nivodaPublic.nonce);
            },
            success: function(response) {
                // Clear cart
                localStorage.removeItem('nivoda_cart');
                cart = [];

                // Show success message
                $('#nivoda-checkout-success').html(
                    '<h3>Order Placed Successfully!</h3>' +
                    '<p>Thank you for your order. We will contact you shortly at ' + customerDetails.email + '</p>' +
                    '<p>Your order reference: ' + orderItems[0].customer_order_number + '</p>'
                ).show();

                // Hide form
                $('#nivoda-checkout-form').hide();

                // Scroll to success message
                $('html, body').animate({
                    scrollTop: $('#nivoda-checkout-success').offset().top - 100
                }, 500);

                // Redirect after 5 seconds
                setTimeout(function() {
                    window.location.href = '<?php echo esc_url(home_url('/')); ?>';
                }, 5000);
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Failed to place order. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $('#nivoda-checkout-error').text(errorMessage).show();

                button.prop('disabled', false).text(originalText);

                // Scroll to error
                $('html, body').animate({
                    scrollTop: $('#nivoda-checkout-error').offset().top - 100
                }, 500);
            }
        });
    });

    // Initialize
    loadCart();
});
</script>
