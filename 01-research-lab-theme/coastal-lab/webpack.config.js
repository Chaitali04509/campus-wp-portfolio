/**
 * Extends the @wordpress/scripts webpack config with theme-specific entries.
 *
 *  main   -> build/main.js + build/main.css   (front end)
 *  editor -> build/editor.css                 (block editor styles, via add_editor_style)
 *
 * Each entry also emits build/<name>.asset.php (dependencies + content hash),
 * which inc/enqueue.php reads for cache busting.
 */
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		main: path.resolve( __dirname, 'src/js/main.js' ),
		editor: path.resolve( __dirname, 'src/scss/editor.scss' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
	},
};
