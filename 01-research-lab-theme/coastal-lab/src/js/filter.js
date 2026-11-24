/**
 * Research-area filter (progressive enhancement).
 *
 * Markup: archive-research_project.php. Each link points to a real term archive,
 * so the filter works without JS. With JS we filter in place, keep the URL
 * shareable with ?area=slug, and announce the count to screen readers.
 */

export function initFilters() {
	document
		.querySelectorAll( '.filter[data-filter-target]' )
		.forEach( setupFilter );
}

function setupFilter( filter ) {
	const grid = document.querySelector( filter.dataset.filterTarget );
	if ( ! grid ) {
		return;
	}

	const buttons = [ ...filter.querySelectorAll( '[data-filter]' ) ];
	const cards = [ ...grid.querySelectorAll( '[data-areas]' ) ];
	const status = filter.querySelector( '.filter__status' );
	const template = window.coastalLab?.i18n?.showing || 'Showing %d projects';

	const apply = ( slug, updateUrl = true ) => {
		let visible = 0;

		cards.forEach( ( card ) => {
			const areas = card.dataset.areas.split( ' ' );
			const match = slug === 'all' || areas.includes( slug );
			card.hidden = ! match;
			if ( match ) {
				visible++;
			}
		} );

		buttons.forEach( ( btn ) => {
			if ( btn.dataset.filter === slug ) {
				btn.setAttribute( 'aria-current', 'true' );
			} else {
				btn.removeAttribute( 'aria-current' );
			}
		} );

		if ( status ) {
			status.textContent = template.replace( '%d', visible );
		}

		if ( updateUrl ) {
			const url = new URL( window.location.href );
			if ( slug === 'all' ) {
				url.searchParams.delete( 'area' );
			} else {
				url.searchParams.set( 'area', slug );
			}
			window.history.replaceState( {}, '', url );
		}
	};

	buttons.forEach( ( btn ) => {
		btn.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			apply( btn.dataset.filter );
		} );
	} );

	// Honor ?area= on load (shared links).
	const initial = new URL( window.location.href ).searchParams.get( 'area' );
	if ( initial && buttons.some( ( b ) => b.dataset.filter === initial ) ) {
		apply( initial, false );
	}
}
