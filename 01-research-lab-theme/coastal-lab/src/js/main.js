/**
 * Front-end entry point.
 */
import '../scss/main.scss';
import { initNavigation } from './navigation';
import { initFilters } from './filter';

const ready = ( fn ) => {
	if ( document.readyState !== 'loading' ) {
		fn();
	} else {
		document.addEventListener( 'DOMContentLoaded', fn );
	}
};

ready( () => {
	document.documentElement.classList.add( 'js' );
	initNavigation();
	initFilters();
} );
