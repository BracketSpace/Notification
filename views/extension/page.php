<div class="wrap notification-extensions">

	<h1><?php esc_html_e( 'Extensions', 'notification' ); ?></h1>

	<div id="the-list">
		<?php foreach ( (array) $this->get_var( 'extensions' ) as $extension ) : ?>
			<?php
				$this->set_var( 'extension', $extension, true );
				$this->get_view( 'extension/extension-box' );
			?>
		<?php endforeach; ?>
		<?php $this->get_view( 'extension/promo-box' ); ?>
	</div>

</div>
