"use strict";

var app = require('express')();

app.get('/nofollow', function (req, res) {
    console.log('Request at /nofollow');

    res.writeHead(200, {
        'X-Robots-Tag': 'nofollow'
    });

    res.end();
});

app.get('/nofollow-noindex', function (req, res) {
    console.log('Request at /nofollow-noindex');

    res.writeHead(200, {
        'X-Robots-Tag': 'nofollow, noindex'
    });

    res.end();
});

app.get('/nofollow-noindex-google', function (req, res) {
    console.log('Request at /nofollow-noindex-google');

    res.writeHead(200, {
        'X-Robots-Tag': 'google: nofollow, noindex'
    });

    res.end();
});

app.get('/robots.txt', function (req, res) {
    console.log('Request at /robots.txt');

    const content = `# robotstxt.org/

user-agent: *

disallow: /nl/admin/
disallow: /en/admin / `;

    res.writeHead(200);

    res.end(content);
});

app.get('/nl', function (req, res) {
    console.log('Request at /nl');

    res.writeHead(200);

    res.end();
});

var server = app.listen(4020, function () {
    var host = 'localhost';
    var port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
