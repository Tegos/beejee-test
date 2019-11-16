function isDefined(v) {
    return typeof v !== "undefined" && v !== null;
}

function strip_tags(s) {
    return s.replace(/(<([^>]+)>)/ig,"");
}

function ajax(url, queryString, onOK, onError) {
    let xhr = new XMLHttpRequest();

    queryString = 'ajax=1&' + queryString;

    xhr.open('POST', url + '?' + queryString);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            if (onOK) {
                onOK(xhr.response);
            }
        } else {
            if (onError) {
                onError(xhr)
            }
        }
    };
    xhr.send(encodeURI(queryString));
}