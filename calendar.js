'use strict';

const calendar = document.getElementById('calendar');

const weeks = ['日', '月', '火', '水', '木', '金', '土'];

const today = new Date();

const year = today.getFullYear();
const month = today.getMonth();

const monthBegin = new Date(year, month, 1);
const monthEnd = new Date(year, month + 1, 0);

//はじめの曜日
const dayBegin = monthBegin.getDay();
//さいごの日
const lastDay = monthEnd.getDate();



let day = 1;

let html = '<h2>' + year + '/' + (month + 1) + '</h2>';

html += '<table><tr>';

for (let i = 0; i < weeks.length; i++) {
  html += '<td>' + weeks[i] + '</td>';
}

html += '</tr>';

for (let w = 0; w < 6; w++) {

  html += '<tr>';
  
  for (let d = 0; d < 7; d++) {
    
    if (w === 0 && d < dayBegin) {
      html += '<td></td>';
    } else if (day <= lastDay) {
      html += '<td>' + day + '</td>';
      day++;
    } else {
      html += '<td></td>';
    }
  }

  html += '</tr>';
}

html += '</table>';

calendar.innerHTML = html;