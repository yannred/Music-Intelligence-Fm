window.grid = null;

function loadGrid () {

  let url = '/myPage/grid';
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
    .then(response => response.json())
    .then((widgetResponse) => {
      // TODO : control grid options
      const gridOption = {
        "minRow": 2,
        "cellHeight": "7rem",
        //"cellHeight" : 'initial', // start square but will set to % of window width later
        "animate": false, // show immediate (animate : true is nice for user dragging though)
        "disableOneColumnMode": true, // will manually do 1 column
        "float": true
      };
      window.grid = GridStack.init(gridOption);
      setGridstackEvents(grid);
      grid.load(widgetResponse);
      $('#button-add-widget-page').css("display", "inline");
    })
    .then(() => {
      loadCharts();
    })
    .catch((error) => {
      console.error('error loading widget grid, detail : ', error);
    })

}



function setGridstackEvents () {

  grid.on('change', function(event, gridStackItems) {
    gridStackItems.forEach(function(gridStackItem) {
      updateWidget(gridStackItem);
    });
  });

}

function addWidget () {

  let url = '/myPage/widget';
  // url = url + '?XDEBUG_SESSION_START=1';

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "POST",
    headers: myHeaders,
    redirect: "follow",
    body : JSON.stringify({})
  };

  fetch(url, requestOptions)
    .then(response => response.json())
    .then((widgetResponse) => {
      console.log('widget created, detail : ', widgetResponse);
      grid.addWidget(widgetResponse);
    })
    .catch((error) => {
      console.error('error creating new widget, detail : ', error);
    })

}

function updateWidget (gridStackItem) {

  let url = '/myPage/widget/' + gridStackItem.id;
  // url = url + '?XDEBUG_SESSION_START=1';

  let widgetData = JSON.stringify({
    "id": gridStackItem.id + "",
    "w": gridStackItem.w + "",
    "h": gridStackItem.h + "",
    "x": gridStackItem.x + "",
    "y": gridStackItem.y + ""
  })

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "UPDATE",
    headers: myHeaders,
    redirect: "follow",
    body: widgetData
  };

  fetch(url, requestOptions)
    .then(response => response.json())
    .then((widgetResponse) => {
      // console.log('widget saved, detail : ', widgetResponse);
    })
    .catch((error) => {
      console.error('error saving widget, detail : ', error);
    })


}

/**
 * Redirect to the modify widget page with the widget id
 * @param gridstackItemId
 */
function modifyWidget (gridstackItemId) {
  let url = '/myPage/myStatistics/new/' + gridstackItemId
  // url = url + '?XDEBUG_SESSION_START=1';
  document.location.href=url;
}


function deleteWidget (gridstackItemId) {

  let gridstackItem = null;

  window.grid.getGridItems().forEach(function(item) {
    if (item.gridstackNode.id == gridstackItemId) {
      gridstackItem = item;
    }
  });

  if (gridstackItem === null) {
    console.error('Can\'t delete widget, gridstackItem not found (id : ' + gridstackItemId + ')');
    return;
  }

  let url = '/myPage/widget/' + gridstackItemId;
  // url = url + '?XDEBUG_SESSION_START=1';

  const myHeaders = new Headers();
  // myHeaders.append('Authorization', 'Bearer ' + 'token' + '');
  myHeaders.append("Content-Type", "application/json");

  const requestOptions = {
    method: "DELETE",
    headers: myHeaders,
    redirect: "follow"
  };

  fetch(url, requestOptions)
    .then((widgetResponse) => {
      grid.removeWidget(gridstackItem);
    })
    .catch((error) => {
      console.error('error deleting widget, detail : ', error);
    })

}

/**
 * Callback function for the date type change of the new widget form
 */
function onChangeDateType(dateType){

  const valueOfCustomDate = 1;

  console.log('dateType', dateType);
  console.log('dateType.value', dateType.value);
  console.log('valueOfCustomDate', valueOfCustomDate);




  if(dateType.value == valueOfCustomDate){
    console.log('show');
    $('.period-custom').show();
  } else {
    console.log('hide');
    $('.period-custom').hide();
  }
}


/**
 * Change the tab and the content of widget
 * @param node
 */
function activeWidgetTab(node){

  const targetId = $(node).attr('data-target');
  const target = $('#' + targetId);

  const clickedTab = $(node);

  if (target.hasClass('hidden')){
    //change content class
    target.parent().children().addClass('hidden');
    target.removeClass('hidden');

    //change tab class
    const oldActiveTab = $('.active-tab-widget');
    oldActiveTab.removeClass('active-tab-widget');
    oldActiveTab.addClass('inactive-tab-widget');
    clickedTab.removeClass('inactive-tab-widget');
    clickedTab.addClass('active-tab-widget');
  }


}