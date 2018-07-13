loadPhastJS(['public/es6-promise.js', 'public/scripts-loader.js'], function (phast) {

    var ScriptsLoader = phast.ScriptsLoader,
        Promise = phast.ES6Promise;

    QUnit.module('ScriptsLoader', function () {

        QUnit.module('ScriptsLoader.Utilities', function (hooks) {

            var utils, testDoc;

            hooks.beforeEach(function () {
                testDoc = document.implementation.createHTMLDocument();
                utils = new ScriptsLoader.Utilities(testDoc);
            });

            QUnit.test('Test scriptFromPhastScript()', function (assert) {
                var original = testDoc.createElement('script');
                original.setAttribute('defer', true);
                original.setAttribute('src', 'some-src');
                original.setAttribute('type', 'some-type');
                original.setAttribute('data-phast-original-src', 'the-original-src');
                original.setAttribute('data-phast-original-type', 'the-original-type');
                original.setAttribute('id', 'should-see');
                original.setAttribute('async', true);

                var newOne = utils.scriptFromPhastScript(original);
                ['data-phast-original-src', 'data-phast-original-type'].forEach(function (attr) {
                    assert.notOk(newOne.hasAttribute(attr), attr + ' is missing');
                });

                ['id', 'async'].forEach(function (attr) {
                    assert.equal(newOne.getAttribute(attr), original.getAttribute(attr), attr + ' has been copied');
                });

                ['src', 'type'].forEach(function (attr) {
                    var phastAttr = 'data-phast-original-' + attr;
                    assert.equal(
                        newOne.getAttribute(attr),
                        original.getAttribute(phastAttr),
                        attr + ' has been set'
                    )
                });
            });

            QUnit.test('Test copySrc()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                s1.setAttribute('src', 'the-src');
                utils.copySrc(s1, s2);
                assert.equal(s2.getAttribute('src'), s1.getAttribute('src'), 'src was copied');
            });

            QUnit.test('Test setOriginalSrc()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                s1.setAttribute('data-phast-original-src', 'the-original-src');
                utils.setOriginalSrc(s1, s2);
                assert.equal(
                    s2.getAttribute('src'),
                    s1.getAttribute('data-phast-original-src'),
                    'The original src has been set'
                );
            });

            QUnit.test('Test setOriginalType()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                s1.setAttribute('data-phast-original-type', 'the-original-type');
                utils.setOriginalType(s1, s2);
                assert.equal(
                    s2.getAttribute('type'),
                    s1.getAttribute('data-phast-original-type'),
                    'The original type has been set'
                );
            });

            QUnit.test('Test executeString()', function (assert) {
                window.STRING_EXECUTED = false;
                var string = func2string(function  () { window.STRING_EXECUTED = true; });
                utils.executeString(string);
                assert.ok(window.STRING_EXECUTED, 'String was executed');
                delete window.STRING_EXECUTED;
            });

            QUnit.test('Test writeProtectAndExecuteString()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                testDoc.body.appendChild(s1);
                testDoc.body.appendChild(s2);
                var func = function () {
                    document.write('<p>write works</p>');
                    document.writeln('<p>writeln works</p>');
                };
                var checkWrites = getWriteProtectChecker(assert);
                assert.timeout(100);
                var done = assert.async();

                utils = new ScriptsLoader.Utilities(document);
                utils.writeProtectAndExecuteString(s1, func2string(func))
                    .then(function () {
                        checkWrites();
                        var paragraphs = testDoc.getElementsByTagName('p');
                        assert.equal(paragraphs.length, 2, 'Paragraphs have been written');
                        assert.equal(paragraphs[0].innerHTML, 'write works', 'paragraph 0 is correct');
                        assert.equal(paragraphs[1].innerHTML, 'writeln works', 'paragraph 1 is correct');
                        assert.ok(s1 === paragraphs[0].previousSibling, 'paragraph 0 is in the correct position');
                        done();
                    });
            });

            QUnit.test('Test writeProtectAndExecuteString() restores write and writeln after exception', function (assert) {
                var s = testDoc.createElement('script');
                var checkWrites = getWriteProtectChecker(assert);
                assert.timeout(100);
                var done = assert.async();
                var script = 'throw "error";';

                utils = new ScriptsLoader.Utilities(document);
                utils.writeProtectAndExecuteString(s, script)
                    .finally(function () {
                        checkWrites();
                        done();
                    });
            });

            QUnit.test('Test replaceElement()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                testDoc.body.appendChild(s1);
                Element.prototype.insertBefore = function () {};
                utils.replaceElement(s1, s2);
                delete Element.prototype.insertBefore;
                assert.equal(1, testDoc.body.children.length, 'There is only one body child');
                assert.ok(testDoc.body.children[0] === s2, 'Script has been replaced');
            });

            QUnit.test('Test writeProtectAndReplaceElement()', function (assert) {
                var s1 = testDoc.createElement('script');
                var s2 = testDoc.createElement('script');
                var checkWrite = getWriteProtectChecker(assert);
                assert.timeout(200);
                var done = assert.async();
                s1.setAttribute('id', 'writeProtectAndReplaceElementTestScriptToRemove')
                var writeFunc = function () {
                    document.write('<p id="writtenline"></p>');
                };
                s2.setAttribute('src', 'data:text/javascript;base64,' + utoa(func2string(writeFunc)));
                s2.setAttribute('id', 'writeProtectAndReplaceElementReplacementScript');
                document.body.appendChild(s1);

                utils = new ScriptsLoader.Utilities(document);
                utils.writeProtectAndReplaceElement(s1, s2)
                    .finally(function () {
                        checkWrite();
                        var replaced = document.getElementById('writeProtectAndReplaceElementTestScriptToRemove');
                        var replacement = document.getElementById('writeProtectAndReplaceElementReplacementScript');
                        var written = document.getElementById('writtenline');

                        assert.ok(replaced === null, 'Target was replaced');
                        assert.ok(replacement === s2, 'Replacement is in place');
                        assert.ok(written, 'Written exists');
                        document.body.removeChild(replacement);
                        document.body.removeChild(written);
                        done();
                    });
            });

            function func2string(func) {
                return '(' + func.toString() + ')();';
            }

            function getWriteProtectChecker(assert) {
                var originalWrite = document.write;
                var originalWriteLn = document.writeln;
                return function () {
                    assert.ok(originalWrite === document.write, 'document.write has been restored');
                    assert.ok(originalWriteLn === document.writeln, 'document.writeln has been restored');
                }
            }

            function utoa(str) {
                return btoa(
                    encodeURIComponent(str).replace(
                        /%([0-9A-F]{2})/g,
                        function toSolidBytes(match, p1) {
                            return String.fromCharCode('0x' + p1);
                        }
                    )
                );
            }
        });


    });

});
