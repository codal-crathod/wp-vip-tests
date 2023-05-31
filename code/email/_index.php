<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>

        body {
            font-family: Raleway, Calibri, sans-serif;
        }

        h1 {
            text-align: center;
            color: #FF5512;
            margin-top: 40px
        }

        fieldset {
            background-color: transparent;
            max-width: 400px;
            padding: 16px;
            border: dotted 1px #FF5512;
            border-color: #FF5512;
            border-width: 1px;
            -moz-border-radius: 8px;
            -webkit-border-radius: 8px;
            border-radius: 8px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"], select {
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            color: #111;
            border: solid 1px #ccc;
            border-radius: 10px;
        }

        select {
            width: 100%;
        }

        button {
            background: #ff5512;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 20px auto;
            cursor: pointer;
            border-radius: 20px;
        }

        .align-center {
            text-align: center;
        }

        #form {
            margin: 20px;
        }

        #wrapper {
            padding: 25px 40px 40px;
            margin: 40px;
            background: #fff;
            border: dashed 1px #ccc;
        }
    </style>
</head>

<body>
<h1 class="title">Email Signature Generator</h1>

<div id="form">
    <fieldset>
        <legend>Your Information</legend>
        <label for="fn">Full Name </label>
        <input type="text" name="fn" id="fn" required class="changing-inputs" placeholder="Ex: John Doe"><br/>
        <label for="pos">Position </label>
        <input type="text" name="pos" id="pos" required class="changing-inputs"
               placeholder="Ex: Creative Director"><br/>
        <label for="mobile">Mobile </label>
        <input type="text" name="mb" id="mb" required class="changing-inputs" placeholder="Mobile: (+xx) xxx xxx xxxx">
        <br>
        <label for="add">Office Location </label>
        <select name="add" id="add" class="changing-inputs">
            <option value="glendale">Glendale HQ</option>
            <option value="connecticut">Connecticut</option>
            <option value="armenia">Armenia</option>
        </select>
    </fieldset>
    <div class="align-center">
        <button onclick="copyToClipboard('signature')">Copy to Clipboard</button>
    </div>
</div>

<div id="wrapper">
    <div id="signature">
        <table border="0" cellpadding="0" cellspacing="0" id="result_table" style="padding-top: 10px;" width="600">
            <tbody>
            <tr>
                <td style="vertical-align: top;" width="206">
                    <a href="http://www.disqo.com/" target="_blank">
                        <img src="https://www.disqo.com/email/disqo-logo-line-divider-outline.png" width="200">
                    </a>
                </td>
                <td align="left" valign="top"
                    style="
                                        padding-left:11px;
                                        text-decoration: none;
                                        font-family: Calibri, Arial,sans-serif;
                                        color: #000;
                                        line-height:16px;
                                        font-size:18px;
                                        padding-bottom: 0px;
                                        ">
                                        <span>
                                            <strong id="name">Robin Sodaro</strong>
                                        </span>
                    <span style=";
                                            color: #FF8C00;
                                            text-align: center;
                                            font-size: 24px;
                                            ">
                                            <strong style="
                                                   width:7px;
                                                   height: 7px;
                                                   background-color: #FF8C00;
                                                   border-radius: 50%;
                                                   display: inline-block;
                                                ">
                                            </strong>
                                        </span>
                    <span id="position">Creative Director</span><br>
                    <div style="margin-top: 10px;display: none" id="mobile-wrapper">
                        <strong>
                            <span id="mobile" style="font-weight:normal; font-size: 16px;">(M) 201.611.1235</span>
                        </strong>
                    </div>
                <div style="color: #000; text-decoration: none; font-size:14px;padding-top:10px;padding-bottom:10px;padding-left:0;">
                    <span id="address">440 N Brand Blvd., 6th Floor <br>Glendale, CA 91203</span><br>
                </div>
                <div style="padding-left:0;padding-top:5px">
                    <a id="linkedin" href="https://www.linkedin.com/company/disqo/"><img
                            src="https://www.disqo.com/email/disqo-email-linkedin-small.png" width="27"
                            alt="linkedin.com "/></a>
                    <a id="twitter" href="https://twitter.com/disqo"><img
                            src="https://www.disqo.com/email/disqo-email-twitter-small.png" width="27"
                            alt="twitter.com "/></a>
                    <a id="facebook" href="http://facebook.com/godisqo"><img
                            src="https://www.disqo.com/email/disqo-email-facebook-small.png" width="27"
                            alt="facebook.com "/></a>
                    <a id="instagram" href="https://instagram.com/godisqo/"><img
                            src="https://www.disqo.com/email/disqo-email-instagram-small.png" width="27"
                            alt="instagram.com "/></a>
                </div>
                </td>
            </tr>
            </tbody>
        </table>

        <p style="font-size: 11px; font-family: Raleway, Calibri, Arial, sans-serif;" >The information contained in this message may be privileged, confidential and protected from disclosure. If the reader of this message is not the intended recipient, or an employee or agent responsible for delivering this message to the intended recipient, you are hereby notified that any dissemination, distribution or copying of this communication is strictly prohibited. If you have received this communication in error, please notify your representative immediately and delete this message from your computer. Thank you.</p>
    </div>
</div>

<script>
    document.querySelectorAll('.changing-inputs').forEach(item => {
        item.addEventListener('change', generateSignature, false);
        item.addEventListener('keyup', generateSignature, false);
    });

    function generateSignature() {
        document.getElementById('name').innerHTML = document.getElementById('fn').value;
        document.getElementById('position').innerHTML = document.getElementById('pos').value;
        if (document.getElementById('mb').value.length) {
            document.getElementById('mobile').innerHTML = '(M) ' + document.getElementById('mb').value;
            document.getElementById('mobile-wrapper').style.display = 'block';
        } else {
            document.getElementById('mobile').innerHTML = '';
            document.getElementById('mobile-wrapper').style.display = 'none';

        }
        const location = document.getElementById('add').value
        if (location === "glendale") {
            document.getElementById('address').innerHTML = `${"400 N. Brand Blvd., 6th Floor" + "<br>" + "Glendale, CA 91203"}`;
        }
        if (location === "connecticut") {
            document.getElementById('address').innerHTML = `${"75 Glen Rd., Suite 313" + "<br>" + "Newtown, CT 06482"}`;
        }
        if (location === "armenia") {
            document.getElementById('address').innerHTML = `${"16 Halabyan Str" + "<br>" + "Yerevan 0038" + "<br>" + "Armenia"}`;
        }

		var nameLength = document.getElementById('name').innerHTML.length;
		var positionLength = document.getElementById('position').innerHTML.length;
		var finalWidth = 400;
		if(nameLength + positionLength > 20) {
			finalWidth += (nameLength + positionLength) * 6.8;
		}
//		document.getElementById("result_table").style.minWidth = finalWidth + 'px';

    }

    function copyToClipboard(id) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(id));
            range.select().createTextRange();
            document.execCommand("copy");
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(id));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand("copy");
            window.getSelection().removeAllRanges();
            alert("Copied to Clipboard!")
        }
    }
</script>

</body>

</html>