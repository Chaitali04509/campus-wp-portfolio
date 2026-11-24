<?php
/**
 * Accessibility fixes for Divi.
 *
 * Found during the initial audit (see docs/build-log.md):
 *  - No skip link to bypass the header (WCAG 2.4.1)
 *  - Focus outlines removed on buttons and menu links (WCAG 2.4.7)
 *  - Mobile menu toggle is a <span>, not a button: no keyboard access or state
 *
 * @package Marine_Dept
 */

defined( 'ABSPATH' ) || exit;

/**
 * Skip link. Divi's main content wrapper is #main-content.
 */
function marine_dept_skip_link() {
	printf(
		'<a class="marine-skip-link" href="#main-content">%s</a>',
		esc_html__( 'Skip to content', 'marine-dept' )
	);
}
add_action( 'wp_body_open', 'marine_dept_skip_link', 5 );

/**
 * Make Divi's mobile menu toggle keyboard-operable and expose its state.
 * Small inline script. It only runs if the toggle exists.
 */
function marine_dept_mobile_menu_a11y() {
	if ( is_admin() ) {
		return;
	}
	$script = <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.mobile_menu_bar').forEach(function (bar) {
		bar.setAttribute('role', 'button');
		bar.setAttribute('tabindex', '0');
		bar.setAttribute('aria-label', bar.getAttribute('aria-label') || 'Menu');
		bar.setAttribute('aria-expanded', 'false');
		var nav = bar.closest('.mobile_nav');
		bar.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); bar.click(); }
		});
		if (nav) {
			new MutationObserver(function () {
				bar.setAttribute('aria-expanded', String(nav.classList.contains('opened')));
			}).observe(nav, { attributes: true, attributeFilter: ['class'] });
		}
	});
});
JS;
	wp_register_script( 'marine-dept-a11y', false, array(), MARINE_DEPT_VERSION, true );
	wp_enqueue_script( 'marine-dept-a11y' );
	wp_add_inline_script( 'marine-dept-a11y', $script );
}
add_action( 'wp_enqueue_scripts', 'marine_dept_mobile_menu_a11y' );
