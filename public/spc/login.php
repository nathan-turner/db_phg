<?php
//TODO: add Spc::checkMobileAgent() method
if (preg_match('/iphone|android|blackberry/i', $_SERVER['HTTP_USER_AGENT'])) {
    header('Location: m/login.php');
}

require 'SpcEngine.php';
if (isset($_SESSION['spcUserLoggedIn'])) {
	header("Location: index.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Smart PHP Calendar - Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <script src="js/jquery.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/login.js?v=1.5"></script>

    <link rel="stylesheet" href="css/login.css" />
    <link rel="stylesheet" href="css/theme/smart-white/jquery-ui.css" />

    <script>
        $(function() {
            if (/signup/i.test(window.location.search)) {
                $("#signup-dialog").dialog("open");
            }
        });
    </script>
</head>

<body>
	<div id="container">
		<div class="login-form-top">
			<h1 class="header1">Smart PHP Calendar - User Login</h1>
			<form>
				<table id="login-table">
					<tr> <td colspan="2"><div id="statusMsg"></div></td></tr>
					<tr> <td>Username : </td> <td> <input id="username" type="text" value="" /> </td> </tr>
					<tr> <td>Password : </td> <td> <input id="password" type="password" value="" /> </td> </tr>
					<tr> <td></td><td style="text-align: left;"><input type="submit" id="do-login" value="Login" /></td></tr>
				</table>
			</form>
			<div class="login-form-bottom"></div>
            <div id="bottom-links">
                <a href="#" id="forgot-pass">Forgot Password?</a>
            </div>
		</div>

        <div id="reset-pass-dialog">
            Email: <input type="text" id="reset-pass-dialog-email" />
        </div>

        <div id="signup-dialog">
            <table id="signup-table">
                <tbody>
                    <tr>
                        <td>
                            <label for="signup-form-first-name">First Name</label>
                        </td>
                        <td>
                            <input type="text" id="signup-form-first-name" />
                        </td>
                        <td>
                            <div class="err">Invalid first name</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-last-name">Last Name</label>
                        </td>
                        <td>
                            <input type="text" id="signup-form-last-name" />
                        </td>
                        <td>
                            <div class="err">Invalid last name</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-full-name">Company or Organization</label>
                        </td>
                        <td>
                            <input type="text" id="signup-form-company" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-username">Username</label>
                        </td>
                        <td>
                            <input type="text" id="signup-form-username" />
                        </td>
                        <td>
                            <div class="err">Invalid username</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-password">Password</label>
                        </td>
                        <td>
                            <input type="password" id="signup-form-password" />
                        </td>
                        <td>
                            <div class="err">Invalid password</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-re-password">Retype Password</label>
                        </td>
                        <td>
                            <input type="password" id="signup-form-re-password" />
                        </td>
                        <td>
                            <div class="err">Passwords don't match</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-email">Email</label>
                        </td>
                        <td>
                            <input type="text" id="signup-form-email" />
                        </td>
                        <td>
                            <div class="err">Invalid email</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="signup-form-timezone">Timezone</label>
                        </td>
                        <td>
                            <select id="signup-form-timezone">
                                <?php
                                    foreach (Spc::getTimezones() as $zone => $tz) {
                                        echo '<option value="' . $tz . '">' . $zone . '</option>';
                                    }
                                ?>
                            </select>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Captcha</label>
                        </td>
                        <td colspan="2">
                            <div id="smart-captcha"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
	</div>
</body>
</html>