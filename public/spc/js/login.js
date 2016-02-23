/**
 * Copyright (c) 2009 Smart PHP Calendar
 */
$(function() {

    $("#username").trigger("focus");

    $.ajaxSetup({
        type: "post",
        dataType: "json",
        url: "SpcEngine.php"
    });

	function isMobile() {
		var uA = window.navigator.userAgent,
			mobileDevices = ["android",
							 "iphone",
							 "ipad",
							 "blackberry",
							 "palm"],
			re = new RegExp("");

		for (var i = 0; i < mobileDevices.length; i++) {
			// ! safari doesn't return RegExp object from compile method
			re.compile(mobileDevices[i], "i");

			if (re.test(uA)) {
				return true;
			}
		}

		return false;
	}

	$('#do-login').bind("click", function(e) {
		var username = $('#username').val(),
			password = $('#password').val();

		$("#statusMsg")
            .addClass("error")
            .text("Please wait.")
            .slideDown("slow");

		if (username == "" || password == "") {
			$("#statusMsg")
			.addClass("error")
			.text("Please type your username and password.")
			.slideDown("slow");
            return false;
		}

		$.ajax({
			type: 'post',
			dataType: 'json',
			url: 'SpcEngine.php',
			data: {
				sender: "login",
				doLogin: true,
                spcAppRequest: "core/login/checkLogin",
				params: [username, password]
			},
			success: function(res) {
				if (res.success === false) {
					$("#statusMsg").addClass("error").text(res.errorMsg).slideDown("slow");
				} else {
					if (isMobile()) {
						window.location.href = "m";
					} else {
						var a = window.location.toString().split("/");
						a.pop();
						var redirect = a.join("/");

						window.location.href = redirect;
					}
				}
			}
		});

		return false;
	});

    $("#forgot-pass").on("click", function() {
        $("#reset-pass-dialog").dialog("open");
    });

    $("#reset-pass-dialog").dialog({
        title: "Reset Password",
        autoOpen: false,
        modal: true,
        buttons: {
            "Cancel": function() {
                $("#reset-pass-dialog").dialog("close");
            },
            "Reset Password": function() {
                var email = $("#reset-pass-dialog-email").val();
                $("#reset-pass-dialog").dialog("close");
                $("#statusMsg").addClass("error").text("Please wait...").slideDown("slow");
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: 'SpcEngine.php',
                    data: {
                        sender: "login",
                        doLogin: true,
                        spcAppRequest: "core/login/resetPass",
                        params: [email]
                    },
                    success: function(res) {
                        if (res.success === false) {
                            $("#statusMsg").addClass("error").text(res.errorMsg).slideDown("slow");
                        } else {
                            $("#statusMsg").addClass("error").text("Your new password has been sent to your email.").slideDown("slow");
                        }
                    }
                });
            }
        }
    });

    //
    // jQuery SmartCaptcha Plugin
    // Copyright (c) Yasin Dağlı
    //

    $.fn.smartCaptcha = function(options) {
        options = $.extend({imgPath: "js/smart-captcha/img"}, options);
        var $this = $(this),
            images = ["bell.png", "briefcase.png", "clock.png", "star.png"],
            dragImg = images[Math.floor(Math.random() * 4)],
            dragImgName = dragImg.split(".")[0];

        $this
            .addClass("smart-captcha-wrapper")
            .data("validCaptcha", false);

        var imgList = "";
        $.each(images, function(i, img) {
            imgList += "<img src='" + options.imgPath + "/" + img + "' data-img-name='" + img + "' />";
        });

        var dropBox = "<div class='smart-captcha-dropbox'>Drag '" + dragImgName + "' here</div>";
        $this.html(imgList + dropBox);

        $this.find("img[data-img-name='" + dragImg + "']").draggable({
        });
        $this.find(".smart-captcha-dropbox").droppable({
            drop: function(e, ui) {
                $this.data("validCaptcha", true);
                ui.draggable.remove();
                $(this).html("Done");
            }
        });

    };

    $("#smart-captcha").smartCaptcha();

    //
    // signup
    //

    $("#signup-dialog").dialog({
        title: "Smart PHP Calendar Signup",
        width: "auto",
        height: "auto",
        modal: true,
        autoOpen: false,
        buttons: {
            "Cancel": function() {
                $("#signup-dialog").dialog("close");
            },
            "Submit": function() {
                var allFieldsValidated = true;
                $("#signup-table").find(".validate").each(function() {
                    if ($(this).attr("data-validated") == "0") {
                        allFieldsValidated = false;
                        return false;
                    }
                });

                if (!allFieldsValidated) {
                    alert("Please fill the required fields!");
                    return;
                }

                if (!$("#smart-captcha").data("validCaptcha")) {
                    alert("Invalid captcha!");
                    return;
                }

                if (signupFormValid && $("#smart-captcha").data("validCaptcha")) {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: "SpcEngine.php",
                        data: {
                            sender: "public",
                            spcAppRequest: "core/user/signup",
                            params: {
                                isArray: true,
                                first_name: $("#signup-form-first-name").val(),
                                last_name: $("#signup-form-last-name").val(),
                                company: $("#signup-form-company").val(),
                                username: $("#signup-form-username").val(),
                                password: $("#signup-form-password").val(),
                                email: $("#signup-form-email").val(),
                                mobile: $("#signup-form-mobile").val(),
                                timezone: $("#signup-form-timezone").val()
                            }
                        },
                        success: function(res) {
                            if (res.success == true) {
                                window.location.href = "index.php";
                            }
                        }
                    });
                }
            }
        }
    });

    $("#signup").on("click", function(e) {
        $("#signup-dialog").dialog("open");
    });

    function isRFC822ValidEmail(sEmail) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(sEmail);
    }

    //signup form validators
    var $this,
        signupFormValid;
    $("#signup-table")

        //username
        .on("blur", "#signup-form-username", function(e) {
            $this = $(this);
            $this.attr("data-validated", 1);
            $this.parents("tr").find(".err").hide();
            signupFormValid = true;

            if ($this.val() == "") {
                $this.parents("tr").find(".err").text("Username is missing").show();
                signupFormValid = false;
                return;
            }

            if ($this.val().length < 3) {
                $this.parents("tr").find(".err").text("Username must be at least 3 characters").show();
                signupFormValid = false;
                return;
            }

            if (!/^[a-zA-Z0-9\-\_\.]{8,}$/.test($this.val())) {
                $this.parents("tr").find(".err").text("Oops, that username doesn't appear to be valid. Please try again.").show();
                signupFormValid = false;
                return;
            }

            $.ajax({
                data: {
                    spcAppRequest: "core/user/getUser",
                    params: [$this.val(), "username"]
                },
                success: function(res) {
                    if (res.user) {
                        $this.parents("tr").find(".err").text("This username is not available").show();
                        signupFormValid = false;
                    }
                }
            });
        })

        //password
        .on("blur", "#signup-form-password", function(e) {
            $this = $(this);
            $this.attr("data-validated", 1);
            $this.parents("tr").find(".err").hide();
            signupFormValid = true;

            if ($this.val() == "") {
                $this.parents("tr").find(".err").text("Password is missing").show();
                signupFormValid = false;
                return;
            }

            if ($this.val().length < 6) {
                $this.parents("tr").find(".err").text("Password must be at least 6 characters").show();
                signupFormValid = false;
                return;
            }
        })

        //password-match
        .on("blur", "#signup-form-re-password", function(e) {
            $this = $(this);
            $this.attr("data-validated", 1);
            $this.parents("tr").find(".err").hide();
            signupFormValid = true;
            if ($this.val() !== $("#signup-form-password").val()) {
                $this.parents("tr").find(".err").show();
                signupFormValid = false;
            }
        })

        //email
        .on("blur", "#signup-form-email", function(e) {
            $this = $(this);
            $this.attr("data-validated", 1);
            $this.parents("tr").find(".err").hide();
            signupFormValid = true;

            if ($this.val() == "") {
                $this.parents("tr").find(".err").text("Email is missing").show();
                signupFormValid = false;
                return;
            }

            if (!isRFC822ValidEmail($this.val())) {
                $this.parents("tr").find(".err").text("Email is not valid").show();
                signupFormValid = false;
                return;
            }

            $.ajax({
                data: {
                    spcAppRequest: "core/user/getUser",
                    params: [$this.val(), "email"]
                },
                success: function(res) {
                    if (res.user) {
                        $this.parents("tr").find(".err").text("This email is not available").show();
                        signupFormValid = false;
                    }
                }
            });
        })

        //first name
        .on("blur", "#signup-form-full-name", function(e) {
            $this = $(this);
            $this.attr("data-validated", 1);
            $this.parents("tr").find(".err").hide();
            signupFormValid = true;

            if ($this.val() == "") {
                $this.parents("tr").find(".err").text("First name is missing").show();
                signupFormValid = false;
                return;
            }

            if (!/^.{2,16}$/.test($this.val())) {
                $this.parents("tr").find(".err").show();
                signupFormValid = false;
            }
        })

        //lastt name
        .on("blur", "#signup-form-last-name", function(e) {
            $this = $(this);
            $this.attr("data-validated", 1);
            $this.parents("tr").find(".err").hide();
            signupFormValid = true;

            if ($this.val() == "") {
                $this.parents("tr").find(".err").text("Oops, it looks like your last name is missing").show();
                signupFormValid = false;
                return;
            }

            if (!/^.{2,16}$/.test($this.val())) {
                $this.parents("tr").find(".err").show();
                signupFormValid = false;
            }
        })

        //init all input to be validated to 0
        .find(".validate").each(function() {
            $(this).attr("data-validated", 0);
        });

    //
    // signup
    //
});