define(
    [
        "https://aplazame.com/static/aplazame.js",
    ],
    function (aplazameSdk) {
        return function(config) {
            aplazameSdk.init({
                sandbox: config.sandbox,
                publicKey: config.publicKey,
                host: config.host,
            });

            return aplazameSdk;
        }
    }
);
