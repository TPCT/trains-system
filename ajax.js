    function sendRequest(url, onload, data=null, isPost=true){
        let body = ""
        function parseData(data){
            let body = "";
            if (typeof data === 'object'){
                for(const key in data)
                    body += `${key}=${data[key]}&`;
            }else if(typeof data === 'string'){
                body = data;
            }else{
                throw new Error("data either be string or object to be send");
            }
            return body;
        }

        if (!isPost) {
            body = parseData(data);
            url += `?${body}`;
        }else {
            body = parseData(data);
        }

        const request = new XMLHttpRequest();
        request.onload = onload;
        request.open(isPost ? "POST" : "GET", url, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(body);
    }
