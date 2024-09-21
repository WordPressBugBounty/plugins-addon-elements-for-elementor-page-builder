jQuery(document).ready(function (){
    
	elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
		//console.log(view.container.renderer); return;
            
		var widget_type = model.attributes.widgetType;
		if (widget_type === 'eae-instagram-feed') {
			var wid = model.attributes.id;
			var post_count = model.attributes.settings.attributes.post_count;
			var cache_timeout = model.attributes.settings.attributes.cache_timeout;
			var insta_caption_size = model.attributes.settings.attributes.insta_caption_size;
			var postId = model.attributes.settings.attributes.post_id;
			var transient_key = 'eae_insta_fetched_data_' + 
				wid + '_' +
				post_count + '_' +
				cache_timeout + '_' +
				insta_caption_size;
			
			jQuery(document).on('click', ".eae-refresh-cache-btn button", function () {
                
				jQuery.ajax({
					url: eaeEditor.ajaxurl,
					dataType: 'json',
					method: 'post',
					data: {
						action: 'eae_refresh_insta_cache',
						transient_key: transient_key,
                        nonce: eaeEditor.nonce,
						post_id : postId
					},
					success: function (res) {
                        if(res.success == false){
                            console.log('Invalid Nonce');
                        }else{
                            if (res.data) {
                                view.container.renderer.view.container.renderer.render();
                            } else {
                                console.log('Refresh Cache:', res.data);
                            }
                        }
						
					}
				})
            });
		}


    } );
});


