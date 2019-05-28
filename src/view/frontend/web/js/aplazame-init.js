define(
    [
        "https://cdn.aplazame.com/aplazame.js",
    ],
    function (aplazameSdk) {
        return function (config) {
            aplazameSdk.init({
                sandbox: config.sandbox,
                public_key: config.public_key,
            });

            return aplazameSdk;
        }
    }
);
