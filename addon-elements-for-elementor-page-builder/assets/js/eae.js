jQuery(window).on("elementor/frontend/init", function () {
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/wts-gmap.default",
    function ($scope) {
      var $wrapper = $scope.find(".eae-markers");
      if($wrapper.length == 0){
        return;
      }
      
      map = new_map($scope.find(".eae-markers"));
      
      function new_map($el) {
        $wrapper = $scope.find(".eae-markers");
        var zoom = $wrapper.data("zoom");
        var $markers = $el.find(".marker");
        var styles = $wrapper.data("style");
        var prevent_scroll = $wrapper.data("scroll");
        // vars
        var args = {
          zoom: zoom,
          center: new google.maps.LatLng(0, 0),
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          styles: styles,
        };

        // create map
        var map = new google.maps.Map($el[0], args);

        // add a markers reference
        map.markers = [];

        // add markers
        $markers.each(function () {
          add_marker(jQuery(this), map);
        });

        // center map
        center_map(map, zoom);

        // return
        return map;
      }

      function add_marker($marker, map) {
        var animate = $wrapper.data("animate");
        var info_window_onload = $wrapper.data("show-info-window-onload");
        $wrapper = $scope.find(".eae-markers");
        //alert($marker.attr('data-lat') + ' - '+ $marker.attr('data-lng'));
        var latlng = new google.maps.LatLng(
          $marker.attr("data-lat"),
          $marker.attr("data-lng")
        );

        icon_img = $marker.attr("data-icon");
        if (icon_img != "") {
          var icon = {
            url: $marker.attr("data-icon"),
            scaledSize: new google.maps.Size(
              $marker.attr("data-icon-size"),
              $marker.attr("data-icon-size")
            ),
          };
        }

        //var icon = $marker.attr('data-icon');

        // create marker
        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          icon: icon,
          animation: google.maps.Animation.DROP,
        });
        if (animate == "animate-yes" && $marker.data("info-window") != "yes") {
          marker.setAnimation(google.maps.Animation.BOUNCE);
        }
        if (animate == "animate-yes") {
          google.maps.event.addListener(marker, "click", function () {
            marker.setAnimation(null);
          });
        }

        // add to array
        map.markers.push(marker);
        // if marker contains HTML, add it to an infoWindow

        if ($marker.html()) {
          // create info window
          var infowindow = new google.maps.InfoWindow({
            content: $marker.html(),
          });

          // show info window when marker is clicked
          if ($marker.data("info-window") == "yes") {
            infowindow.open(map, marker);
          }
          google.maps.event.addListener(marker, "click", function () {
            infowindow.open(map, marker);
          });
        }
        if (animate == "animate-yes") {
          google.maps.event.addListener(infowindow, "closeclick", function () {
            marker.setAnimation(google.maps.Animation.BOUNCE);
          });
        }
      }

      function center_map(map, zoom) {
        // vars
        var bounds = new google.maps.LatLngBounds();
        // loop through all markers and create bounds
        jQuery.each(map.markers, function (i, marker) {
          var latlng = new google.maps.LatLng(
            marker.position.lat(),
            marker.position.lng()
          );
          bounds.extend(latlng);
        });

        // only 1 marker?
        if (map.markers.length == 1) {
          // set center of map
          map.setCenter(bounds.getCenter());
          map.setZoom(zoom);
        } else {
          // fit to bounds
          map.fitBounds(bounds);
        }
      }
    }
  );

  elementorFrontend.hooks.addAction(
    "frontend/element_ready/global",
    function ($scope) {
      var eae_slides = [];
      var eae_slides_json = [];
      var eae_transition;
      var eae_animation;
      var eae_custom_overlay;
      var eae_overlay;
      var eae_cover;
      var eae_delay;
      var eae_timer;
      var slider_wrapper = $scope
        .children(".eae-section-bs")
        .children(".eae-section-bs-inner");

      if (slider_wrapper && slider_wrapper.data("eae-bg-slider")) {
        slider_images = slider_wrapper.data("eae-bg-slider");
        eae_transition = slider_wrapper.data("eae-bg-slider-transition");
        eae_animation = slider_wrapper.data("eae-bg-slider-animation");
        eae_custom_overlay = slider_wrapper.data("eae-bg-custom-overlay");
        if (eae_custom_overlay == "yes") {
          eae_overlay =
            eae_editor.plugin_url +
            "assets/lib/vegas/overlays/" +
            slider_wrapper.data("eae-bg-slider-overlay");
        } else {
          if (slider_wrapper.data("eae-bg-slider-overlay")) {
            eae_overlay =
              eae_editor.plugin_url +
              "assets/lib/vegas/overlays/" +
              slider_wrapper.data("eae-bg-slider-overlay");
          } else {
            eae_overlay =
              eae_editor.plugin_url +
              "assets/lib/vegas/overlays/" +
              slider_wrapper.data("eae-bg-slider-overlay");
          }
        }

        eae_cover = slider_wrapper.data("eae-bg-slider-cover");
        eae_delay = slider_wrapper.data("eae-bs-slider-delay");
        eae_timer = slider_wrapper.data("eae-bs-slider-timer");

        if (typeof slider_images != "undefined") {
          eae_slides = slider_images.split(",");

          jQuery.each(eae_slides, function (key, value) {
            var slide = [];
            slide.src = value;
            eae_slides_json.push(slide);
          });

          slider_wrapper.vegas({
            slides: eae_slides_json,
            transition: eae_transition,
            animation: eae_animation,
            overlay: eae_overlay,
            cover: eae_cover,
            delay: eae_delay,
            timer: eae_timer,
            init: function () {
              if (eae_custom_overlay == "yes") {
                var ob_vegas_overlay =
                  slider_wrapper.children(".vegas-overlay");
                ob_vegas_overlay.css("background-image", "");
              }
            },
          });
        }
      }
    }
  );
});

var isEditMode = false;
// var breakpoints = eae.breakpoints;
var popupInstance = [];
(function ($) {
  $(window).on("elementor/frontend/init", function () {
    var ab_image = function ($scope, $) {
      $scope
        .find(".eae-img-comp-container")
        .imagesLoaded()
        .done(function () {
          ab_style = $scope.find(".eae-img-comp-container").data("ab-style");
          slider_pos = $scope
            .find(".eae-img-comp-container")
            .data("slider-pos");
          if (ab_style === "horizontal") {
            separator_width = parseInt(
              $scope.find(".eae-img-comp-overlay").css("border-right-width")
            );
            horizontal($scope);
          } else {
            separator_width = parseInt(
              $scope.find(".eae-img-comp-overlay").css("border-bottom-width")
            );
            vertical($scope);
          }
        });

      function horizontal($scope) {
        var x, i, start_pos;
        /*find all elements with an "overlay" class:*/
        x = $scope.find(".eae-img-comp-overlay");
        start_pos = x.width();
        start_pos = (start_pos * slider_pos) / 100;
        compareImages(x[0]);

        function compareImages(img) {
          var slider,
            clicked = 0,
            w,
            h;
          /*get the width and height of the img element*/
          w = img.offsetWidth;
          h = img.offsetHeight;
          /*set the width of the img element to 50%:*/
          img.style.width = start_pos + "px";
          /*create slider:*/
          slider = $scope.find(".eae-img-comp-slider");
          slider = slider[0];
          /*position the slider in the middle:*/
          slider.style.top = h / 2 - slider.offsetHeight / 2 + "px";
          slider.style.left =
            start_pos - slider.offsetWidth / 2 - separator_width / 2 + "px";
          /*execute a function when the mouse button is pressed:*/
          if (!$scope.hasClass("elementor-element-edit-mode")) {
            slider.addEventListener("mousedown", slideReady);
            //slider.addEventListener("mouseover", slideReady);
            //img.addEventListener("mouseover", slideReady);

            /*and another function when the mouse button is released:*/
            window.addEventListener("mouseup", slideFinish);
            //slider.addEventListener("mouseout", slideFinish);
            //img.addEventListener("mouseout", slideFinish);
            /*or touched (for touch screens:*/
            slider.addEventListener("touchstart", slideReady);
            /*and released (for touch screens:*/
            window.addEventListener("touchstop", slideFinish);
          }

          function slideReady(e) {
            /*prevent any other actions that may occur when moving over the image:*/
            e.preventDefault();
            /*the slider is now clicked and ready to move:*/
            clicked = 1;
            /*execute a function when the slider is moved:*/
            window.addEventListener("mousemove", slideMove);
            //window.addEventListener("mouseover", slideMove);
            //window.addEventListener("touchmove", slideMove);
            slider.addEventListener("touchmove", touchMoveaction);
          }

          function slideFinish() {
            /*the slider is no longer clicked:*/
            clicked = 0;
          }

          function slideMove(e) {
            var pos;
            /*if the slider is no longer clicked, exit this function:*/
            if (clicked == 0) return false;
            /*get the cursor's x position:*/
            pos = getCursorPos(e);
            /*prevent the slider from being positioned outside the image:*/
            if (pos < 0) pos = 0;
            if (pos > w) pos = w;
            /*execute a function that will resize the overlay image according to the cursor:*/
            slide(pos);
          }

          function touchMoveaction(e) {
            var pos;
            /*if the slider is no longer clicked, exit this function:*/
            if (clicked == 0) return false;
            /*get the cursor's x position:*/
            pos = getTouchPos(e);

            /*prevent the slider from being positioned outside the image:*/
            if (pos < 0) pos = 0;
            if (pos > w) pos = w;
            /*execute a function that will resize the overlay image according to the cursor:*/
            slide(pos);
          }

          function getTouchPos(e) {
            var a,
              x = 0;
            a = img.getBoundingClientRect();

            /*calculate the cursor's x coordinate, relative to the image:*/
            x = e.changedTouches[0].clientX - a.left;
            return x;
          }

          function getCursorPos(e) {
            var a,
              x = 0;
            e = e || window.event;
            /*get the x positions of the image:*/
            a = img.getBoundingClientRect();
            /*calculate the cursor's x coordinate, relative to the image:*/
            x = e.pageX - a.left;

            /*consider any page scrolling:*/
            //x = x - window.pageXOffset;
            return x;
          }

          function slide(x) {
            /*resize the image:*/
            img.style.width = x + "px";
            /*position the slider:*/
            slider.style.left =
              img.offsetWidth -
              slider.offsetWidth / 2 -
              separator_width / 2 +
              "px";
          }
        }
      }

      function vertical($scope) {
        var x, i;
        /*find all elements with an "overlay" class:*/
        //x = document.getElementsByClassName("eae-img-comp-overlay");
        x = $scope.find(".eae-img-comp-overlay");
        start_pos = x.height();
        start_pos = (start_pos * slider_pos) / 100;
        compareImages(x[0]);

        function compareImages(img) {
          var slider,
            img,
            clicked = 0,
            w,
            h;
          /*get the width and height of the img element*/
          w = img.offsetWidth;
          h = img.offsetHeight;
          /*set the width of the img element to 50%:*/
          img.style.height = start_pos + "px";
          /*create slider:*/
          slider = $scope.find(".eae-img-comp-slider");
          slider = slider[0];
          /*position the slider in the middle:*/
          slider.style.top =
            start_pos - slider.offsetHeight / 2 - separator_width / 2 + "px";
          slider.style.left = w / 2 - slider.offsetWidth / 2 + "px";
          /*execute a function when the mouse button is pressed:*/
          if (!$scope.hasClass("elementor-element-edit-mode")) {
            slider.addEventListener("mousedown", slideReady);
            /*and another function when the mouse button is released:*/
            window.addEventListener("mouseup", slideFinish);
            /*or touched (for touch screens:*/
            slider.addEventListener("touchstart", slideReady);
            /*and released (for touch screens:*/
            window.addEventListener("touchstop", slideFinish);
          }

          function slideReady(e) {
            /*prevent any other actions that may occur when moving over the image:*/
            e.preventDefault();
            /*the slider is now clicked and ready to move:*/
            clicked = 1;
            /*execute a function when the slider is moved:*/
            window.addEventListener("mousemove", slideMove);
            slider.addEventListener("touchmove", touchMoveaction);
          }

          function slideFinish() {
            /*the slider is no longer clicked:*/
            clicked = 0;
          }

          function slideMove(e) {
            var pos;
            /*if the slider is no longer clicked, exit this function:*/
            if (clicked == 0) return false;
            /*get the cursor's x position:*/
            pos = getCursorPos(e);
            /*prevent the slider from being positioned outside the image:*/
            if (pos < 0) pos = 0;
            if (pos > h) pos = h;
            /*execute a function that will resize the overlay image according to the cursor:*/
            slide(pos);
          }

          function getCursorPos(e) {
            var a,
              x = 0;
            e = e || window.event;
            /*get the x positions of the image:*/
            a = img.getBoundingClientRect();
            /*calculate the cursor's x coordinate, relative to the image:*/
            x = e.pageY - a.top;
            /*consider any page scrolling:*/
            x = x - window.pageYOffset;

            return x;
          }

          function touchMoveaction(e) {
            var pos;
            /*if the slider is no longer clicked, exit this function:*/
            if (clicked == 0) return false;
            /*get the cursor's x position:*/
            pos = getTouchPos(e);

            /*prevent the slider from being positioned outside the image:*/
            if (pos < 0) pos = 0;
            if (pos > h) pos = h;
            /*execute a function that will resize the overlay image according to the cursor:*/
            slide(pos);
          }

          function getTouchPos(e) {
            var a,
              x = 0;
            a = img.getBoundingClientRect();

            /*calculate the cursor's x coordinate, relative to the image:*/
            x = e.changedTouches[0].clientY - a.top;

            //x = x - slider.offsetHeight;

            return x;
          }

          function slide(x) {
            /*resize the image:*/
            img.style.height = x + "px";
            /*position the slider:*/
            slider.style.top =
              img.offsetHeight -
              slider.offsetHeight / 2 -
              separator_width / 2 +
              "px";
          }
        }
      }
    };


    var ParticlesBG = function ($scope, $) {
      if ($scope.hasClass("eae-particle-yes")) {
        id = $scope.data("id");
        element_type = $scope.data("element_type");
        pdata = $scope.data("eae-particle");
        pdata_wrapper = $scope.find(".eae-particle-wrapper").data("eae-pdata");
        if (typeof pdata != "undefined" && pdata != "") {
          if ($scope.find(".eae-section-bs").length > 0) {
            $scope
              .find(".eae-section-bs")
              .after(
                '<div class="eae-particle-wrapper" id="eae-particle-' +
                  id +
                  '"></div>'
              );
            particlesJS("eae-particle-" + id, pdata);
          } else {
            if (element_type == "column") {
              $scope.prepend(
                '<div class="eae-particle-wrapper" id="eae-particle-' +
                  id +
                  '"></div>'
              );
            } else {
              $scope.prepend(
                '<div class="eae-particle-wrapper " id="eae-particle-' +
                  id +
                  '"></div>'
              );
            }
            particlesJS("eae-particle-" + id, pdata);
          }
        } else if (typeof pdata_wrapper != "undefined" && pdata_wrapper != "") {
          // console.log('Editor');
          // $scope.prepend('<div class="eae-particle-wrapper" id="eae-particle-'+ id +'"></div>');
          //console.log('calling particle js else', JSON.parse(pdata_wrapper));
          if (element_type == "column") {
            $scope.prepend(
              '<div class="eae-particle-wrapper eae-particle-area" id="eae-particle-' +
                id +
                '"></div>'
            );
          } else {
            $scope.prepend(
              '<div class="eae-particle-wrapper eae-particle-area" id="eae-particle-' +
                id +
                '"></div>'
            );
          }

          particlesJS("eae-particle-" + id, JSON.parse(pdata_wrapper));
        }
      }
    };

    /*EAE Animated Gradient Background*/

    var AnimatedGradient = function ($scope, $) {
      if ($scope.hasClass("eae-animated-gradient-yes")) {
        id = $scope.data("id");
        color = $scope.data("color");
        angle = $scope.data("angle");
        if ($scope.hasClass("elementor-element-edit-mode")) {
          color = $scope.find(".animated-gradient").data("color");
          angle = $scope.find(".animated-gradient").data("angle");
          gradient_color_editor =
            "linear-gradient(" + angle + "," + color + ")";
          $scope.prepend(
            '<div class="animated-gradient" style="background-image : ' +
              gradient_color_editor +
              ' "></div>'
          );
        }else{
          var gradient_color = "linear-gradient(" + angle + "," + color + ")";
          $scope.css("background-image", gradient_color);
        }
      }
	};
	  
	//   var jsEscaping = function (str) {
	// 	return String(str).replace(/[^\w. ]/gi, function(c){
	// 		return '\\u'+('0000'+c.charCodeAt(0).toString(16)).slice(-4);
	// 	});
	// }

    var EaePopup = function ($scope, $) {
      // To assign event
      const eaePopupLoaded = new Event("eaePopupLoaded");
      $preview_modal = $scope.find(".eae-popup-wrapper").data("preview-modal");
      var effect = $scope.find(".eae-popup-wrapper").data("effect");
      $close_btn_type = $scope
        .find(".eae-popup-wrapper")
        .data("close-button-type");
		$close_btn =  $scope.find(".eae-popup-wrapper").data("close-btn");
      if ($close_btn_type == "icon") {
        $close_btn_html = '<i class="eae-close ' + $close_btn + '"> </i>';
      } else {
        $close_btn_html =
          '<svg class="eae-close" style="-webkit-mask: url(' +
          $close_btn +
          "); mask: url(" +
          $close_btn +
          '); "></svg>';
      }
    
		var eae_popup = $scope.find('.eae-popup-container');
		var eae_popup_id = eae_popup.attr('id');
		if (typeof popupInstance[eae_popup_id] === 'undefined' || popupInstance[eae_popup_id] === null) {
			popupInstance[eae_popup_id] = eae_popup.find('.eae-popup-content').html();
			eae_popup.find('.eae-popup-content').html('');
		}
		
		var popup_link = $scope.find(".eae-popup-wrapper .eae-popup-link");
		popup_link.on('click', function () {
		  
			eae_popup_id = jQuery(this).data('id');
			eae_popup_arr = jQuery('.eae-popup-container.eae-popup-' + eae_popup_id); //Its working
			eae_popup_arr.each(function (index, element) {
			jQuery(element).find('.eae-popup-content').html(popupInstance[eae_popup_id]);
		});
		//eae_popup.find('.eae-popup-content').html(popupInstance[eae_popup_id]);
      });
  
        $magnific = $scope.find(".eae-popup-link").eaePopup({
          type: "inline",
  
          disableOn: 0,
  
          key: null,
  
          midClick: false,
  
          mainClass:
            "eae-popup eae-popup-" +
            $scope.find(".eae-popup-link").data("id") +
            " eae-wrap-" +
            $scope.find(".eae-popup-link").data("ctrl-id"),
  
          preloader: true,
  
          focus: "", // CSS selector of input to focus after popup is opened
  
          closeOnContentClick: false,
  
          closeOnBgClick: true,
  
          closeBtnInside: $scope.find(".eae-popup-wrapper").data("close-in-out"),
  
          showCloseBtn: true,
  
          enableEscapeKey: true,
  
          modal: false,
  
          alignTop: false,
  
          removalDelay: 200,
  
          prependTo: null,
  
          fixedContentPos: true,
  
          fixedBgPos: "auto",
  
          overflowY: "auto",
  
          closeMarkup: $close_btn_html,
  
          tClose: "Close (Esc)",
  
          tLoading: "Loading...",
  
          autoFocusLast: true,
  
        callbacks: {
        beforeOpen: function () {
          if (effect != '') {
            this.st.mainClass = "eae-popup eae-popup-" +
              $scope.find(".eae-popup-link").data("id") +
              " eae-wrap-" + $scope.find(".eae-popup-link").data("ctrl-id") + " mfp-" + effect;
          }
  
        },
        open: function () {
          var id = $scope.find(".eae-popup-link").data("id");
          var wrapper = jQuery('.eae-popup-' + id + '.eae-popup-container .eae-modal-content');
			eae_element_reinitialize(wrapper);
			var cf7 = wrapper.find('.wpcf7-form');
			//console.log(cf7);
			if (cf7.length > 0) {
				cf7.each(function (index, element) {
					wpcf7.init(element);
				});
			}
        },
        
        afterClose: function () {
          var eae_popup = $scope.find('.eae-popup-container');
          eae_popup.find('.eae-popup-content').html('');
        },
            },
        });
  
        if ($preview_modal == "yes") {
          if ($scope.hasClass("elementor-element-edit-mode")) {
            $scope.find(".eae-popup-link").click();
          }
        }
      };
      
      var eae_element_reinitialize = function (wrapper) {
        wrapper.find('.e-con').each(function(){
          elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
        });
  
        wrapper.find('.elementor-section').each(function(){
          elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
        });
  
        wrapper.find('.elementor-column').each(function(){
          elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
        });
        
        wrapper.find('.elementor-widget').each(function(){
          elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
        });
      }

    var EAETestimonial = function ($scope, $) {
      if ($scope.find(".eae-grid-wrapper").hasClass("eae-masonry-yes")) {
        //console.log('grid');
        var grid = $scope.find(".eae-grid");
        var $grid_obj = grid.masonry({});
        $grid_obj.imagesLoaded().progress(function () {
          $grid_obj.masonry("layout");
        });
      }
      if ($scope.find(".eae-layout-carousel").length) {
        outer_wrapper = $scope.find(".eae-swiper-outer-wrapper");
        wid = $scope.data("id");
        wclass = ".elementor-element-" + wid;
        var direction = outer_wrapper.data("direction");
        var speed = outer_wrapper.data("speed");
        var autoplay = outer_wrapper.data("autoplay");
        var duration = outer_wrapper.data("duration");
        //console.log(duration);
        var effect = outer_wrapper.data("effect");
        var space = outer_wrapper.data("space");
        var loop = outer_wrapper.data("loop");
        if (loop == "yes") {
          loop = true;
        } else {
          loop = false;
        }
        var slides_per_view = outer_wrapper.data("slides-per-view");
        var slides_per_group = outer_wrapper.data("slides-per-group");
        var ptype = outer_wrapper.data("ptype");
        var navigation = outer_wrapper.data("navigation");
        var clickable = outer_wrapper.data("clickable");
        var keyboard = outer_wrapper.data("keyboard");
        var scrollbar = outer_wrapper.data("scrollbar");
        adata = {
          direction: direction,
          effect: effect,
          spaceBetween: space.desktop,
          loop: loop,
          speed: speed,
          slidesPerView: slides_per_view.desktop,
          slidesPerGroup: slides_per_group.desktop,
          observer: true,
          mousewheel: {
            invert: true,
          },
          breakpoints: {
            1024: {
              spaceBetween: space.tablet,
              slidesPerView: slides_per_view.tablet,
              slidesPerGroup: slides_per_group.tablet,
            },
            767: {
              spaceBetween: space.mobile,
              slidesPerView: slides_per_view.mobile,
              slidesPerGroup: slides_per_group.mobile,
            },
          },
        };
        if (effect == "fade") {
          adata["fadeEffect"] = {
            crossFade: false,
          };
        }
        if (autoplay == "yes") {
          adata["autoplay"] = {
            delay: duration,
            disableOnInteraction: false,
          };
        } else {
          adata["autoplay"] = false;
        }
        if (navigation == "yes") {
          adata["navigation"] = {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          };
        }
        if (ptype != "") {
          adata["pagination"] = {
            el: ".swiper-pagination",
            type: ptype,
          };
        }
        if (ptype == "bullets" && clickable == "yes") {
          adata["pagination"] = {
            el: ".swiper-pagination",
            clickable: true,
            type: ptype,
          };
        }
        if (scrollbar == "yes") {
          adata["scrollbar"] = {
            el: ".swiper-scrollbar",
            draggable: true,
          };
        }
        if (keyboard == "yes") {
          adata["keyboard"] = {
            enabled: true,
            onlyInViewport: true,
          };
        }
        if (loop == false) {
          adata["autoplay"] = {
            delay: duration,
            stopOnLastSlide: true,
            disableOnInteraction: false,
          };
        }
        //console.log(adata);
        window.mswiper = new Swiper(
          ".elementor-element-" +
            wid +
            " .eae-swiper-outer-wrapper .swiper-container",
          adata
        );
        $(
          ".elementor-element-" +
            wid +
            " .eae-swiper-outer-wrapper .swiper-container"
        ).css("visibility", "visible");
      }
    };

    /* Info Circle */
    var InfoCircleHandler = function ($scope, $) {
      $wrap_class = ".elementor-element-" + $scope.data("id");
      $angle = 0;

      function set_icon_mobile($wrap_class) {
        $icons = $(document).find($wrap_class).find(".eae-ic-icon-wrap");

        if (window.innerWidth < 767) {
          
          $icons.each(function (index, value) {
            $(value).css("top", $(value).height() / 2 + 8 + "px");
            $(value)
              .next(".eae-info-circle-item__content-wrap")
              .css("padding-top", $(value).height() / 2 + 8 + "px");
          });
        } else {
          $icons.each(function (index, value) {
            $(value).css("margin-left", $(value).outerWidth() * -0.5);
            $(value).css("margin-top", $(value).outerHeight() * -0.5);
            $a = arc_to_coords($angle);
            $b = 360 / $icons.length;
            $(value).css("left", $a.x + "%");
            $(value).css("top", $a.y + "%");
            $angle += $b;
          });
        }
      }

      set_icon_mobile($scope);

      function arc_to_coords(angle) {
        angle = ((angle - 90) * Math.PI) / 180;

        return {
          x: 50 + 45 * Math.cos(angle),
          y: 50 + 45 * Math.sin(angle),
        };
      }

      var timer = null;
      $autoplayDuration = $scope.find(".eae-info-circle").data("delay");

      function startSetInterval() {
        if ($scope.find(".eae-info-circle").data("autoplay") == "yes") {
          timer = setInterval(showDiv, $autoplayDuration);
        }
      }

      // start function on page load
      startSetInterval();

      // hover behaviour
      $scope.find(".eae-ic-icon-wrap").hover(
        function () {
          clearInterval(timer);
        },
        function () {
          startSetInterval();
        }
      );
      if ($scope.find(".eae-info-circle-item").length > 0) {
        $($scope.find(".eae-info-circle-item")[0]).addClass("eae-active");
      }

      $scope.find(".eae-ic-icon-wrap").on("click", function () {
        $scope.find(".eae-info-circle-item").removeClass("eae-active");
        $(this).parent().addClass("eae-active");
      });
      if ($scope.hasClass("eae-mouseenter-yes")) {
        $scope.find(".eae-ic-icon-wrap").on("mouseenter", function () {
          $scope.find(".eae-info-circle-item").removeClass("eae-active");
          $(this).parent().addClass("eae-active");
        });
      }

      function showDiv() {
        if ($scope.find(".eae-active").next().length > 0) {
          $scope
            .find(".eae-active")
            .next()
            .addClass("eae-active")
            .siblings()
            .removeClass("eae-active");
        } else {
          $scope
            .find(".eae-info-circle-item")
            .eq(0)
            .addClass("eae-active")
            .siblings()
            .removeClass("eae-active");
        }
      }

      window.addEventListener(
        "resize",
        set_icon_mobile.bind(this, $wrap_class)
      );
    };

    var TimelineHandler = function ($scope, $){
      const wid = $scope.data("id");
      const wid_class = ".elementor-element-" + wid;
      const timeline = document.querySelector(wid_class);
      const timelineWrapper = timeline.querySelector('.eae-timeline');
      const offsetTop  = timelineWrapper.dataset.topOffset;
      const items = timeline.querySelectorAll(".eae-timeline-item");
      const items_icon = timeline.querySelectorAll(".eae-tl-icon-wrapper");
      const progress_bar = timeline.querySelector(".eae-timline-progress-bar");
      const progressInner = timeline.querySelector('.eae-pb-inner-line');
      const progressBarStop = items_icon[items_icon.length - 1].getBoundingClientRect().bottom;
      //console.log('timeline', timeline);
      setProgressBar();
      onScroll();
      window.addEventListener('resize', () => {
        setProgressBar();
      });
      document.addEventListener('scroll', () => {
        if (window.requestAnimationFrame) {
          window.requestAnimationFrame(() => {
            onScroll();
          });
        } else {
          onScroll();
        }
      });

      function setProgressBar() {
        const first_card = items[0].getBoundingClientRect();
        const last_card = items[items.length - 1].getBoundingClientRect();
        const first_icon_space =  items_icon[0].getBoundingClientRect().bottom - first_card.top;
        const first_item_icon = items_icon[0].getBoundingClientRect();
        const progress_bar_left = items_icon[0].offsetLeft + items_icon[0].offsetWidth / 2;
        const last_item_icon = items_icon[items_icon.length - 1].getBoundingClientRect();
        progress_bar.style.height = last_item_icon.top - first_item_icon.bottom + "px";
        progress_bar.style.top = first_icon_space + "px";
        progress_bar.style.left = progress_bar_left + "px";
        progress_bar.style.display = 'block';
        //progressInner.style.maxHeight = last_item_icon.bottom - first_item_icon.top + "px";
        // progress_bar.style.top = first_item_icon.top + "px";
        // progress_bar.style.bottom = first_item_icon.top + "px";
        // console.log('first Icon', first_item_icon);
      }

      function onScroll(){
        const progressInner = timeline.querySelector('.eae-pb-inner-line');
        const scrollTop = Math.abs(window.scrollY + parseFloat(offsetTop));
        const wrapperOffsetTop = timelineWrapper.getBoundingClientRect().top + window.scrollY;
        const lastIconBottom = items_icon[items_icon.length - 1].getBoundingClientRect().bottom + window.scrollY;
        // && window.scrollY < timelineWrapper.getBoundingClientRect().bottom + window.scrollY
        if (scrollTop > wrapperOffsetTop ) {
          progressInner.style.height = scrollTop - wrapperOffsetTop + 'px';
          //console.log('scrollTop', scrollTop);
          items.forEach((item, index) => {
            let itemOffsetTop = item.getBoundingClientRect().top + window.scrollY;
            if(scrollTop > itemOffsetTop){
              item.classList.add('eae-tl-item-focused');
            }else{
              item.classList.remove('eae-tl-item-focused');
            }
          });
        } else{
          items[0].classList.remove('eae-tl-item-focused');
        }
      }
    }


    function eaeSetCookie(cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + exdays * 60 * 60 * 1000);

      //console.log('exp time',cookie_expire);
      //d.setTime(d.getTime() + ( exdays * 60 * 60 * 1000));
      var expires = "expires=" + d.toUTCString();
      document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function eaeGetCookie(cname) {
      var name = cname + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var ca = decodedCookie.split(";");
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }

    var EgTimerSkin1 = function ($scope, $) {
      var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
      var cookie_expire = $scope
        .find(".eae-evergreen-wrapper")
        .data("egt-expire");
      var element_type = $scope
        .find(".eae-evergreen-wrapper")
        .data("element-type");
      var element_id =
        "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var element_cookie_id =
        "eae-temp-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var actions = $scope.find(".eae-evergreen-wrapper").data("actions");

      var unqId = $scope.find(".eae-evergreen-wrapper").data("unqid");

      var now = new Date().getTime();

      // for front end time get from cookie

      if (!$scope.hasClass("elementor-element-edit-mode")) {
        if (element_type === "countdown") {
          date1 = new Date(countDownDate);
          countDownDate = date1.getTime();

          var expires1 = "expires=" + date1.toUTCString();
          document.cookie =
            element_cookie_id +
            "=" +
            date1.getTime() +
            ";" +
            expires1 +
            ";path=/";
        } else {
          var first_load_value = eaeGetCookie(element_id);
          var date1 = "";
          if (first_load_value !== "") {
            date1 = new Date(parseInt(first_load_value));
            date1.setSeconds(
              date1.getSeconds() +
                $scope.find(".eae-evergreen-wrapper").data("egtime")
            );
            countDownDate = date1.getTime();

            var d2 = new Date(parseInt(first_load_value));
            d2.setTime(d2.getTime() + cookie_expire * 60 * 60 * 1000);
            var expires2 = "expires=" + d2.toUTCString();
            document.cookie =
              element_id + "=" + first_load_value + ";" + expires2 + ";path=/";

            var d1 = new Date(parseInt(first_load_value));
            d1.setTime(
              d1.getTime() +
                $scope.find(".eae-evergreen-wrapper").data("egtime") * 1000
            );
            var expires1 = "expires=" + d1.toUTCString();

            //console.log('expire',expires);
            if (countDownDate - now > 0) {
              document.cookie =
                element_cookie_id +
                "=" +
                first_load_value +
                ";" +
                expires1 +
                ";path=/";
            }
          } else {
            //console.log('countdown date set cookie',countDownDate);
            temp_date = countDownDate;
            date1 = new Date();
            date1.setSeconds(
              date1.getSeconds() +
                $scope.find(".eae-evergreen-wrapper").data("egtime")
            );
            countDownDate = date1.getTime();
            //console.log('countdown date set cookie',countDownDate);
            eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
            //eaeSetCookie(element_cookie_id, new Date().getTime(), countDownDate);

            var d = new Date();
            d.setTime(d.getTime() + temp_date * 1000);
            var expires = "expires=" + d.toUTCString();
            //console.log('first load');
            //console.log('expire',expires);

            document.cookie =
              element_cookie_id +
              "=" +
              new Date().getTime() +
              ";" +
              expires +
              ";path=/";
          }
        }
      }
      if (!$scope.hasClass("elementor-element-edit-mode")) {
        var distance = countDownDate - now;

        if (distance < 0) {
          if (actions.length > 0) {
            actions.forEach(function (value) {
              if (value === "redirect") {
                $url = $scope
                  .find(".eae-evergreen-wrapper")
                  .data("redirected-url");
                if ($.trim($url) !== "") {
                  window.location.href = $url1;
                }
              }
              if (value === "hide") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $scope.find("#eaeclockdiv").css("display", "none");
                  $scope.find(".egt-title").css("display", "none");
                }
              }
              if (value === "message") {
                $scope.find(".eae-egt-message").css("display", "block");
              }
              if (value === "hide_parent") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $p_secs = $scope.closest("section");
                  $p_secs.css("display", "none");
                }
              }
            });
          }

          days = "00";
          hours = "00";
          minutes = "00";
          seconds = "00";

          $scope
            .find("." + unqId)
            .find("#eaedivDays")
            .html(days);
          $scope
            .find("." + unqId)
            .find("#eaedivHours")
            .html(hours.slice(-2));
          $scope
            .find("." + unqId)
            .find("#eaedivMinutes")
            .html(minutes.slice(-2));
          $scope
            .find("." + unqId)
            .find("#eaedivSeconds")
            .html(seconds.slice(-2));
          return;
        }
      }

      // For editor

      if ($scope.hasClass("elementor-element-edit-mode")) {
        if (element_type === "countdown") {
          date1 = new Date(countDownDate);
          countDownDate = date1.getTime();
        } else {
          date1 = new Date();
          date1.setSeconds(
            date1.getSeconds() +
              $scope.find(".eae-evergreen-wrapper").data("egtime")
          );
          countDownDate = date1.getTime();
        }
      }

      var y = setInterval(function () {
        //console.log('c date inner',countDownDate);
        // Get todays date and time

        var now = new Date().getTime();
        // Find the distance between now and the count down date

        var distance = countDownDate - now;

        //console.log('distance',distance);
        var days = 0;
        var hours = 0;
        var minutes = 0;
        var seconds = 0;
        if (distance > 0) {
          // Time calculations for days, hours, minutes and seconds
          days = Math.floor(distance / (1000 * 60 * 60 * 24));
          hours =
            "0" +
            Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          minutes =
            "0" + Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          seconds = "0" + Math.floor((distance % (1000 * 60)) / 1000);
        } else {
          if (actions.length > 0) {
            if (!$scope.hasClass("elementor-element-edit-mode")) {
              actions.forEach(function (value) {
                if (value === "redirect") {
                  $url1 = $scope
                    .find(".eae-evergreen-wrapper")
                    .data("redirected-url");
                  if ($.trim($url1) !== "") {
                    window.location.href = $url1;
                  }
                }
                if (value === "hide") {
                  $scope.find("#eaeclockdiv").css("display", "none");
                  $scope.find(".egt-title").css("display", "none");
                }
                if (value === "message") {
                  $scope.find(".eae-egt-message").css("display", "block");
                }
                if (value === "hide_parent") {
                  if (!$scope.hasClass("elementor-element-edit-mode")) {
                    $p_secs = $scope.closest("section");
                    $p_secs.css("display", "none");
                  }
                }
              });
            }
          }
          clearInterval(y);
          days = "0";
          hours = "00";
          minutes = "00";
          seconds = "00";
        }

        if (days < 10) {
          days = "0" + days;
        }
        $scope
          .find("." + unqId)
          .find("#eaedivDays")
          .html(days);
        $scope
          .find("." + unqId)
          .find("#eaedivHours")
          .html(hours.slice(-2));
        $scope
          .find("." + unqId)
          .find("#eaedivMinutes")
          .html(minutes.slice(-2));
        $scope
          .find("." + unqId)
          .find("#eaedivSeconds")
          .html(seconds.slice(-2));
      }, 1000);
    };

    var EgTimerSkin2 = function ($scope, $) {
      var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
      var cookie_expire = $scope
        .find(".eae-evergreen-wrapper")
        .data("egt-expire");
      var element_type = $scope
        .find(".eae-evergreen-wrapper")
        .data("element-type");
      var element_id =
        "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var element_cookie_id =
        "eae-temp-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var actions = $scope.find(".eae-evergreen-wrapper").data("actions");
      var unqId = $scope.find(".eae-evergreen-wrapper").data("unqid");

      var now = new Date().getTime();

      if (!$scope.hasClass("elementor-element-edit-mode")) {
        if (element_type === "countdown") {
          date1 = new Date(countDownDate);
          countDownDate = date1.getTime();
          var expires1 = "expires=" + date1.toUTCString();

          document.cookie =
            element_cookie_id +
            "=" +
            date1.getTime() +
            ";" +
            expires1 +
            ";path=/";
        } else {
          var first_load_value = eaeGetCookie(element_id);
          var date1 = "";
          if (first_load_value !== "") {
            date1 = new Date(parseInt(first_load_value));
            date1.setSeconds(
              date1.getSeconds() +
                $scope.find(".eae-evergreen-wrapper").data("egtime")
            );
            countDownDate = date1.getTime();

            var d2 = new Date(parseInt(first_load_value));
            d2.setTime(d2.getTime() + cookie_expire * 60 * 60 * 1000);
            var expires2 = "expires=" + d2.toUTCString();
            document.cookie =
              element_id + "=" + first_load_value + ";" + expires2 + ";path=/";

            var d1 = new Date(parseInt(first_load_value));
            d1.setTime(
              d1.getTime() +
                $scope.find(".eae-evergreen-wrapper").data("egtime") * 1000
            );
            var expires1 = "expires=" + d1.toUTCString();

            if (countDownDate - now > 0) {
              document.cookie =
                element_cookie_id +
                "=" +
                first_load_value +
                ";" +
                expires1 +
                ";path=/";
            }
          } else {
            temp_date = countDownDate;
            date1 = new Date();
            date1.setSeconds(
              date1.getSeconds() +
                $scope.find(".eae-evergreen-wrapper").data("egtime")
            );
            countDownDate = date1.getTime();
            //console.log('countdown date set cookie',countDownDate);
            eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
            //eaeSetCookie(element_cookie_id, new Date().getTime(), countDownDate);

            var d = new Date();
            d.setTime(d.getTime() + temp_date * 1000);
            var expires = "expires=" + d.toUTCString();

            document.cookie =
              element_cookie_id +
              "=" +
              new Date().getTime() +
              ";" +
              expires +
              ";path=/";
          }
        }
      }
      if (!$scope.hasClass("elementor-element-edit-mode")) {
        var distance = countDownDate - now;
        if (distance < 0) {
          if (actions.length > 0) {
            actions.forEach(function (value) {
              if (value === "redirect") {
                $url = $scope
                  .find(".eae-evergreen-wrapper")
                  .data("redirected-url");
                if ($.trim($url) !== "") {
                  window.location.href = $url;
                }
              }
              if (value === "hide") {
                $scope
                  .find("." + unqId)
                  .find(".timer-container")
                  .css("display", "none");
                $scope
                  .find("." + unqId)
                  .find(".egt-title")
                  .css("display", "none");
              }
              if (value === "message") {
                $scope
                  .find("." + unqId)
                  .find(".eae-egt-message")
                  .css("display", "block");
              }
              if (value === "hide_parent") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $p_secs = $scope.closest("section");
                  $p_secs.css("display", "none");
                }
              }
            });
          }

          return;
        }
      }

      if ($scope.hasClass("elementor-element-edit-mode")) {
        if (element_type === "countdown") {
          date1 = new Date(countDownDate);
          countDownDate = date1.getTime();
        } else {
          date1 = new Date();
          date1.setSeconds(
            date1.getSeconds() +
              $scope.find(".eae-evergreen-wrapper").data("egtime")
          );
          countDownDate = date1.getTime();
        }
      }

      // Update the count down every 1 second
      var x = setInterval(function () {
        // Get todays date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor(
          (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $scope
          .find("." + unqId)
          .find("#eaeulSec1")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulSec1")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");
        $scope
          .find("." + unqId)
          .find("#eaeulSec")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulSec")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");
        // If the count down is finished, write some text
        if (distance < 0) {
          clearInterval(x);
          if (actions.length > 0) {
            actions.forEach(function (value) {
              if (value === "redirect") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $url1 = $scope
                    .find(".eae-evergreen-wrapper")
                    .data("redirected-url");
                  if ($.trim($url1) !== "") {
                    window.location.href = $url1;
                  }
                }
              }
              if (value === "hide") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $scope
                    .find("." + unqId)
                    .find(".timer-container")
                    .css("display", "none");
                  $scope
                    .find("." + unqId)
                    .find(".egt-title")
                    .css("display", "none");
                }
              }
              if (value === "message") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $scope
                    .find("." + unqId)
                    .find(".eae-egt-message")
                    .css("display", "block");
                }
              }
              if (value === "hide_parent") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $p_secs = $scope.closest("section");
                  $p_secs.css("display", "none");
                }
              }
            });
          }
          //document.getElementById("demo").Html = "EXPIRED";
          return;
        }
        if ($.trim(seconds).length === 2) {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulSec1 li:eq( " + $.trim(seconds).charAt(1) + " )";
          var b = "#eaeulSec li:eq( " + $.trim(seconds).charAt(0) + " )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulSec1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulSec1 li:first-child")
              .addClass("flip-clock-before");
          }
          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulSec li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulSec li:first-child")
              .addClass("flip-clock-before");
          }
        } else {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulSec1 li:eq( " + $.trim(seconds).charAt(0) + " )";
          var b = "#eaeulSec li:eq( 0 )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulSec1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulSec1 li:first-child")
              .addClass("flip-clock-before");
          }

          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulSec li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulSec li:first-child")
              .addClass("flip-clock-before");
          }
        }

        $scope
          .find("." + unqId)
          .find("#eaeulMin1")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulMin1")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");
        $scope
          .find("." + unqId)
          .find("#eaeulMin")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulMin")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");

        if ($.trim(minutes).length == 2) {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulMin1 li:eq( " + $.trim(minutes).charAt(1) + " )";
          var b = "#eaeulMin li:eq( " + $.trim(minutes).charAt(0) + " )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulMin1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulMin1 li:first-child")
              .addClass("flip-clock-before");
          }
          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulMin li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulMin li:first-child")
              .addClass("flip-clock-before");
          }
        } else {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulMin1 li:eq( " + $.trim(minutes).charAt(0) + " )";
          var b = "#eaeulMin li:eq( 0 )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulMin1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulMin1 li:first-child")
              .addClass("flip-clock-before");
          }

          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulMin li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulMin li:first-child")
              .addClass("flip-clock-before");
          }
        }

        $scope
          .find("." + unqId)
          .find("#eaeulHour1")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulHour1")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");
        $scope
          .find("." + unqId)
          .find("#eaeulHour")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulHour")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");

        if ($.trim(hours).length == 2) {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulHour1 li:eq( " + $.trim(hours).charAt(1) + " )";
          var b = "#eaeulHour li:eq( " + $.trim(hours).charAt(0) + " )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulHour1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulHour1 li:first-child")
              .addClass("flip-clock-before");
          }
          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulHour li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulHour li:first-child")
              .addClass("flip-clock-before");
          }
        } else {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulHour1 li:eq( " + $.trim(hours).charAt(0) + " )";
          var b = "#eaeulHour li:eq( 0 )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulHour1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulHour li:first-child")
              .addClass("flip-clock-before");
          }

          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulHour li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulHour li:first-child")
              .addClass("flip-clock-before");
          }
        }

        $scope
          .find("." + unqId)
          .find("#eaeulDay1")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulDay1")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");
        $scope
          .find("." + unqId)
          .find("#eaeulDay")
          .find(".flip-clock-active")
          .removeClass("flip-clock-active");
        $scope
          .find("." + unqId)
          .find("#eaeulDay")
          .find(".flip-clock-before")
          .removeClass("flip-clock-before");

        if ($.trim(days).length == 2) {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulDay1 li:eq( " + $.trim(days).charAt(1) + " )";
          var b = "#eaeulDay li:eq( " + $.trim(days).charAt(0) + " )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulDay1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulDay1 li:first-child")
              .addClass("flip-clock-before");
          }
          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulDay li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulDay li:first-child")
              .addClass("flip-clock-before");
          }
        } else {
          //var x = parseInt($.trim(seconds).charAt(1)) - 1;
          var a = "#eaeulDay1 li:eq( " + $.trim(days).charAt(0) + " )";
          var b = "#eaeulDay li:eq( 0 )";

          if (
            $scope
              .find("." + unqId)
              .find(a)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(a)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(a)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulDay1 li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulDay li:first-child")
              .addClass("flip-clock-before");
          }

          if (
            $scope
              .find("." + unqId)
              .find(b)
              .next().length > 0
          ) {
            $scope
              .find("." + unqId)
              .find(b)
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find(b)
              .next()
              .addClass("flip-clock-before");
          } else {
            $scope
              .find("." + unqId)
              .find("#eaeulDay li:last-child")
              .addClass("flip-clock-active");
            $scope
              .find("." + unqId)
              .find("#eaeulDay li:first-child")
              .addClass("flip-clock-before");
          }
        }
      }, 1000);
    };

    var EgTimerSkin3 = function ($scope, $) {
      var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
      var cookie_expire = $scope
        .find(".eae-evergreen-wrapper")
        .data("egt-expire");
      var element_type = $scope
        .find(".eae-evergreen-wrapper")
        .data("element-type");
      var element_id =
        "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var element_cookie_id =
        "eae-temp-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var actions = $scope.find(".eae-evergreen-wrapper").data("actions");
      var dayShow = $scope.find(".eae-evergreen-wrapper").data("days");
      var hourShow = $scope.find(".eae-evergreen-wrapper").data("hours");
      var minShow = $scope.find(".eae-evergreen-wrapper").data("mins");
      var secShow = $scope.find(".eae-evergreen-wrapper").data("seconds");
      var unqId = $scope.find(".eae-evergreen-wrapper").data("unqid");

      var now = new Date().getTime();

      // for front end time get from cookie

      if (!$scope.hasClass("elementor-element-edit-mode")) {
        if (element_type === "countdown") {
          date1 = new Date(countDownDate);
          countDownDate = date1.getTime();

          var expires1 = "expires=" + date1.toUTCString();
          document.cookie =
            element_cookie_id +
            "=" +
            date1.getTime() +
            ";" +
            expires1 +
            ";path=/";
        } else {
          var first_load_value = eaeGetCookie(element_id);
          var date1 = "";
          if (first_load_value !== "") {
            date1 = new Date(parseInt(first_load_value));
            date1.setSeconds(
              date1.getSeconds() +
                $scope.find(".eae-evergreen-wrapper").data("egtime")
            );
            countDownDate = date1.getTime();

            var d2 = new Date(parseInt(first_load_value));
            d2.setTime(d2.getTime() + cookie_expire * 60 * 60 * 1000);
            var expires2 = "expires=" + d2.toUTCString();
            document.cookie =
              element_id + "=" + first_load_value + ";" + expires2 + ";path=/";

            var d1 = new Date(parseInt(first_load_value));
            d1.setTime(
              d1.getTime() +
                $scope.find(".eae-evergreen-wrapper").data("egtime") * 1000
            );
            var expires1 = "expires=" + d1.toUTCString();

            //console.log('expire',expires);
            if (countDownDate - now > 0) {
              document.cookie =
                element_cookie_id +
                "=" +
                first_load_value +
                ";" +
                expires1 +
                ";path=/";
            }
          } else {
            //console.log('countdown date set cookie',countDownDate);
            temp_date = countDownDate;
            date1 = new Date();
            date1.setSeconds(
              date1.getSeconds() +
                $scope.find(".eae-evergreen-wrapper").data("egtime")
            );
            countDownDate = date1.getTime();
            //console.log('countdown date set cookie',countDownDate);
            eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
            //eaeSetCookie(element_cookie_id, new Date().getTime(), countDownDate);

            var d = new Date();
            d.setTime(d.getTime() + temp_date * 1000);
            var expires = "expires=" + d.toUTCString();
            //console.log('expire',expires);

            document.cookie =
              element_cookie_id +
              "=" +
              new Date().getTime() +
              ";" +
              expires +
              ";path=/";
          }
        }
      }
      if (!$scope.hasClass("elementor-element-edit-mode")) {
        var distance = updateTime(countDownDate);

        if (parseInt(distance.all) < 1) {
          if (actions.length > 0) {
            actions.forEach(function (value) {
              if (value === "redirect") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $url = $scope
                    .find(".eae-evergreen-wrapper")
                    .data("redirected-url");
                  if ($url !== "") {
                    window.location.href = $url;
                  }
                }
              }
              if (value === "hide_parent") {
                if (!$scope.hasClass("elementor-element-edit-mode")) {
                  $p_secs = $scope.closest("section");
                  $p_secs.css("display", "none");
                }
              }
              if (value === "hide") {
                $scope.find("#timer").css("display", "none");
                $scope.find(".egt-title").css("display", "none");
                $scope.find(".desc").css("display", "none");
              }
              if (value === "message") {
                $scope.find(".eae-egt-message").css("display", "block");
              }
            });

            if (actions.length === 1) {
              if (actions[0] === "" || actions[0] === "message") {
                var clock = $scope.find("." + unqId).find("#timer")[0];
                //clock.innerHTML = "<span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span>";
                if (dayShow === "yes") {
                  clock.innerHTML =
                    "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                }
                if (hourShow === "yes") {
                  if (dayShow === "yes") {
                    $(clock).append(
                      "<span class='egt-time eae-time-wrapper'><div>00</div></span>"
                    );
                  } else {
                    clock.innerHTML =
                      "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                  }
                }
                if (minShow === "yes") {
                  if (dayShow === "yes" || hourShow === "yes") {
                    $(clock).append(
                      "<span class='egt-time eae-time-wrapper'><div>00</div></span>"
                    );
                  } else {
                    clock.innerHTML =
                      "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                  }
                }
                if (secShow === "yes") {
                  if (
                    dayShow === "yes" ||
                    hourShow === "yes" ||
                    minShow === "yes"
                  ) {
                    $(clock).append(
                      "<span class='egt-time eae-time-wrapper'><div>00</div></span>"
                    );
                  } else {
                    clock.innerHTML =
                      "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
                  }
                }
              }
            }
          }

          // set html for 000000

          return;
        }
      }

      if ($scope.hasClass("elementor-element-edit-mode")) {
        if (element_type === "countdown") {
          date1 = new Date(countDownDate);
          countDownDate = date1.getTime();
        } else {
          date1 = new Date();
          date1.setSeconds(
            date1.getSeconds() +
              $scope.find(".eae-evergreen-wrapper").data("egtime")
          );
          countDownDate = date1.getTime();
        }
      }

      /* if (element_type === 'countdown') {
                 date1 = new Date(countDownDate);
                 countDownDate = date1.getTime();
             }
             else {
                 var first_load_value = eaeGetCookie(element_id);
                 var date1 = "";
                 if (first_load_value !== "") {
                     date1 = new Date(parseInt(first_load_value));
                     date1.setSeconds(date1.getSeconds() + countDownDate);
                     countDownDate = date1.getTime();
                 }
                 else {
                     date1 = new Date();
                     date1.setSeconds(date1.getSeconds() + countDownDate);
                     countDownDate = date1.getTime();
                     eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
                 }
             }*/

      var timer = updateTime(countDownDate);

      if (timer.all > 1) {
        startTimer("timer", countDownDate);
      }

      function updateTime(endDate) {
        var time = countDownDate - new Date();

        return {
          days: Math.floor(time / (1000 * 60 * 60 * 24)),
          hours: "0" + Math.floor((time / (1000 * 60 * 60)) % 24),
          minutes: "0" + Math.floor((time / (1000 * 60)) % 60),
          seconds: "0" + Math.floor((time / 1000) % 60),
          all: time,
        };
      }

      function animate(span) {
        span.classList.add("fade");
        setTimeout(function () {
          span.classList.remove("fade");
        }, 700);
      }

      function startTimer(clockID, endDate) {
        var timeInt = setInterval(function () {
          //var clock = document.getElementById(clockID);
          var clock = $scope.find("." + unqId).find("#timer")[0];
          var timer = updateTime(countDownDate);

          //clock.innerHTML = "<span class='egt-time eae-time-wrapper'><div>"+timer.days+"</div></span><span class='egt-time eae-time-wrapper'><div>"+timer.hours+" </div></span><span class='egt-time eae-time-wrapper'><div>"+timer.minutes+"</div></span><span class='egt-time eae-time-wrapper'><div>"+timer.seconds+"</div></span>";
          if (dayShow === "yes") {
            if (timer.days < 10) {
              timer.days = "0" + timer.days;
            }
            clock.innerHTML =
              "<span class='egt-time eae-time-wrapper'><div>" +
              timer.days +
              "</div></span>";
          }
          if (hourShow === "yes") {
            if (dayShow === "yes") {
              $(clock).append(
                "<span class='egt-time eae-time-wrapper'><div>" +
                  timer.hours.slice(-2) +
                  "</div></span>"
              );
            } else {
              clock.innerHTML =
                "<span class='egt-time eae-time-wrapper'><div>" +
                timer.hours.slice(-2) +
                "</div></span>";
            }
          }
          if (minShow === "yes") {
            if (dayShow === "yes" || hourShow === "yes") {
              $(clock).append(
                "<span class='egt-time eae-time-wrapper'><div>" +
                  timer.minutes.slice(-2) +
                  "</div></span>"
              );
            } else {
              clock.innerHTML =
                "<span class='egt-time eae-time-wrapper'><div>" +
                timer.minutes.slice(-2) +
                "</div></span>";
            }
          }
          if (secShow === "yes") {
            if (dayShow === "yes" || hourShow === "yes" || minShow === "yes") {
              $(clock).append(
                "<span class='egt-time eae-time-wrapper'><div>" +
                  timer.seconds.slice(-2) +
                  "</div></span>"
              );
            } else {
              clock.innerHTML =
                "<span class='egt-time eae-time-wrapper'><div>" +
                timer.seconds.slice(-2) +
                "</div></span>";
            }
          }
          //console.log('d',dayShow,'h',hourShow,'m',minShow,'s',secShow);
          // animate
          var spans = clock.getElementsByTagName("span");
          if (dayShow === "yes") {
            if (timer.hours == 59 && timer.minutes == 59 && timer.seconds == 59)
              animate(spans[0]);
          }

          if (hourShow === "yes") {
            if (dayShow === "yes") {
              if (timer.minutes == 59 && timer.seconds == 59) animate(spans[1]);
            } else {
              if (timer.minutes == 59 && timer.seconds == 59) animate(spans[0]);
            }
          }

          if (minShow === "yes") {
            if (dayShow === "yes") {
              if (hourShow === "yes") {
                if (timer.seconds == 59) animate(spans[2]);
              } else {
                if (timer.seconds == 59) animate(spans[1]);
              }
            } else {
              if (hourShow === "yes") {
                if (timer.seconds == 59) animate(spans[1]);
              } else {
                if (timer.seconds == 59) animate(spans[0]);
              }
            }
          }
          if (secShow === "yes") {
            if (dayShow === "yes") {
              if (hourShow === "yes") {
                if (minShow === "yes") {
                  animate(spans[3]);
                }
              } else {
                if (minShow === "yes") {
                  animate(spans[2]);
                } else {
                  animate(spans[1]);
                }
              }
            } else {
              if (hourShow === "yes") {
                if (minShow === "yes") {
                  animate(spans[2]);
                }
              } else {
                if (minShow === "yes") {
                  animate(spans[1]);
                } else {
                  animate(spans[0]);
                }
              }
            }
          }

          if (timer.all <= 1) {
            clearInterval(timeInt);
            //clock.innerHTML = "<span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span><span class='egt-time eae-time-wrapper'>0</span>";
            if (dayShow === "yes") {
              clock.innerHTML =
                "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
            }
            if (hourShow === "yes") {
              if (dayShow === "yes") {
                $(clock).append(
                  "<span class='egt-time eae-time-wrapper'><div>00</div></span>"
                );
              } else {
                clock.innerHTML =
                  "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
              }
            }
            if (minShow === "yes") {
              if (dayShow === "yes" || hourShow === "yes") {
                $(clock).append(
                  "<span class='egt-time eae-time-wrapper'><div>00</div></span>"
                );
              } else {
                clock.innerHTML =
                  "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
              }
            }
            if (secShow === "yes") {
              if (
                dayShow === "yes" ||
                hourShow === "yes" ||
                minShow === "yes"
              ) {
                $(clock).append(
                  "<span class='egt-time eae-time-wrapper'><div>00</div></span>"
                );
              } else {
                clock.innerHTML =
                  "<span class='egt-time eae-time-wrapper'><div>00</div></span>";
              }
            }

            if (!$scope.hasClass("elementor-element-edit-mode")) {
              if (actions.length > 0) {
                actions.forEach(function (value) {
                  if (value === "redirect") {
                    $url1 = $scope
                      .find(".eae-evergreen-wrapper")
                      .data("redirected-url");
                    if ($url1 !== "") {
                      window.location.href = $url1;
                    }
                  }
                  if (value === "hide") {
                    $scope.find("#timer").css("display", "none");
                    $scope.find(".egt-title").css("display", "none");
                    $scope.find(".desc").css("display", "none");
                  }
                  if (value === "message") {
                    $scope.find(".eae-egt-message").css("display", "block");
                  }
                  if (value === "hide_parent") {
                    $p_secs = $scope.closest("section");
                    $p_secs.css("display", "none");
                  }
                });
              }
            }
          }
        }, 1000);
      }
    };

    var EgTimerSkin4 = function ($scope, $) {
      var countDownDate = $scope.find(".eae-evergreen-wrapper").data("egtime");
      var cookie_expire = $scope
        .find(".eae-evergreen-wrapper")
        .data("egt-expire");
      var element_type = $scope
        .find(".eae-evergreen-wrapper")
        .data("element-type");
      var element_id =
        "eae-" + $scope.find(".eae-evergreen-wrapper").data("id");
      var actions = $scope.find(".eae-evergreen-wrapper").data("actions");
      var dayShow = $scope.find(".eae-evergreen-wrapper").data("days");
      var hourShow = $scope.find(".eae-evergreen-wrapper").data("hours");
      var minShow = $scope.find(".eae-evergreen-wrapper").data("mins");
      var secShow = $scope.find(".eae-evergreen-wrapper").data("seconds");

      if (element_type === "countdown") {
        date1 = new Date(countDownDate);
        countDownDate = date1.getTime();
        countDownDate = Math.floor((countDownDate - new Date()) / 1000);
      } else {
        var first_load_value = eaeGetCookie(element_id);
        var date1 = "";
        var cur_date = "";
        if (first_load_value !== "") {
          date1 = new Date(parseInt(first_load_value));
          cur_date = new Date().getTime();
          date1 = cur_date - first_load_value;
          date1 = date1 / 1000;
          countDownDate = countDownDate - date1;
        } else {
          //date1 = new Date();
          //date1.setSeconds(date1.getSeconds() + countDownDate);
          //console.log('date1 else',date1);
          //countDownDate = countDownDate;
          eaeSetCookie(element_id, new Date().getTime(), cookie_expire);
        }
      }

      C3Counter("counter", { startTime: countDownDate });

      function C3Counter(id, opt) {
        this.options = {
          stepTime: 60, // not used
          format: "dd:hh:mm:ss", // not used
          startTime: "00:00:00:00",
          digitImages: 1,
          digitWidth: 30,
          digitHeight: 44,
          digitSlide: true,
          digitSlideTime: 200,
          digitImageHeight: 484,
          digitAnimationHeight: 44,
          timerEnd: function () {},
          image: "digits.png",
          updateInterval: 1000,
        };
        var s;
        if (typeof opt != "undefined") {
          for (s in this.options) {
            if (typeof opt[s] != "undefined") {
              this.options[s] = opt[s];
            }
          }
        }
        if (String(options.startTime).indexOf(":") == -1) {
          options.tempStartTime = options.startTime;
        } else {
          //TODO - does not convert time with : to seconds to count
          var td = new Date(options.startTime);
        }

        this.pad2 = function (number) {
          return (number < 10 ? "0" : "") + number;
        };

        var timer = setInterval("this.updateCounter()", options.updateInterval);
        var startTime = new Date().getTime();
        var secNo = 0;
        var timerSingle = new Array();
        var dc = 0;
        var digits = new Array();
        var d = new Date();
        var lastTime = d.getTime();

        this.calculateTime = function () {
          var tempTime = options.tempStartTime;

          if (String(options.tempStartTime).indexOf(":") == -1) {
            var seconds = Math.round(options.tempStartTime % 60);
            options.tempStartTime = Math.floor(options.tempStartTime / 60);
            var minutes = Math.round(options.tempStartTime % 60);
            options.tempStartTime = Math.floor(options.tempStartTime / 60);
            var hours = Math.round(options.tempStartTime % 24);
            options.tempStartTime = Math.floor(options.tempStartTime / 24);
            var days = Math.round(options.tempStartTime);
            options.timeStr =
              this.pad2(days) +
              this.pad2(hours) +
              this.pad2(minutes) +
              this.pad2(seconds);
          }

          var currTime = new Date().getTime();
          var diff = currTime - startTime;
          if (seconds < 0 || minutes < 0 || hours < 0 || days < 0) {
            options.timeStr =
              this.pad2(0) + this.pad2(0) + this.pad2(0) + this.pad2(0);
          }
          options.tempStartTime = options.startTime - Math.round(diff / 1000);
        };
        this.calculateTime();

        for (dc = 0; dc < 8; dc++) {
          digits[dc] = { digit: this.options.timeStr.charAt(dc) };
          /*if(dayShow !== 'yes'){
                        console.log('day no show',dc);
                        //dc = 3;
                        return true;
                    }
                    console.log('if out',dc);*/
          $("#" + id).append(
            "<div id='digit" +
              dc +
              "' style='position:relative;float:left;width:" +
              this.options.digitWidth +
              "px;height:" +
              this.options.digitHeight +
              "px;overflow:hidden;'><div class='digit' id='digit-bg" +
              dc +
              "' style='position:absolute; top:-" +
              digits[dc].digit * this.options.digitAnimationHeight +
              "px; width:" +
              this.options.digitWidth +
              "px; height:" +
              this.options.digitImageHeight +
              "px; '></div></div>"
          );

          if (dc % 2 == 1 && dc < 6) {
            $("#" + id).append(
              "<div class='digit-separator' style='float:left;'></div>"
            );
          }
        }

        $("#" + id).append("<div style='clear:both'></div>");

        this.animateDigits = function () {
          for (var dc = 0; dc < 8; dc++) {
            digits[dc].digitNext = Number(this.options.timeStr.charAt(dc));
            digits[dc].digitNext = (digits[dc].digitNext + 10) % 10;
            var no = dc;

            if (digits[no].digit == 0)
              $("#digit-bg" + no).css(
                "top",
                -this.options.digitImageHeight + this.options.digitHeight + "px"
              );
            if (digits[no].digit != digits[no].digitNext) {
              $("#digit-bg" + no).animate(
                { top: -digits[no].digitNext * options.digitHeight + "px" },
                options.digitSlideTime
              );
              digits[no].digit = digits[no].digitNext;
            }
          }

          var end = this.checkEnd();
        };

        this.checkEnd = function () {
          for (var i = 0; i < digits.length; i++) {
            if (digits[i].digit != 0) {
              return false;
            }
          }
          clearInterval(timer);
          if (typeof actions !== "undefined") {
            actions.forEach(function (value) {
              if (value.type === "redirect") {
                if (value.redirect_url !== "") {
                  window.location.href = value.redirect_url;
                }
              }
              if (value.type === "hide") {
                $scope.find(".eae-evergreen-wrapper").css("display", "none");
              }
              if (value.type === "message") {
                $scope.find(".eae-egt-message").css("display", "block");
              }
            });
          }
          this.options.timerEnd();
          return true;
        };

        this.updateCounter = function () {
          d = new Date();

          if (d.getTime() - lastTime < options.updateInterval - 50) {
            return;
          }
          lastTime = d.getTime();
          this.calculateTime();
          this.animateDigits();
        };
      }
    };

    var CompareTable = function ($scope, $) {
      $($scope.find(".eae-ct-heading")[0]).addClass("active");
      $scope.find("ul").on("click", "li", function () {
        var pos = $(this).index() + 2;
        $scope.find("tr").find("td:not(:eq(0))").hide();
        $scope.find("td:nth-child(" + pos + ")").css("display", "table-cell");
        $scope.find("tr").find("th:not(:eq(0))").hide();
        $scope.find("li").removeClass("active");
        $(this).addClass("active");
      });

      // Initialize the media query
      // if($($scope.hasClass('eae-tab-format-mobile')) || $($scope.hasClass('eae-tab-format-tab-mob')) || $($scope.hasClass('eae-tab-format-all')) ){
      //     //console.log($(window).width());
      //     var feature_box_header = false;
      //     var feature_box_header_val= null;
      //     //console.log($scope.find("tbody .eae-ct-header .eae-fbox-heading"));
      //     if($scope.find("tbody .eae-ct-header .eae-fbox-heading").length > 0){
      //             feature_box_header = true;
      //             feature_box_header_val = $scope.find("tbody .eae-ct-header .eae-fbox-heading").text();
      //      }
      //
      //     if($scope.hasClass('eae-tab-format-all') && feature_box_header){
      //         var p_row  = $scope.find("tbody tr:eq(1)");
      //         p_row.prepend('<td class="eae-fbox-heading">'  +feature_box_header_val +'</td>');
      //     }
      //
      //     if($(window).width() >= '767' && $(window).width() <= '1023'){
      //         // if(feature_box_header){
      //         //     $scope.find("tbody .eae-ct-header .eae-fbox-heading").css('display' , 'none !important');
      //         // }
      //         if($scope.hasClass('eae-tab-format-tab-mob') && feature_box_header){
      //             var p_row  = $scope.find("tbody tr:eq(1)");
      //             p_row.prepend('<td class="eae-fbox-heading">'  +feature_box_header_val +'</td>');
      //         }
      //     }
      //     if($(window).width() <= '767'){
      //         // if(feature_box_header){
      //         //     $scope.find("tbody .eae-ct-header .eae-fbox-heading").css('display' , 'none !important');
      //         // }
      //         if($scope.hasClass('eae-tab-format-mobile') && feature_box_header){
      //             var p_row  = $scope.find("tbody tr:eq(1)");
      //             p_row.prepend('<td class="eae-fbox-heading">'  +feature_box_header_val +'</td>');
      //         }
      //     }
      //
      // }
      var mediaQuery = window.matchMedia("(min-width: 767px)");

      // Add a listen event
      mediaQuery.addListener(doSomething);

      // Function to do something with the media query
      function doSomething(mediaQuery) {
        if (mediaQuery.matches) {
          $scope.find(".sep").attr("colspan", 5);
        } else {
          $scope.find(".sep").attr("colspan", 2);
        }
      }

      // On load
      doSomething(mediaQuery);
    };

    var ProgressBar = function ($scope, $) {
      $is_rtl = jQuery("body").hasClass("rtl");
      $wrapper = $scope.find(".eae-progress-bar");
      var skill = $wrapper.attr("data-skill");
      var skill_value = $wrapper.attr("data-value");
      var skin = $wrapper.attr("data-skin");
      var skillELem = $wrapper.find(".eae-pb-bar-skill");
      var valueELem = $wrapper.find(".eae-pb-bar-value");
      var prgBar = $wrapper.find(".eae-pb-bar");
      var prgInner = $wrapper.find(".eae-pb-bar-inner");

      if (skin === "skin1") {
        $(prgInner).attr("style", "width : " + skill_value + "%");
      }
      if (skin === "skin2") {
        $(prgInner).attr("style", "width : " + skill_value + "%");
      }
      if (skin === "skin3") {
        $(valueELem).addClass("eae-pb-bar-value--aligned-value");
        if ($is_rtl) {
          $(valueELem).attr("style", "right :" + skill_value + "%");
        } else {
          $(valueELem).attr("style", "left :" + skill_value + "%");
        }

        $(prgInner).attr("style", "width :" + skill_value + "%");
      }
      if (skin === "skin4") {
        $(valueELem).addClass("eae-pb-bar-value--aligned-value");
        if ($is_rtl) {
          $(valueELem).attr("style", "right :" + skill_value + "%");
        } else {
          $(valueELem).attr("style", "left :" + skill_value + "%");
        }
        $(prgInner).attr("style", "width :" + skill_value + "%");
        $(prgBar).addClass("eae-pb-bar--no-overflow");
      }
      if (skin === "skin5") {
        $(valueELem).addClass("eae-pb-bar-value--aligned-value");
        if ($is_rtl) {
          $(valueELem).attr("style", "right :" + skill_value + "%");
        } else {
          $(valueELem).attr("style", "left :" + skill_value + "%");
        }
        $(prgInner).attr("style", "width :" + skill_value + "%");
      }
      $wrapper.each(function (index, value) {
          const observerOptions = {
            root: null, // This is the viewport
            rootMargin: "0px 0px -100px 0px", // Trigger when 200px above the bottom
            threshold: 0, // Trigger as soon as even one pixel is visible
          };
          let lastScrollY = window.scrollY;
          const observerCallback = (entries) => {
            entries.forEach((entry) => {
            //console.log('entries', entry);
              const currentScrollY = window.scrollY;
              if (entry.isIntersecting) {
                let valueElem = entry.target.querySelector(".eae-pb-bar-value");
                let skillBar =  entry.target.querySelector(".eae-pb-bar-skill");
                let prgInner = entry.target.querySelector(".eae-pb-bar-inner");
                if(valueElem != null && !valueElem.classList.contains("js-animated")){
                  valueElem.classList.add("js-animated");
                }
                if(skillBar != null && !skillBar.classList.contains("js-animated")){
                  skillBar.classList.add("js-animated");
                }
                if(prgInner != null && !prgInner.classList.contains("js-animated")){
                  prgInner.classList.add("js-animated");
                }
              // Only add the animation class when scrolling down
              } else if (!entry.isIntersecting) {
              // Optionally, remove the class when scrolling up or out of view
                  entry.target.classList.remove("animate");
              }
      
              lastScrollY = currentScrollY;
            });
          };

          const observer = new IntersectionObserver(observerCallback, observerOptions);
          observer.observe(value);
      });
    };

    var contentSwitcherButton = function ($scope, $) {
      var $wrapper = $scope.find(".eae-content-switcher-wrapper");
      var wid = $scope.data("id");
      var buttons = $wrapper.find(".eae-content-switch-button");
      // var content_section = $wrapper.fin
      buttons.each(function (index, button) {
        $(this).on("click", function (e) {
          e.preventDefault();
          let label = $(this).find(".eae-content-switch-label");
          
          if ($(this).hasClass("active")) {
            return;
          } else {
            $(buttons).removeClass("active");
            let label_id = $(label).attr("id");
            $(this).addClass("active");
            var content_sections = $($wrapper).find(".eae-cs-content-section");
            $(content_sections).removeClass("active");
            let current_content_section = $($wrapper).find(
              ".eae-content-section-" + label_id
            );
            $(current_content_section).addClass("active");

            // dispatch event resize using vanilla js
            window.dispatchEvent(new Event("resize"));
            
          }
        });
      });
    };

    var contentSwitcherRadio = function ($scope, $) {
      let wrapper = $scope.find(".eae-content-switcher-wrapper");
      let wid = $scope.data("id");
      let toggle_switch = wrapper.find(".eae-cs-switch-label");
      let primary_label = wrapper.find(
        ".eae-content-switch-label.primary-label"
      );
      const primary_id = $(primary_label).attr("item_id");
      let secondary_label = wrapper.find(
        ".eae-content-switch-label.secondary-label"
      );
      const secondary_id = $(secondary_label).attr("item_id");
      let primary_content_section = wrapper.find(
        ".eae-cs-content-section.eae-content-section-" + primary_id
      );
      let secondary_content_section = wrapper.find(
        ".eae-cs-content-section.eae-content-section-" + secondary_id
      );
      $(toggle_switch).on("click", function (e) {
        var checkbox = $(this).find("input.eae-content-toggle-switch");
        if (checkbox.is(":checked")) {
          secondary_label.addClass("active");
          secondary_content_section.addClass("active");
          primary_label.removeClass("active");
          primary_content_section.removeClass("active");
        } else {
          primary_label.addClass("active");
          primary_content_section.addClass("active");
          secondary_label.removeClass("active");
          secondary_content_section.removeClass("active");
        }
          window.dispatchEvent(new Event("resize"));
      });

    };

    var FilterableGallery = function ($scope, $) {
      var $wrapper = $scope.find(".eae-fg-wrapper");
      var wid = $scope.data("id");
      var maxtilt = $wrapper.attr("data-maxtilt");
      var perspective = $wrapper.attr("data-perspective");
      var speed = $wrapper.attr("data-speed");
      var axis = $wrapper.attr("data-tilt-axis");
      var glare = $wrapper.attr("data-glare");
      var overlay_speed = parseInt($wrapper.attr("data-overlay-speed"));

      if (axis === "x") {
        axis = "y";
      } else if (axis === "y") {
        axis = "x";
      } else {
        axis = "both";
      }

      if (glare === "yes") {
        var max_glare = $wrapper.attr("data-max-glare");
      }

      if (glare === "yes") {
        glare = true;
      } else {
        glare = false;
      }

      var $container = $(".elementor-element-" + wid + " .eae-fg-image");
      var layoutMode = $wrapper.hasClass("masonry-yes") ? "masonry" : "fitRows";
      let container_outerheight = $container.outerHeight();
      adata = {
        percentPosition: true,
        animationOptions: {
          duration: 750,
          easing: "linear",
          queue: false,
        },
      };

      if (layoutMode === "fitRows") {
        adata["layoutMode"] = "fitRows";
      }

      if (layoutMode === "masonry") {
        adata["masonry"] = {
          columnWidth: ".eae-gallery-item",
          horizontalOrder: true,
        };
      }
      if (!$scope.hasClass("eae-show-all-yes")) {
        $scope.find(".eae-gallery-filter a").first().addClass("current");
        adata["filter"] = $scope
          .find(".eae-gallery-filter a")
          .first()
          .attr("data-filter");
      }

      var $grid = $container.isotope(adata);
      $grid.imagesLoaded().progress(function () {
        $grid.isotope("layout");
        //$scope.find('.eae-fg-image').css({"min-height":"300px" ,"height" : container_outerheight});
      });

      if ($scope.find(".eae-tilt-yes")) {
        atilt = {
          maxTilt: maxtilt,
          perspective: perspective, // Transform perspective, the lower the more extreme the tilt gets.
          //easing:         "cubic-bezier(.03,.98,.52,.99)",   // Easing on enter/exit.
          easing: "linear",
          scale: 1, // 2 = 200%, 1.5 = 150%, etc..
          speed: speed, // Speed of the enter/exit transition.
          disableAxis: axis,
          transition: true, // Set a transition on enter/exit.
          reset: true, // If the tilt effect has to be reset on exit.
          glare: glare, // Enables glare effect
          maxGlare: max_glare, // From 0 - 1.
        };

        $scope.find(".el-tilt").tilt(atilt);
      }

      $(".elementor-element-" + wid + " .eae-gallery-filter a").on(
        "click",
        function () {
          $scope.find(".eae-gallery-filter .current").removeClass("current");
          $(this).addClass("current");
          //console.log(adata);
          var selector = $(this).attr("data-filter");
          adata["filter"] = selector;

          var $grid = $container.isotope(adata);

          $grid.imagesLoaded().progress(function () {
            $grid.isotope("layout");
            if (isEditMode) {
              return false;
            }
            if ($scope.find(".eae-tilt-yes")) {
              $scope.find(".el-tilt").tilt(atilt);
              $scope.find(".el-tilt").tilt.reset.call($scope.find(".el-tilt"));
            }
          });

          return false;
        }
      );

      if (!$wrapper.hasClass("eae-hover-direction-effect")) {
        $scope.find(".eae-gallery-item-inner").hover(function () {
          $(this).find(".eae-grid-overlay").addClass("animated");
        });
      }
      if ($wrapper.hasClass("eae-hover-direction-effect")) {
        $scope.find(".eae-gallery-item-inner").hover(function () {
          $(this).find(".eae-grid-overlay").addClass("overlay");
        });
        $wrapper.find(".eae-gallery-item-inner").EAEHoverDirection({
          //speed: 900,
          speed: overlay_speed,
        });
      }
    };

    // var RibbonsBadgesHandler = function ($scope, $) {
    //     if (!isEditMode) {
    //         if ($scope.hasClass('wts-eae-enable-ribbons-badges-yes')) {
    //             $scope.prepend('<div class="wts-eae-ribbons-badges-wrapper">' +
    //                 '<span class="wts-eae-ribbons-badges-inner">' +
    //                 $scope.data('wts-eae-rb-text') +
    //                 '</span>' +
    //                 '</div>');
    //         }
    //     }
    //     if(isEditMode){
    //         if($scope.hasClass('wts-eae-enable-ribbons-badges-yes') && $scope.find('.wts-eae-ribbons-badges-column-yes')){
    //             var col_content = $scope.find('.wts-eae-ribbons-badges-column-yes').data('text');
    //                 var column = $scope.find('.elementor-column-wrap');
    //                 console.log(column);
    //                 column.prepend('<div class="wts-eae-ribbons-badges-wrapper">' +
    //                     '<span class="wts-eae-ribbons-badges-inner">' +
    //                     col_content +
    //                     '</span>' +
    //                     '</div>');
    //             // if($scope.find('.wts-eae-ribbons-badges-section-yes')){
    //             //     var row_content = $scope.find('.wts-eae-ribbons-badges-section-yes').data('text');
    //             //     $scope.prepend('<div class="wts-eae-ribbons-badges-wrapper">' +
    //             //         '<span class="wts-eae-ribbons-badges-inner">' +
    //             //         row_content +
    //             //         '</span>' +
    //             //         '</div>');
    //             // }
    //         }
    //     }
    // };

    var WrapperLinksHander = function ($scope, $) {
      if (isEditMode) {
        return;
      }
      if ($scope.data("wts-url") && $scope.data("wts-link") == "yes") {
        $scope.on("click", function (e) {
          if (
            $scope.data("wts-url") &&
            $scope.data("wts-new-window") == "yes"
          ) {
            window.open($scope.data("wts-url"));
          } else {
            location.href = $scope.data("wts-url");
          }
        });
      }
    };

    $.fn.EAEHoverDirection = function (options) {
      var settings = $.extend(
        {
          inaccuracy: 30,
          speed: 200,
        },
        options
      );
      this.find(".overlay").css({ top: -9999999 });
      this.mouseenter(function (e) {
        container = $(this);
        overlay = container.find(".overlay");
        parentOffset = container.offset();
        relX = e.pageX - parentOffset.left;
        //
        //
        // (e.pageX);
        // console.log(parentOffset);
        // console.log(relX);
        relY = e.pageY - parentOffset.top;
        overlay.css({
          top: 0,
          left: 0,
          width: container.width(),
          height: container.height(),
        });
        if (relX > container.width() - settings.inaccuracy) {
          //From Right to Left
          overlay.css({
            top: 0,
            left: container.width(),
          });
        } else if (relX < settings.inaccuracy) {
          //From Left to Right
          overlay.css({
            top: 0,
            left: -container.width(),
          });
        } else if (relY > container.height() - settings.inaccuracy) {
          //BOTTOM TO TOP
          overlay.css({
            top: container.width(),
            left: 0,
          });
        } else if (relY < settings.inaccuracy) {
          //console.log('adfa');
          //TOP TO BOTTOM
          overlay.css({
            top: -container.width(),
            left: 0,
          });
        }
        overlay.animate(
          {
            top: 0,
            left: 0,
          },
          settings.speed
        );
      });

      this.mouseleave(function (e) {
        container = $(this);
        overlay = container.find(".overlay");
        parentOffset = container.offset();
        relX = e.pageX - parentOffset.left;
        relY = e.pageY - parentOffset.top;
        if (relX <= 0) {
          overlay.animate(
            {
              top: 0,
              left: -container.width(),
            },
            settings.speed
          );
        }
        if (relX >= container.width()) {
          overlay.animate(
            {
              top: 0,
              left: container.width(),
            },
            settings.speed
          );
        }
        if (relY <= 0) {
          overlay.animate(
            {
              left: 0,
              top: -container.height(),
            },
            settings.speed
          );
        }
        if (relY >= container.height()) {
          overlay.animate(
            {
              left: 0,
              top: container.height(),
            },
            settings.speed
          );
        }
      });
    };

    let EAEThumbGallery = function ($scope, $) {
      swiper_outer_wrapper = $scope.find(".eae-swiper-outer-wrapper");
      wid = $scope.data("id");
      wClass = ".elementor-element-" + wid;
		  thumb_outer_wrapper = $scope.find(".eae-gallery-thumbs");
		  let sswiper;
      let slider_data = swiper_outer_wrapper.data("swiper-settings");
      let slides_per_view = swiper_outer_wrapper.data("slides-per-view");
      let spaceBetween = swiper_outer_wrapper.data("space");
      let active_breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
      //let navigation         = swiper_outer_wrapper.data('navigation');

      // BreakPoints Thumbnail

      

      // const bp = eae.breakpoints;
      // let breakpoints = {};
      // breakpoints[bp.lg - 1] = {
      //   slidesPerView: slides_per_view.desktop,
      //   spaceBetween: spaceBetween.desktop,
      // };
      // breakpoints[bp.md - 1] = {
      //   slidesPerView: slides_per_view.tablet,
      //   spaceBetween: spaceBetween.tablet,
      // };


      // BreakPoints Slider Form main slide
      // const res_props = {
      //   'slidesPerView' : 'slidesPerView',
      //   'slidesPerGroup' : 'slidesPerGroup',
      //   'spaceBetween' : 'spaceBetween'
      // };

      // if(active_breakpoints.hasOwnProperty('mobile')){
      //   for (const key in res_props) {
      //     if (slider_data.hasOwnProperty(key)) {
      //       sliderData[key] = data[key].mobile;       
      //     }
      //     if(slider_data.thumb.hasOwnProperty(key)){
      //       slider_data[thumbs][key] = data[key].mobile;       
      //     }
      //   }
      // }

      // let BreakPoints = {};
      // BreakPoints[Bp.lg - 1] = {
      //   spaceBetween: slider_data.spaceBetween.desktop,
      // };
      // BreakPoints[Bp.md - 1] = {
      //   spaceBetween: slider_data.spaceBetween.tablet,
      // };
      
      //console.log('mainSlider',BreakPoints);
      
      sliderData = {
        direction: "horizontal",
        effect: slider_data.effect,
        keyboard: {
          enabled: slider_data.keyboard,
        },
        // spaceBetween: slider_data.spaceBetween.mobile,
        // breakpoints: BreakPoints,
        speed: slider_data.speed,
        loop: "yes" === slider_data.loop ? true : false,
        thumbs: {
          swiper: {
            el: wClass + ' .eae-gallery-thumbs',
            direction: "horizontal",
            // spaceBetween: spaceBetween.mobile,
            // slidesPerView: slides_per_view.mobile,
            navigation: {
              nextEl: wClass + " .eae-swiper-button-next",
              prevEl: wClass + " .eae-swiper-button-prev",
            },
            speed: slider_data.speed,
            loop: "yes" === slider_data.loop ? true : false,
            freeMode: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            //breakpoints: breakpoints,
            // autoScrollOffset :true,
            // reverseDirection : true,
            slideToClickedSlide: true,
			    },
		    },
      };

      const res_props = {
        'slidesPerView' : 'slidesPerView',
        'slidesPerGroup' : 'slidesPerGroup',
        'spaceBetween' : 'spaceBetween'
      };

      if(active_breakpoints.hasOwnProperty('mobile')){
        for (const key in res_props) {
          if (slider_data.hasOwnProperty(key)) {
            sliderData[key] = slider_data[key].mobile;       
          }
          if(slider_data.thumbs.hasOwnProperty(key)){
            sliderData.thumbs.swiper[key] = slider_data.thumbs[key].mobile;       
          }
        }
      }

      
      const arr = {};
      const arrThumbs = {};

		// Responsive BreakPoints Sets
		if(slider_data.hasOwnProperty('breakpoints_value')){
        Object.keys(slider_data.breakpoints_value).map(key => {
          //console.log('Key', key);
          const value = parseInt(slider_data.breakpoints_value[key]); 
          //console.log('Value', value);
          if(key === 'desktop'){
            key = 'default';
          }
          if(key  !== 'mobile'){
            const spaceBetween = parseInt(slider_data.spaceBetween[key]);
            // const slidesPerView = parseInt(slider_data.slidesPerView[key]);
            // const slidesPerGroup = parseInt(slider_data.slidesPerGroup[key]);
            arr[value - 1] = {
              spaceBetween,
            };
  
            const spaceBetweenThumbs = parseInt(slider_data.thumbs.spaceBetween[key]);
            const slidesPerViewThumbs = parseInt(slider_data.thumbs.slidesPerView[key]);
            //const slidesPerGroupThumbs = parseInt(slider_data.thumbs.slidesPerGroup[key]);
            arrThumbs[value - 1] = {
              spaceBetween : spaceBetweenThumbs,
              slidesPerView : slidesPerViewThumbs,
            };
          }
          
        });
		}
    sliderData.breakpoints = arr;
    sliderData.thumbs.swiper.breakpoints = arrThumbs;
    
      if (typeof slider_data.autoplay !== "undefined") {
        sliderData["thumbs"]["swiper"]["autoplay"] = {
          delay: slider_data.autoplay.duration,
          disableOnInteraction: slider_data.autoplay.disableOnInteraction,
          reverseDirection: slider_data.autoplay.reverseDirection,
        };
      }

      if (slider_data.navigation == "yes") {
        sliderData["navigation"] = {
          nextEl: wClass + " .eae-swiper-button-next",
          prevEl: wClass + " .eae-swiper-button-prev",
        };
      }

      if (slider_data.pagination !== "") {
        sliderData["pagination"] = {
          type: slider_data.pagination,
          el: wClass + " .swiper-pagination",
          clickable: slider_data.clickable,
        };
      }
      if (typeof slider_data.autoplay !== "undefined") {
        sliderData["autoplay"] = {
          delay: slider_data.autoplay.duration,
          disableOnInteraction: slider_data.autoplay.disableOnInteraction,
          reverseDirection: slider_data.autoplay.reverseDirection,
        };
      }
      swiperContainer = jQuery(".elementor-element-" + wid + " .eae-swiper-outer-wrapper .eae-swiper-container");
      const asyncSwiper = elementorFrontend.utils.swiper;
      if(swiperContainer !== null && swiperContainer.length === 0){
        return;
      }else{
        new asyncSwiper(jQuery(swiperContainer), sliderData).then((newSwiperInstance) => {
          const sswiper = newSwiperInstance;
          const pause_on_hover = slider_data.pauseOnHover;
          if(slider_data.loop == 'yes'){
            after_swiper_load_func(sswiper , wid);
          }
          if (pause_on_hover == 'yes') {
              pause_on_hover_func(sswiper, pause_on_hover, wid , slider_data);
          }
          if (typeof slider_data.autoplay !== "undefined") {
        
            let pause_on_hover = slider_data.pauseOnHover;
            if (pause_on_hover == 'yes') {
              jQuery(wClass + " .eae-swiper-container").hover(
                function () {
                  if(sswiper != undefined){
                    sswiper.autoplay.stop();
                    sswiper.thumbs.swiper.autoplay.stop();
                  }
                },
                function () {
                  if(sswiper != undefined){
                    sswiper.autoplay.start();
                    sswiper.thumbs.swiper.autoplay.start();
                  }
                }
              );
            }
            
          }
		    });
      } 
    };

    // Cf Styler
    const EAECfStyler = function ($scope, $) {
        if($scope.hasClass( "elementor-element-edit-mode" )){
            if($("#error-field-hidden").hasClass("validation-field-box")){
                const inputWrap = $scope.find(".wpcf7-validates-as-required");
                inputWrap.parent().append("<p class='error-field'>The field is required.</p>");
            }
        }
    };

    let EAEChart = function ($scope, $) {

      const Chart_Outer_Wrapper = $scope.find(".eae-chart-outer-container");
      const cid = $scope.data("id");
      const chartClass = ".elementor-element-" + cid;
      const chart = document.querySelector(chartClass + " .eae-chart-outer-container");
      const chart_canvas = $scope.find("#eae-chart-canvas");
      let settings = Chart_Outer_Wrapper.data("settings");
      const pie_chart = $scope.find(".eae-chart-outer-container");  
      // Use Observer Instersection

      const observerOptions = {
        root: null, // Observe relative to the viewport
        threshold: 0.3 // 70% visibility corresponds to 0.3 as it means 30% is outside
      };

      const observerCallback = (entries, observer) => {
        entries.forEach(entry => {
          const chartElement = entry.target;
          if (entry.isIntersecting) {
            // When the chart comes into view
            if (!chartElement.classList.contains('trigger')) {
              chartElement.classList.add('trigger');
              new Chart(chart_canvas, settings); // Initialize chart
            }
            // Optionally, unobserve the element if you only want it to trigger once
            observer.unobserve(chartElement);
          }
        });
      };
      const observer = new IntersectionObserver(observerCallback, observerOptions);
      observer.observe(chart);
    };

    let EAETable = function ($scope, $) {
      const table = $scope.find(".eae-table");
      const wrapper = $scope.find(".eae-table-container");
      lottie_class = $scope.find(".eae-lottie");
      settings = table.data("settings");

      // lottie animation
      lottie_class.each(function () {
        let lottie_data = $(this).data("lottie-settings");

        let eae_animation = lottie.loadAnimation({
          container: document.getElementById(lottie_data.id),
          path: lottie_data.url,
          renderer: "svg",
          loop: lottie_data.loop,
        });

        if (lottie_data.reverse == true) {
          eae_animation.setDirection(-1);
        }
      });

      // Table Sort
      if (settings.sort === true) {
        head_class = $(".eae-table thead tr:not(:last-child)").addClass(
          "eae-sort__ignoreRow"
        );

        table.tablesorter({
          sortReset: false,
          sortRestart: true,
        });
      } else {
        head_class = $(".eae-table thead tr:not(:last-child)").removeClass(
          "eae-sort__ignoreRow"
        );
      }

      // Table Search
      if (settings.search) {
        wrapper.find("#eae-searchable").keyup(function () {
          _this = this;

          table.find(".eae-table__body tr").each(function () {
            if (
              $(this)
                .text()
                .toLowerCase()
                .indexOf($(_this).val().toLowerCase()) === -1
            )
              $(this).addClass("eae-table-search-hide");
            else $(this).removeClass("eae-table-search-hide");
          });
        });
      }
    };

    const EAEAnythingCarousel = function($scope, $){
      const outer_wrapper =  $scope.find('.eae-swiper-outer-wrapper');
        const wid = $scope.data('id');
        const swiper_settings = outer_wrapper.data('swiper-settings'); 
        swiperBase(swiper_settings, wid, $scope, outer_wrapper);
    }

    const prepare_thumb_object = function(active_breakpoints,res_props,thumb_data, breakpoints){
      let thumb_obj = {};
      const arr = {};
      // Minimum Screen Set
      if(active_breakpoints.hasOwnProperty('mobile')){
        for (const key in res_props) {
            if (thumb_data.hasOwnProperty(key)) {
              thumb_obj[key] = thumb_data[key].mobile;       
            }
        }
      }
    
      if(breakpoints){
        Object.keys(breakpoints).map(key => {
            //console.log('Key', key);
            const value = parseInt(breakpoints[key]); 
            //console.log('Value', value);
            if(key === 'desktop'){
                key = 'default';
            }
            const spaceBetween = parseInt(thumb_data.spaceBetween[key]);
            const slidesPerView = parseInt(thumb_data.slidesPerView[key]);
            arr[value - 1] = {
                spaceBetween,
                slidesPerView,
            };
        });
      } 
      thumb_obj['breakpoints'] = arr;
      thumb_obj['direction'] = "horizontal";
      thumb_obj['watchSlidesVisibility'] = true;
      thumb_obj['watchSlidesProgress'] = true;
      thumb_obj['freeMode'] = true;
      thumb_obj['slideToClickedSlide'] = true;
      
      return thumb_obj;
    }

    const swiperBase = function(data, wid, scope = null, outer_wrapper){
      let $thumb_obj = {};
      let swiper = [];
      let swiperContainer = '.elementor-element-' + wid + ' .eae-swiper-container';
      let active_breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
      const res_props = {
        'slidesPerView' : 'slidesPerView',
        'slidesPerGroup' : 'slidesPerGroup',
        'spaceBetween' : 'spaceBetween'
    }
      const wclass = '.elementor-element-' + wid;
      if (scope !== null) {
          wid = scope.data('id');
          const slideId = scope.find('.swiper-container').data('eae-slider-id');
          swiperContainer = wclass + ' .eae-swiper-container[data-eae-slider-id="' + slideId + '"]';
      }
      let show_thumbnail = outer_wrapper.data('show-thumbnail');
      if(show_thumbnail === 'yes'){
        const thumb_data = outer_wrapper.data('thumb-settings');
        // console.log('swiper data', data);
        // console.log('In Condi', thumb_data);
        $thumb_obj = prepare_thumb_object(active_breakpoints,res_props,thumb_data,data.breakpoints_value); 
        $thumb_obj['el'] = jQuery('.elementor-element-' + wid + ' .eae-thumb-container');
      }

      //console.log('dfadfadfaf ==--->',$thumb_obj);
      if (typeof data === "undefined") {
        return false;
      }
      
      swiper = {
        direction: data.direction,
        speed: data.speed,
        autoHeight: data.autoHeight,
        autoplay: data.autoplay,
        effect: data.effect,
        loop: data.loop,
        zoom: data.zoom,
        wrapperClass: 'eae-swiper-wrapper',
        slideClass: 'eae-swiper-slide',
        observer: true,
        observeParents: true,
      }
    
      // Minimum Screen Set
      if(active_breakpoints.hasOwnProperty('mobile')){
        for (const key in res_props) {
            if (data.hasOwnProperty(key)) {
                swiper[key] = data[key].mobile;       
            }
        }
      }

      if (data.loop && data.hasOwnProperty('slidersPerView')) {
          if (document.querySelectorAll(wclass + ' .eae-swiper-slide').length < data.slidesPerView.tablet) {
              swiper['loop'] = false;
          }
      }
    
      const arr = {};

      // Responsive BreakPoints Sets
      if(data.hasOwnProperty('breakpoints_value')){
        Object.keys(data.breakpoints_value).map(key => {
            //console.log('Key', key);
            const value = parseInt(data.breakpoints_value[key]); 
            //console.log('Value', value);
            if(key === 'desktop'){
                key = 'default';
            }
            const spaceBetween = parseInt(data.spaceBetween[key]);
            const slidesPerView = parseInt(data.slidesPerView[key]);
            const slidesPerGroup = parseInt(data.slidesPerGroup[key]);
            arr[value - 1] = {
                spaceBetween,
                slidesPerView,
                slidesPerGroup
            };
        });
      } 
      swiper['breakpoints'] = arr;
      swiper['keyboard'] = (data.keyboard === 'yes') ? { enabled: true, onlyInViewport: true } : false;

      if (data.navigation === 'yes') {
            swiper['navigation'] = {
                nextEl: wclass + ' .eae-swiper-button-next',
                prevEl: wclass + ' .eae-swiper-button-prev',
            }
      }

      if (data.ptype !== '') {
          swiper['pagination'] = {
              el: wclass + ' .eae-swiper-pagination',
              type: data.ptype,
              clickable: data.clickable
          }
      }
      if (data.scrollbar == 'yes') {
          swiper['scrollbar'] = {
              el: wclass + ' .eae-swiper-scrollbar',
              hide: true
          };
      }
      swiper['thumbs'] = {
        swiper: $thumb_obj,
      }
        
      // swiper['init'] = false;
      //console.log('last swiper =----->', swiper);


      if ("undefined" === typeof Swiper) {
        const asyncSwiper = elementorFrontend.utils.swiper;
        //console.log('swiper container',swiperContainer);
        new asyncSwiper( jQuery( swiperContainer ), swiper).then((newSwiperInstance) => {
          let mswiper = newSwiperInstance;
          after_swiper_load_func(mswiper);
          const pause_on_hover = data.pause_on_hover;
          if (pause_on_hover == 'yes' && data.autoplay) {
              pause_on_hover_func(mswiper, pause_on_hover, wid);
          }
          // elementorFrontend.hooks.doAction( `aepro/trigger/swiper/widget/${wid}`, mswiper);
        });
      } else {
        const mswiper = new Swiper('.elementor-element-' + wid + ' .eae-swiper-container', swiper)          
            after_swiper_load_func(mswiper);
            const pause_on_hover = data.pause_on_hover;
            if (pause_on_hover == 'yes') {
                pause_on_hover_func(mswiper, pause_on_hover, wid);
            }
      }
        jQuery('.elementor-element-' + wid + ' .eae-swiper-container').css('visibility', 'visible');
    }

    const after_swiper_load_func = function(mswiper) {
        if (mswiper.length > 0) {

                mswiper.forEach(function (slider) {

                    slider.on('slideChangeTransitionStart', function () {

                        // set dynamic background
                        slider.$wrapperEl.find('.ae-featured-bg-yes').each(function () {
                            if (jQuery(this).css('background-image') == 'none') {
                                let img = jQuery(this).attr('data-ae-bg');
                                jQuery(this).css('background-image', 'url(' + img + ')');
                            }
                        });
                        slider.$wrapperEl.find('.ae-bg-color-yes').each(function () {
                            let color = jQuery(this).attr('data-ae-bg-color');
                            let blank_color = 'rgba(0, 0, 0, 0)';
                            if (jQuery(this).css('background-color') === blank_color) {
                                jQuery(this).css('background-color', color);
                            }
                        });

                        // reveal animated widgets
                        slider.$wrapperEl.find('.swiper-slide-duplicate').find('.elementor-invisible').each(function () {
                            // get settings
                            elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
                        });

                        slider.$wrapperEl.find('.swiper-slide').find('.animated').each(function () {
                            // get settings
                             elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
                        });

                    });

                    slider.on('click', function () {

                        const clickedSlide = mswiper.clickedSlide;
                        if (typeof clickedSlide === 'undefined') {
                            return;
                        }

                        const wrapper = clickedSlide.querySelector('.ae-link-yes');

                        if (wrapper === null || wrapper.length == 0) {
                            return;
                        } else {
                            const url = jQuery(wrapper).data('ae-url');
                            if (url !== undefined) {
                                if (jQuery(wrapper).data('ae-url') && jQuery(wrapper).hasClass('ae-new-window-yes')) {
                                    window.open(jQuery(wrapper).data('ae-url'));
                                } else {
                                    location.href = jQuery(wrapper).data('ae-url');
                                }
                            }
                        }
                    });

                    slider.init();
                });

            } else {

                mswiper.on('slideChangeTransitionStart', function () {
                    // set dynamic background
                    mswiper.$wrapperEl.find('.ae-featured-bg-yes').each(function () {
                        
                        if (jQuery(this).css('background-image') == 'none') {
                            let img = jQuery(this).attr('data-ae-bg');
                            jQuery(this).css('background-image', 'url(' + img + ')');
                        }
                    });

                    mswiper.$wrapperEl.find('.ae-bg-color-yes').each(function () {
                        let color = jQuery(this).attr('data-ae-bg-color');
                            let blank_color = 'rgba(0, 0, 0, 0)';
                            if (jQuery(this).css('background-color') === blank_color) {
                                jQuery(this).css('background-color', color);
                            }
                    });
                    // reveal animated widgets
                    mswiper.$wrapperEl.find('.swiper-slide-duplicate').find('.elementor-invisible').each(function () {
                        // get settings
                         elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
                    });

                    mswiper.$wrapperEl.find('.swiper-slide').find('.animated').each(function () {
                        // get settings
                         elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
                    });
                });
                

                mswiper.on('click', function () {

                    const clickedSlide = mswiper.clickedSlide;
                    if (typeof clickedSlide === 'undefined') {
                        return;
                    }

                    const wrapper = clickedSlide.querySelector('.ae-link-yes');

                    if (wrapper === null || wrapper.length == 0) {
                        return;
                    } else {
                        const url = jQuery(wrapper).data('ae-url');
                        if (url !== undefined) {
                            if (jQuery(wrapper).data('ae-url') && jQuery(wrapper).hasClass('ae-new-window-yes')) {
                                window.open(jQuery(wrapper).data('ae-url'));
                            } else {
                                location.href = jQuery(wrapper).data('ae-url');
                            }
                        }
                    }
                });

                mswiper.init();

            }
    }

    const pause_on_hover_func = function(mswiper, pause_on_hover, wid) {
      
      jQuery('.elementor-element-' + wid + ' .eae-swiper-container').hover(function () {
        
          mswiper.autoplay.stop();
      }, function () {
          mswiper.autoplay.start();
      });
    }

    const EAEContentTicker = function($scope){
      let wid = $scope.data('id');
      let swiperContainer = $scope.find('.swiper');
      swiper_outer = $scope.find(".eae-content-ticker-wrapper");
      let swiper_data = swiper_outer.data("swiper");
      let swiper = {};
      swiper = {
        effect : swiper_data.effect,
        loop : swiper_data.loop,
        speed : swiper_data.speed,
        slidesPerView: 1,
        spaceBetween: 30, 
        fadeEffect: {
        crossFade: true
        },   
      }
      if(swiper_data.autoplayDuration != null){
        swiper['autoplay'] = {
          delay : swiper_data.autoplayDuration,   
          disableOnInteraction: true,      
        }
      }     
      if(swiper_data.keyboardControl != false){
        swiper['keyboard'] ={
          enabled: true,
        }
      }

      if (swiper_data.arrows === 'yes') {
        swiper['navigation'] = {
            nextEl: '.eae-navigation-icon-wrapper .eae-swiper-button-next',
            prevEl: '.eae-navigation-icon-wrapper .eae-swiper-button-prev',
        }
      }
      if(swiper_data.direction != 'null' && swiper_data.effect == 'slide'){
        swiper['direction'] = swiper_data.direction
      }
      if (jQuery(swiperContainer) !== null && jQuery(swiperContainer).length === 0){
			return;
		}
        const asyncSwiper = elementorFrontend.utils.swiper;       
        new asyncSwiper( jQuery( swiperContainer ), swiper).then((newSwiperInstance) => {
          let mswiper = newSwiperInstance;   
          const pause_on_hover = swiper_data.pauseOnHover;
            if (pause_on_hover == 'true') {
              jQuery('.elementor-element-' + wid + ' .eae-content-ticker-content-wrapper').hover(function () {
                mswiper.autoplay.stop();
            }, function () {
                mswiper.autoplay.start();
            });
          }
        });
      
    }

    

    
   
const EAERadialChart = function($scope){
  let chartInstance = null;
  $wid = $scope.data('id');
  const chatWrapper = document.querySelector('.elementor-element-' + $wid);
  const pieChart = chatWrapper.querySelector('.eae-radial-chart');
  const chartSettings = chatWrapper.querySelector('.eae-radial-chart-container').dataset.chart;
  const Chart_Wrapper = $scope.find(".eae-radial-chart-container");
  const pie_chart = $scope.find(".eae-radial-chart");    
  let settings = Chart_Wrapper.data("chart");
  if(settings.type=='polarArea' && settings.enablePercentage=='true'){
    settings.options.scales.r.ticks['callback'] = function(value, index, values) {
      return `${value}%`;
    } 
  }

  const observerOptions = {
    root: null, // Observe relative to the viewport
    rootMargin: '0px 0px -300px 0px', // Margin around the root
  };

  const observerCallback = (entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const element = entry.target;
            element.classList.add("trigger");
            if(chartInstance == null){  
              chartInstance = new Chart(pie_chart, settings);
            }
        }
    });
  };
  const observer = new IntersectionObserver(observerCallback, observerOptions);
  observer.observe(pieChart);
}

const EAECouponCode = function($scope){
    const eId = $scope.data('id');
    console.log('eID', eId);
    const element = document.querySelector('.elementor-element-' + eId);
    console.log('element', element);
    const wrapper = element.querySelector('.wts-eae-coupon-code-wrapper');
    console.log('wrapper', wrapper);
}

var ModuleHandler = elementorModules.frontend.handlers.Base,
            CouponCodeHandler;
            CouponCodeHandler = ModuleHandler.extend({
                getDefaultSettings: function getDefaultSettings() {
                    return {
                        settings: this.getElementSettings(),
                    };
                },
                getDefaultElements: function getDefaultElements(){
                    
                    const eId = this.$element.data('id');
                    const element = document.querySelector('.elementor-element-' + eId);
                    const wrapper = element.querySelector('.wts-eae-coupon-code-wrapper');
                    return {
                        eid: eId,
                        element: element,
                        wrapper: wrapper,
                    }
                },
                onInit: function onInit(){
                    const { settings } = this.getDefaultSettings();
                    const { wrapper } = this.getDefaultElements();
                    const { element } = this.getDefaultElements();

                    var couponWrapper = element.querySelector('.eae-cc-button');
                    var textWrapper = element.querySelector('.eae-code');
                     

                    let lottieWrapper  =  element.querySelectorAll('.wts-eae-coupon-code-wrapper');
                    lottieWrapper.forEach(data => {
                        let isLottiePanle = data.querySelector('.eae-lottie');
                        if (isLottiePanle != null) {
                            let lottie_data = JSON.parse(isLottiePanle.getAttribute('data-lottie-settings'));
                            let eae_animation = lottie.loadAnimation({
                                container: isLottiePanle,
                                path: lottie_data.url,
                                renderer: "svg",
                                loop: lottie_data.loop,
                            });

                            if (lottie_data.reverse == true) {
                                eae_animation.setDirection(-1);
                            }
                        }
                    })

                    if(couponWrapper != null){            
                    couponWrapper.addEventListener('click', function() {
                        const textToCopy = textWrapper.getAttribute('data-code-value');
                        const tempElement = document.createElement('textarea');
                        tempElement.value = textToCopy;
                        document.body.appendChild(tempElement);
                        tempElement.select();
                        document.execCommand('copy');
                        document.body.removeChild(tempElement);
                        const text = couponWrapper.innerHTML;
                        if(settings.coupon_type == 'scratch' || settings.coupon_type == 'peel' || settings.coupon_type == 'slide' ){
                            couponWrapper.innerText = settings.peel_after_copy_button;
                        }else{
                            couponWrapper.innerText = settings.after_copy_button;
                        }
                        let time
                        if(settings.coupon_type == 'standard'){
                          time = settings.sta_speed
                        }else{
                          time = settings.peel_speed
                        }

                        setTimeout(function() {
                            couponWrapper.innerHTML = text;
                        }, time);

                    });

                  }

                    if(settings.coupon_type == 'peel' && settings.dynamic_coupon != ''){
                        var eaePeel = new Peel('#fade-out', {
                            corner: Peel.Corners.TOP_RIGHT
                        });
                        eaePeel.setFadeThreshold(.9);
                        
                        eaePeel.handleDrag(function(evt, x, y) {
                            this.setPeelPosition(x, y);
                            if (eaePeel.getAmountClipped() === 1) {
                                eaePeel.removeEvents();
                            }
                        });
                        eaePeel.setPeelPosition(440, 100);
                    }

                if(settings.sta_layout == 'pop' && settings.dynamic_coupon != ''){
                    const popWrapper = wrapper.querySelector(".eae-coupon-popup-link");
                    
                    const wId = element.getAttribute('data-id');
                   
                    if (settings.pop_icon.library == "svg") {

                      $close_btn_html = '';

                        $close_btn_html =
                        '<svg class="eae-close" style="-webkit-mask-image: url(' +
                        settings.pop_icon.value.url +
                        "); mask-image: url(" +
                        settings.pop_icon.value.url +
                        '); "></svg>';
                        } else {
                            $close_btn_html = '<i class="eae-close ' + settings.pop_icon.value + '"> </i>';
                        }
                    $(popWrapper).eaePopup({
                        type:'inline',
                        midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
                        mainClass:"eae-coupon-popup eae-popup  eae-cc-"+wId,
                        closeMarkup: $close_btn_html,
                        closeBtnInside: settings.btn_in_out == 'yes' ? true : false ,
                        callbacks : {
                            beforeOpen: function() {
                              if(settings.effect != ''){
                                  this.st.mainClass = " eae-coupon-popup eae-popup  eae-cc-"+wId+ " mfp-"+settings.effect;
                              }
                            },
                            open : function(){
                                var id = popWrapper.getAttribute('data-id');
                                const popUp = document.querySelector(".eae-coupon-popup-"+ id)
                                var couponWrapper = popUp.querySelector('.eae-cc-button');
                                var textWrapper = popUp.querySelector('.eae-code');  
                                const text = couponWrapper.innerText;
                                
                                
                                couponWrapper.addEventListener('click', function() {
                                  
                                    // const textToCopy = textWrapper.innerText;
                                    const textToCopy = textWrapper.getAttribute('data-code-value');
                                    const tempElement = document.createElement('textarea');
                                    tempElement.value = textToCopy;
                                    popUp.appendChild(tempElement);
                                    tempElement.select();
                                    document.execCommand('copy');
                                    popUp.removeChild(tempElement);
                                   
                                    couponWrapper.innerText = settings.after_copy_button;
                                    setTimeout(function() {
                                        couponWrapper.innerText = text;
                                    }, settings.sta_speed);
                                });
                            }
                        }
                    });

                    if (settings.preview_modal == "yes" && elementorFrontend.isEditMode()) {
                      popWrapper.click()
                    }

                  }
                    if(settings.coupon_type == 'slide' && settings.dynamic_coupon != '' ){
                      var draggable = wrapper.querySelector(".eae-slide-fr");
                      if(settings.preview_modal == 'yes' && elementorFrontend.isEditMode()){
                        draggable.style.display = 'none';
                      }else{
                        var posX = 0,
                              posY = 0,
                              mouseX = 0,
                              mouseY = 0;

                              draggable.addEventListener('mousedown', mouseDown, false);
                          
                          window.addEventListener('mouseup', mouseUp, false);

                          function mouseDown(e) {
                          e.preventDefault();
                          posX = e.clientX - draggable.offsetLeft;
                          posY = e.clientY - draggable.offsetTop;
                          window.addEventListener('mousemove', moveElement, false);
                          }

                          function mouseUp() {
                          window.removeEventListener('mousemove', moveElement, false);
                          }

                          function moveElement(e) {
                            mouseX = e.clientX - posX;
                            mouseY = e.clientY - posY;
                            if(!(mouseX > 2) && !(mouseX < settings.Peel_scratch_width )  ){
                                
                                draggable.style.left = mouseX + 'px';
                            }
                          }

                            const draggableElement = draggable;
                            let offsetX;
                            draggableElement.addEventListener('touchstart', (e) => {
                            const touch = e.touches[0];
                            offsetX = touch.clientX - draggableElement.getBoundingClientRect().left;
                            // offsetY = touch.clientY - draggableElement.getBoundingClientRect().top;
                            draggableElement.style.cursor = 'grabbing';
                            });
                            draggableElement.addEventListener('touchmove', (e) => {
                            if (offsetX === undefined) return;
                            const touch = e.touches[0];
                            const x = touch.clientX - offsetX;
                            if(!(x > 4) && !(x < settings.Peel_scratch_width )  ){
                              draggableElement.style.left = x + 'px';
                            }
                          
                            });
                            draggableElement.addEventListener('touchend', () => {
                            offsetX = undefined;
                          
                            draggableElement.style.cursor = 'grab';
                            });

                      }
                  }
                    
                    if(settings.coupon_type === 'scratch' && settings.dynamic_coupon != ''){
                      if(settings.preview_modal == 'yes' && elementorFrontend.isEditMode()){
                        wrapper.querySelector('#eae-scratch-canvas').style.display = 'none';
                      }
                        
                      else{
                        var isDrawing, lastPoint;
                          
                            canvas    = wrapper.querySelector('#eae-scratch-canvas')
                             var canvasWidth  = canvas.width,
                              canvasHeight = canvas.height,
                              ctx          = canvas.getContext('2d'),
                              image        = new Image(),
                              brush        = new Image();

                            if(settings.item_bg_image == null  && settings.item_bg_color == null && settings.item_bg_color_b == null){
                              image.src = eae.plugin_url + 'assets/img/coupon/scratch_img.png';
                              image.onload = function() {
                                ctx.drawImage(image, 0, 0,canvasWidth,canvasHeight); 
                              };
                            }
                             
                            if(settings.item_bg_image != null){
                              image.src = settings.item_bg_image.url;
                              image.onload = function() {
                                ctx.drawImage(image, 0, 0,canvasWidth,canvasHeight); 
                              };
                            } else if(settings.item_bg_color_b == null && settings.item_bg_background == 'classic' && settings.item_bg_image == null ){
                              if(settings.item_bg_color != null){
                                let gradientColor = ctx.createLinearGradient(0, 0, 135, 135);
                                gradientColor.addColorStop(0, settings.item_bg_color );
                                ctx.fillStyle = gradientColor;
                                ctx.fillRect(0, 0, canvasWidth,canvasHeight);
                              }
                            } else if(settings.item_bg_color_b != null && settings.item_bg_color != null && settings.item_bg_background == 'gradient' && settings.item_bg_image == null){
                              let gradientColor = ctx.createLinearGradient(0, 0, settings.item_bg_color_stop.size , settings.item_bg_color_b_stop.size);
                              gradientColor.addColorStop(0,   settings.item_bg_color);
                              gradientColor.addColorStop(1, settings.item_bg_color_b)
                              ctx.fillStyle = gradientColor;
                              ctx.fillRect(0, 0, canvasWidth,canvasHeight);
                            }
                            
                            brush.src = eae.plugin_url + 'assets/img/coupon/brush.png'
                            canvas.addEventListener('mousedown', handleMouseDown, false);
                            canvas.addEventListener('touchstart', handleMouseDown, false);
                            canvas.addEventListener('mousemove', handleMouseMove, false);
                            canvas.addEventListener('touchmove', handleMouseMove, false);
                            canvas.addEventListener('mouseup', handleMouseUp, false);
                            canvas.addEventListener('touchend', handleMouseUp, false);
                            
                            function distanceBetween(point1, point2) {
                              return Math.sqrt(Math.pow(point2.x - point1.x, 2) + Math.pow(point2.y - point1.y, 2));
                            }
                            
                            function angleBetween(point1, point2) {
                              return Math.atan2( point2.x - point1.x, point2.y - point1.y );
                            }
                            
                            // Only test every `stride` pixel. `stride`x faster,
                            // but might lead to inaccuracy
                            function getFilledInPixels(stride) {
                              if (!stride || stride < 1) { stride = 1; }

                            
                              
                              var pixels   = ctx.getImageData(0, 0, canvasWidth, canvasHeight),
                                  pdata    = pixels.data,
                                  l        = pdata.length,
                                  total    = (l / stride),
                                  count    = 0;

                              // Iterate over all pixels
                              for(var i = count = 0; i < l; i += stride) {
                                if (parseInt(pdata[i]) === 0) {
                                  count++;
                                }
                              }
                              
                              return Math.round((count / total) * 100);
                            }
                            
                            function getMouse(e, canvas) {
                              var offsetX = 0, offsetY = 0, mx, my;
                          
                              if (canvas.offsetParent !== undefined) {
                                do {
                                  offsetX += canvas.offsetLeft;
                                  offsetY += canvas.offsetTop;
                                } while ((canvas = canvas.offsetParent));
                              }
                          
                              mx = (e.pageX || e.touches[0].clientX) - offsetX;
                              my = (e.pageY || e.touches[0].clientY) - offsetY;
                          
                              return {x: mx, y: my};
                            }
                            
                            function handlePercentage(filledInPixels) {
                              filledInPixels = filledInPixels || 0;
                              if (filledInPixels > 40) {
                                const textWrapper = wrapper.querySelector('.eae-back-wrapper');
                                textWrapper.style.zIndex = '1';
                                const canvasWrap = wrapper.querySelector('.eae-coupon-canvas');
                                canvasWrap.remove();

                              }
                            }
                            
                            function handleMouseDown(e) {
                              isDrawing = true;
                              lastPoint = getMouse(e, canvas);
                            }
                          
                            function handleMouseMove(e) {
                              if (!isDrawing) { return; }
                              
                              e.preventDefault();
                          
                              var currentPoint = getMouse(e, canvas),
                                  dist = distanceBetween(lastPoint, currentPoint),
                                  angle = angleBetween(lastPoint, currentPoint),
                                  x, y;
                              
                              for (var i = 0; i < dist; i++) {
                                x = lastPoint.x + (Math.sin(angle) * i) - 25;
                                y = lastPoint.y + (Math.cos(angle) * i) - 25;
                                ctx.globalCompositeOperation = 'destination-out';
                                ctx.drawImage(brush, x, y);
                              }
                              
                              lastPoint = currentPoint;
                              handlePercentage(getFilledInPixels(32));
                            }
                          
                            function handleMouseUp(e) {
                              isDrawing = false;
                            }     
                      }
                                                   
                    }                    
                },
                onElementChange: function onElementChange(propertyName) {                  
                  const { wrapper } = this.getDefaultElements();
                  const { settings } = this.getDefaultSettings();
                  if(settings.dynamic_coupon != '' && settings.source == 'dynamic' || settings.source == 'static')  {
                    if(settings.coupon_type === 'scratch'){
                      var  canvas    = wrapper.querySelector('#eae-scratch-canvas')
                    }
                  }
                   
                  if ( propertyName == 'item_bg_background' || propertyName == 'item_bg_color' ||
                       propertyName == 'item_bg_color_b' || propertyName == 'item_bg_color_stop' || propertyName == 'item_bg_color_b_stop' ){
                      var   canvasWidth  = canvas.width,
                      canvasHeight = canvas.height,
                      ctx = canvas.getContext('2d')
                    if(settings.item_bg_color_b == null && settings.item_bg_background == 'classic'){
                      // let gradientColor = ctx.createLinearGradient(0, 0, 135, 135);
                      // gradientColor.addColorStop(0, settings.item_bg_color );
                      // gradientColor.addColorStop(1, "#6414E9")
                      ctx.fillStyle = settings.item_bg_color;
                      ctx.fillRect(0, 0, canvasWidth,canvasHeight);
                    }
                    if(settings.item_bg_color_b != null && settings.item_bg_background == 'gradient'){
                      let gradientColor = ctx.createLinearGradient(0, 0, settings.item_bg_color_stop.size , settings.item_bg_color_b_stop.size);
                      gradientColor.addColorStop(0, settings.item_bg_color );
                      gradientColor.addColorStop(1, settings.item_bg_color_b)
                      ctx.fillStyle = gradientColor;
                      ctx.fillRect(0, 0, canvasWidth,canvasHeight);
                    }
                  }   
                        
                },

            });


    var ModuleHandler = elementorModules.frontend.handlers.Base,
    DropbarHandler;
    DropbarHandler = ModuleHandler.extend({
            getDefaultSettings: function getDefaultSettings() {
                return {
                    settings: this.getElementSettings(),
                };
            },
            getDefaultElements: function getDefaultElements(){
                
                const eId = this.$element.data('id');
                const element = document.querySelector('.elementor-element-' + eId);
                const wrapper = element.querySelector('.eae-dropbar-wrapper');
                return {
                    eid: eId,
                    element: element,
                    wrapper: wrapper,
                }
            },
            onInit: function onInit(){
              const { settings } = this.getDefaultSettings();
              const { wrapper } = this.getDefaultElements();
              const { element } = this.getDefaultElements();
              const contentWrapper = wrapper.querySelector(".eae-drop-content");

              if (settings.content_mode === 'hover') {
                let animationTimeout;
            
                wrapper.addEventListener('mouseenter', () => {
                  clearTimeout(animationTimeout);
                  animationTimeout = setTimeout(() => {
                    wrapper.classList.add('eae-active');
                  }, settings.show_delay.size);
                  wrapper.classList.add('eae-animation');
                });
            
                wrapper.addEventListener('mouseleave', () => {
                  if (!settings.caption_animation_out) {
                      wrapper.classList.remove('eae-animation');
                  }
                    animationTimeout = setTimeout(() => {
                        wrapper.classList.remove('eae-active');
                    }, settings.hide_delay.size);
                  });
              }
              
              
              if (settings.content_mode === 'click') {
                    wrapper.addEventListener('click', () => {
                    wrapper.classList.toggle('eae-active');
                    if (wrapper.classList.contains('eae-active')) {
                      wrapper.classList.add('eae-animation');
                    }else{
                      if (!settings.caption_animation_out) {
                        wrapper.classList.remove('eae-animation');
                      }
                    }
                });
              }

              function setPosition(element, position, animationType) {
                const parentRect = element.parentElement.getBoundingClientRect();
                const elemRect = element.getBoundingClientRect();
                const wrapper = element.parentElement;
                const screenWidth = window.innerWidth;
            
                const positionCalculations = {
                    "bottom-left": () => ({ top: settings.off_set.size ? `${wrapper.offsetHeight + settings.off_set.size}` : "unset", left: 0 }),
                    "bottom-center": () => ({ top: settings.off_set.size ? `${wrapper.offsetHeight + settings.off_set.size}` : "unset", left: (parentRect.width - elemRect.width) / 2 }),
                    "bottom-right": () => ({ top: settings.off_set.size ? `${wrapper.offsetHeight + settings.off_set.size}` : "unset", left: parentRect.width - elemRect.width }),
                    "top-left": () => ({ top: - (elemRect.height + settings.off_set.size), left: 0 }),
                    "top-center": () => ({ top: - (elemRect.height + settings.off_set.size), left: (parentRect.width - elemRect.width) / 2 }),
                    "top-right": () => ({ top: - (elemRect.height + settings.off_set.size), left: parentRect.width - elemRect.width }),
                    "left-top": () => ({ top: 0, left: - (elemRect.width + settings.off_set.size) }),
                    "left-center": () => ({ top: (parentRect.height - elemRect.height) / 2, left: - (elemRect.width + settings.off_set.size) }),
                    "left-bottom": () => ({ top: parentRect.height - elemRect.height, left: - (elemRect.width + settings.off_set.size) }),
                    "right-top": () => ({ top: 0, left: parentRect.width + settings.off_set.size }),
                    "right-center": () => ({ top: (parentRect.height - elemRect.height) / 2, left: parentRect.width + settings.off_set.size }),
                    "right-bottom": () => ({ top: parentRect.height - elemRect.height, left: parentRect.width + settings.off_set.size }),
                };
            
                if (!positionCalculations[position]) {
                    console.error("Invalid position provided");
                    return;
                }
            
                let { top, left } = positionCalculations[position]();
            
                top = typeof top === "string" ? parseFloat(top) : top;
                left = typeof left === "string" ? parseFloat(left) : left;
            
                const elementWidth = elemRect.width;
                const wrapperLeft = parentRect.left + left;
                const wrapperRight = wrapperLeft + elementWidth;
            
                if (wrapperLeft < 0) {
                    left = -parentRect.left;
                } else if (wrapperRight > screenWidth) {
                    left -= wrapperRight - screenWidth; 
                }
            
                element.style.top = `${top}px`;
                element.style.left = `${left}px`;
            
                const animationStyles = {
                    "slide-left": () => {
                        element.style.clipPath = "inset(0 100% 0 0)";
                    },
                    "slide-top": () => {
                        element.style.clipPath = "inset(0 0 100% 0)";
                    },
                    "slide-bottom": () => {
                        element.style.clipPath = "inset(100% 0 0 0)";
                    },
                    "slide-right": () => {
                        element.style.clipPath = "inset(0 0 0 100%)";
                    },
                    "animation-fade": () => {
                        element.style.opacity = "0";
                    },
                };
            
                if (animationStyles[animationType]) {
                    animationStyles[animationType]();
                } else {
                    console.error("Invalid animation type provided");
                    return;
                }
            
                if (wrapper) {
                    const toggleAnimation = (isActive) => {
                        const animationToggles = {
                            "slide-left": () => (element.style.clipPath = isActive ? "inset(0 0 0 0)" : "inset(0 100% 0 0)"),
                            "slide-top": () => (element.style.clipPath = isActive ? "inset(0 0 0 0)" : "inset(0 0 100% 0)"),
                            "slide-bottom": () => (element.style.clipPath = isActive ? "inset(0 0 0 0)" : "inset(100% 0 0 0)"),
                            "slide-right": () => (element.style.clipPath = isActive ? "inset(0 0 0 0)" : "inset(0 0 0 100%)"),
                            "animation-fade": () => (element.style.opacity = isActive ? "1" : "0"),
                        };
                        if (animationToggles[animationType]) {
                            animationToggles[animationType]();
                        }
                    };
                    let isAnimationActive = false;
            
                    let removeClassTimeout;
            
                    if (settings.content_mode === "hover") {
                        wrapper.addEventListener("mouseenter", () => {
                            clearTimeout(removeClassTimeout);
                            toggleAnimation(true);
                        });
            
                        wrapper.addEventListener("mouseleave", () => {
                            removeClassTimeout = setTimeout(() => {
                                toggleAnimation(false);
                            }, settings.hide_delay.size);
                        });
                    } else {
                        wrapper.addEventListener("click", () => {
                            isAnimationActive = !isAnimationActive;
                            toggleAnimation(isAnimationActive);
                        });
                    }
                }
            }
              
              setPosition(contentWrapper, settings.content_position, settings.content_animation);

              var isLottiePanle = wrapper.querySelector('.eae-lottie-animation');

              if (isLottiePanle != null) {
                let lottie_data = JSON.parse(isLottiePanle.getAttribute('data-lottie-settings'));
                let eae_animation = lottie.loadAnimation({
                  container: isLottiePanle,
                  path: lottie_data.url,
                  renderer: "svg",
                  loop: lottie_data.loop,
                });
          
                if (lottie_data.reverse == true) {
                  eae_animation.setDirection(-1);
                }
              }

            },
        });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/wts-ab-image.default",
      ab_image
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/global",
      ParticlesBG
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/global",
      AnimatedGradient
    );
    //elementorFrontend.hooks.addAction('frontend/element_ready/global', EaeUnfold);
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/wts-modal-popup.default",
      EaePopup
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/wts-testimonial-slider.default",
      EAETestimonial
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-info-circle.skin1",
      InfoCircleHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-info-circle.skin2",
      InfoCircleHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-info-circle.skin3",
      InfoCircleHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-info-circle.skin4",
      InfoCircleHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-timeline.skin1",
      TimelineHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-timeline.skin2",
      TimelineHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-timeline.skin3",
      TimelineHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-timeline.skin4",
      TimelineHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-evergreen-timer.skin1",
      EgTimerSkin1
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-evergreen-timer.skin2",
      EgTimerSkin2
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-evergreen-timer.skin3",
      EgTimerSkin3
    );
    //elementorFrontend.hooks.addAction('frontend/element_ready/eae-evergreen-timer.skin4', EgTimerSkin4);
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-comparisontable.default",
      CompareTable
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-progress-bar.skin1",
      ProgressBar
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-progress-bar.skin2",
      ProgressBar
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-progress-bar.skin3",
      ProgressBar
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-progress-bar.skin4",
      ProgressBar
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-progress-bar.skin5",
      ProgressBar
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-filterableGallery.default",
      FilterableGallery
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-content-switcher.skin1",
      contentSwitcherButton
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-content-switcher.skin2",
      contentSwitcherButton
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-content-switcher.skin3",
      contentSwitcherRadio
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-content-switcher.skin4",
      contentSwitcherRadio
    );
    //elementorFrontend.hooks.addAction('frontend/element_ready/global', RibbonsBadgesHandler);
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/global",
      WrapperLinksHander
    );
    // elementorFrontend.hooks.addAction('frontend/element_ready/eae-charts.pie', EAECharts);
    // elementorFrontend.hooks.addAction('frontend/element_ready/eae-charts.doughnut', EAECharts);
    // elementorFrontend.hooks.addAction('frontend/element_ready/eae-charts.polarArea', EAECharts);
    // // elementorFrontend.hooks.addAction('frontend/element_ready/eae-charts.radar', EAECharts);
    // elementorFrontend.hooks.addAction('frontend/element_ready/eae-charts.bubble', EAECharts);
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-thumbgallery.default",
      EAEThumbGallery
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-chart.bar",
      EAEChart
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-chart.horizontalBar",
      EAEChart
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-chart.line",
      EAEChart
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-data-table.default",
      EAETable
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/CfStyler.default",
      EAECfStyler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-anythingcarousel.default",
      EAEAnythingCarousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/wts-content-ticker.default",
      EAEContentTicker
    );
   
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/eae-radial-charts.default",
      EAERadialChart
    );

    // elementorFrontend.hooks.addAction(
    //   "frontend/element_ready/eae-coupon-code.default",
    //   EAECouponCode
    // );

    elementorFrontend.hooks.addAction('frontend/element_ready/eae-coupon-code.default', function ($scope) {	
      elementorFrontend.elementsHandler.addHandler(CouponCodeHandler, {
          $element: $scope
        });
  });

    elementorFrontend.hooks.addAction('frontend/element_ready/eae-dropbar.default', function ($scope) {	
      elementorFrontend.elementsHandler.addHandler(DropbarHandler, {
          $element: $scope
        });
  });
    
  });
})(jQuery);


