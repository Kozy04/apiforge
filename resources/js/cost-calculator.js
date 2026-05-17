/**
 * APIForge Live Cost Calculator
 * Vanilla JS — no dependencies
 */

function initCalculator(inputCost, outputCost, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const tokensInput = container.querySelector('#calc-tokens');
    const splitSlider = container.querySelector('#calc-split');
    const splitLabel = container.querySelector('#calc-split-label');
    const resultEl = container.querySelector('#calc-result');

    if (!tokensInput || !resultEl) return;

    function compute() {
        const tokens = parseInt(tokensInput.value) || 0;
        const inRatio = splitSlider ? parseInt(splitSlider.value) / 100 : 0.7;
        const outRatio = 1 - inRatio;

        const inputTokens = tokens * inRatio;
        const outputTokens = tokens * outRatio;

        const cost = (inputTokens / 1_000_000 * inputCost) + (outputTokens / 1_000_000 * outputCost);
        resultEl.textContent = '$' + cost.toFixed(2);
    }

    tokensInput.addEventListener('input', compute);

    if (splitSlider) {
        splitSlider.addEventListener('input', function() {
            const v = parseInt(this.value);
            if (splitLabel) splitLabel.textContent = v + '% input / ' + (100 - v) + '% output';
            compute();
        });
    }

    tokensInput.focus();
    compute();
}

function initDualCalculator(inA, outA, inB, outB) {
    const tokensInput = document.getElementById('calc-tokens-dual');
    const resultA = document.getElementById('calc-dual-result-a');
    const resultB = document.getElementById('calc-dual-result-b');

    if (!tokensInput || !resultA || !resultB) return;

    function compute() {
        const tokens = parseInt(tokensInput.value) || 0;
        const inRatio = 0.7;
        const outRatio = 0.3;
        const inputTokens = tokens * inRatio;
        const outputTokens = tokens * outRatio;

        const costA = (inputTokens / 1_000_000 * parseFloat(inA)) + (outputTokens / 1_000_000 * parseFloat(outA));
        const costB = (inputTokens / 1_000_000 * parseFloat(inB)) + (outputTokens / 1_000_000 * parseFloat(outB));

        resultA.textContent = '$' + costA.toFixed(2);
        resultB.textContent = '$' + costB.toFixed(2);

        resultA.classList.toggle('text-emerald-400', costA <= costB);
        resultA.classList.toggle('text-red-400', costA > costB);
        resultB.classList.toggle('text-emerald-400', costB <= costA);
        resultB.classList.toggle('text-red-400', costB > costA);
    }

    tokensInput.addEventListener('input', compute);
    tokensInput.focus();
    compute();
}

window.initCalculator = initCalculator;
window.initDualCalculator = initDualCalculator;
