'use strict';

const input = document.getElementById('input');
const form = document.getElementById('form');
const availabilities = document.getElementById('availabilities');
const user_form = document.getElementById('user-form');
const userId = document.getElementById('userId');
const submit_button = document.getElementById('submit_button');
let candidates = document.getElementsByClassName('candidate');
let symbols = document.getElementsByClassName('symbol');
let users = document.getElementsByClassName('user');

const symbol_array = ['○', '△', '×'];

input.addEventListener('click', () => {
  if (form.classList.contains('hidden')) {

    form.classList.remove('hidden');
    // hiddenが効かない->d-none
    input.classList.add('d-none');
  } else {
    //テスト中は繰り返せるようにしておく
    input.classList.add('d-none');
    submit_button.setAttribute('value', '登録');
    userId.removeAttribute('value');
    user_form.removeAttribute('value');
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
    // 送信ボタンの名前かえる。
    submit_button.value = '更新';
    input.classList.remove('hidden');

    let user = e.target;
    let userName = e.target.innerHTML;
    //HTML collectionをarrayに変換、この1行ちょっと難しい。
    users = [].slice.call(users);
    //userクラスの中で何番目か。
    let index = users.indexOf(user);

    candidates = [].slice.call(candidates);

    //配列つくってからフォームに反映させる？　どうするか考える。
    let availability_array = [];
    candidates.forEach( (candidate) => {
      //候補日、symbol分ずらすので4たす。
      let symbol = candidate.children[index + 4].innerHTML;
      availability_array.push(symbol_array.indexOf(symbol));
    });

    userId.setAttribute('value', user.dataset.id);

    user_form.setAttribute('value', userName);
    form.classList.remove('hidden');

    let trows = document.querySelectorAll('#availability-form tr');
    
    trows.forEach ( (trow, index) => {
      //selectedクラスを外す
      for (let i = 1; i < 4; i++) {
        if (trow.children[i].classList.contains('selected')) {
          trow.children[i].classList.remove('selected');
        }
      }
      //候補日があるから1足すとsymbolのindexになる。
      let sindex = availability_array[index] + 1;
      trow.children[sindex].classList.add('selected');

      // availabilityのinputも更新
      let value_attr = availability_array.join('-');
      availabilities.setAttribute('value', value_attr);
      
    });
    
  }
});