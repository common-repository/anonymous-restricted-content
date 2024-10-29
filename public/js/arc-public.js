jQuery(document).ready(function( $ ) {

  if ( $('body').hasClass('arc-content-blur') ) {

      var ajaxUrl = $("#arc-ajax-login-url").val();
      var nonceValue = $("#arc-ajax-security").val();

      if ( !ajaxUrl || !nonceValue ) {
        console.log("Error: ARC plugin requires wp_body_open action.");
        return false;
      }

      var loginContainer = jQuery('<div/>', { id: 'arc-ajax-login-container', "class": 'arc-ajax-login-container'});

      var loginForm = jQuery('<form/>', { "class": 'arc-ajax-login-form'});

      loginForm.submit(function( e ) {
        e.preventDefault();

        $("#arc-ajax-login-status-text", loginForm).html(ArcPubLStrings.SendingUserInfo);
        $("#arc-ajax-login-submit-btn", loginForm).prop( "disabled", true );


          $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxUrl,
            data: {
                'action': 'arcajaxlogin',
                'username': $('#arc-ajax-login-username-input', loginForm).val(),
                'password': $('#arc-ajax-login-password-input', loginForm).val(),
                'arc-ajax-security': nonceValue },
            success: function(data){
                $("#arc-ajax-login-status-text", loginForm).html(data.message);

                if (data.loggedin == true) {

                  $('body').removeClass('arc-content-blur');
                  loginContainer.remove();

                  window.location.reload(false);

                } else if ( data.error == true ) {
                  $("#arc-ajax-login-submit-btn", loginForm).prop( "disabled", false );
                }
            },
            error: function(){
              $("#arc-ajax-login-status-text", loginForm).html(ArcPubLStrings.LogInFailed);
              $("#arc-ajax-login-submit-btn", loginForm).prop( "disabled", false );
            }
          });


      });

      jQuery('<h5/>').html(ArcPubLStrings.RestrictedContent).appendTo(loginForm);
      jQuery('<p/>').html(ArcPubLStrings.PleaseLogIn).appendTo(loginForm);
      jQuery('<label/>', { for: 'arc-ajax-login-username-input'}).html(ArcPubLStrings.Username).appendTo(loginForm);
      jQuery('<input/>', { "type": 'text', id: 'arc-ajax-login-username-input'}).appendTo(loginForm);
      jQuery('<label/>', { for: 'arc-ajax-login-password-input'}).html(ArcPubLStrings.Password).appendTo(loginForm);
      jQuery('<input/>', { "type": 'password', id: 'arc-ajax-login-password-input'}).appendTo(loginForm);
      jQuery('<p/>', { id: 'arc-ajax-login-status-text'}).appendTo(loginForm);
      jQuery('<input/>', { "type": 'submit', "class": "submit_button", "value": ArcPubLStrings.LogIn, id: 'arc-ajax-login-submit-btn'}).appendTo(loginForm);
      jQuery('<p/>').appendTo(loginForm);
      jQuery('<input/>', { "type": 'button',
                            "class": "cancel_button",
                            "value": ArcPubLStrings.GoBack,
                            "id": 'arc-login-go-back-btn',
                            "click": function() {
                              history.back();
                              return false;
                            }

                          }).appendTo(loginForm);

      loginForm.appendTo(loginContainer);

      loginContainer.appendTo('body');
  }

});
