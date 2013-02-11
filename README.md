Collectd to Statsd bridge
-------------------------

Run from a webserver to serve `index.php` and add the following config
to `collectd.conf`:

    <Plugin write_http>
        <URL "http://127.0.0.1:20100/">
            Format "JSON"
        </URL>
    </Plugin>

Remember to uncommend the `write_http` plugin

Now statsd compatible data will be sent to `localhost:8125`
