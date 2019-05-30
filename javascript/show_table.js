'use strict';

const input = document.getElementById('input');
const form = document.getElementById('form');
const availabilities = document.getElementById('availabilities');
let candidates = document.getElementsByClassName('candidate');
let users = document.getElementsByClassName('user');
const symbols = ['○', '△', '×'];

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
  if (e.target.classList.contains('symbol')) {
    if (! e.target.classList.contains('selected')) {
      //index[0]: candidates何行目,　index[1]:まる、さんかく、ばつ(numeric)
      let index_tmp = e.target.dataset.index;
      let index = index_tmp.split('-');
      index[0] = parseInt(index[0]);
      index[1] = parseInt(index[1]);

      //隠れてるinputのvalue属性を更新する
      let value_tmp = availabilities.value;
      let value_array = value_tmp.split('-');
      value_array[index[0]] = index[1];
      let value_attr = value_array.join('-');
      availabilities.setAttribute('value', value_attr);

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
  } else if (e.target.classList.contains('user')) {
    const user = e.target;
    //HTML collectionをarrayに変換、この1行ちょっと難しい。
    users = [].slice.call(users);
    //userクラスの中で何番目か。
    let index = users.indexOf(user);

    candidates = [].slice.call(candidates);

    //配列つくってからフォームに反映させる？　どうするか考える。
    candidates.forEach( (candidate) => {
      let symbol = candidate.children[index + 4].innerHTML;
      console.log(symbols.indexOf(symbol));
    })
    
  }
});