"use strict";

var app = require('express')();

app.get('/nofollow', function (req, res) {
    console.log('Request at /nofollow');

    res.writeHead(200, { 'X-Robots-Tag': 'nofollow' });

    res.end();
});

app.get('/nofollow-noindex', function (req, res) {
    console.log('Request at /nofollow-noindex');

    res.writeHead(200, { 'X-Robots-Tag': 'nofollow, noindex' });

    res.end();
});

app.get('/nofollow-noindex-google', function (req, res) {
    console.log('Request at /nofollow-noindex-google');

    res.writeHead(200, { 'X-Robots-Tag': 'google: nofollow, noindex' });

    res.end();
});

app.get('/none', function (req, res) {
    console.log('Request at /none');

    res.writeHead(200, { 'X-Robots-Tag': 'none' });

    res.end();
});

app.get('/none-google', function (req, res) {
    console.log('Request at /none-google');

    res.writeHead(200, { 'X-Robots-Tag': 'google: none' });

    res.end();
});

app.get('/robots.txt', function (req, res) {
    console.log('Request at /robots.txt');

    const content = `# robotstxt.org/

User-agent: *

Disallow: /nl/admin/
Disallow: /en/admin/`;

    res.writeHead(200);

    res.end(content);
});

app.get('/nl', function (req, res) {
    console.log('Request at /nl');

    res.writeHead(200);

    res.end();
});

app.get('/nl/admin', function (req, res) {
    console.log('Request at /nl/admin');

    res.writeHead(200);

    res.end();
});

app.get('/client-ua-must-match', function (req, res) {
    const userAgent = req.headers['user-agent'];

    return res.status(userAgent === 'test-user-agent' ? 200 : 400).end()
});

var server = app.listen(4020, function () {
    var host = 'localhost';
    var port = server.address().port;

    console.log('Testing server listening at http://%s:%s', host, port);
});
