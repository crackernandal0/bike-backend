<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Deletion Mail - {{ env('APP_NAME') }}</title>
<style>
    @media only screen and (max-width: 600px) {
		.main {
			width: 320px !important;
		}

		table[class="contenttable"] { 
            width: 320px !important;
            text-align: left !important;
        }
        td[class="force-col"] {
	        display: block !important;
	    }
	     td[class="rm-col"] {
	        display: none !important;
	    }
		.mt {
			margin-top: 15px !important;
		}
		*[class].width300 {width: 255px !important;}
		*[class].block {display:block !important;}
		*[class].blockcol {display:none !important;}
		.emailButton{
            width: 100% !important;
        }

        .emailButton a {
            display:block !important;
            font-size:18px !important;
        }

	}
</style>

</head>

<body link="#bf0db3" vlink="#bf0db3" alink="#bf0db3">

    <table class=" main contenttable" align="center"
        style="font-weight: normal;border-collapse: collapse;border: 0;margin-left: auto;margin-right: auto;padding: 0;font-family: Arial, sans-serif;color: #555559;background-color: white;font-size: 16px;line-height: 26px;width: 600px;margin-top:4rem;">
        <tr>
            <td class="border"
                style="border-collapse: collapse;border: 1px solid #eeeff0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;">
                <table
                    style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;border-bottom: 4px solid #bf0db3;box-shadow: 0px 10px 10px rgba(0,0,0,0.06);">
                    <tr>
                        <td colspan="4" valign="top" class="image-section"
                            style="border-collapse: collapse;border: 0;margin: 0;padding: 8px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff;border-bottom: 4px solid #bf0db3;display:flex; align-items:center;justify-content:center;">
                            <img height="60px" width="auto"
                            style="margin: 10px auto;"
                                src="https://femirides.com/assets/images/logo.png" alt="logo">
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="side title"
                            style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;vertical-align: top;background-color: white;border-top: none;">
                            <table
                                style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                <tr>
                                    <td class="head-title"
                                        style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #484849;font-family: Arial, sans-serif;font-size: 28px;line-height: 34px;font-weight: bold; text-align: center;">
                                        <div class="mktEditable" id="main_title">
                                            Account Deletion Mail - {{ env('APP_NAME') }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="top-padding"
                                        style="border-collapse: collapse;border: 0;margin: 0;padding: 5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="grey-block"
                                        style="border-collapse: collapse;border: 0;margin: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff; text-align:start;padding:10px;">
                                        <div class="mktEditable" id="cta">
                                            <ul>
                                             <li>Email: <b>{{$data['email']}}</b></li>
                                             <li>Phone Number: <b>{{$data['phone_number']}}</b></li>
                                             <li>First Name: <b>{{$data['first_name']}}</b></li>
                                             <li>Last Name: <b>{{$data['last_name']}}</b></li>
                                             <li>Reason: <b>{{$data['reason']}}</b></li>
                                             <li>Type: <b>{{$data['type']}}</b></li>
                                             <li>Details: <b>{{$data['details']}}</b></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
