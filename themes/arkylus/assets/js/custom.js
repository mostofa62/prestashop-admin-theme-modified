/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */

$("#submit-otp").click(sendOtp);
$("#submit-otp-resend").click(resendOtp);
function sendOtp(){


    data = $("#otp-form").serialize();
    $.ajax({
        type: 'POST',
        url: url,
        cache: false,
        data: data,
        success: function (d) {
            //console.log(d.result);
            if(d.result > 0){
                $("#submit-otp").addClass('hide');
                $("#mobile_no").addClass('hide');
                $("[name^='continue']").removeClass('hide');
                $("#otp").removeClass('hide');
                $("#submit-otp-resend").removeClass('hide');
                if( $('#otp-error-block').length > 0 ){
                    $('#otp-error-block').remove();
                }

                $("<div id='otp-error-block' class='help-block'><ul><li class='alert alert-success'>"+d.msg+"</li></ul></div>").insertBefore("#otp-form");

            }else{

                $("<div id='otp-error-block' class='help-block'><ul><li class='alert alert-danger'>"+d.msg+"</li></ul></div>").insertBefore("#otp-form");
                
            }
        }
    });

  
}



function resendOtp(){


    data = $("#otp-form").serialize();
    $.ajax({
        type: 'POST',
        url: url,
        cache: false,
        data: data,
        success: function (d) {
            //console.log(d.result);
            if(d.result > 0){
                //$("#submit-otp").addClass('hide');
                //$("#mobile_no").addClass('hide');
                //$("[name^='continue']").removeClass('hide');
                //$("#otp").removeClass('hide');
                //$("#submit-otp-resend").removeClass('hide');
                if( $('#otp-error-block').length > 0 ){
                    $('#otp-error-block').remove();
                }

                $("<div id='otp-error-block' class='help-block'><ul><li class='alert alert-success'>"+d.msg+"</li></ul></div>").insertBefore("#otp-form");


            }else{

                $("<div id='otp-error-block' class='help-block'><ul><li class='alert alert-danger'>"+d.msg+"</li></ul></div>").insertBefore("#otp-form");
                
            }
        }
    });

  
}