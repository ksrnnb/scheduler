'use strict';

const container = document.getElementById('c-container');
const calendar = document.getElementById('calendar');
const candidate = document.getElementById('candidate');

const weeks = ['日', '月', '火', '水', '木', '金', '土'];

let candidateArray = new Array();

const today = new Date();

let year = today.getFullYear();
let month = today.getMonth();

let html = '<i id="prev" class="fas fa-chevron-circle-left"></i>';
html += '<i id="next" class="fas fa-chevron-circle-right"></i>';

container.insertAdjacentHTML('afterbegin', html);

const prev = document.getElementById('prev');
const next = document.getElementById('next');

prev.addEventListener('click', () => {
  document.removeEventListener('click', add_data_attribute);
  if (month > 0) {
    month--;
  } else {
    month = 11;
    year--;
  }
  show_calendar(year, month);
});

next.addEventListener('click', () => {
  document.removeEventListener('click', add_data_attribute);
  if (month < 11) {
    month++;
  } else {
    year++;
    month = 0;
  }
  show_calendar(year, month);
});

show_calendar(year, month);

function add_data_attribute(e) {
  if (e.target.classList.contains('able-day')) {
    const data_date = e.target.dataset.date;
    let candidate_date = new Date(data_date);

    //すでに候補日に追加されてる場合は追加しない。
    if (candidateArray.includes(data_date)) {
      return;
    }
    //候補日の配列に追加
    candidateArray.push(data_date);
    //日付を候補日に追加
    candidate.innerHTML += `${candidate_date.getMonth() + 1}/${candidate_date.getDate()}(${weeks[candidate_date.getDay()]})\n`;
  }
}

function show_calendar(year, month) {
  const monthBegin = new Date(year, month, 1);
  const monthEnd = new Date(year, month + 1, 0);

  //はじめの曜日
  const dayBegin = monthBegin.getDay();
  //さいごの日
  const dayEnd = monthEnd.getDate();
  const lastMonthDayEnd = (new Date(year, month, 0)).getDate();

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
        html += `<td class="disable">${lastMonthDayEnd - dayBegin + d + 1}</td>`;
      } else if (day <= dayEnd) {
        html += `<td class="able-day" data-date=${year}-${month + 1}-${day}>${day}</td>`;
        day++;
      } else {
        html += `<td class="disable">${day - dayEnd}</td>`;
        day++;
      }
    }

    html += '</tr>';
  }

  html += '</table>';

  calendar.innerHTML = html;

  document.addEventListener("click", add_data_attribute);

}
