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
});

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('btn')) {
    if (! e.target.classList.contains('selected')) {
      let index_tmp = e.target.dataset.index;
      let index = index_tmp.split('-');
      index[0] = parseInt(index[0]);
      index[1] = parseInt(index[1]);

      // availabilityの更新
      for (let si = 0; si <= 2; si++) {
        let data_index = `${index[0]}-${si}`;
        //データ属性一致するものを取得。"がないとエラー出る。
        let btn = document.querySelector(`[data-index="${data_index}"]`);
        if (btn.classList.contains('selected')) {
          btn.classList.remove('selected');
        }
      }
      e.target.classList.add('selected');
      


    }
  }
});
