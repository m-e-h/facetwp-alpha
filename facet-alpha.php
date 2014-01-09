<?php

class FacetWP_Facet_Alpha
{

    function __construct() {
        $this->label = __( 'Alphabet', 'fwp' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {
        global $wpdb;


        $output = '';
        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $where_clause = $params['where_clause'];
        $where_clause = str_replace( 'post_id', 'ID', $where_clause );

        $sql = "
        SELECT SUBSTR(post_title, 1, 1) AS thechar
        FROM {$wpdb->posts}
        WHERE 1 $where_clause";
        $results = $wpdb->get_col( $sql );

        $available_chars = array( '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );

        $output .= '<span class="facetwp-alpha available" data-id="">Any</span>';

        foreach ( $available_chars as $char ) {
            if ( '#' == $char) {
                $number_exists = false;

                foreach ( $results as $result ) {
                    if ( in_array( $result, array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ) ) ) {
                        $number_exists = true;
                        break;
                    }
                }

                if ( $number_exists ) {
                    $output .= '<span class="facetwp-alpha available" data-id="#">#</span>';
                }
                else {
                    $output .= '<span class="facetwp-alpha">#</span>';
                }
            }
            else {
                if ( in_array( $char, $results ) ) {
                    $output .= '<span class="facetwp-alpha available" data-id="' . $char . '">' . $char . '</span>';
                }
                else {
                    $output .= '<span class="facetwp-alpha">' . $char . '</span>';
                }
            }
        }

        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        // The "#" character is an alias for all numbers
        if ( '#' == $selected_values ) {
            $selected_values = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 );
            $selected_values = implode( "','", $selected_values );
        }

        $sql = "
        SELECT DISTINCT ID FROM {$wpdb->posts}
        WHERE SUBSTR(post_title, 1, 1) IN ('$selected_values')";
        return $wpdb->get_col( $sql );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/alpha', function($this, obj) {
    });

    wp.hooks.addFilter('facetwp/save/alpha', function($this, obj) {
        return obj;
    });

    wp.hooks.addAction('facetwp/change/alpha', function($this) {
        $this.closest('.facetwp-facet').find('.name-source').hide();
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
?>
<style>
.facetwp-type-alpha {
    margin-bottom: 20px;
}
.facetwp-alpha {
    display: inline-block;
    color: #ddd;
    margin-right: 8px;
}
.facetwp-alpha.available {
    color: #333;
    cursor: pointer;
}
.facetwp-alpha.selected {
    font-weight: bold;
}
</style>
<script>
(function($) {
    wp.hooks.addAction('facetwp/refresh/alpha', function($this, facet_name) {
        FWP.facets[facet_name] = $this.find('.facetwp-alpha.selected').attr('data-id') || '';
    });

    $(document).on('click', '.facetwp-alpha.available', function() {
        $(this).addClass('selected');
        FWP.refresh();
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {

    }
}
