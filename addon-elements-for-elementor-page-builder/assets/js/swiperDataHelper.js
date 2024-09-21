window.prepareSwiperData = function(sett){
    let swiper_data = {};
    if(sett['speed']['size']){
        swiper_data['speed'] = sett['speed']['size'];
    }else{
        swiper_data['speed'] = 1000;
    }
    swiper_data['direction'] = 'horizontal';

    if(sett['autoplay'] == 'yes'){
        swiper_data['autoplay'] = [];
        swiper_data['autoplay']['delay'] = sett['duration']['size'];
    }else{
        swiper_data['autoplay'] = false;
    }

    if( sett['pause_on_hover'] === 'yes'){
        swiper_data['pause_on_hover'] = sett['pause_on_hover'];
    }
    
    if(sett['pause_on_interaction'] !== undefined && sett['pause_on_interaction'] == 'yes'){
        swiper_data['pause_on_interaction'] = sett['pause_on_interaction'];
    }

    if(sett['keyboard'] == 'yes'){
        swiper_data['keyboard'] = sett['keyboard'];
    }

    if(sett['grid'] == 'yes'){
        swiper_data['grid'] = [];
        swiper_data['grid']['fill'] = 'row';
        swiper_data['grid']['rows'] = sett['grid_rows'];
    }else{
        swiper_data['grid'] = false;
    }

    swiper_data['effect'] = sett['effect'];

    swiper_data['loop'] = sett['loop'];

    let height = sett['auto_height'];
    swiper_data['autoHeight'] = ( height === 'yes' ) ? true : false;

    let break_value = {};
    break_value = JSON.parse(eaeEditor.elementorBreakpoints);

    let active_devices = Object.keys(break_value);
    
    if(sett['effect'] == 'fade' || sett['effect'] == 'flip'){
        
        swiper_data['spaceBetween'] = {};
        active_devices.forEach(breakpointName => {
            if(breakpointName == 'desktop'){
                breakpointName = 'default';
            }
            swiper_data['spaceBetween'][breakpointName] = 0;
        });

        swiper_data['slidesPerView'] = {};
        active_devices.forEach(breakpointName => {
            if(breakpointName == 'desktop'){
                breakpointName = 'default';
            }
            swiper_data['slidesPerView'][breakpointName] = 1;
        });

        swiper_data['slidesPerGroup'] = {};
        active_devices.forEach(breakpointName => {
            if(breakpointName == 'desktop'){
                breakpointName = 'default';
            }
            swiper_data['slidesPerGroup'][breakpointName] = 1;
        });
    }else{
        devices_array = [ 'mobile', 'tablet', 'desktop' ];
        //Space Between
        swiper_data['spaceBetween'] = {};
        active_devices.forEach(breakpointName => {
            if(devices_array.includes(breakpointName)){
                switch (breakpointName) {
                    case 'mobile':
                        swiper_data['spaceBetween'][breakpointName] = parseInt( (sett['space_' + breakpointName]['size'] !== '') ? sett['space_' + breakpointName]['size'] : 5 );
                        break;
                    case 'tablet':
                        swiper_data['spaceBetween'][breakpointName] = parseInt( (sett['space_' + breakpointName]['size'] !== '') ? sett['space_' + breakpointName]['size'] : 10 );
                        break;
                    case 'desktop':
                        swiper_data['spaceBetween']['default'] = parseInt( (sett['space']['size'] !== '') ? sett['space']['size'] : 15 );
                        break;
                }
            }else{
                swiper_data['spaceBetween'][breakpointName] = parseInt( (sett['space_' + breakpointName]['size'] !== '') ? sett['space_' + breakpointName]['size'] : 15 );
                
            }
        });
        
        // Slide Per View
        swiper_data['slidesPerView'] = {};
        active_devices.forEach(breakpointName => {
            devices_array = [ 'mobile', 'tablet', 'desktop' ];
            if(devices_array.includes(breakpointName)){
                switch (breakpointName) {
                    case 'mobile':
                        swiper_data['slidesPerView'][ breakpointName ] = parseInt( sett['slide_per_view_' + breakpointName] !== '' ? sett['slide_per_view_' + breakpointName] : 1 );
                        break;
                    case 'tablet':
                        swiper_data['slidesPerView'][ breakpointName ] = parseInt( sett['slide_per_view_' + breakpointName] !== '' ? sett['slide_per_view_' + breakpointName] : 2 );
                        break;
                    case 'desktop':
                        swiper_data['slidesPerView']['default'] = parseInt( sett['slide_per_view'] !== '' ? sett['slide_per_view'] : 3 );
                        break;
                }
            } else {
                swiper_data['slidesPerView'][ breakpointName ] = parseInt( sett['slide_per_view_' + breakpointName] !== '' ? sett['slide_per_view_' + breakpointName] : 2 );
            }
        });

        

        //Slides Per Group
        swiper_data['slidesPerGroup'] = {};
        active_devices.forEach(breakpointName => {
            devices_array = [ 'mobile', 'tablet', 'desktop' ];
            if(devices_array.includes(breakpointName)){
                switch( breakpointName ){
                    case 'mobile':
                        swiper_data[ 'slidesPerGroup' ][ breakpointName ] = sett['slides_per_group_' + active_devices] != '' ? sett['slides_per_group_' + breakpointName] : 1;
                        break;
                    case 'tablet':
                        swiper_data[ 'slidesPerGroup' ][ breakpointName ] = sett['slides_per_group_' + active_devices] != '' ? sett['slides_per_group_' + breakpointName] : 1;
                        break; 
                    case 'desktop':
                        swiper_data[ 'slidesPerGroup' ][ 'default' ] = sett['slides_per_group'] !== '' ? sett['slides_per_group'] : 1;
                        break;
                }
            } else {
                swiper_data['slidesPerGroup'][ breakpointName ] = sett['slides_per_group_' + active_devices] !== '' ? sett['slides_per_group_'] : 1;
            }
        });
    }

    if( sett['ptype'] !== ''){
        swiper_data['ptype'] = sett['ptype'];
    }

    swiper_data['breakpoints_value'] = break_value;
    clickable = sett['clickable'];
    swiper_data['clickable'] = (clickable !== '') ? clickable : false;
    swiper_data['navigation'] = sett['navigation_button'];
    swiper_data['scrollbar'] = sett['scrollbar'];

    return swiper_data;

}