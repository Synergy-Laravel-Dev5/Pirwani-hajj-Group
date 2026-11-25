const HIJRI_MONTH_NAMES = {
    1: 'MUHARRAM', 2: 'SAFAR', 3: 'RABI-I', 4: 'RABI-II',
    5: 'JAMADI-I', 6: 'JAMADI-II', 7: 'RAJAB', 8: 'SHABAN',
    9: 'RAMADAN', 10: 'SHAWWAL', 11: 'ZILQAIDA', 12: 'ZILHAJJ'
};

function kuwaitiHijri(year, month, day) {
    let jd;
    if ((year > 1582) || ((year === 1582) && (month > 10)) || ((year === 1582) && (month === 10) && (day > 14))) {
        jd = Math.floor((1461 * (year + 4800 + Math.floor((month - 14) / 12))) / 4) +
             Math.floor((367 * (month - 2 - 12 * (Math.floor((month - 14) / 12)))) / 12) -
             Math.floor((3 * Math.floor((year + 4900 + Math.floor((month - 14) / 12)) / 100)) / 4) +
             day - 32075;
    } else {
        jd = 367 * year - Math.floor((7 * (year + 5001 + Math.floor((month - 9) / 7))) / 4) +
             Math.floor((275 * month) / 9) + day + 1729777;
    }

    let l = jd - 1948440 + 10632;
    const n = Math.floor((l - 1) / 10631);
    l = l - 10631 * n + 354;
    const j = Math.floor((10985 - l) / 5316) * Math.floor((50 * l) / 17719) +
              Math.floor(l / 5670) * Math.floor((43 * l) / 15238);
    l = l - Math.floor((30 - j) / 15) * Math.floor((17719 * j) / 50) -
        Math.floor(j / 16) * Math.floor((15238 * j) / 43) + 29;
    const m = Math.floor((24 * l) / 709);
    const d = l - Math.floor((709 * m) / 24);

    const dd = String(d).padStart(2, '0');
    const monthName = HIJRI_MONTH_NAMES[m] || ('M' + m);
    return `${dd}-${monthName}`;
}

function testIntl(y, m, d) {
    const dateObj = new Date(Date.UTC(y, m - 1, d, 12, 0, 0));
    const formatter = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura-nu-latn', {
        day: 'numeric',
        month: 'numeric',
        year: 'numeric'
    });
    return formatter.format(dateObj);
}

console.log('Intl 2027-04-27:', testIntl(2027, 4, 27));
console.log('Intl 2027-04-28:', testIntl(2027, 4, 28));
console.log('Kuwaiti 2027-04-27:', kuwaitiHijri(2027, 4, 27));
console.log('Kuwaiti 2027-04-28:', kuwaitiHijri(2027, 4, 28));
