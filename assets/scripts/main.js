/**
 * Add or remove a track from the user's favorite tracks
 * @param node
 */
function loveTrack(node) {
  const idTrack = node.id.split('-')[3];

  if (node.classList.contains('loved-track')) {
    console.log('loved-track');
    node.classList.add('not-loved-track');
    node.classList.remove('loved-track');
    // node.firstElementChild.src = "../img/not-loved.svg";
  } else {
    console.log('not-loved-track');
    node.classList.add('loved-track');
    node.classList.remove('not-loved-track');
    // node.firstElementChild.src = "../img/loved.svg";
  }

  let url = '/myPage/myScrobbles/love/' + idTrack;
  // url = url + '?XDEBUG_SESSION_START=1';

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "GET",
    headers: myHeaders,
    redirect: "follow"
  };

  fetch(url, requestOptions)
    .catch((error) => {
      console.error('error loving track, detail : ', error);
    })

}


/**
 * Toggle panel content
 * can be used to show/hide a panel
 * @param tabSelected
 */
function changeTab(tabSelected) {
  const tabName = tabSelected.id.split('-')[3];
  const tabType = tabSelected.id.split('-')[2];
  tabType.replace('-' + tabName, '');

  $('.tab-li-' + tabType).addClass('menu-panel-select').removeClass('menu-panel-selected');
  $('#tab-li-' + tabType + '-' + tabName).removeClass('menu-panel-select').addClass('menu-panel-selected');

  $('.tab-content-' + tabType).hide();
  $('#tab-content-' + tabType + '-' + tabName).show();
}


/**
 * Show the modal
 */
function showModal() {
  const targetEl = document.getElementById("static-modal");
  if (targetEl){
    console.log('Modal');
    const options = {};
    const modal = new Modal(targetEl, options);
    modal.show();
  } else {
    console.error('Modal not found');
  }
}

/**
 * Hide the modal
 */
function hideModal() {
  const targetEl = document.getElementById("static-modal");
  if (targetEl){
    const options = {};
    const modal = new Modal(targetEl, options);
    modal.hide();
  } else {
    console.error('Modal not found');
  }
}