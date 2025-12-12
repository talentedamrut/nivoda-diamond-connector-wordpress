<div class="nivoda-search-wrapper">
    <div class="nivoda-search-filters">
        <h2><?php esc_html_e('Search Diamonds', 'nivoda-api-integration'); ?></h2>
        
        <form id="nivoda-search-form">
            <div class="nivoda-filter-row">
                <div class="nivoda-filter-group">
                    <label for="nivoda-labgrown"><?php esc_html_e('Type', 'nivoda-api-integration'); ?></label>
                    <select id="nivoda-labgrown" name="labgrown">
                        <option value=""><?php esc_html_e('All', 'nivoda-api-integration'); ?></option>
                        <option value="false" <?php echo ($atts['labgrown'] === 'false') ? 'selected' : ''; ?>><?php esc_html_e('Natural', 'nivoda-api-integration'); ?></option>
                        <option value="true" <?php echo ($atts['labgrown'] === 'true') ? 'selected' : ''; ?>><?php esc_html_e('Lab Grown', 'nivoda-api-integration'); ?></option>
                    </select>
                </div>

                <div class="nivoda-filter-group">
                    <label for="nivoda-shapes"><?php esc_html_e('Shape', 'nivoda-api-integration'); ?></label>
                    <select id="nivoda-shapes" name="shapes" multiple>
                        <option value="ROUND">Round</option>
                        <option value="PRINCESS">Princess</option>
                        <option value="EMERALD">Emerald</option>
                        <option value="ASSCHER">Asscher</option>
                        <option value="CUSHION">Cushion</option>
                        <option value="OVAL">Oval</option>
                        <option value="RADIANT">Radiant</option>
                        <option value="PEAR">Pear</option>
                        <option value="HEART">Heart</option>
                        <option value="MARQUISE">Marquise</option>
                    </select>
                </div>
            </div>

            <div class="nivoda-filter-row">
                <div class="nivoda-filter-group">
                    <label><?php esc_html_e('Carat Weight', 'nivoda-api-integration'); ?></label>
                    <div class="nivoda-range-inputs">
                        <input type="number" id="nivoda-size-from" name="size_from" placeholder="From" step="0.01" min="0">
                        <span>-</span>
                        <input type="number" id="nivoda-size-to" name="size_to" placeholder="To" step="0.01" min="0">
                    </div>
                </div>

                <div class="nivoda-filter-group">
                    <label><?php esc_html_e('Price Range', 'nivoda-api-integration'); ?></label>
                    <div class="nivoda-range-inputs">
                        <input type="number" id="nivoda-price-from" name="price_from" placeholder="From" step="100" min="0">
                        <span>-</span>
                        <input type="number" id="nivoda-price-to" name="price_to" placeholder="To" step="100" min="0">
                    </div>
                </div>
            </div>

            <div class="nivoda-filter-row">
                <div class="nivoda-filter-group">
                    <label for="nivoda-color"><?php esc_html_e('Color', 'nivoda-api-integration'); ?></label>
                    <select id="nivoda-color" name="color" multiple>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                        <option value="I">I</option>
                        <option value="J">J</option>
                        <option value="K">K</option>
                    </select>
                </div>

                <div class="nivoda-filter-group">
                    <label for="nivoda-clarity"><?php esc_html_e('Clarity', 'nivoda-api-integration'); ?></label>
                    <select id="nivoda-clarity" name="clarity" multiple>
                        <option value="FL">FL</option>
                        <option value="IF">IF</option>
                        <option value="VVS1">VVS1</option>
                        <option value="VVS2">VVS2</option>
                        <option value="VS1">VS1</option>
                        <option value="VS2">VS2</option>
                        <option value="SI1">SI1</option>
                        <option value="SI2">SI2</option>
                    </select>
                </div>
            </div>

            <div class="nivoda-filter-row">
                <div class="nivoda-filter-group">
                    <label>
                        <input type="checkbox" id="nivoda-has-image" name="has_image" <?php echo ($atts['has_image'] === 'true') ? 'checked' : ''; ?>>
                        <?php esc_html_e('Has Image', 'nivoda-api-integration'); ?>
                    </label>
                </div>

                <div class="nivoda-filter-group">
                    <label>
                        <input type="checkbox" id="nivoda-has-video" name="has_v360" <?php echo ($atts['has_v360'] === 'true') ? 'checked' : ''; ?>>
                        <?php esc_html_e('Has 360° Video', 'nivoda-api-integration'); ?>
                    </label>
                </div>
            </div>

            <div class="nivoda-filter-actions">
                <button type="submit" class="nivoda-btn nivoda-btn-primary"><?php esc_html_e('Search', 'nivoda-api-integration'); ?></button>
                <button type="button" id="nivoda-reset-filters" class="nivoda-btn nivoda-btn-secondary"><?php esc_html_e('Reset', 'nivoda-api-integration'); ?></button>
            </div>
        </form>
    </div>

    <div class="nivoda-search-results">
        <div id="nivoda-results-header" style="display: none;">
            <h3><?php esc_html_e('Search Results', 'nivoda-api-integration'); ?></h3>
            <div class="nivoda-results-info">
                <span id="nivoda-results-count"></span>
                <select id="nivoda-results-per-page">
                    <option value="20">20 per page</option>
                    <option value="50" selected>50 per page</option>
                    <option value="100">100 per page</option>
                </select>
            </div>
        </div>

        <div id="nivoda-loading" style="display: none;">
            <div class="nivoda-spinner"></div>
            <p><?php esc_html_e('Searching diamonds...', 'nivoda-api-integration'); ?></p>
        </div>

        <div id="nivoda-results-grid"></div>
        
        <div id="nivoda-pagination"></div>
    </div>
</div>
