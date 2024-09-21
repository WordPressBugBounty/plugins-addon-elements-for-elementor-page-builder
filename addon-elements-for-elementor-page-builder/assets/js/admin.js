jQuery(document).ready(function($){
  eae_modules();
  function eae_modules() {
      
      const eae_wrap  = document.querySelector('.eae-wrap');
      if(eae_wrap === null){
          // Not on settings page
          return;
      }

      const selectAll = document.querySelector('#eae-select-all');
      const moduleCtas = document.querySelectorAll('.eae-module-action');
      const applyAll = document.querySelector("#eae-apply");
      const saveConfig = document.querySelector('#save-config');

      const tabs = document.querySelectorAll('.eae-tabs .eae-title a');
     
      // Settings Tab
      tabs.forEach( tab => {
          tab.addEventListener('click', function(e){

              e.preventDefault();

              const tab_anchors = document.querySelectorAll('.eae-tabs .eae-title');
              const tab_id = e.target.dataset.tabid;
              
              if(tab_id === 'eae-get-pro'){
                const pro_link = e.target.getAttribute('href');
                window.open(pro_link, '_blank');
                return;
              }
              tab_anchors.forEach( tab_anchor => tab_anchor.classList.remove('active') );
              e.target.parentElement.classList.add('active');
              document.querySelectorAll('.eae-tab-content').forEach( tab_content => tab_content.classList.remove('active') );
            //   document.querySelector(`#${tab_id}`).classList.add('active');
                document.querySelectorAll('.eae-tab-content').forEach( tab_content => tab_content.hasAttribute('id') ? tab_content.getAttribute('id') === tab_id ? tab_content.classList.add('active') : '' : ''); 

          });
      });

      let $hashtag = window.location.hash.substring(1);
      let all_tabs = document.querySelectorAll('.eae-tabs .eae-title');
      
        if ($hashtag) {
            all_tabs.forEach( tab => tab.classList.remove('active') );
            document.querySelectorAll('.eae-tab-content').forEach( tab_content => tab_content.classList.remove('active') );
            all_tabs.forEach( tab => tab.hasAttribute('id') ? tab.getAttribute('id') === $hashtag ? tab.classList.add('active') : '' : '');
            document.querySelectorAll('.eae-tab-content').forEach( tab_content => tab_content.hasAttribute('id') ? tab_content.getAttribute('id') === $hashtag ? tab_content.classList.add('active') : '' : '');                
        }



      // Select All for Bulk Action
      selectAll.addEventListener('change', function(e){

          const modules = document.querySelectorAll('.eae-module-item');
          if(this.checked){
              modules.forEach(function(module){
                  module.checked = true;
              });
          }else{
              modules.forEach(function(module){
                  module.checked = false;
              });
          }
      });


      // Bind event for Activate/Deactivate button

      moduleCtas.forEach(function(moduleAction){
          moduleAction.addEventListener('click', function(e){  
              e.stopPropagation();
              e.preventDefault();
              
              // check if target has href and it is not blank
            if(e.target.hasAttribute('href') && e.target.getAttribute('href') !== '#'){
                // redirect to the link
                const link = e.target.getAttribute('href');
                window.open(link, '_blank');
                return;
            }
              

              const cta = e.target;
              const moduleKey = cta.dataset.moduleid;
              const moduleAction = cta.dataset.action;

              let moduleData = {};
              moduleData[moduleKey] = moduleAction;
              // console.log(eaeGlobalVar);
              // console.log(ajaxurl);
              cta.classList.add('updating');
              $.ajax({
                  url: eaeGlobalVar.ajax_url,
                  method: 'post',
                  data: {
                      action : 'eae_elements_save',
                      moduleData,
                      nonce: eaeGlobalVar.nonce
                  },
                  success: function(res){

                    const modules = res.modules;
                    
                    if(modules != undefined){

                        for(module in modules){
                           
                          if (modules.hasOwnProperty(module)) {
                              let status = modules[module];
                              
                              let module_anchor = document.querySelector(`[data-moduleid='${module}']`);
                              
                                if(module_anchor === null){
                                    continue;
                                }
                              
                              if(status == 'false'){
                                  module_anchor.textContent = 'Activate';
                                  module_anchor.dataset.action = 'activate';
                                  module_anchor.parentElement.parentElement.classList.remove('eae-enabled');
                                  module_anchor.parentElement.parentElement.classList.add('eae-disabled');
                              }else{
                                  module_anchor.textContent = 'Deactivate';
                                  module_anchor.dataset.action = 'deactivate';
                                  module_anchor.parentElement.parentElement.classList.add('eae-enabled');
                                  module_anchor.parentElement.parentElement.classList.remove('eae-disabled');
                              }

                              module_anchor.classList.remove('updating');
                              // uncheck all checkboxes
                              moduleCBs = document.querySelectorAll('.eae-module-item');
                              moduleCBs.forEach( modulecb => modulecb.checked = false );
                          }
                        }
                    }
                  }
              });

          });
      });

      // Apply all button
      applyAll.addEventListener('click', function(e){

          const bulkAction = document.querySelector('[name="eae-bulk-action"]').value;
          const moduleData = {};
          if(bulkAction === ''){
              alert('Please select an action');
              return;
          }


          modules = document.querySelectorAll('.eae-module-item');
          
          modules.forEach(function(module){
              
              if(module.checked){
                  moduleData[module.value] = bulkAction;
                  if(module.closest('.eae-module-row').querySelector('.eae-module-action').children[0].hasAttribute('data-moduleid')){
                    module.closest('.eae-module-row').querySelector('.eae-module-action').classList.add('updating');
                  }
              }
              
          });

          if(Object.keys(moduleData).length === 0){
              alert('Please select atleast one module');
              return;
          }

          // all set - now call the ajax and update modules.
          $.ajax({
              url: eaeGlobalVar.ajax_url,
              method: 'post',
              data: {
                  action : 'eae_elements_save',
                  moduleData,
                  nonce : eaeGlobalVar.nonce
              },
              success: function(res){

                  const modules = res.modules;

                  for(module in modules){
                      if (modules.hasOwnProperty(module)) {

                          const status = modules[module];

                          const module_anchor = document.querySelector(`[data-moduleid='${module}']`);

                          if(module_anchor === null){
                              continue;
                          }

                          if(status === 'false'){
                              module_anchor.textContent = 'Activate';
                              module_anchor.dataset.action = 'activate';
                              module_anchor.parentElement.parentElement.classList.remove('eae-enabled');
                              module_anchor.parentElement.parentElement.classList.add('eae-disabled');
                          }else{
                              module_anchor.textContent = 'Deactivate';
                              module_anchor.dataset.action = 'deactivate';
                              module_anchor.parentElement.parentElement.classList.remove('eae-disabled');
                              module_anchor.parentElement.parentElement.classList.add('eae-enabled');
                          }

                          module_anchor.classList.remove('updating');
                          // uncheck all checkboxes
                          moduleCBs = document.querySelectorAll('.eae-module-item');
                          moduleCBs.forEach( modulecb => modulecb.checked = false );
                      }
                  }

              }
          });
      });

      // Save Config

      saveConfig.addEventListener('click', function(e){
          let wts_eae_youtube_api_key = '';
          const wts_eae_gmap_key = document.querySelector('#wts_eae_gmap_key').value;
          if(document.querySelector('#wts_eae_youtube_api_key') !== null){
             wts_eae_youtube_api_key = document.querySelector('#wts_eae_youtube_api_key').value;
          }
          //const use_tsParticle = document.querySelector('#use_tsParticle').checked;
          //const eae_particle_library = document.querySelector('[name="eae-particle-library"]').value;
          //console.log(use_tsParticle);
          
          const btn = this;

          btn.classList.add('loading');

          $.ajax({
              url: eaeGlobalVar.ajax_url,
              method: 'post',
              data: {
                  action: 'eae_save_config',
                  config: {
                      wts_eae_gmap_key,
                      wts_eae_youtube_api_key
                      //eae_particle_library
                  },
                nonce: eaeGlobalVar.nonce
              },
              
              success: function(res){
                if(res.success == 1){
                    btn.classList.remove('loading');
                }   
              }
          })
      })

      async function postData(url = '', data = {}) {

          // Prepare form data
          let formData = new FormData();
          for (let [key, value] of Object.entries(data)) {
              formData.append(key, value);
          }
          
          // Default options are marked with *
          const response = await fetch(url, {
          method: 'POST', // *GET, POST, PUT, DELETE, etc.
          mode: 'cors', // no-cors, *cors, same-origin
          cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
          credentials: 'same-origin', // include, *same-origin, omit
          headers: {
              //'Content-Type': 'application/json'
              // 'Content-Type': 'application/x-www-form-urlencoded',
              'Content-Type': 'multipart/form-data'
          },
          redirect: 'follow', // manual, *follow, error
          referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
          body: formData // body data type must match "Content-Type" header
          });
          return response.json(); // parses JSON response into native JavaScript objects
      }

  }
});    