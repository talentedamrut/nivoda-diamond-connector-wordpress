<?php
/**
 * Cart Page Template
 * Shortcode: [nivoda_cart]
 */
?>

<div class="nivoda-cart-wrapper">
    <h1>Shopping Cart</h1>
    
    <div id="nivoda-cart-container">
        <div id="nivoda-cart-empty" style="display:none;">
            <p>Your cart is empty.</p>
            <a href="<?php echo esc_url(home_url('/diamonds')); ?>" class="nivoda-btn nivoda-btn-primary">Continue Shopping</a>
        </div>

        <div id="nivoda-cart-items"></div>

        <div id="nivoda-cart-summary" style="display:none;">
            <div class="nivoda-cart-totals">
                <h3>Cart Summary</h3>
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td class="nivoda-amount" id="nivoda-cart-subtotal">$0</td>
                    </tr>
                    <tr class="nivoda-cart-total-row">
                        <td><strong>Total:</strong></td>
                        <td class="nivoda-amount"><strong id="nivoda-cart-total">$0</strong></td>
                    </tr>
                </table>

                <div class="nivoda-cart-actions">
                    <a href="<?php echo esc_url(home_url('/diamonds')); ?>" class="nivoda-btn nivoda-btn-secondary">Continue Shopping</a>
                    <button id="nivoda-proceed-checkout" class="nivoda-btn nivoda-btn-primary">Proceed to Checkout</button>
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
        displayCart();
    }

    // Display cart
    function displayCart() {
        if (cart.length === 0) {
            $('#nivoda-cart-empty').show();
            $('#nivoda-cart-items').hide();
            $('#nivoda-cart-summary').hide();
            return;
        }

        $('#nivoda-cart-empty').hide();
        $('#nivoda-cart-items').show();
        $('#nivoda-cart-summary').show();

        let html = '<table class="nivoda-cart-table">';
        html += '<thead><tr>';
        html += '<th>Diamond</th>';
        html += '<th>Details</th>';
        html += '<th>Price</th>';
        html += '<th>Action</th>';
        html += '</tr></thead>';
        html += '<tbody>';

        let total = 0;

        cart.forEach(function(item, index) {
            const cert = item.diamond.certificate;
            const price = parseFloat(item.price);
            total += price;

            html += '<tr data-index="' + index + '">';
            
            // Image
            html += '<td class="nivoda-cart-item-image">';
            if (item.diamond.image) {
                html += '<img src="' + item.diamond.image + '" alt="Diamond">';
            } else {
                html += '<div class="no-image">No Image</div>';
            }
            html += '</td>';

            // Details
            html += '<td class="nivoda-cart-item-details">';
            html += '<h4>' + cert.carats + ' Carat ' + cert.shape + '</h4>';
            html += '<p>Color: ' + cert.color + ' | Clarity: ' + cert.clarity + ' | Cut: ' + cert.cut + '</p>';
            html += '<p class="nivoda-cert-small">Cert: ' + cert.certNumber + '</p>';
            html += '</td>';

            // Price
            html += '<td class="nivoda-cart-item-price">$' + price.toLocaleString() + '</td>';

            // Remove button
            html += '<td class="nivoda-cart-item-remove">';
            html += '<button class="nivoda-btn-remove" data-index="' + index + '">Remove</button>';
            html += '</td>';

            html += '</tr>';
        });

        html += '</tbody></table>';

        $('#nivoda-cart-items').html(html);
        $('#nivoda-cart-subtotal').text('$' + total.toLocaleString());
        $('#nivoda-cart-total').text('$' + total.toLocaleString());
    }

    // Remove from cart
    $(document).on('click', '.nivoda-btn-remove', function() {
        const index = $(this).data('index');
        cart.splice(index, 1);
        localStorage.setItem('nivoda_cart', JSON.stringify(cart));
        displayCart();
    });

    // Proceed to checkout
    $('#nivoda-proceed-checkout').on('click', function() {
        window.location.href = '<?php echo esc_url(home_url('/checkout')); ?>';
    });

    // Initialize
    loadCart();
});
</script>
