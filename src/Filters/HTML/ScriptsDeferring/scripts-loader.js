var Promise = phast.ES6Promise;

phast.ScriptsLoader = {};

phast.ScriptsLoader.Utilities = function (document) {

    var insertBefore = Element.prototype.insertBefore;

    function scriptFromPhastScript(original) {
        var newScript = document.createElement('script');
        Array.prototype.forEach.call(original.attributes, function (attr) {
            var attrName;
            var phastAttr = attr.nodeName.match(/^data-phast-original-(.*)/i);
            if (phastAttr) {
                attrName = phastAttr[1];
            } else {
                attrName = attr.nodeName;
            }
            newScript.setAttribute(attrName, attr.nodeValue);
        });
        return newScript;
    }

    function copySrc(source, target) {
        target.setAttribute('src', source.getAttribute('src'));
    }

    function setOriginalSrc(original, target) {
        target.setAttribute('src', original.getAttribute('data-phast-original-src'));
    }

    function setOriginalType(original, target) {
        target.setAttribute('type', original.getAttribute('data-phast-original-type'));
    }

    function executeString(string) {
        return new Promise(function (resolve, reject) {
            try {
                // See: http://perfectionkills.com/global-eval-what-are-the-options/
                (1,eval)(string);
            } catch (e) {
                console.error("[Phast] Error in inline script:", e);
                console.log("First 100 bytes of script body:", string.substr(0, 100));
                reject(e);
            }
            resolve();
        })
    }

    function writeProtectAndExecuteString(sourceElement, scriptString) {
        return writeProtectAndCallback(sourceElement, function () {
            return executeString(scriptString);
        });
    }

    function writeProtectAndCallback(sourceElement, callback) {
        var writeBuffer = '';
        document.write = function (string) {
            writeBuffer += string;
        };
        document.writeln = function (string) {
            writeBuffer += string + '\n';
        };
        return callback()
            .then(function () {
                sourceElement.insertAdjacentHTML('afterend', writeBuffer);
            })
            .finally(function () {
                delete document.write;
                delete document.writeln;
            });
    }

    function writeProtectAndReplaceElement(target, replacement) {
        return writeProtectAndCallback(replacement, function () {
            return replaceElement(target, replacement);
        });
    }

    function replaceElement(target, replacement) {
        return new Promise(function (resolve, reject) {
            replacement.addEventListener('load', resolve);
            replacement.addEventListener('error', reject);
            insertBefore.call(target.parentNode, replacement, target);
            target.parentNode.removeChild(target);
        });
    }

    this.scriptFromPhastScript = scriptFromPhastScript;
    this.copySrc = copySrc;
    this.setOriginalSrc = setOriginalSrc;
    this.setOriginalType = setOriginalType;
    this.executeString = executeString;
    this.writeProtectAndExecuteString = writeProtectAndExecuteString;
    this.replaceElement = replaceElement;
    this.writeProtectAndReplaceElement = writeProtectAndReplaceElement;

};
