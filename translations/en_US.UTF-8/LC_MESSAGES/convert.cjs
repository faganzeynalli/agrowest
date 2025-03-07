const fs = require('fs');
const gettextParser = require('gettext-parser');

// Read and parse the PO file
const inputPo = fs.readFileSync('default.po');
const parsed = gettextParser.po.parse(inputPo);

// Prepare JSON structure for `en_US`
const outputJson = { "en_US": {} };

// Convert translations
for (const key in parsed.translations['']) {
    if (key !== '') {
        const entry = parsed.translations[''][key];
        // Handle plural forms
        if (entry.msgstr.length > 1) {
            outputJson["en_US"][key] = entry.msgstr;
        } else {
            outputJson["en_US"][key] = entry.msgstr[0] || key;
        }
    }
}

// Convert special characters to Unicode escape sequences
const formattedJson = JSON.stringify(outputJson, null, 4)
    .replace(/[\u007F-\uFFFF]/g, (c) => '\\u' + ('000' + c.charCodeAt(0).toString(16)).slice(-4));

// Write output to `translations.json`
fs.writeFileSync('translations.json', formattedJson, 'utf8');
console.log('Translations successfully generated for en_US!');
