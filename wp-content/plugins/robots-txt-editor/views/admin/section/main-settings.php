<?php

if ( ! defined('WPINC')) {
    die;
}

use RobotsTxt\Admin\Admin;
$robotsUrl = home_url('/robots.txt');
?>


<table class="form-table" id="robotstxt-settings">
    <tbody>

    <tr>
        <th scope="row"><?php _e('Robots.txt','robots-txt-editor') ?></th>
        <td>
            <textarea cols="70" rows="23" name="<?= Admin::OPTIONS?>[robotsTxt]"/><?php echo esc_attr( $robotsTxt ) ?></textarea>
            <p class="description" id="menu-height-description">
                <a href="<?= $robotsUrl; ?>" target="_blank" rel="noopener noreferrer"><?php _e('View','robots-txt-editor') ?> robots.txt</a>
            </p>
        </td>

    </tr>

    </tbody>
</table>
