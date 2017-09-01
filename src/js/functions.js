function text2bin(text) {
    var bin = '';
    jQuery.each(text.split(""), function (i, x) {
        var c = x.charCodeAt(0).toString(2);
        var c0 = '';
        for (var j = c.length; j < 8; j++) {
            c0 += '0';
        }
        bin += c0 + c + ' ';
    });
    return '<br>' + bin;
}
