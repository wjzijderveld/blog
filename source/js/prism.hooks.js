Prism.hooks.add('after-highlight', function (env) {
    var code = env.element,
        pre = code.parentElement,
        linesNum,
        lines,
        lineNumbersWrapper;

    if (code.className.indexOf('line-numbers') > -1) {

        pre.className += ' line-numbers';

        linesNum = (env.code.split('\n').length);

        lines = new Array(linesNum);
        lines = lines.join('<span></span>');

        lineNumbersWrapper = document.createElement('span');
        lineNumbersWrapper.className = 'line-numbers-rows';
        lineNumbersWrapper.innerHTML = lines;

        console.log(env.element);
        console.log(lineNumbersWrapper);

        env.element.appendChild(lineNumbersWrapper);
    }
});