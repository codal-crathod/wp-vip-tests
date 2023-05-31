let styles = "<style type='text/css'>#social-proof {\n" +
    "        position: fixed;\n" +
    "        right: 20px;\n" +
    "        bottom: 20px;\n" +
    "        width: 270px;\n" +
    "        background: rgb(252, 109, 94);\n" +
    "        color: #fff;\n" +
    "        padding: 8px;\n" +
    "        border-radius: 8px;\n" +
    "        z-index: 999;\n" +
    "        opacity: 0;\n" +
    "        display: flex;\n" +
    "        flex-direction: row;\n" +
    "        align-items: center;\n" +
    "        gap: 10px;\n" +
    "        text-align: center;\n" +
    "    }\n" +
    "\n" +
    "    @media (min-width: 500px) { \n" +
    "       #social-proof {\n" +
    "        width: 300px;\n" +
    "    }\n" +
    "\n" +
    "    #social-proof .content > p:first-of-type {\n" +
    "        font-size: 16px !important;\n" +
    "    }\n" +
    "\n" +
    "    #social-proof .content > p:nth-of-type(2) {\n" +
    "        font-size: 14px !important;\n" +
    "        line-height: 24px !important;;\n" +
    "    }\n" +
    "    #social-proof img {\n" +
    "        max-width: 80px !important;\n" +
    "    }\n" +
    "}\n" +
    "\n" +
    "    #social-proof .content > p:last-of-type {\n" +
    "        font-size: 14px !important;;\n" +
    "        line-height: 24px !important;;\n" +
    "    }\n" +
    "    #social-proof img {\n" +
    "        max-width: 60px;\n" +
    "        background-color: #fff;\n" +
    "        border-radius: 8px;\n " +
    "    }\n" +
    "\n" +
    "    #social-proof .content p {\n" +
    "        margin: 0;\n" +
    "    }\n" +
    "\n" +
    "    #social-proof .content {\n" +
    "        width: 100%;\n" +
    "    }\n" +
    "\n" +
    "    #social-proof .content .member {\n" +
    "        color: #fff;\n" +
    "    }\n" +
    "\n" +
    "    #social-proof .content > p:first-of-type {\n" +
    "        font-size: 14px;\n" +
    "        font-weight: bold;\n" +
    "        line-height: 24px;\n" +
    "    }\n" +
    "\n" +
    "    #social-proof .content > p:nth-of-type(2) {\n" +
    "        font-size: 12px;\n" +
    "        line-height: 16px;\n" +
    "        font-weight: 700;\n" +
    "        text-transform: uppercase;\n " +
    "    }\n" +
    "\n" +
    "    #social-proof .content > p:last-of-type {\n" +
    "        font-size: 12px;\n" +
    "        line-height: 20px;\n" +
    "        font-weight: 700;\n" +
    "    }\n" +
    "\n" +
    "    @keyframes fadeOutDown {\n" +
    "        0% {\n" +
    "            opacity: 1;\n" +
    "            transform: translateY(0);\n" +
    "        }\n" +
    "        100% {\n" +
    "            opacity: 0;\n" +
    "            transform: translateY(20px);\n" +
    "        }\n" +
    "    }\n" +
    "\n" +
    "    @keyframes fadeInUp {\n" +
    "        from {\n" +
    "            transform: translateY(40px);\n" +
    "        }\n" +
    "\n" +
    "        to {\n" +
    "            transform: translateY(0);\n" +
    "            opacity: 1\n" +
    "        }\n" +
    "    }\n" +
    "\n" +
    "    #social-proof.fadeInUp {\n" +
    "        opacity: 0;\n" +
    "        animation-name: fadeInUp;\n" +
    "        -webkit-animation-name: fadeInUp;\n" +
    "}</style>";

document.getElementsByTagName('head')[0].innerHTML += styles;


let fadeOutStyles = "<style type='text/css'>#social-proof {\n" +

    "        animation-duration: 1s;\n" +
    "        animation-fill-mode: both;\n" +
    "        -webkit-animation-duration: 1s;\n" +
    "        -webkit-animation-fill-mode: both;\n" +
    "        animation-name: fadeOutDown;\n" +
    "        -webkit-animation-name: fadeOutDown;\n" +
    "    }\n" +
    "</style>";

var socialProofElement = document.createElement('div');
socialProofElement.setAttribute("id", "social-proof");
socialProofElement.innerHTML = '<img id="survey-logo" src="https://disqo.com/wp-content/themes/disqo2021/assets/img/survey-junkie.png"><div class="content"><p><span class="member"><span id="initials"></span> from <span id="location"></span></p>\n' +
    '    <p>cashed out $<span id="amount"></span></p>\n' +
    '    <p>via <span id="payment_method"></span></p></div>';
document.getElementsByTagName('body')[0].prepend(socialProofElement);

getData();

let socialProofData = [];

function formatMoney(number, decPlaces, decSep, thouSep) {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSep = typeof decSep === "undefined" ? "." : decSep;
    thouSep = typeof thouSep === "undefined" ? "," : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    var j = (j = i.length) > 3 ? j % 3 : 0;

    return sign +
        (j ? i.substr(0, j) + thouSep : "") +
        i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
        (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
}

function showSocialProof(data) {
    setTimeout(function () {
        socialProofData = data;
        document.getElementsByTagName('head')[0].innerHTML += fadeOutStyles;
        document.getElementById('social-proof').classList.add('fadeInUp')
        document.getElementById('initials').innerHTML = data[0]['first_initial'] + data[0]['last_initial'];
        document.getElementById('location').innerHTML = data[0]['location'];
        document.getElementById('amount').innerHTML = formatMoney(data[0]['amount']);
        document.getElementById('payment_method').innerHTML = data[0]['redemption_method'];
        setTimeout(function () {
            document.getElementById('social-proof').classList.remove('fadeInUp');

            updateContent(data);
        }, 5000)

        let socialProofTimer = setInterval(function () {
            let socialProof = document.getElementById('social-proof');
            socialProof.classList.add('fadeInUp');
            window.setTimeout(function () {
                socialProof.classList.remove('fadeInUp');
                updateContent(socialProofTimer);
            }, 5000)
        }, 30000);
    }, 5000)
}

let counter = 1;

function updateContent(socialProofTimer) {
    window.setTimeout(function () {
        if (!socialProofData[counter]) {
            counter = 0;
        }
        document.getElementById('initials').innerHTML = socialProofData[counter]['first_initial'] + socialProofData[counter]['last_initial'];
        document.getElementById('location').innerHTML = socialProofData[counter].location;
        document.getElementById('amount').innerHTML = formatMoney(socialProofData[counter].amount);
        document.getElementById('payment_method').innerHTML = socialProofData[counter].redemption_method;
        counter++;
    }, 1000)
}


function csvJSON(csv) {
    var lines = csv.split("\n");

    var result = [];

    var headers = lines[0].split(",");

    for (var i = 1; i < lines.length; i++) {

        var arr = {};
        var currentline = lines[i].split(",");
        for (var j = 0; j < headers.length; j++) {
            arr[headers[j].replaceAll('"', '')] = currentline[j].replaceAll('"', '');
        }

        result.push(arr);
    }
    return result;
}

async function getData() {
    // TODO make this working dynamic with files
    // there was an cors error coming from disqo.com
    // const response = await fetch("redemptions-sj.csv");
    // const data = await response.text();
    let data = '"first_initial","last_initial","location","amount","redemption_method"\n' +
        '"S","J","CHULA VISTA",54.9,"PayPal"\n' +
        '"B","P","Chester",54.78,"PayPal"\n' +
        '"G","A","Indianapolis",54.77,"PayPal"\n' +
        '"T","B","Fort Worth",54.7,"PayPal"\n' +
        '"K","O","Diamondhead",54.68,"Bank Transfer"\n' +
        '"C","J","FARGO",54.64,"PayPal"\n' +
        '"A","M","Florence",54.62,"PayPal"\n' +
        '"J","T","Palo Alto",54.5,"Bank Transfer"\n' +
        '"B","P","Chester",54.49,"PayPal"\n' +
        '"J","H","Wheelwright",54.49,"PayPal"\n' +
        '"C","W","Broken Arrow",54.15,"Bank Transfer"\n' +
        '"G","R","ELMHURST",54.03,"Bank Transfer"\n' +
        '"R","H","Syracuse",54.02,"Bank Transfer"\n' +
        '"P","K","Visalia",53.96,"Bank Transfer"\n' +
        '"C","O","Orlando",53.86,"PayPal"\n' +
        '"A","C","Cedar Rapids",53.68,"PayPal"\n' +
        '"L","J","Southlake",53.47,"Bank Transfer"\n' +
        '"L","J","Roseto",53.46,"Bank Transfer"\n' +
        '"R","F","Drums",53.41,"PayPal"\n' +
        '"A","S","Holly Springs",53.38,"PayPal"\n' +
        '"G","H","Marion",53.34,"PayPal"\n' +
        '"Z","L","SAN DIEGO",53.26,"PayPal"\n' +
        '"A","S","Metairie",53.2,"Bank Transfer"\n' +
        '"M","J","Salem",53.1,"PayPal"\n' +
        '"M","S","Palm Bay",53.1,"Bank Transfer"\n' +
        '"J","E","San Diego",53,"PayPal"\n' +
        '"D","W","Wylie",53,"Bank Transfer"\n' +
        '"F","W","Port Saint Lucie",52.97,"Bank Transfer"\n' +
        '"K","E","Vancouver",52.78,"PayPal"\n' +
        '"M","B","Falls Church",52.69,"Bank Transfer"\n' +
        '"B","C","Mountain Home",52.53,"Bank Transfer"\n' +
        '"O","M","Henderson",52.5,"Bank Transfer"\n' +
        '"D","Z","REVERE",52.48,"PayPal"\n' +
        '"J","D","Knoxville",52.44,"Bank Transfer"\n' +
        '"J","W","Leland",52.34,"PayPal"\n' +
        '"S","B","DAYTON",52.24,"Bank Transfer"\n' +
        '"J","F","East Stroudsburg",52.05,"PayPal"\n' +
        '"C","M","Mission Viejo",51.9,"PayPal"\n' +
        '"A","R","Ogden",51.85,"Bank Transfer"\n' +
        '"T","A","SOUTH SAN FRANCISCO",51.8,"Bank Transfer"\n' +
        '"S","B","Woodstock",51.79,"Bank Transfer"\n' +
        '"A","R","Ogden",51.78,"Bank Transfer"\n' +
        '"J","B","Caledonia",51.64,"PayPal"\n' +
        '"P","T","Fort Worth",51.63,"Bank Transfer"\n' +
        '"C","M","Bonney Lake",51.61,"Bank Transfer"\n' +
        '"M","L","York",51.57,"PayPal"\n' +
        '"D","B","Pittsburg",51.34,"Bank Transfer"\n' +
        '"S","S","Marietta",51.31,"PayPal"\n' +
        '"A","S","Metairie",51.3,"Bank Transfer"\n' +
        '"C","Y","Vancouver",51.28,"PayPal"\n' +
        '"J","C","Washington",51.27,"PayPal"\n' +
        '"F","M","Webster",51.22,"PayPal"\n' +
        '"A","H","Myrtle Beach",51.21,"Bank Transfer"\n' +
        '"M","R","Bay City",51.18,"Bank Transfer"\n' +
        '"M","S","Plano",51.15,"Bank Transfer"\n' +
        '"K","H","Hastings",51.15,"Bank Transfer"\n' +
        '"R","D","North Hills",51.14,"PayPal"\n' +
        '"R","B","Richmond",51.11,"Bank Transfer"\n' +
        '"L","D","Tyrone",51.04,"PayPal"\n' +
        '"T","B","LOUISVILLE",51,"Bank Transfer"\n' +
        '"T","P","Celina",50.98,"Bank Transfer"\n' +
        '"A","R","North Salt Lake",50.91,"PayPal"\n' +
        '"J","W","Delmont",50.88,"PayPal"\n' +
        '"A","T","El Paso",50.87,"Bank Transfer"\n' +
        '"L","S","Quakertown",50.86,"Bank Transfer"\n' +
        '"M","L","York",50.81,"PayPal"\n' +
        '"G","A","Arlington",50.8,"PayPal"\n' +
        '"N","B","DENVER",50.8,"Bank Transfer"\n' +
        '"L","M","Palmdale",50.78,"PayPal"\n' +
        '"S","C","MILLINGTON",50.75,"PayPal"\n' +
        '"S","P","Clarks Summit",50.73,"Bank Transfer"\n' +
        '"A","P","MISSION",50.69,"Bank Transfer"\n' +
        '"S","T","Algonquin",50.67,"Bank Transfer"\n' +
        '"C","M","CHANDLER",50.67,"Bank Transfer"\n' +
        '"Y","C","South San Francisco",50.65,"Bank Transfer"\n' +
        '"A","P","Topeka",50.64,"Bank Transfer"\n' +
        '"J","B","Pasco",50.64,"PayPal"\n' +
        '"V","H","Georgetown",50.64,"Bank Transfer"\n' +
        '"K","R","Spring",50.63,"Bank Transfer"\n' +
        '"A","B","Cincinnati",50.6,"Bank Transfer"\n' +
        '"R","S","Las Vegas",50.53,"Bank Transfer"\n' +
        '"M","A","WILLISTON",50.52,"Bank Transfer"\n' +
        '"D","Z","Schenectady",50.51,"Bank Transfer"\n' +
        '"G","C","Cambridge",50.49,"Bank Transfer"\n' +
        '"S","Z","HAMILTON",50.48,"PayPal"\n' +
        '"A","C","Smyrna",50.48,"PayPal"\n' +
        '"J","S","Kyle",50.47,"Bank Transfer"\n' +
        '"D","I","Chelsea",50.44,"PayPal"\n' +
        '"J","H","MESA",50.42,"PayPal"\n' +
        '"G","K","Gorham",50.42,"Bank Transfer"\n' +
        '"L","J","Alton",50.4,"PayPal"\n' +
        '"A","S","Harrison",50.4,"Bank Transfer"\n' +
        '"R","B","Cincinnati",50.39,"Bank Transfer"\n' +
        '"M","T","LOS ANGELES",50.37,"PayPal"\n' +
        '"J","H","Brooklyn",50.37,"Bank Transfer"\n' +
        '"L","D","FAYETTEVILLE",50.37,"PayPal"\n' +
        '"N","S","Tampa",50.33,"PayPal"\n' +
        '"J","D","Knoxville",50.32,"Bank Transfer"\n' +
        '"R","W","Somers Point",50.31,"PayPal"\n' +
        '"B","G","CANTON",50.31,"PayPal"';
    showSocialProof(csvJSON(data));
    return true;
}
