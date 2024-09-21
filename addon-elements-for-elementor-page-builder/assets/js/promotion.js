(function ($) {
 
        parent.document.addEventListener("mousedown", function (e) {
            var widgets = parent.document.querySelectorAll(".elementor-element--promotion");
            if (widgets.length > 0) {
                for (var i = 0; i < widgets.length; i++) {
                    if (widgets[i].contains(e.target)) {
                        var dialog = parent.document.querySelector("#elementor-element--promotion__dialog");
                        var icon = widgets[i].querySelector(".icon > i");
                        var widget_title = widgets[i].querySelector(".title-wrapper > .title");
                        if (icon.classList.contains('pro-widget')) {
                            dialog.classList.add('eae-pro-widget');
                            dialog.querySelector(".dialog-buttons-message").innerHTML = 'Use ' + widget_title.innerHTML + '  and many other pro features to extend your toolbox and build your site faster and better.';
                            if (dialog.querySelector("a.eae-pro-dialog-buttons-action") === null) {
                                var button = document.createElement("a");
                                var buttonText = document.createTextNode("Upgrade to Pro");
                                button.setAttribute("href", "https://wpvibes.link/go/eae-upgrade/");
                                button.setAttribute("target", "_blank");
                                button.classList.add(
                                    "dialog-button",
                                    "dialog-action",
                                    "dialog-buttons-action",
                                    "elementor-button",
                                    "elementor-button-success",
                                    "eae-pro-dialog-buttons-action"
                                );
                                button.appendChild(buttonText);
                                dialog.querySelector(".dialog-buttons-action").insertAdjacentHTML("afterend", button.outerHTML);
                                dialog.querySelector(".eae-pro-dialog-buttons-action").style.display = "inline-block";
                                dialog.querySelector(".eae-pro-dialog-buttons-action").style.textAlign = "center"; 
                                dialog.querySelector(".elementor-button.go-pro.dialog-buttons-action").classList.add('eae-pro-hide');
                            } else {
                                dialog.querySelector(".eae-pro-dialog-buttons-action").style.display = "inline-block";
                                dialog.querySelector(".eae-pro-dialog-buttons-action").style.textAlign = "center"; 
                                dialog.querySelector(".elementor-button.go-pro.dialog-buttons-action").classList.add('eae-pro-hide');
                            }
                        } else {
                            dialog.classList.remove('pro-widget');
                            dialog.querySelector(".dialog-buttons-action").style.display = "block";
                            if (dialog.querySelector(".eae-pro-dialog-buttons-action") !== null) {
                                dialog.querySelector(".eae-pro-dialog-buttons-action").style.display = "none";
                            }
                        }
                        // stop loop
                        break;
                    }
                }
            }
        });
})(jQuery);    
