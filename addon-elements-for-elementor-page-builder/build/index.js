/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 45:
/***/ (() => {

(function ($){
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            CircularProgressHandler;
           
        CircularProgressHandler = ModuleHandler.extend({
            getDefaultElements: function getDefaultElements() {
				
                const eId = this.$element.data('id'); 
                const element = document.querySelector('.elementor-element-' + eId);
                const wrapper = element.querySelector('.eae-cp-wrapper');
                const data = JSON.parse(wrapper.getAttribute('data-settings'));
                return{
                    eid: eId,
                    element: element,
                    wrapper: wrapper, 
                    data: data,
                }
			},
            onInit: function onInit(){
                const that = this;
                const { wrapper,data} = this.getDefaultElements();
                //Lottie 
                that.getLottie(wrapper);
                
                that.contentBoxSize();
                if(data != null){
                    const element = wrapper.querySelector('.eae-cp-canvas-wrapper');
                    const observerOptions = {
                        root: null, // Observe relative to the viewport
                        rootMargin: '0px 0px -30% 0px', // Margin around the root
                      };
                
                      const observerCallback = (entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const wrapper = entry.target;
                                if(!wrapper.classList.contains("trigger")){
                                    wrapper.classList.add('trigger');
                                    that.getTrack(wrapper,data);
                                }
                            }
                        });
                      };
                      const observer = new IntersectionObserver(observerCallback, observerOptions);
                      observer.observe(element);
                }
            },
            onElementChange: function onElementChange(propertyName) {
				if (propertyName === 'cp_track_width' || propertyName === 'cp_track_width') {
					this.contentBoxSize();
				}
			},
			getTrack: function getTrack(wrapper, data) {
				
                const settings = this.getElementSettings();

                const can = wrapper.querySelector('.eae-cp-canvas');
                const circleSize = can.width/2; 

                const progressWidth = (can.width * (data.progress_width/100))/2;
                const trackWidth = (can.width * (data.track_width/100))/2;

                const valueContainer = wrapper.querySelector('.eae-cp-procent');
				
                const canvas = can.getContext('2d');
                const startAngle = data.start_angle;

                const value = data.value // value
                const layoutType = data.layout_type; 
                const circle =  (layoutType == 'full-circle') ? 360 : 180;
                
                const posX = can.width / 2;
                let posY = 0;
                let percent = 0;

                const maxValue = (settings.cp_value_type == 'percentage') ? '100' : settings.cp_max_value; 


                const fps = (data.animation_duration/( (value/maxValue) * circle));
                
                // let oneProcent = (value/maxValue)*100;
                

                let result = 0;
                let pointResult = 0
                let gradientColorAg = 0;

                let circleRadius = 0;
                if(progressWidth >= trackWidth){
                    circleRadius = (circleSize) - (progressWidth/2);
                }else{
                    circleRadius = (circleSize) - (trackWidth/2);
                }

                if(layoutType == 'full-circle'){
                    pointResult =  (value/maxValue) * 360; //value
                    result = value;
                    posY = can.height / 2;
					if (data.track_layout == 'butt') {
						if (data.animation_direction == 'reverse') {
							gradientColorAg = (Math.PI / 180) * (360 - (startAngle + 90));
						} else {
							gradientColorAg = (Math.PI / 180) * ((360 - (90 - startAngle)));
						}
                    }else{
                        if(data.animation_direction == 'reverse') {
							gradientColorAg = (Math.PI / 180) * ((360 - (startAngle + 90) + (data.progress_width/2)));
                        }else{
                            gradientColorAg = (Math.PI/180)  * (((360 - (90 - startAngle)) - (data.progress_width/2)) );
                        }
                    }
                
                }else{
                    pointResult =  (value/maxValue) * 180;
                    posY = can.height;
                    result = value; //value
                }
                
                canvas.lineCap = data.track_layout;
                
                let deegres = 0;
                let progressColor = '';
                
                    
                if(data.progress_color_type == 'gradient'){
                    const gradient = canvas.createConicGradient(gradientColorAg, posX, posY);
                    const gradientColor = data.progress_gradient_color;
                    let offset = 0;
                    let color = '';
                    
                    gradientColor.forEach(function (info){
                        if(info.cp_progress_gradient_color !== ''){
                            if(layoutType == 'full-circle'){
                                offset = (1 * info.cp_progress_color_stop.size)/100;
                                if(data.animation_direction == 'reverse'){
                                    offset = 1 - offset;
                                }
                                
                            }else{
								if(data.animation_direction == 'reverse') {
                                    offset = 1 - ((0.5 * info.cp_progress_color_stop.size)/100);
                                }else{
                                    offset = ((0.5 * info.cp_progress_color_stop.size)/100)+ 0.5;
                                }
                            }
                            
                            color = info.cp_progress_gradient_color;
                            gradient.addColorStop(offset, color);
                        }
                    });
                    progressColor = gradient;
                }else{
                    progressColor = data.progress_color;
                }

                
                
                let count = 0; 
                if(valueContainer !== null || data.hide_value == 'yes' && value != '' ){

                    let acrInterval = setInterval (function() {
                        
                        if(layoutType == 'full-circle'){
                            // Full circle    
                            if(settings.cp_value_type == 'percentage'){
                                deegres +=1;
                                percent = (deegres/360)*100;

                            }else{
                                deegres += 1;
                                percent = maxValue * (deegres/360);
                                // deegres += 0.1;
                                // percent = maxValue * ((deegres/360)*100)/100;
                                
                            }
                            count = deegres;
                            trackStartA = (Math.PI/180) * 270;
                            trackEndA =  (Math.PI/180) * (270 + 360);

                            if(data.animation_direction == 'reverse'){
                                progStartA = (Math.PI/180)  * (360 - (startAngle + 90) - deegres);
                                progEndA  = (Math.PI/180) * ((360 - (startAngle + 90)));
                            }else{
                                progStartA = (Math.PI/180)  * (360 - (90 - startAngle));
                                progEndA = (Math.PI/180) * ((360 - (90 - startAngle)) + deegres);
                            }

                            if(data.hide_value !== 'yes'){
                                valueContainer.innerHTML = parseInt(percent);
                            }
                        }else if(layoutType == 'half-circle'){
                            // Half circle
                            if(settings.cp_value_type == 'percentage'){
                                if(value != 0){
                                    deegres += 1;
                                }
                                percent = (deegres/180)*100;
                            }else{
                                if(value != 0){
                                    deegres += 1;
                                }
                                // percent = deegres;
                                // deegres += 0.1;
                                percent = maxValue * ((deegres/180)*100)/100;
                                
                            }
                            count = deegres;
                            // 180 to 360;
                            trackStartA = Math.PI * 1;
                            trackEndA =  Math.PI * 0;
                            if(data.animation_direction == 'reverse'){
                                progEndA = (Math.PI/180)  * (360 + (360));
                                progStartA= (Math.PI/180) * (360 + (360 - deegres));
                            }else{
                                progStartA = (Math.PI/180)  * (360 + (180));
                                progEndA = (Math.PI/180) * (180 + deegres);
                            }
                            
                            if(data.hide_value !== 'yes'){
                                valueContainer.innerHTML = parseInt(percent);
                            }
                        }

                        canvas.clearRect( 0, 0, can.width, can.height );

                        if(data.track_width !== '' && data.track_width !== 0){
                            canvas.beginPath();
                            canvas.arc( posX, posY, circleRadius, trackStartA , trackEndA ); // track
                            canvas.strokeStyle = data.track_color;
                            canvas.lineWidth = trackWidth;
                            canvas.stroke();
                        }
                        
                        if(maxValue != '' && value != 0 && data.progress_width !== '' && data.progress_width !== 0){
                            canvas.beginPath();
                            canvas.strokeStyle = progressColor;
                            canvas.lineWidth = progressWidth;
                            canvas.arc( posX, posY, circleRadius , progStartA, progEndA ); // progress
                        }
            
                        canvas.stroke();
            
                        if(layoutType == 'full-circle'){
                            // Full circle
                            // if(deegres >= result){
                            while(count < (deegres + 0.99)){
                                if( count.toFixed(2) == pointResult){
                                    clearInterval(acrInterval);
                                    if(valueContainer !== null){
                                        valueContainer.innerHTML = value;
                                    }
                                }
                                count += 0.01;
                            }
                            if( percent >= result){
                                clearInterval(acrInterval);
                                if(valueContainer !== null){
                                    valueContainer.innerHTML = value;
                                }
                            }
                        }else{
                            // Half circle
                            while(count < (deegres + 0.99)){
                                if( count.toFixed(2) == pointResult){
                                    clearInterval(acrInterval);
                                    if(valueContainer !== null){
                                        valueContainer.innerHTML = value;
                                    }
                                }
                                count += 0.01;
                            }
                            if(  percent >= result || result == 1){
                                clearInterval(acrInterval);
                                if(valueContainer !== null){
                                    valueContainer.innerHTML = value;
                                }
                            }
                        }

                        if(value == 0 || value == ''){
                            clearInterval(acrInterval);
                        }
            
                    },fps);
                }
            },
            getLottie: function getLottie(wrapper){
                isLottiePanel = wrapper.querySelector('.eae-lottie');
                if (isLottiePanel != null) {
                    let lottie_data = JSON.parse(isLottiePanel.getAttribute('data-lottie-settings'));
                    let eae_animation = lottie.loadAnimation({
                        container: isLottiePanel,
                        path: lottie_data.url,
                        renderer: "svg",
                        loop: lottie_data.loop,
                    });

                    if (lottie_data.reverse == true) {
                        eae_animation.setDirection(-1);
                    }
                }
            },
            contentBoxSize: function contentBoxSize(){
                let settings = this.getElementSettings();
                let circleSize = settings.cp_content_box_size;
                let track = settings.cp_track_width;
                let progress = settings.cp_progress_width;

                const { wrapper, data } = this.getDefaultElements();
                
                let container = wrapper.querySelector('.eae-cp-text-contain');
				if (container != null) {
					let trackWidth = 0;
					if (track.size >= progress.size) {
						if (track.size !== '') {
							trackWidth = track.size;
						}
					} else {
						if (progress.size !== '') {
							trackWidth = progress.size;
						}
					}

					let widthdeduct = trackWidth;
					let heightdeduct = trackWidth;
					container.style.width = 'calc(' + circleSize.size + '%' + ' - ' + widthdeduct + '%)';
					container.style.height = 'calc(' + circleSize.size + '%' + ' - ' + heightdeduct + '%)';
					
					if (data.layout_type == 'half-circle') {
						borderRadius = container.offsetHeight + 'px ' + container.offsetHeight + 'px  0 0';
						container.style.borderRadius = borderRadius;
					}
                }
            }
        });
        
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-circular-progress.default', function ($scope){
            elementorFrontend.elementsHandler.addHandler(CircularProgressHandler, {
				$element: $scope
			});
        });
    });

})(jQuery);

/***/ }),

/***/ 82:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

(function ($) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            WooProductsHandler;

            WooProductsHandler = ModuleHandler.extend({
                getDefaultSettings: function getDefaultSettings() {
                    return {
                        settings: this.getElementSettings(),
                    };
                },
                getDefaultElements: function getDefaultElements(){
                    
                    const wId = this.$element.data('id');
                    const scope = this.$element;
                    const element = document.querySelector('.elementor-element-' + wId);
                    const wrapper = element.querySelector('.eae-woo-cat-wrapper');
                    return {
                        eid: wId,
                        element: element,
                        wrapper: wrapper,
                        scope: scope,
                    }
                },
                onInit: function onInit(){
                    const { settings } = this.getDefaultSettings();
                    const { wrapper , scope} = this.getDefaultElements();
                    let { element } = this.getDefaultElements();
                    const { eid } = this.getDefaultElements();
                    let LottieWrapper = wrapper.querySelectorAll('.eae-category-card');

                    if(wrapper.classList.contains('eae-wp-slider')){
                        const outer_wrapper = scope.find('.eae-swiper-outer-wrapper');
                        const swiper_settings = outer_wrapper.data('swiper-settings');
                        new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y(swiper_settings, eid, scope);
                    }  
                    
                    window.addEventListener("resize", function(){
                        let resizeValue = wrapper.getAttribute('data-stacked');
                        let width = this.window.innerWidth;
                        if(width <= resizeValue ){
                            wrapper.classList.add('enable-stacked');
                        }else{
                            wrapper.classList.remove('enable-stacked');
                        }
                    });

                    LottieWrapper.forEach(data => {
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

                },
                onElementChange: function onElementChange(propertyName) {                  
                        
                },

            });
            elementorFrontend.hooks.addAction('frontend/element_ready/eae-woo-category.default', function ($scope) {	
                elementorFrontend.elementsHandler.addHandler(WooProductsHandler, {
                    $element: $scope
                  });
            });

        });   
})(jQuery);

/***/ }),

/***/ 107:
/***/ (() => {

(function ($) {
	const EAECallToAction = function ($scope) {
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
		
        let isLottiePanle = element.querySelector('.eae-cta-icon.eae-lottie');

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

		const buttonContainer = element.querySelector('.eae-cta-button');

		let buttonsLottiePanel = buttonContainer.querySelectorAll('.eae-lottie');
		if (buttonsLottiePanel != null) {
			buttonsLottiePanel.forEach(function(element){
				let lottie_data = JSON.parse(element.getAttribute('data-lottie-settings'));
				let eae_animation = lottie.loadAnimation({
					container: element,
					path: lottie_data.url,
					renderer: "svg",
					loop: lottie_data.loop,
				});

				if (lottie_data.reverse == true) {
					eae_animation.setDirection(-1);
				}
			});
		}
	}
	

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-call-to-action.default",
			EAECallToAction
		);
	});
})(jQuery);

/***/ }),

/***/ 147:
/***/ (() => {

(function ($) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
        BlobShapeHandler;

        BlobShapeHandler = ModuleHandler.extend({
            getDefaultSettings: function getDefaultSettings() {
                    return {
                        settings: this.getElementSettings(),
                    };
                },
                getDefaultElements: function getDefaultElements(){
                    const wId = this.$element.data('id');
                    const scope = this.$element;
                    const element = document.querySelector('.elementor-element-' + wId);
                    const wrapper = element.querySelector('.eae-blob-shape-wrapper');
                    const { settings } = this.getSettings();

                    let blobSize;
                    if(settings.blob_size.unit == '%'){
                        let wrapperWidth = wrapper.offsetWidth
                      
                        if (settings.blob_size.size == 100) {
                            blobSize = wrapperWidth;
                        } else {
                            blobSize = wrapperWidth * parseFloat(`0.${settings.blob_size.size}`); 
                        }
                    }else{
                        blobSize = settings.blob_size.size
                    }

                    return {
                        eid: wId,
                        element: element,
                        wrapper: wrapper,
                        scope: scope,
                        blobSize: blobSize.toFixed(2),
                    }
                },
                onInit: function onInit(){

                    const { settings } = this.getSettings();
                    const { generator } = this.getSvgPath();

                    const { wrapper , scope , blobSize} = this.getDefaultElements();
                                
                    const blobSvgPath = wrapper.querySelector('#eae-blob-svg-path');

                   

                    const blobConfig = {
                        seed: 0.5,
                        edges:settings.blob_randomness.size < 3 ? 3 : settings.blob_randomness.size,  
                        growth: settings.blob_nodes.size,
                        size: blobSize
                    };

                    function generateNewPath() {
                        blobConfig.seed = Math.random();
                        return generator(blobConfig).path;
                    }

                    blobSvgPath.setAttribute('d', generateNewPath());

                    const animeSettings = {   
                        targets: blobSvgPath,  
                        direction: 'alternate',
                        easing: 'easeInOutSine',  
                        duration: settings.animation_delay * 1000, 
                        loop: true,
                        d: [
                            { value:  generator().path },
                            { value: generator().path },
                        ],
                    };
                    if(settings.enable_animation == 'yes'){
                        anime(animeSettings);
                    }

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
                getSvgPath: function getSvgPath(){
                    const { settings } = this.getSettings();
                   const { wrapper , scope , blobSize} = this.getDefaultElements();
                    const blobSvg = wrapper.querySelector('.eae-blob-svg');

                    if(settings.blob_size.unit == '%'){
                        blobSvg.setAttribute('width', blobSize);
                        blobSvg.setAttribute('height', blobSize);
                        blobSvg.setAttribute('viewBox', `0 0 ${blobSize} ${blobSize}`);
                    }
                   
                    const generator = ({ size = blobSize, growth = settings.blob_nodes.size, edges = settings.blob_randomness.size < 3 ? 3 : settings.blob_randomness.size, seed = null } = {}) => {
                        const { destPoints, seedValue } = _createPoints(size, growth, edges, seed);
                        const path = _createSvgPath(destPoints);
                        return { path, seedValue };
                    };

                    const _toRad = (deg) => deg * (Math.PI / 180.0);

                    const _divide = (count) => {
                        const deg = 360 / count;
                        return Array(count).fill("a").map((_, i) => i * deg);
                    };

                    const _randomDoubleGenerator = (s) => {
                        const mask = 0xffffffff;
                        let m_w = (123456789 + s) & mask;
                        let m_z = (987654321 - s) & mask;

                        return function () {
                            m_z = (36969 * (m_z & 65535) + (m_z >>> 16)) & mask;
                            m_w = (18000 * (m_w & 65535) + (m_w >>> 16)) & mask;

                            let result = ((m_z << 16) + (m_w & 65535)) >>> 0;
                            result /= 4294967296;
                            return result;
                        };
                    };

                    const _magicPoint = (value, min, max) => {
                        let radius = min + value * (max - min);
                        radius = Math.min(max, Math.max(min, radius));
                        return radius;
                    };

                    const _point = (origin, radius, degree) => {
                        const x = origin + radius * Math.cos(_toRad(degree));
                        const y = origin + radius * Math.sin(_toRad(degree));
                        return [Math.round(x), Math.round(y)];
                    };

                    const _createPoints = (size, minGrowth, edgesCount, seed) => {
                        const outerRad = size / 2;
                        const innerRad = minGrowth * (outerRad / 10);
                        const center = size / 2;

                        const slices = _divide(edgesCount);
                        const maxRandomValue = 99999;  // Keep randomness controlled
                        const id = Math.floor(Math.random() * maxRandomValue);
                        const seedValue = seed || id;
                        const randVal = _randomDoubleGenerator(seedValue);
                        const destPoints = [];

                        slices.forEach((degree) => {
                            const O = _magicPoint(randVal(), innerRad, outerRad);
                            const end = _point(center, O, degree);
                            destPoints.push(end);
                        });
                        return { destPoints, seedValue };
                    };

                    const _createSvgPath = (points) => {
                        let svgPath = "";
                        let mid = [(points[0][0] + points[1][0]) / 2, (points[0][1] + points[1][1]) / 2];
                        svgPath += "M" + mid[0] + "," + mid[1];

                        for (let i = 0; i < points.length; i++) {
                            const p1 = points[(i + 1) % points.length];
                            const p2 = points[(i + 2) % points.length];
                            mid = [(p1[0] + p2[0]) / 2, (p1[1] + p2[1]) / 2];
                            svgPath += "Q" + p1[0] + "," + p1[1] + "," + mid[0] + "," + mid[1];
                        }
                        svgPath += "Z";
                        return svgPath;
                    };

                    return {
                        generator: generator,
                    }
                },
            });
            elementorFrontend.hooks.addAction('frontend/element_ready/eae-blob-shape.default', function ($scope) {	
                elementorFrontend.elementsHandler.addHandler(BlobShapeHandler, {
                    $element: $scope
                  });
            });
        });   
})(jQuery);

/***/ }),

/***/ 210:
/***/ (() => {

(function ($) {
	const EAEImageStack = function ($scope) {        
        const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
		const wrapper = element.querySelector('.eae-image-stack');  
		let LottieData = wrapper.querySelectorAll('.img-stack-item.eae-is-ct-lottie-animation'); 

            LottieData.forEach(data => {
                isLottiePanle = data.querySelector('.eae-lottie');
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
        }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/eae-image-stack.default",
            EAEImageStack
        );
    });
})(jQuery);

/***/ }),

/***/ 211:
/***/ (() => {

(function ($) {
	const EAEAdvanceHeading = function ($scope) {
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
        var uniLottie = element.querySelector('.eae-ah-icon.eae-lottie-animation');
        var titleLottie = element.querySelector('.eae-ah-title-icon.eae-lottie-animation');
        var separatorLottie =  element.querySelector('.eae-sep-icon.eae-lottie-animation');


		if (uniLottie != null) {
			let lottie_data = JSON.parse(uniLottie.getAttribute('data-lottie-settings'));
			let eae_animation = lottie.loadAnimation({
				container: uniLottie,
				path: lottie_data.url,
				renderer: "svg",
				loop: lottie_data.loop,
			});

			if (lottie_data.reverse == true) {
				eae_animation.setDirection(-1);
			}
		}
		if (titleLottie != null) {
			let lottie_data = JSON.parse(titleLottie.getAttribute('data-lottie-settings'));
			let eae_animation = lottie.loadAnimation({
				container: titleLottie,
				path: lottie_data.url,
				renderer: "svg",
				loop: lottie_data.loop,
			});

			if (lottie_data.reverse == true) {
				eae_animation.setDirection(-1);
			}
		}
		if (separatorLottie != null) {
			let lottie_data = JSON.parse(separatorLottie.getAttribute('data-lottie-settings'));
			let eae_animation = lottie.loadAnimation({
				container: separatorLottie,
				path: lottie_data.url,
				renderer: "svg",
				loop: lottie_data.loop,
			});

			if (lottie_data.reverse == true) {
				eae_animation.setDirection(-1);
			}
		}
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-advanced-heading.default",
			EAEAdvanceHeading
		);
	});
})(jQuery);

/***/ }),

/***/ 234:
/***/ (() => {



/***/ }),

/***/ 259:
/***/ (() => {

(function ($) {
	const EAEAdvancedPriceTable = function ($scope){
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
		const wrapper = element.querySelector('.eae-price-table');
		
		window.addEventListener("resize", function(){
			let resizeValue = wrapper.getAttribute('data-stacked');
			let widnowWidth = this.window.innerWidth;
			if(widnowWidth <= resizeValue ){
				wrapper.classList.add('enable-stacked');
			}else{
				wrapper.classList.remove('enable-stacked');
			}
		});

		get_switch_raido_button($scope);
 
		get_switch_buttons($scope);
		
		let lottie_data = wrapper.querySelectorAll('.eae-price-table-wrapper');
		get_lottie(lottie_data,'.eae-apt-icon.eae-lottie');

		const features = wrapper.querySelectorAll('.eae-apt-features-container');
        // get_lottie(features);
		const features_data = wrapper.querySelectorAll('.eae-apt-features-list-item')

		
		get_lottie(features_data,'.eae-apt-feature-icon.eae-lottie');
	}

	function get_switch_raido_button($scope){
		let wrapper = $scope.find('.eae-price-table');
		let toggle_switch = wrapper.find(".eae-apt-switch-label");

		let label_tab_1 = wrapper.find(".eae-apt-content-switch-button-text.eae-label-tab-1");
		let label_tab_2 = wrapper.find(".eae-apt-content-switch-button-text.eae-label-tab-2");

		let container_tab_1 = wrapper.find(".eae-apt-tab-1.eae-apt-tab-content-section"); 
		let container_tab_2 = wrapper.find(".eae-apt-tab-2.eae-apt-tab-content-section"); 
		
		toggle_switch.on('click',function(e){
			var check = toggle_switch.find(".eae-pt-content-toggle-switch");
			if(check.is(":checked")){
				label_tab_2.addClass("active-button");
				container_tab_2.addClass("active");
				label_tab_1.removeClass("active-button");
				container_tab_1.removeClass("active");
			}else{
				label_tab_1.addClass("active-button");
				container_tab_1.addClass("active");
				label_tab_2.removeClass("active-button");
				container_tab_2.removeClass("active");
			}
		});
	}

	function get_switch_buttons($scope){
		var $wrapper = $scope.find(".eae-price-table");
    	var wid = $scope.data("id");
      	var buttons = $wrapper.find(".eae-apt-content-switch-button");

       buttons.each(function (index, button) {
			$(this).on("click", function (e) {
				e.preventDefault();
				let active_tab = button.getAttribute('data-active-tab');
				// let container = $wrapper.find(".eae-apt-tab");
				buttons.removeClass("active-button");
				$(this).addClass("active-button");
				let current_content_section = $wrapper.find(
						".eae-apt-" + active_tab);
				var content_sections =$wrapper.find(".eae-apt-tab-content-section");
				content_sections.removeClass("active");
				current_content_section.addClass("active");	  
			});
      	});
	}

	function get_lottie(flexList,cssClass){
		flexList.forEach(flexList => {
			isLottiePanle = flexList.querySelector(cssClass);
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
        });
	} 

    $(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-advanced-price-table.default",
			EAEAdvancedPriceTable
		);
	});
})(jQuery);

/***/ }),

/***/ 289:
/***/ (() => {

"use strict";

(function ($) {
	const EAEDevices = function ($scope) {
        const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
        const wrapper = element.querySelector('.eae-device-video-outer-wrapper'); 
        const oriWrapper = element.querySelector('.orientation i');  
        const deviceWrapper=element.querySelector('.eae-wrapper');  
            
        var imageCheckElement = element.querySelector('.device-content');
        if(imageCheckElement.hasAttribute("data-settings")){
            var imageElement = $scope.find(".device-content");
            var imageVertical = imageElement.find(".device-img-content");
            var imagedataElement = imageElement.data("settings");
            var image = imageElement.find("img");
            var imageDirection = imagedataElement["direction"];
            var imageReverse = imagedataElement["reverse"];
            var transformOffset = null;

            // const wrapperForCheck = $scope.find(".eae-device-wrapper");
            let wrapperForCheck = element.querySelector('.eae-device-wrapper');  
    
            function startTransform() {
                image.css("transform", (imageDirection === "vertical" ? "translateY" : "translateX") + "( -" +
                transformOffset + "px)");
            }
            function endTransform() {
                image.css("transform", (imageDirection === "vertical" ? "translateY" : "translateX") + "(0px)");
            }
    
            function setTransform() {

                if(wrapperForCheck.classList.contains("device-iphone11")){
                    if (imageDirection === "vertical") {
                        transformOffset = image.height() - imageElement.height();
                    } else {
                        transformOffset = image.width() - 2.5 * imageElement.width();
                    }
                }else{
                    if (imageDirection === "vertical") {
                        transformOffset = image.height() - imageElement.height();
                    } else {
                        transformOffset = image.width() - 2 * imageElement.width();
                    }  
                }
            }
            if (imagedataElement["trigger"] === "scroll") {
                imageElement.addClass("eae-container-scroll");
                if (imageDirection === "vertical") {
                    imageVertical.addClass("scroll-vertical");
                }
            }
            else {
                if (imageReverse === "yes") {
                    imageElement.imagesLoaded(function () {
                        imageElement.addClass("eae_scroll");
                        setTransform();
                        startTransform();
                    });
                }
                if (imageDirection === "vertical") {
                    imageVertical.removeClass("eae-image-scroll-ver");
                }
              
                imageElement.mouseenter(function () {
                    setTransform();
                    imageReverse === "yes" ? endTransform() : startTransform();
                });
                imageElement.mouseleave(function () {
 
                    imageReverse === "yes" ? startTransform() : endTransform();
                });
            }
        }
        
        let lottieWrapper  =  element.querySelectorAll('.device-img-content');
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

        if(oriWrapper != undefined || oriWrapper != null){
            oriWrapper.addEventListener('click', function(e){ 
                    oriFunction(oriWrapper,deviceWrapper); 
            })
            function oriFunction(ele,deviceWrapper){
            ele.classList.toggle("rotate");
            deviceWrapper.classList.toggle("landscape");
            }
        }
        
        if(wrapper != null){
            let videoType = wrapper.getAttribute('data-video-type');
            let is_autoplay = wrapper.getAttribute('data-autoplay');

            wrapper.addEventListener('click', function(e){
               
                if(!elementorFrontend.isEditMode()){
                    labnolIframe(this);
                }    
            })
            
            if(is_autoplay == '1'){
                if(!elementorFrontend.isEditMode()){
                    const observerOptions = {
                        root: null, // Observe relative to the viewport
                        rootMargin: '0px 0px -300px 0px', // Margin around the root
                      };
                
                      const observerCallback = (entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const element = entry.target;
                            labnolIframe(element, 'autoplay');
                            }
                        });
                      };
                      const observer = new IntersectionObserver(observerCallback, observerOptions);
                      observer.observe(wrapper);
                }    
            }
        }

        function labnolIframe(ele, demo = 'null') {
            let videoType = ele.getAttribute('data-video-type');
            let videoPlayerEle = ele.querySelector('.eae-device-video-play');        
            let src = '';
            let videoHtml = '';
            let textIconWrapper = element.querySelector('.device-text'); 
            textIconWrapper.style.visibility =  "hidden";

            if(videoType != 'hosted'){
                src = videoPlayerEle.getAttribute('data-src');
            }

            if(videoType == 'hosted'){
                videoHtml = ele.getAttribute('data-hosted-html');
            }

            if(videoType != 'hosted'){
                var iframe = document.createElement('iframe');
                    // iframe add class custom
                    iframe.classList.add('eae-video-iframe');
                    iframe.setAttribute('src', src);
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allowfullscreen', '1');
                    iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
                    videoPlayerEle.innerHTML = '';
                    if(!ele.classList.contains('.eae-sticky-apply') && ele.querySelector('.eae-video-display-details') != null){
                        ele.querySelector('.eae-video-display-details').style.display = 'none';
                    }
                    videoPlayerEle.append(iframe);
            }else{
                if(videoType == 'hosted'){
                    if(videoPlayerEle.querySelector('.eae-hosted-video') == null){

                     

                        videoPlayerEle.innerHTML = '';
                        let hostedVideoHtml = JSON.parse(videoHtml);
                        videoPlayerEle.innerHTML += hostedVideoHtml;
                        let hostedVideo = videoPlayerEle.querySelector('video');
                        hostedVideo.setAttribute('autoplay', 'autoplay');

                        //hostedVideoIframe.style.margin = '0px';
                        videoPlayerEle.querySelector('video').style.width = '100%';
                        videoPlayerEle.querySelector('video').style.height = '100%';
                    }
                 
                }
            }

        }
	}
	

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-devices.default",
			EAEDevices
		);
	});
})(jQuery);

/***/ }),

/***/ 305:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Y: () => (/* binding */ SwiperBase)
/* harmony export */ });
/* unused harmony export isEditMode */


const isEditMode = () => {
    return false;
}

class SwiperBase{

    constructor(data, wid, scope = null) {

        let swiper = [];
        let swiperContainer = '.elementor-element-' + wid + ' .eae-swiper-container';
		let active_breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
		var wclass = '.elementor-element-' + wid;
        if (scope !== null) {
            wid = scope.data('id');
            const slideId = scope.find('.eae-swiper-container').data('eae-slider-id');
            swiperContainer = '.elementor-element-' + wid + ' .eae-swiper-container[data-eae-slider-id="' + slideId + '"]';
			wclass = '.elementor-element-' + wid + ' .eae-slider-id-' + slideId;
        }

        if (typeof data === "undefined") {
            return false;
        }

		swiper = {
			direction: data.direction,
			speed: data.speed,
			autoHeight: data.autoHeight,
			autoplay: data.autoplay,
			grid: data.grid,
			effect: data.effect,
			loop: data.loop,
			zoom: data.zoom,
			wrapperClass: 'eae-swiper-wrapper',
			slideClass: 'eae-swiper-slide',
			observer: true,
			observeParents: true,
		}

		if(data.hasOwnProperty('pause_on_interaction')){
			swiper['autoplay']['disableOnInteraction'] = true;
			swiper['autoplay']['pauseOnMouseEnter'] = true;	
		}
		
		const res_props = {
			'slidesPerView' : 'slidesPerView',
			'slidesPerGroup' : 'slidesPerGroup',
			'spaceBetween' : 'spaceBetween'
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
				
				const value = parseInt(data.breakpoints_value[key]); 
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

		// BreakPoints
		const bp = eae.breakpoints;

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
		
		
			swiper['on'] = {
				resize: function () {
					if(data.autoplay != false){
						this.autoplay.start();
					}
				},
			};
		
		
		
		//swiper['init'] = false;
		const asyncSwiper = elementorFrontend.utils.swiper;
		new asyncSwiper(jQuery(swiperContainer), swiper).then((newSwiperInstance) => {
			const mswiper = newSwiperInstance;
			const pause_on_hover = data.pause_on_hover;
			if(data.loop == 'yes'){
				this.after_swiper_load_func(mswiper , wid);
			}
			if (pause_on_hover == 'yes') {
				
					this.pause_on_hover_func(mswiper, pause_on_hover, wid , data);
			}
		});
	
        jQuery('.elementor-element-' + wid + ' .ae-swiper-container').css('visibility', 'visible');
        
    }


    pause_on_hover_func(mswiper, pause_on_hover, wid , data = '') {
        jQuery('.elementor-element-' + wid + ' .eae-swiper-container').hover(function () {
            mswiper.autoplay.stop();
        }, function () {
			if(!data.hasOwnProperty('pause_on_interaction') && data.pause_on_interaction != 'yes'){
				mswiper.autoplay.start();
			}
        });
    }
	after_swiper_load_func(mswiper , wid = '') {		
        if (mswiper.length > 0) {
            mswiper.forEach(function (slider) {
                // slider.on('slideChangeTransitionStart', function () {
                //  slider.$wrapperEl.find('.swiper-slide-duplicate').each(function (element) {
                //      let videoWrapper = element.querySelector('.eae-vg-element');
                //      videoWrapper.addEventListener('click', function(e){
                //          videoWrapper.classList.remove('eae-vg-image-overlay');
                //          let video_type = videoWrapper.getAttribute('data-video-url');
                //          let url = videoWrapper.getAttribute('data-video-url');
                //          videoWrapper.innerHTML = '';
                //          var iframe = document.createElement('iframe');
                //          iframe.classList.add('eae-vg-video-iframe');
                //          iframe.setAttribute('src', url);
                //          iframe.setAttribute('frameborder', '0');
                //          iframe.setAttribute('allowfullscreen', '1');
                //          iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
                //          videoWrapper.append(iframe);
                //      });
                //      // get settings
                //      // elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
                //  });
                // });
                // slider.init();
            });
        } else {
            mswiper.on('slideChangeTransitionStart', function () {
                mswiper.$wrapperEl.find('.swiper-slide-duplicate').each(function (element) {
                    const parentDiv = element.closest('.eae-vg-video-container');
					
                    if(parentDiv !== null){
                        let videoWrapper = element.querySelector('.eae-vg-element');	
                        videoWrapper.addEventListener('click', function(e){
							let element = videoWrapper
                            element.classList.remove('eae-vg-image-overlay');
							let video_type = element.getAttribute('data-video-url');
							let video_t = element.getAttribute('data-video-type');
							if(video_t != 'hosted'){
								let url = element.getAttribute('data-video-url');
								element.innerHTML = '';
								var iframe = document.createElement('iframe');
								iframe.classList.add('eae-vg-video-iframe');
								iframe.setAttribute('src', url);
								iframe.setAttribute('frameborder', '0');
								iframe.setAttribute('allowfullscreen', '1');
								iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
								element.append(iframe);
							}else{
								if(element.querySelector('.eae-hosted-video') == null){
								let videoHtml = element.getAttribute('data-hosted-html');
									element.innerHTML = '';
									let hostedVideoHtml = JSON.parse(videoHtml);
									element.innerHTML += hostedVideoHtml;
									let hostedVideo = element.querySelector('video');
									hostedVideo.setAttribute('autoplay', 'autoplay');
									if(element.hasAttribute('data-video-downaload')){
										hostedVideo.setAttribute('controlslist', 'nodownload');
									}
									if(element.hasAttribute('data-controls')){
										hostedVideo.setAttribute('controls', '');
									}   
								}    
							}
                        });
                    }
					


					// woo products quick view
					const popTriggerButtons = element.querySelectorAll(".open-popup-link");
					popTriggerButtons.forEach(wrapper => jQuery(wrapper).eaePopup({
                        type:'inline',
                        midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
                        mainClass:"eae-wp-modal-box eae-wp-"+wid,
                        callbacks:{
                            open: function(){
                                jQuery(window).trigger("resize"); 
                            },
                            
                          }
                    }));

					// Testimonial Slider 
						
                    const testimonialSlider = element.closest('.eae-testimonial-wrapper');
					if(testimonialSlider !== null){
						const breakpoints = parseInt(testimonialSlider.getAttribute('data-stacked'));
						const imgElements = testimonialSlider.querySelectorAll('.eae-additional-image.eae-preset-2');

						if(testimonialSlider !== null){
							window.addEventListener("resize", function(){
								const currentWindowWidth = this.window.innerWidth;
								if(currentWindowWidth <= breakpoints) {
									imgElements.forEach(img => {
										img.style.display = 'none';
									});
								}
								else {
									imgElements.forEach(img => {
										img.style.display = 'flex';
									});
								}
							})
						}
					}
					
                
					mswiper.init();
				});					
            });
            mswiper.init();
        }
    }
}


/***/ }),

/***/ 322:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

(function ($) {

    const EAEVideoGallery = function($scope){
        const eId = $scope.attr('data-id');
        const element_container = document.querySelector('.elementor-element-' + eId);
        const element = element_container.querySelector(".eae-vg-video-container");
        if(element !==  null){
            const wrapper = element.querySelectorAll('.eae-vg-element-wrapper');    
        
            //thumb   
            const video_types = element.querySelectorAll(".eae-vg-element-wrapper");

            if(!elementorFrontend.isEditMode() && !element.classList.contains('lightbox')){
                get_video(element);
            }

            //lottie
            get_lottie(wrapper);

            //Filter
            if($scope.find('.eae-vg-wrapper').hasClass('eae-vg-filter')){
                const filterButtonContainer = element_container.querySelector('.eae-vg-filter-button-container');
                const tabContaners = filterButtonContainer.querySelectorAll('.eae-vg-filter-tab');
                if(tabContaners.length > 1){
                    const filterButton = filterButtonContainer.querySelectorAll('.eae-filter-button');
                    const fileterElement = element.querySelectorAll('.eae-vg-element-wrapper');
                    const filterHiddenButton = filterButtonContainer.querySelector('.eae-vg-dropdown-tab');
                    let activeButton = filterButtonContainer.querySelector('.eae-vg-active-button');
                    const filterHiddenButtons = filterHiddenButton.querySelectorAll('.eae-vg-filters-item');


                    filterHiddenButtons.forEach(function(button){
                        let hiddenButton = button.querySelector('.eae-filter-button');
                        if(activeButton.getAttribute('data-filter') == hiddenButton.getAttribute('data-filter')){
                            hiddenButton.classList.add('eae-vg-active-button');
                        }
                    });
                    filterButton.forEach(function(element,index){
                        if(element.classList.contains('eae-vg-active-button')){
                            fileterElement.forEach(function(element_class){
                                if(element.getAttribute('data-filter') == 'all'){
                                    element_class.classList.add('eae-vg-active');
                                }else{
                                    if(element_class.classList.contains(element.getAttribute('data-filter'))){
                                        element_class.classList.add('eae-vg-active');
                                        
                                    }else{
                                        element_class.classList.add("eae-vg-filter-hidden");
                                    }
                                }
                            });
                        }
                        
                        element.addEventListener('click', function(e){
                            fileterElement.forEach(function(element){
                                if(element.classList.contains('eae-vg-filter-hidden')){
                                    element.classList.remove('eae-vg-filter-hidden');
                                }
                                if(element.classList.contains('eae-vg-active')){
                                    element.classList.remove("eae-vg-active");
                                }
                            });
                            fileterElement.forEach(function(element_class){
                                if(element.getAttribute('data-filter') == 'all'){
                                    if(!element_class.classList.contains('eae-vg-active')){
                                        element_class.classList.add('transit-in');
                                        setTimeout(showImage,500,element_class);
                                    }
                                }else{
                                    if(element_class.classList.contains('eae-vg-active')){
                                        element_class.classList.remove("eae-vg-active");
                                    }
                                    if(element_class.classList.contains(element.getAttribute('data-filter'))){
                                        element_class.classList.add('transit-in');
                                        setTimeout(showImage,500,element_class);
                                        // showImage(element_class);
                                    }
                                    hideElement(element.getAttribute('data-filter'),fileterElement);
                                }
                            });
                            
                            filterButton.forEach(function(element){
                                if(element.classList.contains('eae-vg-active-button')){
                                    element.classList.remove('eae-vg-active-button');
                                }
                            });
                            element.classList.add('eae-vg-active-button');
                            const filterHiddenButtons = filterHiddenButton.querySelectorAll('.eae-vg-filters-item');
                            filterHiddenButtons.forEach(function(button){
                                let hiddenButton = button.querySelector('.eae-filter-button');
                                if(element.getAttribute('data-filter') == hiddenButton.getAttribute('data-filter')){
                                    hiddenButton.classList.add('eae-vg-active-button');
                                }
                            });
                            getDropdownButtonName($scope);
                        });
                        
                    });

                    const resizeElement = element_container.querySelector('.eae-vg-wrapper');
                    const resizeElementData = resizeElement.querySelectorAll('.eae-vg-filter-tab');
                    let dropdown = '';
                    window.addEventListener("resize", function(){
                        let resizeValue = resizeElement.getAttribute('data-stacked');
                        let widnowWidth = this.window.innerWidth;
                        if(widnowWidth <= resizeValue ){
                            resizeElementData.forEach(function(element){
                                let data = [];
                                if(element.classList.contains('eae-vg-dropdown-tab')){
                                    element.classList.add('enable-vg-dropdown-layout');
                                    element.classList.remove('disable-vg-dropdown-layout');
                                }else{
                                    element.classList.add('disable-vg-dropdown-layout');
                                }
                            });
                        }else{
                            resizeElementData.forEach(function(element){
                                if(element.classList.contains('eae-vg-dropdown-tab')){
                                    element.classList.add('disable-vg-dropdown-layout');
                                    element.classList.remove('enable-vg-dropdown-layout');
                                }else{
                                    element.classList.remove('disable-vg-dropdown-layout');
                                    let filterButtonContainer = element.querySelector('.eae-vg-collapse');
                                    if(filterButtonContainer != null){
                                        let filterButtons = filterButtonContainer.querySelectorAll('.eae-vg-filters-item');
                                        filterButtons.forEach(function(element){
                                            let button = element.querySelector('.eae-vg-active-button');
                                            if(button != null){
                                                let termTilte = button.textContent;
                                                filterButtonContainer.querySelector('.eae-vg-dropdown-filter-text').textContent = termTilte; 
                                                if(!filterButtonContainer.classList.contains('eae-vg-active-button')){
                                                    filterButtonContainer.classList.add('eae-vg-active-button');
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }

                        const wraopper = resizeElement.querySelector('.eae-vg-dropdown-tab');
                        let dropdownActiveButton = wraopper.querySelector('.eae-vg-filter-dropdown');
                        isListItemActive = wraopper.querySelectorAll('.eae-vg-filters-item');
                        isListItemActive.forEach(function(element){
                            let data = element.querySelector('.eae-vg-active-button');
                            if(data != null){
                                let termTilte = data.textContent;
                                dropdown.querySelector('.eae-vg-dropdown-filter-text').textContent = termTilte; 
                                dropdownActiveButton.classList.add('eae-vg-active-button');
                            }
                        });
                    });  
                    resizeElementData.forEach(function(element){
                        if(element.classList.contains('eae-vg-dropdown-tab')){
                            dropdown = element.querySelector('.eae-vg-filter-dropdown');
                        }
                    });
                    dropdown.classList.remove('eae-vg-visible');
                    let collapseableList     = dropdown.querySelector('.eae-vg-collaps-item-list');
                    let isListItemActive = collapseableList.querySelectorAll('.eae-vg-filters-item');
                    const handleClick = (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        dropdown.classList.toggle('eae-vg-visible');
                        isListItemActive.forEach(function(element){
                            let data = '';
                            data = element.querySelector('.eae-vg-active-button');
                            if(data != null){
                                let termTilte = data.textContent;
                                dropdown.querySelector('.eae-vg-dropdown-filter-text').textContent = termTilte;
                                setActiveHiddenButton(data,resizeElementData);
                                dropdown.classList.add('eae-vg-active-button');
                            }
                        });
                    }
                    
                    dropdown.removeEventListener('click', handleClick);
                    dropdown.addEventListener('click', handleClick);
                }
            }

            if (!elementorFrontend.isEditMode() && element.classList.contains('lightbox')) {
                var options = {
                    selector: '.eae-vg-element'
                };
                var lgSettings = JSON.parse(element.getAttribute('data-lg-settings'));
                options = { ...options, ...lgSettings };
                var plugins = {
                    plugins: [
                        lgVideo,
                        lgShare,
                        lgHash,
                        lgFullscreen,
                        lgThumbnail
                    ],
                };


                options = { ...options, ...plugins };

                lightGallery(element, options);
            }


            // DropDown 
            if($scope.find('.eae-vg-wrapper').hasClass('eae-vg-filter')){
                let dropdown = '';
                let data = {};
                dropdown = element_container.querySelector('.eae-vg-collapse');
                data.dropDown = dropdown;
                dropdownFilter(data);
            }

            // Swiper
            if($scope.find('.eae-vg-wrapper').hasClass('eae-swiper-outer-wrapper')){
                const wid = $scope.data('id');
                const outer_wrapper = $scope.find('.eae-swiper-outer-wrapper');
                const swiper_settings = outer_wrapper.data('swiper-settings');
                new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y(swiper_settings, wid);
            }
        }

    }

    function hideImage(element){
        element.classList.add('eae-vg-filter-hidden');
        element.classList.remove('transit-out');
        if(element.classList.contains('eae-vg-active')){
            element.classList.remove('eae-vg-active');
        }
    }

    function showImage(element){
        element.classList.add('eae-vg-active');
        element.classList.remove('transit-in');
        if(element.classList.contains('eae-vg-filter-hidden')){
            element.classList.remove('eae-vg-filter-hidden');
        }
    }

    function getDropdownButtonName($scope){
        const eId = $scope.attr('data-id');
        const element_container = document.querySelector('.elementor-element-' + eId);
        const element = element_container.querySelector(".eae-vg-filter-button-container");
        const wrapper = element.querySelector('.eae-vg-filter-tab');   
        const dropDownContainer = wrapper.querySelector('.eae-vg-filter-dropdown');
        let dropDownElement = '';
        if(dropDownContainer != null){
            dropDownElement = dropDownContainer.querySelector('.eae-vg-active-button');
        }
        if(dropDownElement == null){
            let dropDownButtonText = dropDownContainer.getAttribute('data-button-text');
            dropDownContainer.querySelector('.eae-vg-dropdown-filter-text').textContent = dropDownButtonText;
            if(dropDownContainer.classList.contains('eae-vg-active-button')){
                dropDownContainer.classList.remove('eae-vg-active-button');
            }
        }
    }

    function get_lottie(wrapper){
        wrapper.forEach(function(element , index){
            const wrapper = element.querySelector('.eae-vg-element');
            if(wrapper !== null){
                let isLottiePanle = wrapper.querySelector('.eae-lottie');
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
            }
        });
    }

    function setActiveHiddenButton(data,resizeElementData){
        resizeElementData.forEach(function(elementData){
            if(elementData.classList.contains('disable-vg-dropdown-layout')){
                let hiddenButton = elementData.querySelectorAll('.eae-filter-button');
                hiddenButton.forEach(function(elementData){
                    if(data.getAttribute('data-filter') == elementData.getAttribute('data-filter')){
                        elementData.classList.add('eae-vg-active-button');
                    } 
                });
            }
        });
    }

    function dropdownFilter(options = {}){
        let dropDown = options.dropDown;  
        if(dropDown != null){
            
            dropDown.classList.remove('eae-vg-visible');
            let flag = 0;
            let collapseableList     = dropDown.querySelector('.eae-vg-collaps-item-list');
            let isListItemActive = collapseableList.querySelectorAll('.eae-vg-filters-item');
            const handleClick = (e) => {
                e.stopPropagation();
                e.preventDefault();
                dropDown.classList.toggle('eae-vg-visible');
                isListItemActive.forEach(function(element){
                    let data = '';
                    data = element.querySelector('.eae-vg-active-button');
                    if(data != null){
                        let termTilte = data.textContent;
                        dropDown.querySelector('.eae-vg-dropdown-filter-text').textContent = termTilte;
                        dropDown.classList.add('eae-vg-active-button');
                    }
                
                });
            }
            dropDown.removeEventListener('click', handleClick);
            dropDown.addEventListener('click', handleClick);  
        }
    }

    // function get_droupDown(element){
    //     const dropDownButton = element.querySelector('.eae-vg-filter-dropdown-button');
    //     let dropDownElement = element.querySelector('.eae-vg-collaps-item-list');
    //     dropDownElement.classList.remove('eae-vg-visible');
    //     dropDownButton.addEventListener('click',function(e){
    //         dropDownElement.classList.add('eae-vg-visible');
    //     });  
    // }

    function get_video(element){
        // console.log(element);
        // return;
        const wrapper = element.querySelectorAll('.eae-vg-element');    
        wrapper.forEach(function(element , index){
            element.addEventListener('click', function(e){
                element.classList.remove('eae-vg-image-overlay');
                let video_type = element.getAttribute('data-video-url');
                let video_t = element.getAttribute('data-video-type');
                if(video_t != 'hosted'){
                    let url = element.getAttribute('data-video-url');
                    element.innerHTML = '';
                    var iframe = document.createElement('iframe');
                    iframe.classList.add('eae-vg-video-iframe');
                    iframe.setAttribute('src', url);
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allowfullscreen', '1');
                    iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
                    element.append(iframe);
                }else{
                    if(element.querySelector('.eae-hosted-video') == null){
                       let videoHtml = element.getAttribute('data-hosted-html');
                        element.innerHTML = '';
                        let hostedVideoHtml = JSON.parse(videoHtml);
                        element.innerHTML += hostedVideoHtml;
                        let hostedVideo = element.querySelector('video');
                        hostedVideo.setAttribute('autoplay', 'autoplay');
                        if(element.hasAttribute('data-video-downaload')){
                            hostedVideo.setAttribute('controlslist', 'nodownload');
                        }
                        if(element.hasAttribute('data-controls')){
                            hostedVideo.setAttribute('controls', '');
                        }   
                    }    
                }
            });
        });
    }

    function hideElement(elementAtt,fileterElement){
        fileterElement.forEach(function(element_class){
            if(!element_class.classList.contains(elementAtt)){
                element_class.classList.add("transit-out");
                // setTimeout(hideImage,400,element_class);
                hideImage(element_class);
            }
        });

    }

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/eae-video-gallery.default",
            EAEVideoGallery
        );
    });
})(jQuery);

/***/ }),

/***/ 327:
/***/ (() => {

(function ($) {
    
    const EAEAdvancedList = function ($scope){
        const eId = $scope.attr('data-id');
        const element = document.querySelector('.elementor-element-' + eId);
        const wrapper =element.querySelector('.eae-list-wrapper');
        
        let flexList = wrapper.querySelectorAll('.eae-list-item');
        
        flexList.forEach(flexList => {

            isLottiePanle = flexList.querySelector('.eae-lottie');
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
        });
    }

    
    
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/eae-advanced-list.default",
            EAEAdvancedList
        );
    });
})(jQuery);

/***/ }),

/***/ 340:
/***/ (() => {

(function ($) {
	const EAEBusinessHours = function ($scope) {
        const businessHours = document.querySelectorAll(".wta-eae-business-heading-wrapper");
        const eId = $scope.attr('data-id');
        const element = document.querySelector('.elementor-element-' + eId);
        const wrapper = element.querySelector('.wts-eae-business-days');    
        let titleIcon = element.querySelector('.eae-tile-icon.eae-lottie-animation');
        let LottieData = wrapper.querySelectorAll('.eae-business-weekdays-wrapper');
        
        LottieData.forEach(data => {
            isLottiePanle = data.querySelector('.eae-lottie');
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

        if (titleIcon != null) {
			let lottie_data = JSON.parse(titleIcon.getAttribute('data-lottie-settings'));
			let eae_animation = lottie.loadAnimation({
				container: titleIcon,
				path: lottie_data.url,
				renderer: "svg",
				loop: lottie_data.loop,
			});

			if (lottie_data.reverse == true) {
				eae_animation.setDirection(-1);
			}
		}


        businessHours.forEach(data => {
           
        const eId = $scope.attr('data-id');
        const element = document.querySelector('.elementor-element-' + eId);
       
       
        
    
       
        
        const timezone = data.getAttribute('data-timezone');
        let timeFormat = data.getAttribute('data-format');
        timeFormat = timeFormat == "true" ? true : false;
        let settings = JSON.parse(data.getAttribute('data-settings'));

		const options = {
            hour : "numeric",
            minute : "numeric",
            second: "numeric",
            hour12: timeFormat, 
        }
		if(settings.businessIndicator == 'yes')
        {
            calcTime(timezone);
            setInterval(calcTime, 1000,timezone); // Repeat myFunction every 1 mintb b bb
            get_Time_Left();
            setInterval(get_Time_Left,1000);
        }

		function calcTime(timezone){
			
            const date = new Date();
            let offset;
            let regexp = /^(\+|\-)\d{1,2}:\d{2}$/;
            if(regexp.test(timezone)){
                const [hours,mints] = timezone.split(":").map(Number);
                offset = ((hours * 60) + mints)*60;
            }
            else{
                const tz= Intl.DateTimeFormat(undefined,{timeZone : timezone}).resolvedOptions().timeZone;
                if(tz === timezone){ 
                    let dt = new Date();
                    dt = dt.toLocaleString("en-US",{timeZone: timezone});
                    date.setTime(Date.parse(dt));
                    var datedisplay = date;
                    glbCurrenttime = date.getTime();
                    const biTimehtml = data.querySelector('.eae-indicator-time');
                    biTimehtml.innerHTML= datedisplay.toLocaleString('en-US',options);
                }
            }
            if(offset >= 0 || offset <= 0){
                const utcTime = date.getTime() + (date.getTimezoneOffset() * 60000);
                date.setTime(utcTime + (offset * 1000));
                var datedisplay = date;
                let dateMatch = new Date();
                const matchTime = dateMatch.getTime() + (offset * 1000);
                dateMatch.setTime(matchTime);
                // glbCurrenttime = Math.floor(dateMatch.getTime()/1000);
                glbCurrenttime = Math.ceil(dateMatch.getTime()/1000);
            }
            
            const biTimehtml = data.querySelector('.eae-indicator-time');
           
            if(biTimehtml != undefined){

                biTimehtml.innerHTML = datedisplay.toLocaleString('en-US',options);
            }
        } 
		function get_Time_Left(){
            openWrn = data.querySelector('.eae-bh-bi-open-wmsg');
            closeWrn = data.querySelector('.eae-bh-bi-close-wmsg');
            //get current day wrapper
            const cday_wrap = data.querySelector('.currentday');
            
            if(cday_wrap != undefined)
            {
                    //getting all slots in current day wrap
                const slots = cday_wrap.querySelectorAll('.bultr-bh-label-wrap');
            
                //LABEL checking if current time is btw slot if true then open else close
                const slot = Object.values(slots);
                for (const ele of slot) {
					
                    const Opentime  = parseInt(ele.getAttribute('data-open'));
                    const Closetime = parseInt(ele.getAttribute('data-close'));
                    if(settings.indctLabel == 'yes'){
                        incicatorLabel = data.querySelector('.bultr-labelss');
                        if(incicatorLabel){
							
                            if(glbCurrenttime > Opentime && glbCurrenttime < Closetime){

                                incicatorLabel.innerHTML = settings.openLableTxt;
                                incicatorLabel.classList.add('bultr-lbl-open');
                                incicatorLabel.classList.remove('bultr-lbl-close'); 
                                break;
                            }
                            else{
                                incicatorLabel.innerHTML = settings.closeLabelTxt;
                                incicatorLabel.classList.add('bultr-lbl-close');
                                incicatorLabel.classList.remove('bultr-lbl-open');

                            }
                        }
                    }
                }
                //WARNING MASSAGE
                
                for(const ele of slot){
                   
                    const Opentime  = parseInt(ele.getAttribute('data-open'));
                    const Closetime = parseInt(ele.getAttribute('data-close'));
                    openWrn = data.querySelector('.eae-bh-bi-open-wmsg');
                    closeWrn = data.querySelector('.eae-bh-bi-close-wmsg');
                    
                    
                    //opening warning
                
                    if(Opentime > glbCurrenttime){
        
                        openmints = Math.ceil((Opentime - glbCurrenttime)/60);
                        
                        // openmintsss = Math.floor((Opentime - glbCurrenttime)/60);
                        if(openmints <= parseInt(settings.openMints)){
                            
                            if(settings.openWrnMsg == 'yes'){
                                if(openWrn){
                                    openWrn.innerHTML = settings.openWrnMsgTxt + " " + openmints + " Minutes";
                                }
                                else{
                                        openWrn = document.createElement('div');
                                        openWrn.setAttribute('class', 'bultr-bh-bi-open-wmsg');
                                        // incicatorLeft.appendChild(openWrn);
                                        openWrn.innerHTML = settings.openWrnMsgTxt + " " + openmints + " Minutes";
                                }
                            }        
                        }
                    break;
                    }
                    else{
                        if(openWrn){
                            openWrn.innerHTML = "";
                        }
                    }
                    //closing warning
                    if(glbCurrenttime < Closetime || glbCurrenttime > Opentime){
                        
                        closemints = Math.ceil((Closetime - glbCurrenttime)/60);
                        //  closemintsss = Math.floor((Closetime - glbCurrenttime)/60);
                        if(closemints <= parseInt(settings.closeMints)){
                            if(closemints > 0){
                                if(settings.closeWrnMsg == 'yes'){
                                    if(closeWrn){
                                        closeWrn.innerHTML = settings.closeWrnMsgText + " " + closemints + " Minutes";
                                        
                                    }
                                    else{
                                        closeWrn = document.createElement('div');
                                        closeWrn.setAttribute('class', 'bultr-bh-bi-close-wmsg');
                                        // incicatorLeft.appendChild(closeWrn);
                                        closeWrn.innerHTML = settings.closeWrnMsgText + " " + closemints + " Minutes";
                                    }
                                    closeWrn.innerHTML = settings.closeWrnMsgText + " " + closemints + " Minutes";
                                } 
                            }
                            else{
                                if(closeWrn){
                                    closeWrn.innerHTML = "";
                                }
                            } 
                        }
                    }
                }
            }   
        }   
    })
}

    $(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-business-hours.default",
			EAEBusinessHours
		);
	});

})(jQuery);

/***/ }),

/***/ 361:
/***/ (() => {


(function ($){

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ImageHotspotHandler;
        ImageHotspotHandler = ModuleHandler.extend({
            getDefaultElements: function getDefaultElements() {
				
                const eId = this.$element.data('id'); 
                const element = document.querySelector('.elementor-element-' + eId);
                const wrapper = element.querySelector('.eae-ih-wrapper');
                return{
                    eid: eId,
                    element: element,
                    wrapper: wrapper,
                    settings: this.getElementSettings(),
                }
			},
            onInit: function onInit(){
                const { wrapper , settings} = this.getDefaultElements();
                const markers = wrapper.querySelectorAll('.eae-ih-marker');
                const tooltips = wrapper.querySelectorAll('.eae-ih-tooltip');
                const animation = settings.tooltip_animation_type;
                const preview = wrapper.querySelectorAll(".eae-ih-tooltip-show");  
                const rep_preview = wrapper.querySelectorAll(".eae-ih-rep-tooltip-show");

                this.getLottie();
                const tippyInstance = [];
                
                markers.forEach(function (marker, index){
                    const tooltipContent = tooltips[index].innerHTML;
                    // if(preview.length > 0){
                    //     control_settings['trigger'] = 'manual'; 
                    //    }
                    //    if(rep_preview.length > 0){
                    //     control_settings['trigger'] = 'manual';    
                    // }

                    tippyInstance[index] = tippy(marker, {
                        content: tooltipContent,
                        appendTo: 'parent',
                        placement: 'auto',  
                        allowHTML: true,
                        hideOnClick: false,
                        arrow: true, 
                        trigger: settings.trigger,
                        maxWidth: 'none',
                        onCreate: function(instance){                           
                            instance.popper.classList.add('eae-ih-add-tooltip');
                            instance.popper.childNodes.forEach(function(childNode) {
                                if (childNode.classList) {
                                  childNode.classList.add('animated');
                                  childNode.classList.add('eae-ih-tooltip-animtion');
                                  childNode.classList.add(animation);
                                }
                              });
                            // Previous Button
                            const prevButton = instance.popper.querySelector('.eae-ih-tooltip-prev');
                            if(prevButton != null){
                                prevButton.addEventListener('click', function(){
                                    const tooltipId = this.getAttribute('data-tooltip-id');
                                    const tippyIndex = tooltipId - 1;
                                    tippyInstance[tippyIndex].hide();
                                    tippyInstance[tippyIndex - 1].show();
                                });
                            }

                            // Next Button
                            const nextButton = instance.popper.querySelector('.eae-ih-tooltip-next');
                            if(nextButton != null){
                                nextButton.addEventListener('click', function(){
                                    const tooltipId = this.getAttribute('data-tooltip-id');
                                    const tippyIndex = tooltipId - 1;
                                    tippyInstance[tippyIndex].hide();
                                    tippyInstance[tippyIndex + 1].show();
                                });
                            }
                
                            //End Tour Button
                            const endButton = instance.popper.querySelector('.eae-ih-end-tour-btn');
                            if(endButton != null){
                                endButton.addEventListener('click', function(){
                                    const tooltipId = this.getAttribute('data-tooltip-id');
                                    const tippyIndex = tooltipId - 1;
                                    tippyInstance[tippyIndex].hide();
                                    if (preview.length > 0 && elementorFrontend.isEditMode()) {
                                        tippyInstance[0].show();
                                    }
                                    if (marker.classList.contains("eae-ih-rep-tooltip-show")) {
                                    tippyInstance[index].show();
                                    }
                                });
                            }
                
                            //Close Icon
                            const closeButton = instance.popper.querySelector('.eae-ih-tooltip-close-icon');
                            if(closeButton != null){
                                closeButton.addEventListener('click', function(){
                                    const tooltipId = this.getAttribute('data-tooltip-id');
                                    const tippyIndex = tooltipId - 1;
                                    tippyInstance[tippyIndex].hide();
                                    //preview tooltip
                                    if (preview.length > 0 && elementorFrontend.isEditMode()) {
                                        tippyInstance[0].show();
                                      }
                                      if (marker.classList.contains("eae-ih-rep-tooltip-show")) {
                                        tippyInstance[index].show();
                                      }
                                });
                            }
                            
                            if(index == 0){
                                if(prevButton != null){
                                    prevButton.style.display = 'none';
                                }
                            }
            
                            if(index == markers.length - 1){
                                if(nextButton != null){
                                    nextButton.style.display = 'none';
                                }
                            }
                        },
                        // onShow: function(instance){
                            // let tippyPosition = instance.reference.getBoundingClientRect();
                            // let marker = instance.popper;
                            // console.log('marker',marker);
                            // console.log('tippyPosition',tippyPosition);

                            // marker.style.top = tippyPosition.y + 'px !important';
                            // marker.style.left = tippyPosition.x + 'px !important';
                            // console.log(instance.popper);
                            // setTimeout(function(){
                            //     instance.popperInstance.update();
                            // },0);
                            // instance.popperInstance.update();
                            // window.dispatchEvent(new Event('scroll'));
                        // },
                    });

                    marker.addEventListener('click', function () {
                        tippyInstance.forEach((instance, i) => {
                            if (preview.length > 0 || rep_preview.length > 0) {
                                const tippy_index = this.getAttribute('data-marker') - 1;
                                if(marker.classList.contains('eae-ih-tooltip-show')){
                                    if (tippyInstance[tippy_index]) {
                                        tippyInstance[tippy_index].show();
                                    }
                                }else if(marker.classList.contains('eae-ih-rep-tooltip-show')){
                                    if (tippyInstance[tippy_index]) {
                                        tippyInstance[tippy_index].show();
                                    }
                                }else{
                                    if (tippyInstance[tippy_index]) {
                                        tippyInstance[tippy_index].hide();
                                    }
                                }
                            }
                            
                            //if (rep_preview.length > 0) { 
                            //     const tippy_index = this.getAttribute('data-marker') - 1;
                            //     if(marker.classList.contains('eae-ih-rep-tooltip-show')){
                            //         if (tippyInstance[tippy_index]) {
                            //             tippyInstance[tippy_index].show();
                            //         }
                            //     }else if(marker.classList.contains('eae-ih-tooltip-show')){
                            //         if (tippyInstance[tippy_index]) {
                            //             tippyInstance[tippy_index].show();
                            //         }
                            //     }else{
                            //         if (tippyInstance[tippy_index]) {
                            //             tippyInstance[tippy_index].hide();
                            //         }
                            //     }
                            //}

                            if(preview.length == 0 && rep_preview.length == 0){
                                if (i !== index) {
                                    instance.hide();
                                }
                            }

                            if(!elementorFrontend.isEditMode()){       
                                if (i !== index) {
                                    instance.hide();
                                }
                            }

                        });
                    });

                });

                if (preview.length > 0) {
                    tippyInstance[0].show();
                }

                //tooltip repeat preview
                if (rep_preview.length > 0) {
                    rep_preview.forEach((marker_id) => {
                        const tippy_index = marker_id.getAttribute('data-marker') - 1;
                        if (tippyInstance[tippy_index]) {
                            tippyInstance[tippy_index].show();
                        }
                    });
                }

            },
            getLottie: function getLottie(){
                const { wrapper } = this.getDefaultElements();
                const markers = wrapper.querySelectorAll('.eae-lottie');
                markers.forEach(function (element){
                    if(element != null){
                        let lottie_data = JSON.parse(element.getAttribute('data-lottie-settings'));
                        let eae_animation = lottie.loadAnimation({
                            container: element,
                            appendTo: 'parent',
                            path: lottie_data.url,
                            renderer: "svg",
                            loop: lottie_data.loop,
                        });

                        if (lottie_data.reverse == true) {
                            eae_animation.setDirection(-1);
                        }
                    }
                });
            }
        });
        
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-image-hotspot.default', function ($scope){
            elementorFrontend.elementsHandler.addHandler(ImageHotspotHandler, {
                $element: $scope
            });
        });
    });

})(jQuery);

/***/ }),

/***/ 393:
/***/ (() => {

(function ($) {
    let lastScrollTop = 0;
    let elementPagePosition = 0;
	const EAEVideoBox = function ($scope) {
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
        const wrapper = element.querySelector('.eae-video-outer-wrapper'); 
        elementPagePosition = wrapper.getBoundingClientRect().top + window.scrollY;
        let lastScrollTop = 0;     
        // check is it elementor editor

        if(wrapper != null){
            
            let videoType = wrapper.getAttribute('data-video-type');
            let stickyVideo = '';
            let is_autoplay = '';
            let is_lightbox = '';
            if (wrapper.hasAttribute("data-video-sticky")) {
                stickyVideo = wrapper.getAttribute('data-video-sticky');
            }
            if (wrapper.hasAttribute("data-autoplay")) {
                is_autoplay = wrapper.getAttribute('data-autoplay');
            }
            if (wrapper.hasAttribute("data-lightbox")) {
                is_lightbox = wrapper.getAttribute('data-lightbox');
            }
            var isLottiePanle = element.querySelector('.eae-lottie-animation');
            if(stickyVideo == 'yes'){
                let previewSticky =   wrapper.getAttribute('data-preview-sticky');
                if((elementorFrontend.isEditMode() && previewSticky == 'yes') || !elementorFrontend.isEditMode()){
                    makeStickyVideo(wrapper);
                    document.addEventListener('scroll', () => {
                        if (window.requestAnimationFrame) {
                          window.requestAnimationFrame(() => {
                            makeStickyVideo(wrapper);
                          });
                        } else {
                            makeStickyVideo(wrapper);
                        }
                    });
    
                    const closeButton = wrapper.querySelector('.eae-video-sticky-close');
                    if(closeButton != null){
                        closeButton.addEventListener('click', function(e){
                                e.stopPropagation();
                                wrapper.classList.remove('eae-sticky-apply');
                                wrapper.classList.add('eae-sticky-hide');
                        }); 
                    }
                }                     
            }
            
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
            
    
            if(is_lightbox != 'yes'){
                // if(videoType != 'hosted'){
                wrapper.addEventListener('click', function(e){   
                    if(!elementorFrontend.isEditMode()){
                        labnolIframe(this);
                    }    
                })	
                // }
            }
            
            if(is_autoplay == '1'){
                if(!elementorFrontend.isEditMode()){
                    var eae_video_waypoint = new Waypoint({
                        element: wrapper,
                        handler : function ( direction ) {
                            if(direction == 'down'){
                                labnolIframe(wrapper);
                            }
                        },
                        offset : 'bottom-in-view'
                    });
                }    
            }
    
            if(is_lightbox == 'yes'){
                let galleryId = wrapper.querySelector('.eae-video-wrappper').getAttribute('data-gallery-id');
                if(galleryId == null || galleryId == ''){
                    galleryId = '1';
                }
                let plugins = [
                    lgVideo,
                    lgHash,
                    // lgAutoplay
                ]
                if(wrapper.querySelector('.eae-video-wrappper').getAttribute('data-share') == 'yes'){
                    plugins.push(lgShare);
                }
                if(wrapper.querySelector('.eae-video-wrappper').getAttribute('data-fullscreen') == 'yes'){
                    plugins.push(lgFullscreen);
                }
                //add youtube video params to lightbox
                // if(wrapper.querySelector('.eae-video-wrappper').getAttribute('data-video-type') == 'youtube'){
                //     plugins.push(lgYoutube);
                // }
                
                let videoObject = {
                    'selector': '.eae-video-play',
                    'download': false,
                    'counter' : false,
                    'galleryId' : galleryId,
                    'autoplayFirstVideo' : true,
                    plugins: plugins,
                    videojs: true,
                    videojsOptions: {
                        muted: true,
                    },
                }
                let youtubeParams = {};
                if(videoType != 'hosted'){
                    videoObject[`${videoType}PlayerParams`] = JSON.parse(wrapper.querySelector('.eae-video-wrappper').getAttribute('data-params'));    
                }else{
                    videoObject['videojsOptions'] = JSON.parse(wrapper.querySelector('.eae-video-wrappper').getAttribute('data-params'))
                }
                if(!elementorFrontend.isEditMode()){
                    lightGallery(wrapper,videoObject);
                }
                // videoObject = { ...videoObject, ...youtubeParams };
                
            }
        }

       
	}

    function makeStickyVideo(ele){
        let elementCurrentPositin = ele.getBoundingClientRect().top;
        let currentScrollPosition = window.scrollY;
        if(window.scrollY + 150 >= elementPagePosition){
            ele.classList.remove('eae-sticky-hide');
            ele.classList.add('eae-sticky-apply');
            if(ele.querySelector('.eae-video-display-details') != null){
                ele.querySelector('.eae-video-display-details').style.display = 'block';
            }
        }else{
            ele.classList.remove('eae-sticky-apply');
            ele.classList.add('eae-sticky-hide');
            if(ele.querySelector('.eae-video-display-details') != null){
                ele.querySelector('.eae-video-display-details').style.display = 'none';
            }
        }
        
        lastScrollTop = currentScrollPosition <= 0 ? 0 : currentScrollPosition;
    }

    function labnolIframe(ele) {
        let videoType = ele.getAttribute('data-video-type');
        let videoPlayerEle = ele.querySelector('.eae-video-play');        
        let src = '';
        let videoHtml = '';
        //let thumImage = videoPlayerEle.querySelector('img , video');
        if(videoType != 'hosted'){
            src = videoPlayerEle.getAttribute('data-src');
        }
        if(videoType == 'hosted'){
            videoHtml = ele.getAttribute('data-hosted-html');
        }
        
        let isIframe = ele.querySelector('iframe');
        if(videoType != 'hosted'){
            var iframe = document.createElement('iframe');
                // iframe add class custom
                iframe.classList.add('eae-video-iframe');
                iframe.setAttribute('src', src);
                iframe.setAttribute('frameborder', '0');
                iframe.setAttribute('allowfullscreen', '1');
                iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
                videoPlayerEle.innerHTML = '';
                if(!ele.classList.contains('.eae-sticky-apply') && ele.querySelector('.eae-video-display-details') != null){
                    ele.querySelector('.eae-video-display-details').style.display = 'none';
                }
                videoPlayerEle.append(iframe);
        }else{
            if(videoType == 'hosted'){
                if(videoPlayerEle.querySelector('.eae-hosted-video') == null){
                    videoPlayerEle.innerHTML = '';
                    let hostedVideoHtml = JSON.parse(videoHtml);
                    videoPlayerEle.innerHTML += hostedVideoHtml;
                    let hostedVideo = videoPlayerEle.querySelector('video');
                    hostedVideo.setAttribute('autoplay', 'autoplay');
                    if(videoPlayerEle.hasAttribute('data-video-downaload')){
                        hostedVideo.setAttribute('controlslist', 'nodownload');
                    }
                    if(videoPlayerEle.hasAttribute('data-controls')){
                        hostedVideo.setAttribute('controls', '');
                    }
                    //hostedVideoIframe.style.margin = '0px';
                    videoPlayerEle.querySelector('video').style.width = '100%';
                    videoPlayerEle.querySelector('video').style.height = '100%';
                }
                // else{
                //     //if video is play do pause and vice versa
                //     let hostedVideo = videoPlayerEle.querySelector('video');
                //     if(hostedVideo.paused){
                //         hostedVideo.play();
                //     }else{
                //         hostedVideo.pause();
                //     }
                // }
            }
        }
            
    }

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-video-box.default",
			EAEVideoBox
		);
	});
})(jQuery);

/***/ }),

/***/ 404:
/***/ (() => {

(function ($) {
	const EAEImageAccordion = function ($scope) {
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
		const wrapper = element.querySelector('.eae-img-acc-wrapper');
		const panels = wrapper.getAttribute('data-items');
		wrapper.style.setProperty('--eae-panels', Number(panels) - 1);
		const action = wrapper.getAttribute('data-action');
	// let lottie_data = $(this).data("lottie-settings");

    
	let flexSlides = wrapper.querySelectorAll('.eae-img-panel');
	flexSlides.forEach(flexSlide => {
		if (action == 'hover') {
			flexSlide.addEventListener('mousemove', function (e) {
				if (this.classList.contains('active')) {
					return;
				} else {
					removeActiveClass(wrapper);
					flexSlide.classList.add('active');
				}
				checkAnyActiveSlide(wrapper);
			})
			flexSlide.addEventListener('mouseleave', function (e) {
				flexSlide.classList.remove('active');
				checkAnyActiveSlide(wrapper);
			})
		} else {
			flexSlide.addEventListener('click', function (e) {
				if (this.classList.contains('active')) {
					return;
				} else {
					removeActiveClass(wrapper);
					flexSlide.classList.add('active');
				}
			})
		}

		isLottiePanle = flexSlide.querySelector('.eae-lottie');
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
	});

		window.addEventListener("resize", function(){
			let resizeValue = wrapper.getAttribute('data-stacked');
			let widnowWidth = this.window.innerWidth;
			if(widnowWidth <= resizeValue ){
				wrapper.classList.add('enable-stacked');
			}else{
				wrapper.classList.remove('enable-stacked');
			}
		});
	}
	function removeActiveClass(imageAccordion){
		let flexSlides = imageAccordion.querySelectorAll('.eae-img-panel');
		flexSlides.forEach(flexSlide => {
			flexSlide.classList.remove('active');
		})
	}


	function checkAnyActiveSlide(imageAccordion){
		let defaultActivePanel = imageAccordion.getAttribute('data-defult-panel');
		let flexSlidesActive = imageAccordion.querySelectorAll('.eae-img-panel.active');
		
		if(flexSlidesActive.length > 0){
			return
		}else{
			let flexSlides = imageAccordion.querySelectorAll('.eae-img-panel');
			flexSlides[defaultActivePanel -1].classList.add('active');
		}
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-image-accordion.default",
			EAEImageAccordion
		);
	});
})(jQuery);

/***/ }),

/***/ 482:
/***/ (() => {

(function ($) {
	const EAEFAQ = function ($scope) {
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
		let settings = $scope.find('.eae-faq-wrapper').data('settings');
		let faq_layout = settings.faq_layout;
		if (faq_layout === 'accordion') {
			let faq_trigger_action = settings.faq_trigger_action;
			let faq_accordion_transition_speed = settings.faq_accordion_transition_speed;
			let faq_accordion_toggle = settings.faq_accordion_toggle;
			let eae_faq_answer = $scope.find('.eae-faq-item-wrapper > .eae-faq-answer');
			$scope.find('.eae-faq-question').on(`${faq_trigger_action}`,
				function (e) {
					e.preventDefault();
					$this = $(this);
					if (faq_accordion_toggle === 'yes') {
						if ($this.hasClass('eae-faq-active')) {
							$this.removeClass('eae-faq-active');
							$this.attr('aria-expanded', 'false');
						}
						else {
							$this.addClass('eae-faq-active');
							$this.attr('aria-expanded', 'true');
						}
						$this.next('.eae-faq-answer').slideToggle(faq_accordion_transition_speed, 'swing');
					} else {
						if ($this.hasClass('eae-faq-active')) {
							if (faq_trigger_action === 'click') {
								return false;
							}
							$this.removeClass('eae-faq-active');
							$this.next('.eae-faq-answer').slideUp(faq_accordion_transition_speed, 'swing',
								function () {
									$(this).prev().removeClass('eae-faq-active');
									$this.attr('aria-expanded', 'false');
								});
						} else {
							if (eae_faq_answer.hasClass('eae-faq-active')) {
								eae_faq_answer.removeClass('eae-faq-active');
							}
							eae_faq_answer.slideUp(faq_accordion_transition_speed, 'swing', function () {
								$(this).prev().removeClass('eae-faq-active');
							});

							$this.addClass('eae-faq-active');
							$this.next('.eae-faq-answer').slideDown(faq_accordion_transition_speed, 'swing', function () {
								$(this).prev().addClass('eae-faq-active');
								$this.attr('aria-expanded', 'true');
							});
						}
						return false;
					}
				}
			);
		}

		//Lottie Animation
		const wrapper = element.querySelector('.eae-faq-wrapper');
		let faqs = wrapper.querySelectorAll('.eae-faq-item-wrapper');
        faqs.forEach(faq => {
            let eaeLottie = faq.querySelector('.eae-lottie');
		    if (eaeLottie != null) {
                let lottie_data = JSON.parse(eaeLottie.getAttribute('data-lottie-settings'));
                let eae_animation = lottie.loadAnimation({
                    container: eaeLottie,
                    path: lottie_data.url,
                    renderer: "svg",
                    loop: lottie_data.loop,
                });

                if (lottie_data.reverse == true) {
                    eae_animation.setDirection(-1);
                }
            }
        });
	}

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-faq.default",
			EAEFAQ
		);
	});
})(jQuery);

/***/ }),

/***/ 501:
/***/ (() => {

(function ($) {
	const EAETextSlider = function ($scope) {
        const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
        const wrappers = element.querySelectorAll('.eae-text-scroll-wrapper');
        

        wrappers.forEach(wrapper => {

            const slides = [...wrapper.querySelectorAll('.eae-text-slide')];
       
            const settingsData = JSON.parse(wrapper.getAttribute("data-settings"));
            let wrapperHeight = 0; 
    
            slides.forEach((slide, index) => {
            
                slide.innerHTML =  '&nbsp;'.repeat(settingsData.Gap.size) + slide.innerHTML + '&nbsp;'.repeat(settingsData.Gap.size);

                wrapperHeight += slide.offsetHeight;
                console.log(wrapperHeight);
              
                wrapper.style.height = wrapperHeight + (settingsData.rowGap.size * index) + 'px';
                
                let speed = parseFloat(slide.getAttribute("data-speed"));
                let direction = parseInt(slide.getAttribute("data-direction"), 10);
                const wrapperWidth = wrapper.clientWidth; // Get the width of the wrapper

               
                if (index > 0) {
                    slide.style.top = ((wrapperHeight - slide.offsetHeight) + (settingsData.rowGap.size * index)) + 'px';
                }

                let slideWidth = slide.clientWidth; 
                slide.i = 0; 
                slide.step = speed * direction * 0.1; 
                slide.width = slide.clientWidth + 1; 
                renderLottie(wrapper);
                
                if (wrapperWidth > slideWidth) {
                    let requiredWidth = Math.ceil(wrapperWidth / slideWidth) + 1;
                    let repeatedContent = Array(requiredWidth).fill(`${slide.innerHTML}`).join('');
                    slide.innerHTML = repeatedContent;
                    slideWidth = slide.clientWidth; 
                } else {
                    let repeatedContent = Array(2).fill(`${slide.innerHTML}`).join('');
                    slide.innerHTML = repeatedContent;
                }

                //renderLottie(slide);
                
            
                if (settingsData.pauseOnHover === 'yes') {
                    slide.addEventListener('mouseenter', () => (slide.step = 0), false);
                    slide.addEventListener('mouseleave', () => (slide.step = speed * direction * 0.1), false);
                }
                
            });

            
            requestAnimationFrame(() => move(slides));

            function move(slides) {
                slides.forEach(slide => {
                    
                    slide.style.transform = `translateX(${-slide.i}px)`;
                    slide.i += slide.step; // Update the position index

                    
                    if (slide.i >= slide.width) {
                        slide.i = 0;
                    } else if (slide.i < 0) {
                        slide.i = slide.width;
                    }
                });
                requestAnimationFrame(() => move(slides));
            }

            function renderLottie(wrapper){
                isLottiePanels = wrapper.querySelectorAll('.eae-lottie');
                if (isLottiePanels != null) {
                    isLottiePanels.forEach(isLottiePanel=>{
                       // console.log('lottie', isLottiePanel);
                        let lottie_data = JSON.parse(isLottiePanel.getAttribute('data-lottie-settings'));
                        let eae_animation = lottie.loadAnimation({
                            container: isLottiePanel,
                            path: lottie_data.url,
                            renderer: "svg",
                            loop: lottie_data.loop,
                        });
                       console.log({eae_animation});
                        if (lottie_data.reverse == true) {
                            eae_animation.setDirection(-1);
                        }
                    })
                }
            }

            
          
		   
        });


	}
	

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-text-scroll.default",
			EAETextSlider
		);
	});
})(jQuery);

/***/ }),

/***/ 537:
/***/ (() => {

(function ($) {
	const EAEImageScroll = function ($scope) {

        var imageElement = $scope.find(".wts-eae-image-scroll");
        var imageOverlay = imageElement.find(".image-scroll-wrapper::before");
        var imageVertical = imageElement.find(".image-scroll-wrapper");
        var imagedataElement = imageElement.data("settings");
        var image = imageElement.find("img");
        var imageDirection = imagedataElement["direction"];
        var imageReverse = imagedataElement["reverse"];
        var transformOffset = null;

        
        const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
        var isLottiePanle = element.querySelector('.eae-lottie-animation');

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

        function startTransform() {
            image.css("transform", (imageDirection === "vertical" ? "translateY" : "translateX") + "( -" +
                transformOffset + "px)");
        }
        function endTransform() {
            image.css("transform", (imageDirection === "vertical" ? "translateY" : "translateX") + "(0px)");
        }

        function setTransform() {
            if (imageDirection === "vertical") {
                transformOffset = image.height() - imageElement.height();
            } else {
                transformOffset = image.width() - imageElement.width();
            }
        }
        if (imagedataElement["trigger"] === "scroll") {
            imageElement.addClass("eae-container-scroll");
            if (imageDirection === "vertical") {
                imageVertical.addClass("eae-image-scroll-ver");
            } else {
                
            }
        }
        else {
            if (imageReverse === "yes") {
                imageElement.imagesLoaded(function () {
                    imageElement.addClass("eae_scroll");
                    setTransform();
                    startTransform();
                });
            }
            if (imageDirection === "vertical") {
                imageVertical.removeClass("eae-image-scroll-ver");
            }
          
            imageElement.mouseenter(function () {
                setTransform();
                imageReverse === "yes" ? endTransform() : startTransform();
            });
            imageElement.mouseleave(function () {
                imageReverse === "yes" ? startTransform() : endTransform();
            });
        }
    };
	

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-image-scroll.default",
			EAEImageScroll
		);
	});
})(jQuery);

/***/ }),

/***/ 734:
/***/ (() => {

(function ($) {
	const EAEAddToCalender = function ($scope) {
		const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
        var isLottiePanle = element.querySelector('.eae-lottie-animation');

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
	}
	

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-add-to-calendar.default",
			EAEAddToCalender
		);
	});
})(jQuery);

/***/ }),

/***/ 784:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

(function ($) {

    const EAETeamMember = function($scope){

        if($scope.find('.eae-tm-swiper-container').hasClass('eae-swiper')) {
            const wid = $scope.data('id');
            const outer_wrapper = $scope.find('.eae-tm-swiper-container');
			const swiper_settings = outer_wrapper.data('swiper-settings');
            new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y( swiper_settings, wid, $scope );

        }
    }
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/eae-team-member.default",
            EAETeamMember
        );
    });  

})(jQuery);

/***/ }),

/***/ 793:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

(function ($) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
        TestimonialHandler;

                TestimonialHandler = ModuleHandler.extend({
                getDefaultSettings: function getDefaultSettings() {
                    return {
                        settings: this.getElementSettings(),
                    };
                },
                getDefaultElements: function getDefaultElements(){
                    
                    const wId = this.$element.data('id');
                    const scope = this.$element;
                    const element = document.querySelector('.elementor-element-' + wId);
                    const wrapper = element.querySelector('.eae-testimonial-wrapper');
                    return {
                        eid: wId,
                        element: element,
                        wrapper: wrapper,
                        scope: scope,
                    }
                },
                onInit: function onInit(){
                    const { settings } = this.getDefaultSettings();
                    const { wrapper , scope} = this.getDefaultElements();
                    let { element } = this.getDefaultElements();
                    const { eid } = this.getDefaultElements();
                    const imgElements = wrapper.querySelectorAll('.eae-additional-image.eae-preset-2');
                    const contentElements = wrapper.querySelectorAll('.eae-ts-content-section');
                    const breakpoints = parseInt(wrapper.getAttribute('data-stacked'));
                
                    if(wrapper.classList.contains('eae-testimonial-slider')){
                        const outer_wrapper = scope.find('.eae-swiper-outer-wrapper');
                        const swiper_settings = outer_wrapper.data('swiper-settings');
                        new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y(swiper_settings, eid, scope);
                    }  
                    window.addEventListener("resize", function(){
                        const currentWindowWidth = this.window.innerWidth;
                        if(currentWindowWidth <= breakpoints) {
                            imgElements.forEach(img => {
                                img.style.display = 'none';
                            });
                        }
                        else {
                            imgElements.forEach(img => {
                                img.style.display = 'flex';
                            });
                        }
                    })
                },
            });
            elementorFrontend.hooks.addAction('frontend/element_ready/eae-testimonial.default', function ($scope) {	
                elementorFrontend.elementsHandler.addHandler(TestimonialHandler, {
                    $element: $scope
                  });
            });

        });   
})(jQuery);

/***/ }),

/***/ 836:
/***/ (() => {

( function ($){

    $(window).on('elementor/frontend/init', function(){
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            FormActions;
		//console.log(elementor);
        FormActions = ModuleHandler.extend({
            getDefaultSettings: function getDefaultSettings(){
                return {
                    settings: this.getElementSettings(),
                }
            },
            getDefaultElements: function getDefaultElements(){
                const eId = this.$element.data('id');
				const scope = this.$element;
				const element = document.querySelector('.elementor-element-' + eId);
				let form = null;
				if (element !== null) {
					form = element.querySelector('.elementor-form');
				}
                return {
                    eid: eId,
                    scope: scope,
					element: element,
					form: form,
                }
            },
			onInit: function onInit() {
				const that = this;
                const { eid , scope , element, form } = this.getDefaultElements();
				const { settings } = this.getDefaultSettings();

				//on load eae_dynamic_upload
				if (form !== null) {
					const eaeUploads = form.querySelectorAll('.elementor-field-type-eae_dynamic_upload .eae-uploads');
					if (eaeUploads != null) {
						eaeUploads.forEach(function (eaeUpload) {
							const eaeUploadItem = eaeUpload.querySelectorAll('.eae-upload-item');
							const close = eaeUpload.querySelectorAll('.eae-upload-close');
							if (eaeUploadItem.length > 0) {
								eaeUploadItem.forEach(function (item) {
									const close = item.querySelector('.eae-upload-close');
									close.addEventListener('click', function () {
										const eaeUploadId = item.getAttribute('data-upload-id');
										const eaeUploadField = item.getAttribute('data-upload-field');
										let uploadValue = form.querySelector('input[name="' + eaeUploadField + '"]').value.split(',');
										uploadValue = uploadValue.filter(item => item !== eaeUploadId).join(',');
										form.querySelector('input[name="' + eaeUploadField + '"]').value = uploadValue;
										item.remove();
									
									});
								});
							}
						});
					}

					let hidden_post_id = document.querySelector('.eae-hidden-post-id');
					if (hidden_post_id != null) {
						if (form.querySelector('.eae-hidden-post-id') != null) {
							form.querySelector('.eae-hidden-post-id').remove();
						}
						const hidden_post_id_el = hidden_post_id.cloneNode(true);
						form.appendChild(hidden_post_id_el);
					}
				}

				//on load eae_wysiwyg
				if (form !== null) {
					const eae_wysiwyg = form.querySelectorAll('.elementor-field-type-eae_wysiwyg .elementor-field-wysiwyg');
					if (eae_wysiwyg != null) {
						eae_wysiwyg.forEach(function (wysiwyg) {
							let data_required = wysiwyg.dataset.required;
							let required = false;
							if (data_required == 'yes') {
								required = true;
							}
							wp.editor.remove('form-field-' + wysiwyg.getAttribute('data-custom_id'));
							wp.editor.initialize('form-field-' + wysiwyg.getAttribute('data-custom_id'), {
								tinymce: {
									toolbar1: 'fontsizeselect,backcolor,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,alignjustify,link,wp_more,fullscreen,wp_adv',
									toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'
								},
								quicktags: false,
								mediaButtons: false,
								required: required
							});
						});
					}
				}

				if (elementorFrontend.isEditMode()) {
				
					elementor.hooks.addFilter('elementor_pro/forms/content_template/field/eae_dynamic_upload', function (inputField, item, settings) {
					
						let allow_multiple_upload = '';
						let name = 'form_fields[' + item.custom_id + ']';
						if ( item.dynamic_allow_multiple_upload ) {
							allow_multiple_upload = 'multiple';
							name = 'form_fields[' + item.custom_id + '][]';
						}

						if ( ! item.dynamic_file_sizes ) {
							data_maxsize = item.dynamic_file_sizes;  //MB
							data_maxsize_message = 'This file exceeds the maximum allowed size.';
						}
						
						inputField = '<input class="elementor-field elementor-size-sm elementor-upload-field" type="file" name="' + name + '" id="form-field-' + item.custom_id + '" ' + allow_multiple_upload + ' />';
						inputField += '<div class="eae-uploads elementor-repeater-item-' + item._id + '">';
						inputField += '<div class="eae-upload-item" data-upload-id="1" data-upload-field="' + name + '">';
						inputField += '<span class="eae-upload-close">';
						inputField += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M512 64a448 448 0 1 1 0 896 448 448 0 0 1 0-896zm0 393.664L407.936 353.6a38.4 38.4 0 1 0-54.336 54.336L457.664 512 353.6 616.064a38.4 38.4 0 1 0 54.336 54.336L512 566.336 616.064 670.4a38.4 38.4 0 1 0 54.336-54.336L566.336 512 670.4 407.936a38.4 38.4 0 1 0-54.336-54.336L512 457.664z"></path></svg>';
						inputField += '</span>';
						inputField += '<img src="' + eae.plugin_url + 'assets/img/default.png" class="attachment-thumbnail size-thumbnail" alt="" decoding="async" />';
						inputField += '</div>';
						inputField += '</div>';
						return inputField;
					});
				
					elementor.hooks.addFilter('elementor_pro/forms/content_template/field/eae_wysiwyg',
						function (inputField, item, settings) {
							let itemValue = '';
							const required = item.required ? 'required' : '';
							const rows = item.wysiwyg_rows;
							const placeholder = '';
							let script = '';
								
							inputField = '';
							inputField += '<div style="width:100%">';
							inputField += '<textarea class="elementor-field-wysiwyg elementor-field" row="' + rows + '" placeholder="' + placeholder + '" name="form_fields[' + item.custom_id + ']" id="form-field-' + item.custom_id + '" ' + required + '>' + itemValue + '</textarea>';
							script = "<script>" +
								"wp.editor.remove('form-field-" + item.custom_id + "'); " +
								"wp.editor.initialize('form-field-" + item.custom_id + "', { " +
								"tinymce: { " +
										"toolbar1: 'fontsizeselect,backcolor,bold,italic,underline,bullist,numlist,blockquote,alignleft,aligncenter,alignright,alignjustify,link,wp_more,fullscreen,wp_adv'," +
										"toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'" +
								"}," +
								"quicktags: false, mediaButtons: false, required: true" +
								"}); </script>";
							inputField += script;
							inputField += '</div>';
							return inputField;
						}, 10, 3
					);
				}
            },
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(FormActions, {
				$element: $scope
			});
        });
    });
})(jQuery);

/***/ }),

/***/ 839:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

( function ($){

    $(window).on('elementor/frontend/init', function(){
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            GoogleReviews;
        
        GoogleReviews = ModuleHandler.extend({
            getDefaultSettings: function getDefaultSettings(){
                return {
                    settings: this.getElementSettings(),
                }
            },
            getDefaultElements: function getDefaultElements(){
                const eId = this.$element.data('id');
                const scope = this.$element;
                const element = document.querySelector('.elementor-element-' + eId);
                const wrapper = element.querySelector('.eae-rw-container');

                return {
                    eid: eId,
                    scope: scope,
                    element: element,
                    wrapper: wrapper,
                }
            },
            onInit: function onInit(){
                const that = this;
                const { eid , scope , element , wrapper } = this.getDefaultElements();
                const { settings } = this.getDefaultSettings();
                
                if(wrapper != null){
                    if(wrapper.classList.contains('eae-rw-swiper')){
                        const outer_wrapper = scope.find('.eae-swiper-outer-wrapper');
                        const swiper_settings = outer_wrapper.data('swiper-settings');
                        new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y(swiper_settings, eid, scope);
                    }
    
                    that.getLottie(wrapper);
                }
            },
            getLottie: function getLottie(wrapper){
                const isLottiePanel = wrapper.querySelectorAll('.eae-lottie');
                if (isLottiePanel != null) {
                    isLottiePanel.forEach(function (element) {
                        let lottie_data = JSON.parse(element.getAttribute('data-lottie-settings'));
                        let eae_animation = lottie.loadAnimation({
                            container: element,
                            path: lottie_data.url,
                            renderer: "svg",
                            loop: lottie_data.loop,
                        });

                        if (lottie_data.reverse == true) {
                            eae_animation.setDirection(-1);
                        }
                    });
                }
            },
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-google-reviews.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(GoogleReviews, {
				$element: $scope
			});
        });
    });
})(jQuery);

/***/ }),

/***/ 862:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

(function ($) {
	
	$(window).on('elementor/frontend/init', function () {
		var ModuleHandler = elementorModules.frontend.handlers.Base,
			InstaFeedHandler;
		
		InstaFeedHandler = ModuleHandler.extend({
			// check if is in edit mode 
			
			getDefaultSettings: function getDefaultSettings() {
				
				return {
					selectors: {
						container: '.eae-post-collection',
						item: '.eae-insta-post',
						grid_gap: '.grid-gap',
						swiper_wrapper: '.eae-swiper-outer-wrapper',
					},
					settings: this.getElementSettings(),
				};
			},
			getDefaultElements: function getDefaultElements() {
				
				const selectors = this.getSettings( 'selectors' );
				
				return {
					container: this.$element.find( selectors.container ),
					items: this.$element.find( selectors.item ),
					grid_gap: this.$element.find(selectors.grid_gap),
					swiper_wrapper: this.$element.find(selectors.swiper_wrapper),
				};
			},
			onInit: function onInit() {
				
				const { container } = this.getDefaultElements();
				const { settings } = this.getSettings();
				const that = this;

				if (settings.insta_feed_layout == 'masonry') {
					container.imagesLoaded().done(function () {
						that.runMasonry();
					});
				}
				
				window.addEventListener('resize', this.runMasonry.bind(this));

				this.runSwiper();
				this.runLightbox();

			},
			onElementChange: function onElementChange(propertyName) {
				//alert(propertyName);
				if (propertyName === 'insta_feed_row_gap') {
					this.runMasonry();
				}
			},

			runMasonry: function runMasonry() {
				const { settings } = this.getSettings();

				if(settings.insta_feed_layout != 'masonry') return;

				const { container, items, grid_gap } = this.getDefaultElements();
				
				var heights = [],
				distanceFromTop = 0,
				columnsCount = 3,
				verticalSpaceBetween = 10;

				distanceFromTop = container.position().top;
				columnsCount = container.css('grid-template-columns').split(' ').length;
				verticalSpaceBetween = grid_gap.width();
				distanceFromTop += parseInt(container.css('margin-top'), 10);
				
				items.each(function (index) {
					var row = Math.floor(index / columnsCount),
						$item = jQuery(this),
						itemHeight = $item[0].getBoundingClientRect().height + verticalSpaceBetween;
					if (row) {
						
						var itemPosition = $item.position(),
							indexAtRow = index % columnsCount,
							pullHeight = itemPosition.top - distanceFromTop - heights[indexAtRow];
						
						//pullHeight -= parseInt($item.css('margin-top'), 10);
						
						pullHeight *= -1;
						$item.css('margin-top', pullHeight + 'px');
						heights[indexAtRow] += itemHeight;
					} else {
						heights.push(itemHeight);
						$item.css('margin-top', 0);
					}
					$item.css('visibility', 'visible');
					
				});
				
			},
			runSwiper: function runSwiper() {
				const { settings } = this.getSettings();
				if (settings.insta_feed_layout != 'carousel') return;

				const widget_id = this.$element.data('id');
				const { swiper_wrapper } = this.getDefaultElements();
				const swiper_settings = swiper_wrapper.data('swiper-settings');
				new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y(swiper_settings, widget_id);

			},
			runLightbox: function runLightbox() {
				const { container } = this.getDefaultElements();
				if (!container.hasClass('lightbox')) return;

				var options = {
					selector: '.eae-insta-post-link'
				};

				const widget_id = this.$element.data('id');
				var collection = document.getElementById('insta-grid-' + widget_id);
				var lgSettings = JSON.parse(container.attr('data-lg-settings'));
				options = { ...options, ...lgSettings };

				var plugins = {
					plugins: [
						lgVideo,
						lgShare,
						lgZoom,
						lgHash,
						lgRotate,
						lgFullscreen,
						lgThumbnail
					],
				};

				options = { ...options, ...plugins };
				lightGallery(collection, options);
			}
		});

		elementorFrontend.hooks.addAction('frontend/element_ready/eae-instagram-feed.default', function ($scope) {
		
			elementorFrontend.elementsHandler.addHandler(InstaFeedHandler, {
				$element: $scope
			  });
		});
	});

	
})(jQuery);

/***/ }),

/***/ 867:
/***/ (() => {

(function ($) {
	const EAEFloatingElement = function ($scope) {
        
        const eId = $scope.attr('data-id');
		const element = document.querySelector('.elementor-element-' + eId);
		const wrapper = element.querySelector('.wta-eae-floating-image-wrapper');  
		let LottieData = wrapper.querySelectorAll('.wts-eae-image.lottie-animation'); 
        let animationWrapper =  wrapper.querySelectorAll('.wts-eae-image'); 

        animationWrapper.forEach((items)=>{

            let animationData = items.getAttribute('data-settings');
            let Data = JSON.parse(animationData);
            
            let from = '';
            let to = '';
            // let data_scale_x = Data.scaleX
            // let data_scale_y = Data.scaleZ
            if(Data.hasOwnProperty('isRotate') && Data.isRotate == 'yes'){
                let data_rotate_x = Data.rotateX
                let data_rotate_y = Data.rotateY
                let data_rotate_z = Data.rotateZ
                from = 'rotateX(' + Data.rotateX.from + 'deg)' + ' rotateY(' + Data.rotateY.from + 'deg)' + ' rotateZ(' + Data.rotateZ.from + 'deg)'
                to = 'rotateX(' + Data.rotateX.to + 'deg)' + ' rotateY(' + Data.rotateY.to + 'deg)' + ' rotateZ(' + Data.rotateZ.to + 'deg)'
            }
            if(Data.hasOwnProperty('isTranslate') && Data.isTranslate == 'yes'){
                let dataX=Data.translateX;
                let dataY=Data.translateY;
                from = from + 'translateX(' + dataX.from + 'px)' + ' translateY(' + dataY.from  + 'px)'
                to = to + 'translateX(' + dataX.to + 'px)' + ' translateY(' + dataY.to  + 'px)'
            }

            if(Data.hasOwnProperty('isScale') && Data.isScale == 'yes'){
                let data_scale_x = Data.scaleX
                let data_scale_y = Data.scaleZ
                from = from + ' scaleX(' + data_scale_x.from + ')'   + ' scaleY(' + data_scale_y.from + ')'
                to = to + 'scaleX(' + data_scale_x.to + ')'   + ' scaleY(' + data_scale_y.to + ')'
            }
          
            let animationName = 'crazy'+ Math.random().toString(36).substring(2,7);

            jQuery.keyframe.define({
                name:animationName ,
                from: { 'transform': from}, 
                to:  { 'transform': to } , 
            });


            $(items).playKeyframe({
                name:animationName , 
                duration: Data.Duration+'ms',
                timingFunction: 'linear',
                delay: (Data.Delay=='' ? 0 : Data.Delay)+'ms',
                iterationCount:'infinite',
                direction: Data.animationDirection, 
                fillMode: 'forwards', 
                complete: function(){} 
            });
       
        });


		LottieData.forEach(data => {
            isLottiePanle = data.querySelector('.eae-lottie');
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
    }
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eae-floating-element.default",
			EAEFloatingElement
		);
	});
})(jQuery);

/***/ }),

/***/ 870:
/***/ ((__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(305);

(function ($) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            WooProductsHandler;

            WooProductsHandler = ModuleHandler.extend({
                getDefaultSettings: function getDefaultSettings() {
                    return {
                        settings: this.getElementSettings(),
                    };
                },
                getDefaultElements: function getDefaultElements(){
                    
                    const wId = this.$element.data('id');
                    const scope = this.$element;
                    const element = document.querySelector('.elementor-element-' + wId);
                    const wrapper = element.querySelector('.eae-woo-products');
                    return {
                        eid: wId,
                        element: element,
                        wrapper: wrapper,
                        scope: scope,
                    }
                },
                onInit: function onInit(){
                    const { settings } = this.getDefaultSettings();
                    const { wrapper , scope} = this.getDefaultElements();
                    let { element } = this.getDefaultElements();
                    const { eid } = this.getDefaultElements();
                    const popWrapper = wrapper.querySelectorAll(".open-popup-link");
                    
                    popWrapper.forEach(wrapper => $(wrapper).eaePopup({
                        type:'inline',
                        midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
                        mainClass:"eae-wp-modal-box eae-wp-"+eid,
                        callbacks:{
                            open: function(){
                                jQuery(window).trigger("resize"); 
                            },
                            
                          }
                    }));
          
                    if(wrapper.classList.contains('eae-wp-slider')){
                        const outer_wrapper = scope.find('.eae-swiper-outer-wrapper');
                        const swiper_settings = outer_wrapper.data('swiper-settings');
                        new _base__WEBPACK_IMPORTED_MODULE_0__/* .SwiperBase */ .Y(swiper_settings, eid, scope);
                    }  

                    let buyNowBtn = wrapper.querySelectorAll('.eae-wp-buy-now');
                    buyNowBtn.forEach(function(btn){
                        btn.addEventListener('click',function(e){
                            e.preventDefault();
                            var productId = btn.getAttribute('data-product-id');
                            var quantity = btn.getAttribute('data-quantity');
                            const checkout=eae.checkout_url;
                            const params = new URLSearchParams();
                        
                            //pass parameters to php
                            params.append('action', 'eae_add_to_cart' );
                            params.append('product_id', productId );
                            params.append('quantity', quantity );
                            params.append('eae_nonce', eae.nonce );
                            // send ajax request to php
                            fetch(eae.ajaxurl, {
                                method: 'post',
                                credentials: 'same-origin',
                                body : params,
                            })
                            .then((response) => {
                                response.json();
                            })
                            .then((data) => {
                                window.location.href = checkout;
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                            });
                        });
                    });
                    
                },
                onElementChange: function onElementChange(propertyName) {                  
                        
                },

            });
            elementorFrontend.hooks.addAction('frontend/element_ready/eae-woo-products.default', function ($scope) {	
                elementorFrontend.elementsHandler.addHandler(WooProductsHandler, {
                    $element: $scope
                  });
            });

        });   
})(jQuery);

/***/ }),

/***/ 898:
/***/ (() => {

(function ($){
    
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
			InfoGroupHandler;

        InfoGroupHandler = ModuleHandler.extend({
            getDefaultSettings: function getDefaultSettings() {
                return {
                    settings: this.getElementSettings(),
                };
            },
            getDefaultElements: function getDefaultElements() {
				
                const eId = this.$element.data('id'); 
                const element = document.querySelector('.elementor-element-' + eId);
                const wrapper = element.querySelector('.eae-ig-wrapper');
                
                return{
                    eid: eId,
                    element: element,
                    wrapper: wrapper,
                }
			},
            onInit: function onInit(){
                const { element , wrapper } = this.getDefaultElements();
                const { settings } = this.getDefaultSettings();
                
                isLottiePanel = wrapper.querySelectorAll('.eae-ig-lottie');
                if (isLottiePanel != null) {
                    isLottiePanel.forEach(element => {
                        let lottie_data = JSON.parse(element.getAttribute('data-lottie-settings'));
                        let eae_animation = lottie.loadAnimation({
                            container: element,
                            path: lottie_data.url,
                            renderer: "svg",
                            loop: lottie_data.loop,
                        });
                        if (lottie_data.reverse == true) {
                            eae_animation.setDirection(-1);
                        }
                    });
                }

                let clickedButton = '';
                let activeElement = '';

                const items = element.querySelectorAll('.eae-ig-item-wrapper');

                items.forEach(item => {
                    if(item.classList.contains('eae-ig-active-item')){
                        wrapper.classList.add('eae-ig-active');
                        if(item.classList.contains('slide')){
                            this.infoSlideDown(item);
                        }else{
                            this.infoFadeIn(item);
                        }
                        clickedButton = item;
                        activeElement = item;
                    }
                });
 
                const closeBtu = element.querySelectorAll('.eae-ig-close-button');
                closeBtu.forEach(element => {
                    element.addEventListener('click',() => {
                        items.forEach(item => {
                            if(item.classList.contains('eae-ig-active-item')){
                                this.cloAnimation(item,settings.description_animtion_type);
                                item.classList.remove('eae-ig-active-item');
                                wrapper.classList.remove('eae-ig-active');
                                clickedButton = '';
                                activeElement = '';
                            }
                        });
                    });
                });

                if(settings.description_trigger_on == 'button'){
                    const Button = element.querySelectorAll('.eae-ig-link');
                    Button.forEach(element => {
                        element.addEventListener('click',() => {
                            items.forEach(item => {
                                if(item.classList.contains('eae-ig-active-item')){
                                    this.cloAnimation(item,settings.description_animtion_type);
                                    item.classList.remove('eae-ig-active-item');
                                    wrapper.classList.remove('eae-ig-active');
                                }
                            });
                            if(clickedButton !== element){
                                let parentEle = element.parentElement;
                                let parEle = parentEle.parentElement;
                                parEle.classList.add('eae-ig-active-item');
                                wrapper.classList.add('eae-ig-active');
                                this.opAnimation(parEle,settings.description_animtion_type);
                                clickedButton = element;
                                activeElement = '';
                            }else{
                                clickedButton = '';
                            };
                        });
                    });
                }else{
                    items.forEach(item => {
                        item.addEventListener('click', () => {
                            items.forEach(item => {
                                if(item.classList.contains('eae-ig-active-item')){
                                    item.classList.remove('eae-ig-active-item');
                                    wrapper.classList.remove('eae-ig-active');
                                    
                                    this.cloAnimation(item,settings.description_animtion_type);
                                }
                            });
                            if(activeElement != item){
                                item.classList.add('eae-ig-active-item');
                                wrapper.classList.add('eae-ig-active');
                                
                                this.opAnimation(item,settings.description_animtion_type);
                                activeElement = item;
                            }else{
                                activeElement = '';
                            }
                            clickedButton = '';
                        });
                    });
                }

            },
            opAnimation: function opAnimationActive(element,animationType){
                if(animationType == 'slide'){
                    setTimeout(this.infoSlideDown,400,element);
                }else{
                    setTimeout(this.infoFadeIn,400,element);
                }
            },
            cloAnimation: function cloAnimation(element, animationType){
                if(animationType == 'slide'){
                    this.infoSlideUp(element);
                }else{
                    this.infoFadeOut(element);
                }
            },
            infoSlideUp: function infoSlideUp(element){
                let container = element.nextSibling.nextElementSibling;
                jQuery(container).slideUp();
            },
            infoSlideDown: function infoSlideDown(element){
                let container = element.nextSibling.nextElementSibling;
                jQuery(container).slideDown();
            },
            infoFadeIn: function infoFadeIn(element){
                let container = element.nextSibling.nextElementSibling;
                jQuery(container).fadeIn();
            },
            infoFadeOut: function infoFadeOut(element){
                let container = element.nextSibling.nextElementSibling;
                jQuery(container).fadeOut();
            }
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-info-group.default', function ($scope) {
		
			elementorFrontend.elementsHandler.addHandler(InfoGroupHandler, {
				$element: $scope
			  });
		});
    });
})(jQuery);

/***/ }),

/***/ 994:
/***/ (() => {

(function ($){

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
			TableOfContentHandler;

        TableOfContentHandler = ModuleHandler.extend({
            getDefaultSettings: function getDefaultSettings() {
                return {
                    settings: this.getElementSettings(),
                };
            },
            getDefaultElements: function getDefaultElements(){
                
                const eId = this.$element.data('id');
                const element = document.querySelector('.elementor-element-' + eId);
                const wrapper = element.querySelector('.eae-toc-wrapper');
                return {
                    eid: eId,
                    element: element,
                    wrapper: wrapper,
                }
            },
            onInit: function onInit(){
                const { settings } = this.getDefaultSettings();
                const { wrapper } = this.getDefaultElements();
                const that = this;
                const headingTag = settings.anchors_by_tags.join(',');
                
                this.getLottie();
                let container = '';
                if(settings.included_container != undefined && settings.included_container != ''){
                    container = document.querySelector(settings.included_container);
                }else{
                    container = document.querySelector(".elementor");
                }
                
                let headings = [];
                let headingData = [];
                if(headingTag != ''){
                    const heading = container.querySelectorAll(headingTag);
                    let flag = 0;
                    heading.forEach( function (element, index) {
                        if(element.classList.contains('eae-toc-heading')){
                            flag++;
                        }
                        if(flag == 0){
                            headings[index] = element;
                        }else{
                            flag = 0;
                        }
                    });

                    headings = this.excludeHeadings(headings);
                    
                    this.addAnchors(headings);

                    headings.forEach(function(element,index){
                        headingData.push({
                            tag: element.nodeName.slice(1),
                            text: element.textContent,
                            class: element.className,
                        });
                    });

                    if(settings.hierarchical_view == 'yes'){
                        headingData = this.addLevel(headingData);
                    }else{
                        headingData.forEach(function(heading){
                            heading.level = 0;
                        });
                    }
                    
                    let htmlTag = this.getHeadings(0,headingData,0);


                    if(htmlTag.html != ''){
                        wrapper.querySelector(".eae-toc-headings-wrapper").innerHTML = htmlTag.html;
                    }

                    if(settings.collapse_box == 'yes'){
                        this.minimizeBox(wrapper);
                    }

                    if(settings.toc_sticky == 'yes'){
                        this.stickyToc();
                    }
    
                    if(settings.follow_heading == 'yes'){
                        let tag = wrapper.querySelectorAll('.eae-toc-heading-anchor-wrapper');
                        let headingAnchor = [];
                        let offset = '';
                        if(settings.follow_heading_offset.size != ''){
                            offset = settings.follow_heading_offset.size + settings.follow_heading_offset.unit;
                        }else{
                            offset = '50%'
                        }
                        
                        if(tag != ''){
                            tag.forEach(function(element,index){
                                headingAnchor[index] = document.querySelector('#eae-toc-heading-anchor-' + index);
                            });
                            headingAnchor.forEach(function(element,index){
                                if(element != null){
                                    that.followHeading(element,index,tag,offset);
                                }
                            });
                        }
                    }
                }
                this.getScrollEffect(); 
            },
            getScrollEffect: function getScrollEffect(){
                let html = document.querySelector('html');
                if(!html.classList.contains('eae-toc-scroll')){
                    html.classList.add('eae-toc-scroll');
                }
            },
            getLottie: function getLottie(){
                const { wrapper } = this.getDefaultElements();
                isLottiePanel = wrapper.querySelector('.eae-lottie');
                if (isLottiePanel != null) {
                    let lottie_data = JSON.parse(isLottiePanel.getAttribute('data-lottie-settings'));
                    let eae_animation = lottie.loadAnimation({
                        container: isLottiePanel,
                        path: lottie_data.url,
                        renderer: "svg",
                        loop: lottie_data.loop,
                    });

                    if (lottie_data.reverse == true) {
                        eae_animation.setDirection(-1);
                    }
                }
            },
            addAnchors: function addAnchors(element){
                let flag = 0;
                element.forEach(function (element,index){
                    newNode = "<span id='eae-toc-heading-anchor-"+ flag +"'></span>";
                    element.insertAdjacentHTML("beforebegin",newNode);
                    flag++;
                });
            },
            addLevel: function addLevel(data){
                const { settings } = this.getDefaultSettings();
                data.forEach(function (element , index){
                    element.level = 0;    
                    for( var i = index - 1; i >= 0; i-- ){
                        let currentItem = data[i];
                        if(currentItem.tag <= element.tag){
                            element.level = currentItem.level;
                            if(currentItem.tag < element.tag){
                                element.level++;
                            }
                            break;
                        }
                    }
                });
                return data;
            },
            excludeHeadings: function excludeHeadings(headingData){
                const { settings } = this.getDefaultSettings();

                if(settings.anchors_by_selector !== '' && settings.anchors_by_selector !== undefined){

                    let excludedClassName = settings.anchors_by_selector.split(',');
                
                    headingData = headingData.filter((element) => {
                        flag = 0;
                        for(i=0; i < excludedClassName.length; i++){
                            if(element.class != excludedClassName[i] && element.closest(excludedClassName[i]) == null){
                                flag++;
                            }else{
                                flag = 0;
                                break;
                            }
                        }
                        if(flag!=0){
                            item = element;
                        }else{
                            item ="";
                        }
                        return item;
                    });
                    return headingData;
                }
                return headingData;
            },
            getHeadings: function getHeadings(level,headingData,listItemIn){
                const { settings } = this.getDefaultSettings();
                let html = "";
                let headingsLinkDiv = '<div class="eae-toc-heading-anchor-wrapper">';
                let headingDivClo = '</div>';
                if(headingData.length != 0){
                    html = "<ul>"
                    for(var i = listItemIn; i < headingData.length; i++){
                        if (level > headingData[i].level ) {
                            break;
                        }
                        if(level === headingData[i].level) {
                            let headingsLink = "<a class='eae-toc-heading-anchor eae-toc-heading-anchor-"+ i +"' href='#eae-toc-heading-anchor-"+ i +"'>" + headingData[i].text + "</a></div>";
                            if(settings.marker_type == 'bullets'){
                                headingsLink = headingsLinkDiv + '<i class="'+ settings.icon.value +'"></i>' + headingsLink + headingDivClo;
                            }else{
                                headingsLink = headingsLinkDiv + headingsLink + headingDivClo;
                            }
                            
                            html += "<li>" + headingsLink;
                            listItemIn++;
                            nextItem = headingData[listItemIn];
                            
                            if(nextItem != undefined){
                                if(level < nextItem.level ){
                                    let data = this.getHeadings(nextItem.level,headingData,listItemIn);
                                    html += data.html;
                                    listItemIn = data.listItemIn;
                                }
                            }
                            html += "</li>";
                        }
                    }
                    html += "</ul>";
                }else{
                    html = 'No headings were found on this page.';
                }
                
                return { 
                    html: html,
                    listItemIn: listItemIn,
                };
            },
            minimizeBox: function minimizeBox(wrapper){
                const { settings } = this.getDefaultSettings();
                let element = wrapper.querySelector('.eae-toc-heading-container');
                let headingsCon = wrapper.querySelector('.eae-toc-headings-wrapper');
                
                if(1440 == settings.toc_collapse_devices && screen.width == 1440){
                    element.classList.add('eae-toc-active');
                }

                window.addEventListener("resize", function(){
                    let resizeValue = settings.toc_collapse_devices;
                    let windowWidth = this.window.innerWidth;
                    
                    if(windowWidth < resizeValue ){
                        jQuery(headingsCon).slideUp();
                        element.classList.remove('eae-toc-active');
                    }else{
                        jQuery(headingsCon).slideDown();
                        element.classList.add('eae-toc-active');
                    }
                });
                
                if(element.classList.contains('eae-toc-active')){
                    jQuery(headingsCon).slideDown();
                }

                element.addEventListener('click',(e) => {
                    if(element.classList.contains('eae-toc-active')){
                        jQuery(headingsCon).slideUp();
                        element.classList.remove('eae-toc-active');
                    }else{
                        jQuery(headingsCon).slideDown();
                        element.classList.add('eae-toc-active');
                    }
                    if(headingsCon.classList.contains('eae-toc-hide')){
                        headingsCon.classList.remove('eae-toc-hide');
                    }
                });
            },
            stickyToc: function stickyToc(){
                const { settings } = this.getDefaultSettings();
                const { wrapper } = this.getDefaultElements();
                const { element } = this.getDefaultElements();
                const tocHeader = wrapper.querySelector('.eae-toc-heading-container');
                const that = this;
                let flag = 0;
                let parentWayP = '';
                let offset = this.offsetCal();
                if(settings.toc_sticky == 'yes'){
                    this.stickyDevices();
                    const waypoint = new Waypoint({
                        element: wrapper,
                        handler: function (direction) {
                            if(wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                                if(direction == 'down'){
                                    if(!wrapper.classList.contains('eae-toc-sticky')){
                                        wrapper.classList.add('eae-toc-sticky');
                                    }
                                }else{
                                    if(wrapper.classList.contains('eae-toc-sticky')){
                                        wrapper.classList.remove('eae-toc-sticky');
                                    }
                                }
                            }
                        },
                        offset: '1%',
                    });
                    if(settings.toc_stay_in_column == 'yes'){
                        const parentWaypoint = new Waypoint({
                            element: element.parentElement,
                            handler: function (direction,poi) {
                                if(wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                                    if(direction == 'down'){
                                        if(wrapper.classList.contains('eae-toc-sticky')){
                                            wrapper.classList.remove('eae-toc-sticky');
                                        }
                                        if(!element.classList.contains('eae-toc-fix')){
                                            element.classList.add('eae-toc-fix');
                                        }
                                    }else if(direction == 'up'){
                                        if(element.classList.contains('eae-toc-fix')){
                                            element.classList.remove('eae-toc-fix');
                                        }
                                        if(!wrapper.classList.contains('eae-toc-sticky')){
                                            wrapper.classList.add('eae-toc-sticky');
                                        }
                                    }
                                }
                                if(settings.collapse_box == 'yes'){
                                    if(flag == 0){
                                        parentWaypoint.destroy();
                                        that.addParentWaypoint();
                                        flag++;
                                    }
                                }
                            },
                            offset: '-' + offset + 'px',
                        });

                        if(tocHeader.classList.contains('eae-toc-active')){
                            if(settings.collapse_box == 'yes'){
                                if(flag == 0){
                                    parentWaypoint.destroy();
                                    setTimeout(that.addParentWaypoint,400);
                                    flag++;
                                }
                            }
                        }
                    }else{
                        if(settings.collapse_box != 'yes'){
                            wrapper.parentElement.style.height = wrapper.clientHeight+'px';
                        }
                    }           
                }
            },
            addParentWaypoint: function addParentWaypoint(){
                
                const { settings } = this.getDefaultSettings();
                const { wrapper } = this.getDefaultElements();
                const { element } = this.getDefaultElements();
                const that = this;
                let flag = 0;
                let offset = this.offsetCal();
                const parentWaypoint = new Waypoint({
                    element: element.parentElement,
                    handler: function (direction) {
                        if(wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                            if(direction == 'down'){
                                if(wrapper.classList.contains('eae-toc-sticky')){
                                    wrapper.classList.remove('eae-toc-sticky');
                                }
                                if(!element.classList.contains('eae-toc-fix')){
                                    element.classList.add('eae-toc-fix');
                                }
                            }else if(direction == 'up'){
                                if(element.classList.contains('eae-toc-fix')){
                                    element.classList.remove('eae-toc-fix');
                                }
                                if(!wrapper.classList.contains('eae-toc-sticky')){
                                    wrapper.classList.add('eae-toc-sticky');
                                }
                            }
                            if(settings.collapse_box == 'yes'){
                                if(flag == 0){
                                    parentWaypoint.destroy();
                                    that.addParentWaypoint();
                                    flag++;
                                }
                            }
                        }
                    },
                    offset: '-' + offset + 'px',
                });
                return parentWaypoint;
            },
            offsetCal: function offsetCal(){
                const { settings } = this.getDefaultSettings();
                const { wrapper } = this.getDefaultElements();
                const { element } = this.getDefaultElements();
                
                let offset = element.parentElement.clientHeight - wrapper.clientHeight;
                
                if(settings.vertical_alignment == 'top'){
                    if(settings.top_spacing.size != '' ){
                        offset = offset - settings.top_spacing.size;
                    } 
                }else if(settings.vertical_alignment == 'bottom'){
                    if(settings.bottom_spacing.size !== '' ){
                        offset = offset - ((screen.height - wrapper.clientHeight) - settings.bottom_spacing.size);
                    }
                }
                return offset;
            },
            stickyDevices: function stickyDevices(){
                const { wrapper } = this.getDefaultElements();
                const { settings } = this.getDefaultSettings();

                const stickyDevices = settings.toc_sticky_devices;

                if(wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                    wrapper.classList.remove('eae-toc-sticky-type-on-place');
                }
                
                if(wrapper.classList.contains('eae-toc-sticky')){
                    wrapper.classList.remove('eae-toc-sticky');
                }
                
                
                stickyDevices.forEach(function (deviceName){
                    
                    if(deviceName == elementorFrontend.getCurrentDeviceMode()){   
                        
                        if(!wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                            wrapper.classList.add('eae-toc-sticky-type-on-place');
                        }
                    }
                });
                
                window.addEventListener("resize", function(){
        
                    let device = elementorFrontend.getCurrentDeviceMode();
                    
                    if(wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                        wrapper.classList.remove('eae-toc-sticky-type-on-place');
                    }
                
                    if(wrapper.classList.contains('eae-toc-sticky')){
                        wrapper.classList.remove('eae-toc-sticky');
                    }

                    stickyDevices.forEach(function (deviceName){
                        if(deviceName == device)
                        {
                            if(!wrapper.classList.contains('eae-toc-sticky-type-on-place')){
                                wrapper.classList.add('eae-toc-sticky-type-on-place');
                            }
                        }
                    });
                });
            },
            followHeading: function followHeading(element,index,tag,offset){
                let anchor = '';
                var waypoints = new Waypoint({
                    element: document.getElementById(element.id),
                    handler: function(direction) {
                        tag.forEach(function (tagElement,index){
                            anchor = tagElement.querySelector('.eae-toc-heading-anchor');
                            if(anchor.classList.contains(element.id)){
                                if(!tagElement.classList.contains('eae-toc-active-heading')){
                                    tagElement.classList.add('eae-toc-active-heading');
                                }
                            }else{
                                if(tagElement.classList.contains('eae-toc-active-heading')){
                                    tagElement.classList.remove('eae-toc-active-heading');
                                }
                            }
                        });
                    },
                    offset: offset
                });
            }
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/eae-table-of-content.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(TableOfContentHandler, {
				$element: $scope
			});
        });
    });

})(jQuery);

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	__webpack_require__(305);
/******/ 	__webpack_require__(734);
/******/ 	__webpack_require__(211);
/******/ 	__webpack_require__(327);
/******/ 	__webpack_require__(259);
/******/ 	__webpack_require__(147);
/******/ 	__webpack_require__(340);
/******/ 	__webpack_require__(107);
/******/ 	__webpack_require__(45);
/******/ 	__webpack_require__(234);
/******/ 	__webpack_require__(289);
/******/ 	__webpack_require__(482);
/******/ 	__webpack_require__(867);
/******/ 	__webpack_require__(836);
/******/ 	__webpack_require__(839);
/******/ 	__webpack_require__(404);
/******/ 	__webpack_require__(361);
/******/ 	__webpack_require__(537);
/******/ 	__webpack_require__(210);
/******/ 	__webpack_require__(898);
/******/ 	__webpack_require__(862);
/******/ 	__webpack_require__(994);
/******/ 	__webpack_require__(784);
/******/ 	__webpack_require__(793);
/******/ 	__webpack_require__(501);
/******/ 	__webpack_require__(393);
/******/ 	__webpack_require__(322);
/******/ 	__webpack_require__(82);
/******/ 	var __webpack_exports__ = __webpack_require__(870);
/******/ 	
/******/ })()
;