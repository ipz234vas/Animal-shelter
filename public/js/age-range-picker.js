export default function initAgeRange(
    yearMinId, monthMinId, yearMaxId, monthMaxId,
    hiddenMinId, hiddenMaxId
) {
    const yMin = document.getElementById(yearMinId);
    const mMin = document.getElementById(monthMinId);
    const yMax = document.getElementById(yearMaxId);
    const mMax = document.getElementById(monthMaxId);
    const hMin = document.getElementById(hiddenMinId);
    const hMax = document.getElementById(hiddenMaxId);

    const toMonths = (y, m) => (parseInt(y) || 0) * 12 + (parseInt(m) || 0);

    function sync() {
        const vMin = (yMin.value || mMin.value) ? toMonths(yMin.value, mMin.value) : '';
        const vMax = (yMax.value || mMax.value) ? toMonths(yMax.value, mMax.value) : '';

        hMin.value = vMin === 0 ? '' : vMin;
        hMax.value = vMax === 0 ? '' : vMax;
    }

    [yMin, mMin, yMax, mMax].forEach(el => el.addEventListener('input', sync));
    sync();
}
