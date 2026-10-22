/**
 * EAE Particles on Elementor 4 (v4) Flexbox, Div Block and Grid.
 * Uses the shared runtime in atomic-extensions.js.
 */
( function () {
	'use strict';

	var counter = 0;

	function getWrapper( el ) {
		for ( var i = 0; i < el.children.length; i++ ) {
			if ( el.children[ i ].classList.contains( 'eae-particle-wrapper' ) ) {
				return el.children[ i ];
			}
		}
		return null;
	}

	// particles.js' own destroypJS() sets the global pJSDom to null, which breaks the next particlesJS() call.
	function stopInstances( shouldStop ) {
		var list = window.pJSDom || [];
		for ( var i = list.length - 1; i >= 0; i-- ) {
			var pJS = list[ i ] && list[ i ].pJS;
			if ( pJS && pJS.canvas && pJS.canvas.el && shouldStop( pJS.canvas.el ) ) {
				window.cancelAnimationFrame( pJS.fn.drawAnimFrame );
				list.splice( i, 1 );
			}
		}
	}

	window.eaeAtomicExtensions.register( {
		name: 'particles',

		mount: function ( el, config ) {
			if ( typeof window.particlesJS !== 'function' ) {
				return;
			}
			var wrapper = document.createElement( 'div' );
			wrapper.className = 'eae-particle-wrapper eae-particle-area';
			wrapper.id = 'eae-particle-v4-' + ( ++counter );
			el.classList.add( 'eae-v4-particles' );
			el.insertBefore( wrapper, el.firstChild );
			window.particlesJS( wrapper.id, config );
		},

		unmount: function ( el ) {
			var wrapper = getWrapper( el );
			if ( wrapper ) {
				stopInstances( function ( canvas ) {
					return wrapper.contains( canvas );
				} );
				wrapper.remove();
			}
			el.classList.remove( 'eae-v4-particles' );
		},

		isMounted: function ( el ) {
			return !! getWrapper( el ) && el.classList.contains( 'eae-v4-particles' );
		},

		// Stops instances whose canvas the editor removed while re-rendering a container.
		cleanup: function () {
			stopInstances( function ( canvas ) {
				return ! canvas.isConnected;
			} );
		},

		readEditorConfig: function ( get ) {
			if ( ! get( 'eae_particles' ) ) {
				return null;
			}
			try {
				var config = JSON.parse( get( 'eae_particles_json' ) || '' );
				return config && typeof config === 'object' ? config : null;
			} catch ( e ) {
				return null;
			}
		},
	} );
}() );
