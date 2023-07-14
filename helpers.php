<?php

namespace JcoreBroiler;

/**
 * Used to show an error message when the plugin is missing dependencies.
 *
 * @return void
 */
function dependencies_errors(): void {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'The plugin "Jcore Broiler" could not be activated because it is missing dependencies. Please install the dependencies and try again.', 'jcore-broiler' ); ?></p>
	</div>
	<?php
}
