<?php
/**
 * Shortcodes
 *
 * @package      Social Coup Functionality
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

function sc_sharing_shortcode( $atts, $content = '' ) {
	return '<div class="social-coup-sharing"><span class="facebook-wrapper"><iframe style="border: none; overflow: hidden; width: 150px; height: 21px;" src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.example.com&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=211899938864669" frameborder="0" scrolling="no" width="150" height="20"></iframe></span> <span class="twitter-wrapper"><a class="twitter-follow-button" href="https://twitter.com/billerickson" data-show-count="true" data-lang="en" data-show-screen-name="false">Follow @billerickson</a>
<script type="text/javascript">// <![CDATA[
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
// ]]></script></span></div>';
}

add_shortcode( 'social-coup-sharing', 'sc_sharing_shortcode' );

