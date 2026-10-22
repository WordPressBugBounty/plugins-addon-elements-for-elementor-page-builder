/**
 * Shared runtime for EAE extensions on Elementor 4 (v4) Flexbox, Div Block and Grid.
 *
 * v4 containers render from a fixed template, so extensions cannot add wrapper attributes.
 * Frontend: PHP prints each extension's configs as `window.eaeAtomicConfigs[ name ][ elementId ]`.
 * Editor preview: configs are built live from the element's settings in the editor window, and
 * re-applied whenever a command runs or the canvas re-renders a container.
 *
 * An extension registers { name, mount( el, config ), unmount( el ), isMounted( el ),
 * readEditorConfig( get ) [, cleanup()] }, where get( key ) returns a setting's plain value and
 * readEditorConfig may return a Promise.
 */
( function () {
	'use strict';

	var CONTAINER_SELECTOR = '[data-element_type="e-flexbox"],[data-element_type="e-div-block"],[data-element_type="e-grid"]';
	var extensions = [];
	var editorWindow = null;
	var started = false;
	var timer = null;

	function unwrap( value ) {
		return value && typeof value === 'object' && 'value' in value ? value.value : value;
	}

	function update( extension, el, config ) {
		var attr = 'data-eae-' + extension.name;
		var key = config ? JSON.stringify( config ) : '';

		if ( ( el.getAttribute( attr ) || '' ) === key && ( ! config || extension.isMounted( el ) ) ) {
			return;
		}
		if ( el.hasAttribute( attr ) ) {
			extension.unmount( el );
			el.removeAttribute( attr );
		}
		if ( ! config ) {
			return;
		}
		el.setAttribute( attr, key );
		// Mount a copy, so an extension that mutates its config cannot affect the stored key.
		extension.mount( el, JSON.parse( key ) );
	}

	function runFrontend( extension ) {
		var configs = ( window.eaeAtomicConfigs || {} )[ extension.name ] || {};
		Object.keys( configs ).forEach( function ( id ) {
			document.querySelectorAll( '[data-id="' + CSS.escape( id ) + '"]' ).forEach( function ( el ) {
				if ( el.matches( CONTAINER_SELECTOR ) ) {
					update( extension, el, configs[ id ] );
				}
			} );
		} );
	}

	function syncEditor() {
		timer = null;
		extensions.forEach( function ( extension ) {
			if ( extension.cleanup ) {
				extension.cleanup();
			}
		} );
		document.querySelectorAll( CONTAINER_SELECTOR ).forEach( function ( el ) {
			var container = el.dataset.id && editorWindow.elementor.getContainer && editorWindow.elementor.getContainer( el.dataset.id );
			var settings = container && container.settings;
			var get = function ( key ) {
				return settings ? unwrap( settings.get( key ) ) : null;
			};
			extensions.forEach( function ( extension ) {
				Promise.resolve( settings ? extension.readEditorConfig( get, editorWindow ) : null )
					.then( function ( config ) {
						update( extension, el, config || null );
					} )
					.catch( function () {
						update( extension, el, null );
					} );
			} );
		} );
	}

	function scheduleEditorSync() {
		if ( ! timer ) {
			timer = setTimeout( syncEditor, 150 );
		}
	}

	// The editor adds `elementor-editor-active` to the preview body only after it loads, so check
	// Elementor's preview config and URL instead.
	function isEditPreview() {
		var config = window.elementorFrontendConfig;
		if ( config && config.environmentMode && config.environmentMode.edit ) {
			return true;
		}
		return /[?&]elementor-preview=/.test( window.location.search );
	}

	function start() {
		started = true;
		try {
			editorWindow = window.parent !== window && window.parent.elementor && isEditPreview() ? window.parent : null;
		} catch ( e ) {
			editorWindow = null;
		}

		if ( ! editorWindow ) {
			extensions.forEach( runFrontend );
			return;
		}

		// Every $e.run() dispatches this on the editor window, as Elementor's own preview handlers rely on.
		editorWindow.addEventListener( 'elementor/commands/run/after', function ( event ) {
			var command = event.detail && event.detail.command;
			if ( command && 0 === command.indexOf( 'document/' ) ) {
				scheduleEditorSync();
			}
		} );
		// The canvas replaces a container's markup when it re-renders, which drops mounted nodes.
		new MutationObserver( scheduleEditorSync ).observe( document.body, { childList: true, subtree: true } );
		scheduleEditorSync();
	}

	window.eaeAtomicExtensions = {
		register: function ( extension ) {
			extensions.push( extension );
			if ( ! started ) {
				return;
			}
			if ( editorWindow ) {
				scheduleEditorSync();
			} else {
				runFrontend( extension );
			}
		},
	};

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );
