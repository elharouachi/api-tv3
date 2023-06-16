(function() {
    var matchSearchSectionRegex = /[^a-zA-Z]search[^a-zA-Z]/;

    function fixDocumentation() {
        if (window.navigator.userAgent.indexOf("MSIE ") > 0) {
            // don't bother with internet explorer
            return;
        } else if (!isDocumentationLoaded()) {
            setTimeout(fixDocumentation, 300);

            return;
        }

        fixNestedObjects();
        addPutExplanation();
        fixEndpointsUrls();
        fixSearchDocumentation();
    }

    function isDocumentationLoaded() {
        return null != document.querySelector('h2');
    }

    function fixNestedObjects(containerElement) {
        // ex: transform "BrightcoveSource-write_live" to "BrightcoveSource"
        containerElement = containerElement || document;
        var objectNameElements = containerElement.querySelectorAll('table td div span:nth-child(3)');
        var regexp = /^\([a-zA-Z]+-(write|read)_/;
        var replaceRegex = /-[^)]+/;

        for (var objectNameElement of objectNameElements) {
            var textContent = objectNameElement.textContent;

            if (!regexp.test(textContent.trim())) {
                continue;
            }

            objectNameElement.textContent = textContent.replace(replaceRegex, '');
        }
    }

    function addPutExplanation() {
        // adds an explanation text below PUT titles
        var putElements = document.querySelectorAll('[type=put]');

        for (var putElement of putElements) {
            var itemIdElement = putElement.closest('[data-item-id]');

            if (null === itemIdElement) {
                continue;
            }

            var itemId = itemIdElement.getAttribute('data-item-id');
            var titleElement = document.getElementById(itemId).querySelector('h2');
            var noteElement = document.createElement('h5');
            noteElement.textContent = 'Note: PUT operations act as a PATCH, fields that are not specified will be left untouched.';
            titleElement.insertAdjacentElement('afterend', noteElement);
        }
    }

    function fixEndpointsUrls() {
        // sometimes, endpoints urls look like this: http://api-cinema/#operation/getVideoCollection/v1/movies
        // we transform them to look like this: http://api-cinema/v1/movies
        var httpVerbElements = document.querySelectorAll('.http-verb');
        var urlReplaceRegex = /\/?#.*/;

        for (var httpVerbElement of httpVerbElements) {
            var spanElements = httpVerbElement.parentNode.parentNode.querySelectorAll('span');

            for (var spanElement of spanElements) {
                if ('http' !== spanElement.textContent.substr(0, 4)) {
                    continue;
                }

                spanElement.textContent = spanElement.textContent.replace(urlReplaceRegex, '');
            }
        }
    }

    function fixSearchDocumentation() {
        var postElements = document.querySelectorAll('[type=post]');

        for (var postElement of postElements) {
            var itemIdElement = postElement.closest('[data-item-id]');

            if (null === itemIdElement) {
                continue;
            }

            var itemId = itemIdElement.getAttribute('data-item-id');

            if (!matchSearchSectionRegex.test(itemId)) {
                continue;
            }

            var containerElement = document.getElementById(itemId);
            removeSearchRequestExamples(containerElement);
            fixParametersTitle(containerElement);
        }
    }

    function removeSearchRequestExamples(containerElement) {
        var element = containerElement.querySelector('.redoc-json');

        do {
            element = element.parentNode;
        } while (null === element.querySelector('h3'));

        element.remove();
    }

    function fixParametersTitle(containerElement) {
        var titleElementList = containerElement.querySelectorAll('h5');
        var matchRegex = /request /i;

        for (var titleElement of titleElementList) {
            if (!matchRegex.test(titleElement.textContent)) {
                continue;
            }

            titleElement.textContent = 'Available fields';
        }
    }

    fixDocumentation();
    document.querySelector('body').addEventListener('click', function(event) {
        // response bodies are generated on click
        var containerElement = event.target.closest('[data-section-id]');

        if (null === containerElement) {
            return;
        }

        setTimeout(fixNestedObjects.bind(this, containerElement), 500);
    });
})();

window.onload = () => {
    const data = JSON.parse(document.getElementById('swagger-data').innerText);
    const options = {
        sortPropsAlphabetically: true,
    };

    Redoc.init(data.spec, options, document.getElementById('swagger-ui'));
};
