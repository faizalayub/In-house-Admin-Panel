import interFont from './Inter/Inter-VariableFont_opsz,wght.ttf';
import interItalicFont from './Inter/Inter-Italic-VariableFont_opsz,wght.ttf';

import asapFont from './Asap/Asap-VariableFont_wdth,wght.ttf';
import asapItalicFont from './Asap/Asap-Italic-VariableFont_wdth,wght.ttf';

import timesFont from './Times/Times New Roman.ttf';
import timesItalicFont from './Times/Times New Roman - Italic.ttf';
import timesBoldFont from './Times/Times New Roman - Bold.ttf';

import verdanaFont from './Verdana/Verdana.ttf';
import verdanaItalicFont from './Verdana/Verdana-Italic.ttf';
import verdanaBoldFont from './Verdana/Verdana-Bold.ttf';

const style = document.createElement('style');
style.textContent = `
/* Inter */
@font-face {font-family: 'Inter'; src: url(${interFont}) format('truetype'); font-weight: 400; font-style: normal;}
@font-face {font-family: 'Inter'; src: url(${interItalicFont}) format('truetype'); font-weight: 400; font-style: italic;}

/* Asap */
@font-face {font-family: 'Asap'; src: url(${asapFont}) format('truetype'); font-weight: 400; font-style: normal;}
@font-face {font-family: 'Asap'; src: url(${asapItalicFont}) format('truetype'); font-weight: 400; font-style: italic;}
    
/* Times */
@font-face {font-family: 'Times'; src: url(${timesFont}) format('truetype'); font-weight: 400; font-style: normal;}
@font-face {font-family: 'Times'; src: url(${timesItalicFont}) format('truetype'); font-weight: 400; font-style: italic;}
@font-face {font-family: 'Times'; src: url(${timesBoldFont}) format('truetype'); font-weight: 700; font-style: normal;}

/* Verdana */
@font-face {font-family: 'Verdana'; src: url(${verdanaFont}) format('truetype'); font-weight: 400; font-style: normal;}
@font-face {font-family: 'Verdana'; src: url(${verdanaItalicFont}) format('truetype'); font-weight: 400; font-style: italic;}
@font-face {font-family: 'Verdana'; src: url(${verdanaBoldFont}) format('truetype'); font-weight: 700; font-style: normal;}
`;

document.head.appendChild(style);
