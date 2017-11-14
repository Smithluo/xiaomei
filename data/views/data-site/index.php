<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width,initial-scale=1">
    <meta http-equiv=X-UA-Compatible content="ie=edge">
    <title>小美诚品-数据分析</title></head>
<body>
<div id=app>
    <canvas></canvas>
</div>
<script>document.addEventListener('touchmove', function (e) {
        e.preventDefault();
    })
    var
        c = document.getElementsByTagName('canvas')[0],
        x = c.getContext('2d'),
        pr = 1,
        w = window.innerWidth,
        h = window.innerHeight,
        f = 90,
        q,
        r = 0,
        turn = Math.PI * 2,
        cos = Math.cos,
        random = Math.random;
    c.width = w * pr;
    c.height = h * pr;
    x.scale(pr, pr);
    x.globalAlpha = 0.6;

    function y(p) {
        var t = p + (random() * 2 - 1.1) * f;
        return (t > h || t < 0) ? y(p) : t;
    }

    function i() {
        x.clearRect(0, 0, w, h);
        q = [
            {
                x: 0,
                y: h * .7 + f
            },
            {
                x: 0,
                y: h * .7 - f
            }
        ];
        while (q[1].x < w + f) {
            d(q[0], q[1]);
        }
    }

    function d(i, j) {
        x.beginPath();
        x.moveTo(i.x, i.y);
        x.lineTo(j.x, j.y);
        var
            k = j.x + (random() * 2 - 0.25) * f,
            n = y(j.y);
        x.lineTo(k, n);
        x.closePath();
        r -= turn / -50;
        x.fillStyle = '#' + (cos(r) * 127 + 128 << 16 | cos(r + turn / 3) * 127 + 128 << 8 | cos(r + turn / 3 * 2) * 127 + 128).toString(16);
        x.fill();
        q[0] = q[1];
        q[1] = {
            x: k,
            y: n
        };
    }

    document.onclick = i;
    document.ontouchstart = i;
    i()</script>
<script type=text/javascript src=http://data.xiaomei360.com/static/manifest.js></script>
<script type=text/javascript src=http://data.xiaomei360.com/static/vendor.js></script>
<script type=text/javascript src=http://data.xiaomei360.com/static/index.js></script>
</body>
</html>