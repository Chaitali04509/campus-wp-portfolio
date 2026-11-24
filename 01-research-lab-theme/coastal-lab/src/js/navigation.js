/**
 * Accessible responsive navigation.
 *
 * - The toggle button ships with `hidden` and is only revealed when JS runs,
 *   so no-JS visitors still get a usable (stacked) menu.
 * - aria-expanded reflects state; Escape closes and returns focus to the toggle.
 * - Sub-menus open on click (not hover only) so they work on touch and keyboard.
 */

const MOBILE_QUERY = window.matchMedia( '(max-width: 52em)' );

export function initNavigation() {
	const nav = document.getElementById( 'primary-nav' );
	if ( ! nav ) {
		return;
	}

	const toggle = nav.querySelector( '.menu-toggle' );
	const menu = nav.querySelector( '.primary-menu' );
	if ( ! toggle || ! menu ) {
		return;
	}

	// State is conveyed by aria-expanded alone. The visible "Menu" text stays the
	// accessible name (WCAG 2.5.3 Label in Name), so no aria-label swap is needed.
	const setOpen = ( open ) => {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		nav.classList.toggle( 'is-open', open );
	};

	toggle.hidden = false;
	setOpen( false );

	toggle.addEventListener( 'click', () => {
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	nav.addEventListener( 'keydown', ( event ) => {
		if ( event.key !== 'Escape' ) {
			return;
		}
		const openSub = nav.querySelector(
			'.sub-menu-toggle[aria-expanded="true"]'
		);
		if ( openSub ) {
			openSub.setAttribute( 'aria-expanded', 'false' );
			openSub.focus();
		} else if (
			MOBILE_QUERY.matches &&
			nav.classList.contains( 'is-open' )
		) {
			setOpen( false );
			toggle.focus();
		}
	} );

	// Reset mobile state when resizing up to desktop.
	MOBILE_QUERY.addEventListener( 'change', ( e ) => {
		if ( ! e.matches ) {
			setOpen( false );
		}
	} );

	initSubMenus( menu );
}

/**
 * Inject a disclosure button next to every parent menu item.
 *
 * @param {HTMLElement} menu Top-level <ul>.
 */
function initSubMenus( menu ) {
	menu.querySelectorAll( '.menu-item-has-children' ).forEach(
		( item, index ) => {
			const link = item.querySelector( ':scope > a' );
			const sub = item.querySelector( ':scope > .sub-menu' );
			if ( ! link || ! sub ) {
				return;
			}

			sub.id = sub.id || `sub-menu-${ index }`;

			const button = document.createElement( 'button' );
			button.type = 'button';
			button.className = 'sub-menu-toggle';
			button.setAttribute( 'aria-expanded', 'false' );
			button.setAttribute( 'aria-controls', sub.id );
			const srText = document.createElement( 'span' );
			srText.className = 'screen-reader-text';
			srText.textContent = `${ link.textContent.trim() } submenu`;
			button.append( srText );

			link.after( button );

			button.addEventListener( 'click', () => {
				const open = button.getAttribute( 'aria-expanded' ) === 'true';
				// Only one sub-menu open at a time.
				menu.querySelectorAll(
					'.sub-menu-toggle[aria-expanded="true"]'
				).forEach( ( b ) =>
					b.setAttribute( 'aria-expanded', 'false' )
				);
				button.setAttribute( 'aria-expanded', String( ! open ) );
			} );
		}
	);

	// Clicking outside closes any open sub-menu.
	document.addEventListener( 'click', ( event ) => {
		if ( ! menu.contains( event.target ) ) {
			menu.querySelectorAll(
				'.sub-menu-toggle[aria-expanded="true"]'
			).forEach( ( b ) => b.setAttribute( 'aria-expanded', 'false' ) );
		}
	} );
}
