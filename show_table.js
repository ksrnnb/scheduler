'use strict';

const input = document.getElementById('input');
const form = document.getElementById('form');

input.addEventListener('click', () => {
  if (form.classList.contains('hidden')) {

    form.classList.remove('hidden');
    
    // input.classList.add('hidden');
  } else {
    //テスト中は繰り返せるようにしておく
    form.classList.add('hidden');
  }
})
