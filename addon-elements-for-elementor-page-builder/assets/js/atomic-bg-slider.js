/**
 * EAE Background Slider (Vegas) on Elementor 4 (v4) Flexbox, Div Block and Grid.
 * Uses the shared runtime in atomic-extensions.js.
 */
( function ( $ ) {
	'use strict';

	var settings = window.eaeAtomicBgSlider || {};
	var mounted = [];
	var urlCache = {};

	function getLayer( el ) {
		for ( var i = 0; i < el.children.length; i++ ) {
			if ( el.children[ i ].classList.contains( 'eae-v4-bg-slider-layer' ) ) {
				return el.children[ i ];
			}
		}
		return null;
	}

	function destroy( inner ) {
		try {
			$( inner ).vegas( 'destroy' );
		} catch ( e ) {}
		mounted = mounted.filter( function ( item ) {
			return item !== inner;
		} );
	}

	function plain( value ) {
		return value && typeof value === 'object' && 'value' in value ? value.value : value;
	}

	// A picked image is stored by attachment ID; the editor's media library knows its URLs.
	function resolveUrl( editorWindow, id, size ) {
		var cacheKey = id + ':' + size;
		if ( ! urlCache[ cacheKey ] ) {
			var media = editorWindow.wp && editorWindow.wp.media;
			var attachment = media && media.attachment ? media.attachment( id ) : null;
			urlCache[ cacheKey ] = Promise.resolve( attachment && ! attachment.get( 'url' ) ? attachment.fetch() : null )
				.then( function () {
					var sizes = attachment && attachment.get( 'sizes' );
					if ( sizes && sizes[ size ] ) {
						return sizes[ size ].url;
					}
					return attachment ? attachment.get( 'url' ) || '' : '';
				} )
				.catch( function () {
					delete urlCache[ cacheKey ];
					return '';
				} );
		}
		return urlCache[ cacheKey ];
	}

	window.eaeAtomicExtensions.register( {
		name: 'bg-slider',

		mount: function ( el, config ) {
			if ( typeof $.fn.vegas !== 'function' ) {
				return;
			}
			var layer = document.createElement( 'div' );
			var inner = document.createElement( 'div' );
			layer.className = 'eae-v4-bg-slider-layer';
			inner.className = 'eae-section-bs-inner';
			layer.appendChild( inner );
			el.classList.add( 'eae-v4-bg-slider' );
			el.insertBefore( layer, el.firstChild );

			$( inner ).vegas( {
				slides: config.slides,
				transition: config.transition,
				animation: config.animation,
				overlay: config.overlay ? ( settings.overlayUrl || '' ) + config.overlay + '.png' : false,
				cover: config.cover,
				delay: config.delay,
				timer: config.timer,
			} );
			mounted.push( inner );
		},

		unmount: function ( el ) {
			var layer = getLayer( el );
			if ( layer ) {
				destroy( layer.firstChild );
				layer.remove();
			}
			el.classList.remove( 'eae-v4-bg-slider' );
		},

		isMounted: function ( el ) {
			return !! getLayer( el ) && el.classList.contains( 'eae-v4-bg-slider' );
		},

		// Stops sliders whose markup the editor removed while re-rendering a container.
		cleanup: function () {
			mounted.slice().forEach( function ( inner ) {
				if ( ! inner.isConnected ) {
					destroy( inner );
				}
			} );
		},

		readEditorConfig: function ( get, editorWindow ) {
			var images = get( 'eae_bg_slider_images' );
			if ( ! Array.isArray( images ) || ! images.length ) {
				return null;
			}

			var urls = images.map( function ( item ) {
				var image = item && ! item.disabled ? plain( item ) : null;
				var src = image ? plain( image.src ) : null;
				if ( ! src ) {
					return '';
				}
				var id = plain( src.id );
				var size = plain( image.size ) || 'full';
				return id ? resolveUrl( editorWindow, id, size ) : plain( src.url ) || '';
			} );

			return Promise.all( urls ).then( function ( resolved ) {
				var slides = resolved.filter( Boolean ).map( function ( src ) {
					return { src: src };
				} );
				if ( ! slides.length ) {
					return null;
				}
				var cover = get( 'eae_bg_slider_cover' );
				var timer = get( 'eae_bg_slider_timer' );
				return {
					slides: slides,
					transition: get( 'eae_bg_slider_transition' ) || 'fade',
					animation: get( 'eae_bg_slider_animation' ) || 'kenburns',
					overlay: get( 'eae_bg_slider_overlay' ) || '',
					cover: null === cover || undefined === cover ? true : !! cover,
					delay: Math.max( 500, parseInt( get( 'eae_bg_slider_delay' ), 10 ) || 5000 ),
					timer: null === timer || undefined === timer ? true : !! timer,
				};
			} );
		},
	} );
}( jQuery ) );
