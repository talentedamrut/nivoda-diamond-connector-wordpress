(function($) {
    'use strict';

    let currentPage = 1;
    let totalResults = 0;
    let resultsPerPage = 50;
    let currentFilters = {};
    let cart = [];

    // Load cart from localStorage
    function loadCart() {
        const savedCart = localStorage.getItem('nivoda_cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            updateCartBadge();
        }
    }

    // Save cart to localStorage
    function saveCart() {
        localStorage.setItem('nivoda_cart', JSON.stringify(cart));
        updateCartBadge();
    }

    // Update cart badge
    function updateCartBadge() {
        const count = cart.length;
        $('.nivoda-cart-badge').text(count);
        if (count > 0) {
            $('.nivoda-cart-badge').show();
        } else {
            $('.nivoda-cart-badge').hide();
        }
    }

    $(document).ready(function() {
        
        // Load cart on page load
        loadCart();
        
        // Search form submission
        $('#nivoda-search-form').on('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            performSearch();
        });

        // Reset filters
        $('#nivoda-reset-filters').on('click', function() {
            $('#nivoda-search-form')[0].reset();
            $('#nivoda-results-grid').empty();
            $('#nivoda-results-header').hide();
            $('#nivoda-pagination').empty();
        });

        // Results per page change
        $(document).on('change', '#nivoda-results-per-page', function() {
            resultsPerPage = parseInt($(this).val());
            currentPage = 1;
            performSearch();
        });

        // Pagination
        $(document).on('click', '.nivoda-page-btn', function() {
            if ($(this).hasClass('active') || $(this).prop('disabled')) {
                return;
            }

            const page = $(this).data('page');
            if (page === 'prev') {
                currentPage = Math.max(1, currentPage - 1);
            } else if (page === 'next') {
                currentPage++;
            } else {
                currentPage = page;
            }

            performSearch();
        });

        // Add to cart
        $(document).on('click', '.nivoda-add-to-cart', function() {
            const button = $(this);
            const offerId = button.data('offer-id');
            const diamondData = button.data('diamond');

            // Check if already in cart
            const existsInCart = cart.some(item => item.offerId === offerId);
            if (existsInCart) {
                showNotification('This diamond is already in your cart', 'info');
                return;
            }

            // Add to cart
            cart.push({
                offerId: offerId,
                diamond: diamondData.diamond,
                price: diamondData.price,
                discount: diamondData.discount
            });

            saveCart();
            showNotification('Diamond added to cart!', 'success');
            
            // Change button text temporarily
            const originalText = button.text();
            button.text('Added!').prop('disabled', true);
            setTimeout(function() {
                button.text(originalText).prop('disabled', false);
            }, 2000);
        });

        // Buy now - add to cart and go to checkout
        $(document).on('click', '.nivoda-buy-now', function() {
            const button = $(this);
            const offerId = button.data('offer-id');
            const diamondData = button.data('diamond');

            // Clear cart and add this item
            cart = [{
                offerId: offerId,
                diamond: diamondData.diamond,
                price: diamondData.price,
                discount: diamondData.discount
            }];

            saveCart();
            
            // Redirect to checkout
            window.location.href = nivodaPublic.checkoutUrl || '/checkout';
        });

        // View details
        $(document).on('click', '.nivoda-view-details', function() {
            const diamondData = $(this).data('diamond');
            showDiamondModal(diamondData.item);
        });

        // Show notification
        function showNotification(message, type) {
            const notificationClass = type === 'success' ? 'nivoda-notification-success' : 
                                     type === 'error' ? 'nivoda-notification-error' : 
                                     'nivoda-notification-info';
            
            const notification = $('<div class="nivoda-notification ' + notificationClass + '">' + message + '</div>');
            $('body').append(notification);
            
            setTimeout(function() {
                notification.addClass('show');
            }, 100);

            setTimeout(function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Show diamond details modal
        function showDiamondModal(item) {
            const diamond = item.diamond;
            const cert = diamond.certificate;

            let modalHtml = '<div class="nivoda-modal-overlay">';
            modalHtml += '<div class="nivoda-modal">';
            modalHtml += '<button class="nivoda-modal-close">&times;</button>';
            modalHtml += '<div class="nivoda-modal-content">';
            
            // Image section
            modalHtml += '<div class="nivoda-modal-image">';
            if (diamond.image) {
                modalHtml += '<img src="' + diamond.image + '" alt="Diamond">';
            }
            if (diamond.video) {
                modalHtml += '<div class="nivoda-modal-video"><a href="' + diamond.video + '" target="_blank" class="nivoda-btn-action">View 360° Video</a></div>';
            }
            modalHtml += '</div>';

            // Details section
            modalHtml += '<div class="nivoda-modal-details">';
            modalHtml += '<h2>' + cert.carats + ' Carat ' + cert.shape + ' Diamond</h2>';
            modalHtml += '<p class="nivoda-modal-price">$' + formatPrice(item.price) + '</p>';
            
            modalHtml += '<table class="nivoda-modal-table">';
            modalHtml += '<tr><th>Certificate Number</th><td>' + (cert.certNumber || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Lab</th><td>' + (cert.lab || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Shape</th><td>' + (cert.shape || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Carat Weight</th><td>' + (cert.carats || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Color</th><td>' + (cert.color || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Clarity</th><td>' + (cert.clarity || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Cut</th><td>' + (cert.cut || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Polish</th><td>' + (cert.polish || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Symmetry</th><td>' + (cert.symmetry || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Measurements</th><td>' + (cert.length || '') + 'x' + (cert.width || '') + 'x' + (cert.depth || '') + ' mm</td></tr>';
            modalHtml += '<tr><th>Table %</th><td>' + (cert.table || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Depth %</th><td>' + (cert.depthPercentage || 'N/A') + '</td></tr>';
            modalHtml += '<tr><th>Fluorescence</th><td>' + (cert.floInt || 'N/A') + '</td></tr>';
            modalHtml += '</table>';

            modalHtml += '<div class="nivoda-modal-actions">';
            modalHtml += '<button class="nivoda-btn-action nivoda-btn-primary nivoda-modal-buy" data-offer-id="DIAMOND/' + diamond.id + '" data-diamond=\'' + JSON.stringify(item).replace(/'/g, '&apos;') + '\'>Buy Now</button>';
            modalHtml += '<button class="nivoda-btn-action nivoda-modal-add-cart" data-offer-id="DIAMOND/' + diamond.id + '" data-diamond=\'' + JSON.stringify(item).replace(/'/g, '&apos;') + '\'>Add to Cart</button>';
            modalHtml += '</div>';

            modalHtml += '</div>';
            modalHtml += '</div>';
            modalHtml += '</div>';
            modalHtml += '</div>';

            $('body').append(modalHtml);
            $('.nivoda-modal-overlay').fadeIn(300);
        }

        // Close modal
        $(document).on('click', '.nivoda-modal-close, .nivoda-modal-overlay', function(e) {
            if (e.target === this) {
                $('.nivoda-modal-overlay').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });

        // Modal buy/cart buttons
        $(document).on('click', '.nivoda-modal-buy', function() {
            const offerId = $(this).data('offer-id');
            const diamondData = $(this).data('diamond');

            cart = [{
                offerId: offerId,
                diamond: diamondData.diamond,
                price: diamondData.price,
                discount: diamondData.discount
            }];

            saveCart();
            window.location.href = nivodaPublic.checkoutUrl || '/checkout';
        });

        $(document).on('click', '.nivoda-modal-add-cart', function() {
            const offerId = $(this).data('offer-id');
            const diamondData = $(this).data('diamond');

            const existsInCart = cart.some(item => item.offerId === offerId);
            if (existsInCart) {
                showNotification('This diamond is already in your cart', 'info');
                return;
            }

            cart.push({
                offerId: offerId,
                diamond: diamondData.diamond,
                price: diamondData.price,
                discount: diamondData.discount
            });

            saveCart();
            showNotification('Diamond added to cart!', 'success');
            $('.nivoda-modal-overlay').fadeOut(300, function() {
                $(this).remove();
            });
        });

        function performSearch() {
            // Collect form data
            currentFilters = {};

            const labgrown = $('#nivoda-labgrown').val();
            if (labgrown !== '') {
                currentFilters.labgrown = labgrown;
            }

            const shapes = $('#nivoda-shapes').val();
            if (shapes && shapes.length > 0) {
                currentFilters.shapes = shapes.join(',');
            }

            const sizeFrom = $('#nivoda-size-from').val();
            if (sizeFrom) {
                currentFilters.size_from = sizeFrom;
            }

            const sizeTo = $('#nivoda-size-to').val();
            if (sizeTo) {
                currentFilters.size_to = sizeTo;
            }

            const priceFrom = $('#nivoda-price-from').val();
            if (priceFrom) {
                currentFilters.price_from = priceFrom;
            }

            const priceTo = $('#nivoda-price-to').val();
            if (priceTo) {
                currentFilters.price_to = priceTo;
            }

            const color = $('#nivoda-color').val();
            if (color && color.length > 0) {
                currentFilters.color = color.join(',');
            }

            const clarity = $('#nivoda-clarity').val();
            if (clarity && clarity.length > 0) {
                currentFilters.clarity = clarity.join(',');
            }

            if ($('#nivoda-has-image').is(':checked')) {
                currentFilters.has_image = 'true';
            }

            if ($('#nivoda-has-video').is(':checked')) {
                currentFilters.has_v360 = 'true';
            }

            // Add pagination
            currentFilters.limit = resultsPerPage;
            currentFilters.offset = (currentPage - 1) * resultsPerPage;

            // Show loading
            showLoading();

            // Make API request
            $.ajax({
                url: nivodaPublic.restUrl + 'search',
                method: 'GET',
                data: currentFilters,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', nivodaPublic.nonce);
                },
                success: function(response) {
                    displayResults(response);
                },
                error: function(xhr, status, error) {
                    showError('Failed to search diamonds. Please try again.');
                }
            });
        }

        function showLoading() {
            $('#nivoda-loading').show();
            $('#nivoda-results-grid').empty();
            $('#nivoda-results-header').hide();
            $('#nivoda-pagination').empty();
        }

        function showError(message) {
            $('#nivoda-loading').hide();
            $('#nivoda-results-grid').html(
                '<div class="nivoda-error">' + message + '</div>'
            );
        }

        function displayResults(response) {
            $('#nivoda-loading').hide();

            if (!response.data || !response.data.diamonds_by_query) {
                $('#nivoda-results-grid').html(
                    '<div class="nivoda-no-results">' +
                    '<h3>No Results Found</h3>' +
                    '<p>Try adjusting your search filters.</p>' +
                    '</div>'
                );
                return;
            }

            const data = response.data.diamonds_by_query;
            const items = data.items || [];
            totalResults = data.total_count || 0;

            // Show results header
            $('#nivoda-results-header').show();
            $('#nivoda-results-count').text(totalResults + ' diamonds found');

            // Display diamonds
            let html = '';
            items.forEach(function(item) {
                html += createDiamondCard(item);
            });

            $('#nivoda-results-grid').html(html);

            // Create pagination
            createPagination();

            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#nivoda-results-header').offset().top - 100
            }, 500);
        }

        function createDiamondCard(item) {
            const diamond = item.diamond;
            const cert = diamond.certificate;
            const offerId = 'DIAMOND/' + diamond.id;

            let html = '<div class="nivoda-diamond-card" data-diamond-id="' + diamond.id + '">';
            
            // Image/Video container
            html += '<div class="nivoda-diamond-image-container">';
            
            if (diamond.image) {
                html += '<img src="' + diamond.image + '" alt="Diamond" class="nivoda-diamond-image">';
            } else {
                html += '<div class="nivoda-diamond-image" style="display:flex;align-items:center;justify-content:center;color:#999;">No Image Available</div>';
            }

            if (diamond.video) {
                html += '<span class="nivoda-diamond-video-badge">360° Video</span>';
            }

            html += '</div>';

            // Content
            html += '<div class="nivoda-diamond-content">';
            
            html += '<h4 class="nivoda-diamond-title">' + 
                    (cert.carats || 'N/A') + ' Carat ' + 
                    (cert.shape || 'Diamond') + 
                    '</h4>';

            html += '<div class="nivoda-diamond-details">';
            
            html += '<div class="nivoda-diamond-detail-row">';
            html += '<span class="nivoda-detail-label">Color</span>';
            html += '<span class="nivoda-detail-value">' + (cert.color || 'N/A') + '</span>';
            html += '</div>';

            html += '<div class="nivoda-diamond-detail-row">';
            html += '<span class="nivoda-detail-label">Clarity</span>';
            html += '<span class="nivoda-detail-value">' + (cert.clarity || 'N/A') + '</span>';
            html += '</div>';

            html += '<div class="nivoda-diamond-detail-row">';
            html += '<span class="nivoda-detail-label">Cut</span>';
            html += '<span class="nivoda-detail-value">' + (cert.cut || 'N/A') + '</span>';
            html += '</div>';

            html += '<div class="nivoda-diamond-detail-row">';
            html += '<span class="nivoda-detail-label">Lab</span>';
            html += '<span class="nivoda-detail-value">' + (cert.lab || 'N/A') + '</span>';
            html += '</div>';

            html += '</div>';

            html += '<div class="nivoda-diamond-footer">';
            html += '<div class="nivoda-price">$' + formatPrice(item.price) + '</div>';
            html += '<div class="nivoda-cert-number">Cert: ' + (cert.certNumber || 'N/A') + '</div>';
            html += '</div>';

            // Action buttons
            html += '<div class="nivoda-diamond-actions">';
            html += '<button class="nivoda-btn-action nivoda-view-details" data-diamond=\'' + 
                    JSON.stringify({item: item, offerId: offerId}).replace(/'/g, '&apos;') + 
                    '\'>View Details</button>';
            html += '<button class="nivoda-btn-action nivoda-add-to-cart" data-offer-id="' + offerId + '" ' +
                    'data-diamond=\'' + JSON.stringify(item).replace(/'/g, '&apos;') + 
                    '\'>Add to Cart</button>';
            html += '<button class="nivoda-btn-action nivoda-btn-primary nivoda-buy-now" data-offer-id="' + offerId + '" ' +
                    'data-diamond=\'' + JSON.stringify(item).replace(/'/g, '&apos;') + 
                    '\'>Buy Now</button>';
            html += '</div>';

            html += '</div>';
            html += '</div>';

            return html;
        }

        function createPagination() {
            const totalPages = Math.ceil(totalResults / resultsPerPage);
            
            if (totalPages <= 1) {
                $('#nivoda-pagination').empty();
                return;
            }

            let html = '';

            // Previous button
            html += '<button class="nivoda-page-btn" data-page="prev" ' + 
                    (currentPage === 1 ? 'disabled' : '') + '>Previous</button>';

            // Page numbers
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            if (startPage > 1) {
                html += '<button class="nivoda-page-btn" data-page="1">1</button>';
                if (startPage > 2) {
                    html += '<span>...</span>';
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                html += '<button class="nivoda-page-btn ' + (i === currentPage ? 'active' : '') + 
                        '" data-page="' + i + '">' + i + '</button>';
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span>...</span>';
                }
                html += '<button class="nivoda-page-btn" data-page="' + totalPages + '">' + 
                        totalPages + '</button>';
            }

            // Next button
            html += '<button class="nivoda-page-btn" data-page="next" ' + 
                    (currentPage === totalPages ? 'disabled' : '') + '>Next</button>';

            $('#nivoda-pagination').html(html);
        }

        function formatPrice(price) {
            if (!price) return 'N/A';
            return Number(price).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

    });

})(jQuery);
