
<!DOCTYPE html>
<head>
<title>Walmart Inventory Checker</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script data-cfasync="false" type="text/javascript">
  var freestar = freestar || {};
  freestar.queue = freestar.queue || [];
  freestar.config = freestar.config || {};
  freestar.config.enabled_slots = [];
  freestar.config.disabledProducts = { stickyFooter: window.location !== window.parent.location };
  freestar.initCallback = function () { (freestar.config.enabled_slots.length === 0) ? freestar.initCallbackCalled = false : freestar.newAdSlots(freestar.config.enabled_slots) }
</script>
<script src="dhx/codebase/dhtmlx.js?5" type="text/javascript"></script>
<script src="jquery.min.js" type="text/javascript"></script>
<script>
var gkey="AIzaSyDxk8Z3NIGVD9-FOrQHbJf8hAwHFi4LMG0";localStorage["aa"]=true; localStorage["ab"]=true;localStorage["at"]=599;</script>
<script src="adbd.js" type="text/javascript"></script>
<script src="common_utils.js?2" type="text/javascript"></script>
<script src="utils.js?111" type="text/javascript"></script>
<script src="loc_utils.js?5" type="text/javascript"></script>

<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-125747970-1');
</script>



<link rel="stylesheet" type="text/css" href="dhx/skins/web/dhtmlx.css" />
<link rel="stylesheet" type="text/css" href="common.css?11" />
<script src="barcode_utils.js?61" type="text/javascript"></script>
<link rel="stylesheet" href="barcode_style.css?61"></link>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js" type="text/javascript"></script>
<script src="https://kit.fontawesome.com/dbd5561dfa.js" crossorigin="anonymous"></script>
<script src="stores.js?20221024" type="text/javascript"></script>
<style>

html, body {
        width: 100%;      
        height: 100%;
        margin: 0px;      
    }
#layoutObj {
    height:100%;
    width:100%;
}
table.prev img {
  display: block;
  max-width:75px;
  max-height:75px;
  width: auto;
  height: auto;
}
td.prev_td {
	text-align: center;
	vertical-align: middle;
	width:80px
}

.dhtmlx_message_area{
    left:350px;
    right:auto;
}

img.button {
  width:auto;
  height:25px;
}

.rowhover {
    background-color: #f9f9f9;
}

.fa-custom-counter {
  transform: scale(0.75) translateX(50%) translateY(-50%);
}

span.instock {
    color: green;
}

span.oos {
    color: red;
}

#trends_opt {
    border: none;
    border-radius: 2px;
    cursor: pointer;
}
.clicked { background: #2589ce; }


.dismiss-message-btn {
  border: none;
  cursor: pointer;
  float: right;
}

.dismiss-message-btn:hover, .dismiss-message-btn:focus {
  pointer-events: all;    
}

</style>
<script>

dhtmlXPopup.prototype.showNextTo = function(id) {
    if (typeof(id) == "string") id = document.getElementById(id);
    var x = window.dhx4.absLeft(id);
    var y = window.dhx4.absTop(id);
    var w = id.offsetWidth;
    var h = id.offsetHeight;
    this.show(x,y,w,h);
};

var paramForm, formData, sysgrid, prodList, layoutObj, prodPop, dropsTabs, histPop, histChart, m_layout, trends;
var dropsLists = [];
var gridSort;
var cn_form, cn_list, cn_grid, cn_drops, cn_stores, cn_trends, cn_a;
var isLoading = false;
var foundNum = -1;
var cameraAvailable = mobileCheck();
var upcSrc;
var loadNum = 0;
var dropsWidthNum = 1;


var chk_icons = ['far fa-square', 'far fa-check-square'];
var arrows_icons = ['fas fa-long-arrow-alt-down', 'fas fa-long-arrow-alt-up'];
var radio_icons = ['far fa-circle', 'far fa-dot-circle'];

function getStoresNum()
{
	return Math.min(stores.length, parseInt(paramForm.getItemValue("num")) || 10);
}
function getStoreDistance(store)
{
    if(store.dist != undefined && store.dist >= 0)
        return store.dist;
    
    if(store.geoPoint == undefined || store.geoPoint.latitude == undefined || store.geoPoint.longitude == undefined || store.id == undefined || (store.deleted != undefined && store.deleted == true))
        return Number.MAX_VALUE;
    
    store.dist = getDistance(store.geoPoint.latitude, store.geoPoint.longitude)/1000;
    return store.dist;
}

var chartConfig = {
    type: 'line',
    animation:false,
    options: {
        responsive: true,
        title: {
            display: false
        },
        tooltips: {
            mode: 'nearest',
            intersect: false,
            callbacks: {
                afterLabel: function(tooltipItem, data) {
                    var foot = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].inventory;
                    if(foot)
                        foot = isNaN(parseFloat(foot))?"Status: "+availabilityStatusToString(foot):"Stock: "+foot;
                    return foot || '';
                },
                label: function(tooltipItems, data) { 
                    return  data.datasets[tooltipItems.datasetIndex].label +': $' + tooltipItems.yLabel;
                }
            }
        },
        hover: {
            mode: 'nearest',
            animationDuration: 0, // duration of animations when hovering an item
            intersect: true
        },
        legend: {
            display: false
        },
        layout: {
            padding: {
                left: 0,
                right: 10,
                top: 30,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                display: true,
                type: 'time',
                time: {
                    unit: 'day'
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Price'
                },
                ticks: {
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                        return '$' + value;
                    }
                }
            }]
        }
    },
    data: {
        datasets: [
            {
                label: 'Walmart',
                steppedLine: true,
                data: [],
                backgroundColor: 'rgb(50, 118, 210)',
                borderColor: 'rgb(50, 118, 210)',
                fill: false
            },
            {
                label: '3rd party',
                steppedLine: true,
                data: [],
                backgroundColor: 'rgb(184, 5, 23)',
                borderColor: 'rgb(184, 5, 23)',
                fill: false
            }]
    }
};

function stopLoading(cancelled = false)
{
    isLoading = false;
    
	if(!cancelled && foundNum == 0)
	{
		dhtmlx.message({
			type: "error",
			text: "This item is not sold in any nearby Walmart store. Check UPC code and try again."
		});
        if(paramForm.getItemValue("type")=="upc" && prodList.dataCount() > 0 && 
           !prodList.getSelected() && prodList.get(prodList.first()).Upc != "?" &&
           prodList.get(prodList.first()).Upc != paramForm.getItemValue("q"))
        {
            dhtmlx.confirm({
                top:'100', 
                left:'300',
                title:"Retry?",
                text:"Requested UPC is different than the found item. Do you want to try with found item UPC?",
                callback: function(result){
                    if(result)
                    {
                        prodList.select(prodList.first());
                        loadStockInfo();
                    }
                }
            });
        }
	}
	else 
	{
        if(gridSort != undefined)
            sysgrid.sortRows(gridSort.i, gridSort.t, gridSort.d);
        sysgrid.adjustColumnSize(1);
	}
	foundNum = -1;
}

const CheckIsLoading = (upc) => {
  return new Promise((resolve, reject) => {
    //if not loading - ok
    if(!isLoading)
        return resolve(true);
    
    //if loading with the same upc - don't
    if(sysgrid.upc == upc)
        return resolve(false);
    
    dhtmlx.confirm({
        top:'100', 
        left:'300',
        title:"Cancel",
        type:"confirm-warning",
        text:"Stop checking for current UPC and start and new check?",
        callback: resolve
    });
  });
}

function fixImage(img)
{
    if(!img.startsWith("http") && !img.startsWith("//"))
        img = "//i5.walmartimages.ca/"+img;
    return img;
}

function isStoreLoading(storeId)
{
    var loading = sysgrid.cellById(storeId, 6).getAttribute("loading");
    return (loading != undefined && loading == "true");
}
var loading_div = "<div class='dhx_cell_progress_img' style='position:relative'></div>";
function setStoreLoading(storeId)
{
    sysgrid.cellById(storeId, 6).setAttribute("loading","true");
	sysgrid.cellById(storeId, 5).setValue(loading_div);
}

function checkDone()
{
	if(!isLoading)
		return;
	
	for(var i = 0; i < sysgrid.getRowsNum(); i++)
	{
        if(isStoreLoading(sysgrid.getRowId(i)))
			return;	//still loading
	}
	if(isLoading)
		stopLoading();
}

function updateStatus(status, storeId)
{
    sysgrid.cellById(storeId, 6).setAttribute("loading", false)
    var s_cell = sysgrid.cellById(storeId, 5);
    s_cell.setValue(status);
    if(status == "NotSold")
    {
        sysgrid.setCellTextStyle(storeId,5,"color:gray;"); 
        s_cell.setAttribute("title","Not sold at this location");
        sysgrid.setRowHidden(storeId, paramForm.isItemChecked("hide"));
    }
    else if(status == "Available")
    {
        foundNum++;
        s_cell.setAttribute("title","4+ in stock");
        sysgrid.setCellTextStyle(storeId,5,"color:green;"); 
        sysgrid.setCellTextStyle(storeId,6,"color:green;"); 
        sysgrid.setRowHidden(storeId, false);
    }
    else if(status == "Limited")
    {
        foundNum++;
        s_cell.setValue("Limited");
        sysgrid.setCellTextStyle(storeId,5,"color:orange;"); 
        sysgrid.setCellTextStyle(storeId,6,"color:orange;"); 
        s_cell.setAttribute("title","2 or 3 in stock");
        sysgrid.setRowHidden(storeId, false);
    }
    else if(status == "OutOfStock")
    {
        foundNum++;
        s_cell.setAttribute("title","1 or less in stock");
        sysgrid.setCellTextStyle(storeId,5,"color:red;"); 
        sysgrid.setCellTextStyle(storeId,6,"color:red;"); 
        sysgrid.setRowHidden(storeId, false);
    }
    else if(status == "Error")
    {
        s_cell.setAttribute("title","Try again later");
        sysgrid.setCellTextStyle(storeId,5,"background-color:red;");
        sysgrid.setCellTextStyle(storeId,6,"background-color:red;"); 
        sysgrid.setRowHidden(storeId, false);
    }
}

function availabilityStatusToString(status)
{
    switch(status) {
      case "OUT_OF_STOCK":
        return "OutOfStock";
      case "LIMITED":
        return "Limited";
      case "AVAILABLE":
        return "Available";
      default:
        return "NotSold";
    }
}

function updateStoreInfo(store_info)
{
    var storeId_ret = store_info.id;
					
    if(sysgrid.getRowIndex(storeId_ret) < 0)
        return;
    var status = availabilityStatusToString(store_info.availabilityStatus);
    
    if(store_info.sellPrice != undefined && parseFloat(store_info.sellPrice) > 0)
    {
        sysgrid.cellById(storeId_ret, 4).setValue(store_info.sellPrice);
    }
    if(store_info.wasPrice && parseFloat(store_info.wasPrice) > parseFloat(store_info.sellPrice))
    {
        var tip = "reg. $"+store_info.wasPrice;
        sysgrid.cellById(storeId_ret, 4).setAttribute("title",tip);
    }
    var q_cell = sysgrid.cellById(storeId_ret, 6);
    if(q_cell.getValue() == "" || q_cell.getValue() == "N/A" || q_cell.getValue() == "Error" || isStoreLoading(storeId_ret))
    {
        if(store_info.availableToSellQty != undefined && store_info.availableToSellQty !== "")
        {
            if(q_cell.getValue() == "Error")
            {
                q_cell.setAttribute("title","");
                sysgrid.setCellTextStyle(storeId_ret,6,"background-color:white;");
                sysgrid.setCellTextStyle(storeId_ret,5,"background-color:white;");
            }
            q_cell.setValue(store_info.availableToSellQty);
        }
        else
            q_cell.setValue("N/A");
    }
    
    if(status != "NotSold" || isStoreLoading(storeId_ret))
    {
        updateStatus(status, storeId_ret);
    }
}

var maxRetry = 0;
function updateStore(upc, stores_left, retry=0, upd=false)
{
    if(sysgrid.upc != upc)
        return;
    
	if(stores_left.length == 0)
		return checkDone();
	
	var storeId = stores_left.shift();
	var updStr = upd?"&upd":"";
	if(isStoreLoading(storeId))
	{
	var jqxhr = $.get(
		"https:/stocktrack.ca/wm/availability.php?storeId="+storeId+"&upc="+upc+"&src="+upcSrc+updStr,
		function(result){
            if(sysgrid.upc != upc)
                return;
				
			if(!result || !result.info || result.info.length == 0)
			{
                if(!isStoreLoading(storeId))
                    return;
                
				if(retry<maxRetry)
				{
					setTimeout(function(){ updateStore(upc, [storeId], retry+1); }, (retry+1)*500);
				}
				else
				{
                    updateStatus("NotSold", false, storeId);
                    sysgrid.cellById(storeId, 6).setValue("N/A");
					//dhtmlx.message({
					//	type: "error",
					//	text: "Error while getting stock information for store #"+storeId+". Please try again later."
					//});
				}
			}
			else
			{
				var found_store = false;
				for(var i = 0; i < result.info.length; i++)
				{
					var store_info = result.info[i];
					if(store_info.id == storeId)
						found_store = true;
                    
					updateStoreInfo(store_info)
				}
				if(!found_store)
                {
					updateStatus("NotSold", false, storeId);
                }
			}
            sysgrid.refreshFilters();
		}
	);
	jqxhr.fail(function(){
        if(sysgrid.upc != upc)
            return;
        
        if(!isStoreLoading(storeId))
            return;
        
		if(retry<maxRetry)
		{
			setTimeout(function(){ updateStore(upc, [storeId], retry+1); }, (retry+1)*500);
		}
		else
		{
            updateStatus("Error", false, storeId);
            sysgrid.cellById(storeId, 6).setValue("N/A");
			dhtmlx.message({
				type: "error",
				text: "Error while getting stock information for store #"+storeId+". Please try again later."
			});
		}
	});
	jqxhr.always(function(){
        if(sysgrid.upc != upc)
            return;
		setTimeout(function(){ updateStore(upc, stores_left); }, 500);
		
	});
	}
	else
		updateStore(upc, stores_left);
}

function showChart(hist, this_html, label)
{
    if(!hist)
        return;
    
    histChart.data.datasets[0].data = [];
    histChart.data.datasets[1].data = [];
    histChart.options.legend.display = false;
    histChart.options.layout.padding.top = 30;
    histChart.options.title.display = false;
    if(label != undefined)
    {
        histChart.options.layout.padding.top = 0;
        histChart.options.title.display = true;
        histChart.options.title.text = label;
    }
    
    if(hist['1P'] && hist['1P'].length > 0)
    {
        histChart.data.datasets[0].data = hist['1P'];
    }
    if(hist['3P'] && hist['3P'].length > 0)
    {
        histChart.options.legend.display = true;
        histChart.options.layout.padding.top = 0;
        histChart.data.datasets[1].data = hist['3P'];
    }
    histChart.update();
    
    if(histChart.data.datasets[0].data.length > 1 || histChart.data.datasets[1].data.length > 1)
    {
        histPop.showNextTo(this_html);
    }
    else
        histPop.hide();
}

var regFormHeight = 233;
function onTypeChange()
{
	var t = paramForm.getSelect("type");
	paramForm.setItemLabel("q", t.options[t.selectedIndex].text );
	paramForm.setItemLabel("b_check", paramForm.getItemValue("type") == "search"?"Search":"Check");
	if(cameraAvailable && paramForm.getItemValue("type") == "upc")
		paramForm.showItem("b_scan");
	else
		paramForm.hideItem("b_scan");

	if(paramForm.getItemValue("type") == "deals" || paramForm.getItemValue("type") == "search")
    {
		paramForm.showItem("b_options");
	
        if(paramForm.getItemValue("type") == "deals")
        {
            paramForm.hideItem("search_opt");
            paramForm.hideItem("q");
            
            layoutObj.cells(cn_form).setHeight(regFormHeight-25);
        }
        else
        {
            paramForm.showItem("q");
            paramForm.hideItem("deals_opt");
            layoutObj.cells(cn_form).setHeight(regFormHeight);
        }
    }
    else
    {
        paramForm.showItem("q");
        paramForm.hideItem("deals_opt");
        paramForm.hideItem("search_opt");
        layoutObj.cells(cn_form).setHeight(regFormHeight);
		paramForm.hideItem("b_options");
    }
}

function initDropsList(id)
{
    var dropsListItem = dropsTabs.tabs(id).attachDataView({
        type:{
            template:"<table class='prev'><tr><td class='prev_td'><img src='#Image#'/></td><td><a href='#Href#' target='_blank'>#Name#<br/></a>SKU: #Sku#<br/>UPC: #Upc#<br/>Category: #Category#<br/>Price: <strike>$#PriceMax#</strike>$#Price#<br/>Save: <font color='red'>#Save#%</font><br/>Online stock: #Stock#</td></tr></table>",
            template_loading:loading_div
        },
        autowidth: dropsWidthNum,
        datatype: "json"
    });
    dropsListItem.attachEvent("onItemClick", function (id){
        //get selected
        var list = getDropsList();
        CheckIsLoading(list.get(id).Upc).then(function(result) {
            if(!result)
                return;
            if(list.get(id).Upc != undefined && list.get(id).Upc != "")
            {
                list.select(id);
                paramForm.setItemValue("type", "upc");
                paramForm.setItemValue("q", list.get(id).Upc.split(",")[0]);
                onTypeChange();
                
                prodList.clearAll();
                getProductPreview();
                loadStockInfo("drops");
                call_prnt(layoutObj.cells(cn_grid));
            }
        });
        return true;
    });
    
	dropsListItem.attachEvent("onMouseMove", function (id, ev, html){
		var item = this.get(id);
        
        if(item.History)
        {
            showChart(item.History, html);
        }
        else if(item.Sku && item.Sku.length > 0 && item.Sku != "?")
        {
            $.get("price_hist.php?sku="+item.Sku.split(",")[0], function(data){
                item.History = data;
                showChart(data, html);
            }, "json");
        }
		return true;
	});
	dropsListItem.attachEvent("onMouseOut", function (ev){
        if(!histPop.showit)
            histPop.hide();
        histPop.showit = false;

		return true;
	});
    return dropsListItem;
}

function getDropsList(id)
{
    if(id == undefined)
        id = dropsTabs.getActiveTab();
    
    if(dropsLists[id] == undefined)
        dropsLists[id] = initDropsList(id);
    
    return dropsLists[id]
}

function getSelectedDropItem()
{
    var list = getDropsList();
    return list.get(list.getSelected());
}

function updateStorePriceInfo()
{
	foundNum = 0;
	let upc = (upcSrc=="drops"?getSelectedDropItem().Upc.split(",")[0]:(prodList.getSelected()?prodList.get(prodList.getSelected()).Upc:paramForm.getItemValue("q")));
	window.top.history.replaceState({"store":"wm","upc":upc}, "Walmart Inventory Check UPC "+upc, "?s=wm&upc="+upc);
    if(sysgrid.getRowsNum() > 0)
	{
        sysgrid.setColumnHidden(7, true);
        sysgrid.upc = upc;
        ga('send', 'event', 'Walmart', 'store_stock;'+upcSrc, upc);
		let stores = sysgrid.getAllRowIds("|").split("|");
		stores.forEach(function(storeId){setStoreLoading(storeId);});
        $.get(
		"https:/stocktrack.ca/wm/availability.php?storeId="+stores.join("|")+"&upc="+upc+"&src="+upcSrc,
            function(result){
                if(result)
                {
                    if(result.upc != undefined && upc != result.upc)
                    {
                        upc = result.upc;
                        sysgrid.upc = upc;
                        paramForm.setItemValue("q", upc);
                        window.top.history.replaceState({"store":"wm","upc":upc}, "Walmart Inventory Check UPC "+upc, "?s=wm&upc="+upc);
                    }
                    if(result.info)
                        result.info.forEach(function(store){updateStoreInfo(store);});
                }
                sysgrid.refreshFilters();
            }
        ).then(function(){
            let stores_left = [];
            for(let i = 0; i < stores.length; i++)
            {
                if(isStoreLoading(stores[i]))
                    stores_left.push(stores[i]);
            }
            if(stores_left.length == 0)
                stopLoading();
            else
            {
            updateStore(upc, stores_left);            }
        });
	}
    else
	{
        stopLoading();
	}
}

    
function attachPager()
{
    // attach paging status bar
    layoutObj.cells(cn_list).attachStatusBar({
        text: "<div id='prod_paging' style='text-align:right;margin-top: 5px;margin-right: 10px'></div>",
        paging: true
    });
    var prodListPager = prodList.define("pager",{
                container:"prod_paging",
                size:5,
                group:3,
                template:"{common.first()}{common.prev()}{common.pages()}{common.next()}{common.last()} <span style='margin-left: 10px'> page <span id='cur_page'>1</span> of {obj.limit}</span>"
            });
    prodListPager.attachEvent("onAfterPageChange", function (new_page){
        let page = parseInt(new_page);
        $("span#cur_page").text(page+1);
        
        let id_first = prodList.idByIndex(page*5);
        let first_pageItem = id_first?prodList.get(id_first):null;
        if(first_pageItem && first_pageItem.Href == "")
            getProductPreview(paramForm.getItemValue("type") == "search"?Math.floor(page/12)+1:page+1);//walmart search page is 60 items
        else
        {
            for(let i = 0; i < 5; i++)
            {
                let idx = page*5+i;
                if(idx < prodList.dataCount())
                {
                    let id = prodList.idByIndex(idx);
                    if(id)
                    {
                        let pageItem = prodList.get(id);
                        
                        if(pageItem.Image == "" && pageItem.ImageData != undefined)
                        {
                            pageItem.Image = pageItem.ImageData;
                            prodList.refresh(id);
                        }
                        
                        if(pageItem.Upc == "?")
                            getItemInfo(id, false);
                    }
                }
            }
        }
        return true;
    });
}

function getProductPreview(page = 1)
{
    if(page == 1)
        layoutObj.cells(cn_list).detachStatusBar();
    
	prodList.define("type",{
		template:"<table class='prev'><tr><td class='prev_td'><img src='#Image#'/></td><td><a href='#Href#' target='_blank'>#Name#</a>#SkuText#<br/>UPC: #Upc#<br/>Online price: $#Price#<br/>Online stock: #Stock#<br/>Sold by: #Seller#</td></tr></table>"
	});
	
	var t = paramForm.getItemValue("type");
	var n = (t=="search" || t=="deals"?60:1);
	var q = paramForm.getItemValue("q");
    if(t=="search")
    {
        window.top.history.replaceState({"store":"wm","search":q}, "Walmart Stock Search \""+q+"\"", "?s=wm&search="+q);
    }
	var url = (t=="deals"?"deals":"search")+".php?n=" + n + "&p="+page+"&t="+t;
	if(t=="deals")
	{
        n = 5;  //n is now number of items on a list page...
        var save = paramForm.getItemValue("save");
		url += "&oos="+paramForm.getItemValue("deals_oos")+"&order="+paramForm.getItemValue("deals_order")+"&save="+save;
        var kw = paramForm.getItemValue("deals_keyword");
        if(kw.length > 0)
        {
            url += "&keyword="+kw;
        }
        /*
		if(paramForm.isItemChecked("deals_near"))
		{
			if(lng == 0 || lat == 0)
			{
				getNearestStores(function(){
					getProductPreview(page);
				});
				return;
			}
			url += "&stores="+stores.slice(0, getStoresNum()).map(x => x.id).join("|");
		}
        */
        url += "&store="+paramForm.getItemValue("deals_store");		
		prodList.define("type",{
			template:"<table class='prev'><tr><td class='prev_td'><img src='#Image#'/></td><td><a href='#Href#' target='_blank'>#Name#</a>#SkuText#<br/>UPC: #Upc#<br/>Price: <strike>$#PriceReg#</strike>$#Price#<br/>Save: <font color='red'>#Save#%</font>#LastUp#</td></tr></table>"
		});
        if(page == 1)
            ga('send', 'event', 'Walmart', 'deals', save+";"+kw);
	}
    else if(t == "search")
    {
        url += "&q="+q + "&wm_only="+paramForm.getItemValue("wm_only")+"&order="+paramForm.getItemValue("search_order");
        var savings = paramForm.getCombo("savings").getChecked();
        if(savings.length > 0)
            url += "&save="+savings.join("+");
        if(page==1)
            ga('send', 'event', 'Walmart', 'search', q);
    }
	else
	{
		url += "&q="+q;
	}
	layoutObj.cells(cn_form).progressOn();
	layoutObj.cells(cn_list).progressOn();
	var jqxhr = $.get(url, function(result){
		if(result == null || result.result == null)
		{
			return;
		}
        if(page == 1 && result.total && result.total > 5)
            attachPager();

		res = result.result;
		function addItem(item_id, item)
		{
			if(!prodList.exists(item_id))
				prodList.add({
					id:item_id,
					Href:"",
					Name:"",
					Upc:"?",
					SkuText:"",
					Image:"",
					Price:"?",
					Stock:"?",
					Seller:"?",
					PriceReg:"",
					Save:"",
                    LastUp:""});
			if(item)
			{
				var list_item = prodList.get(item_id);
				list_item.Href = (item.href.startsWith("http")?item.href:"https://www.walmart.ca"+item.href);
				list_item.Name = item.name;
				list_item.Sku = item.sku;
				list_item.ImageData = fixImage(item.img);
				
				if(item.upc)
				{
					list_item.Upc = item.upc;
				}
                if(item.sku && item.sku.length > 0 && item.sku != "?")
                {
                    list_item.SkuText = "<br/>SKU: "+item.sku;
                }
				if(item.feat && item.feat.length > 0)
				{
					list_item.Features = item.feat;
				}
				else if(item.desc && item.desc.length > 0)
				{
					list_item.Features = item.desc;
				}
				else if(item.spec && item.spec.length > 0)
				{
					list_item.Features = item.spec;
				}
				if(item.images)
				{
					list_item.Images = item.images.map(x => fixImage(x));
				}
				
				if(item.minCurrentPrice && item.maxCurrentPrice && item.maxRegularPrice)
				{
					list_item.PriceReg = parseFloat(item.maxRegularPrice);
					list_item.Price = parseFloat(item.minCurrentPrice);
					list_item.Save = Math.round((list_item.PriceReg-list_item.Price)/list_item.PriceReg*100);
					var maxCur = parseFloat(item.maxCurrentPrice);
					if(maxCur != list_item.Price)
					{
						list_item.Price += "-$"+maxCur;
						list_item.Save = Math.round((list_item.PriceReg-maxCur)/list_item.PriceReg*100)+"%-"+list_item.Save;
					}
				}
                if(item.last_update)
                {
                    list_item.LastUp = "<br/>Last checked: "+(new Date(item.last_update*1000).toLocaleDateString("en-Ca"));
                }
				
				prodList.refresh(item_id);
			}
			
		}
		var skus = [];
		var ids = [];
		var i_id = (page-1)*n;
		for(var i = 0; i < res.length; i++){
            i_id++;
            if(res[i].sku != "?" && res[i].sku.length > 0)
            {
                skus.push(res[i].sku);
                ids.push(i_id);
            }
			addItem(i_id, res[i]);
			if(i < 5)
			{
				prodList.get(i_id).Image = prodList.get(i_id).ImageData;
				prodList.refresh(i_id);
			}
		}
		
		var itemCount = prodList.dataCount();
		if(skus.length > 0 && itemCount > 0 && t!="deals")
		{
			$.post("availability.php", { sku: skus.join("|") }, function(result){
				if(!result)
					return;
				for (var i in result) {
					var info = result[i].online[0];
					var n = skus.indexOf(info.sku);
					if(n < 0)
						continue;
					var list_item = prodList.get(ids[n]);
                    if(!list_item || list_item.Sku != info.sku)
                    {
                        return; //too late, wrong item in the list
                    }
                    
					if(info.minCurrentPrice != undefined)
					{
						var price = parseFloat(info.minCurrentPrice);
						if(info.maxRegularPrice != undefined)
						{
							var reg = parseFloat(info.maxRegularPrice);
							if(price < reg)
							{
								price = "<strike>"+reg+"</strike>  "+price;
							}
						}
						list_item.Price = price;
					}
					if(info.inventory != undefined)
                    {
                        list_item.Stock = info.inventory;
                        if(info.status && info.status != 'Available')
                             list_item.Stock += " ("+info.status+")";
                    }
					if(info.sellerName != undefined)
						list_item.Seller = info.sellerName;
					prodList.refresh(ids[n]);
				}
			});
		}
		if(page==1 && itemCount > 0)
		{
			if(result.total && itemCount < result.total)
			{
				for(var i = itemCount; i < result.total; i++){
					addItem(++i_id);
				}
			}
		}
		for(var i = 0; i < 5; i++)
		{
			var idx = (page-1)*n+i;
			if(idx < itemCount)
			{
				var id = prodList.idByIndex(idx);
				if(id && prodList.get(id).Upc == "?")
					getItemInfo(id, false);
			}
		}
		
		if(t == "sku")
		{
			if(itemCount > 0 && prodList.get(prodList.first()).Upc != "?")
			{
				prodList.select(prodList.first());
				loadStockInfo();
			}
			else
			{
				dhtmlx.message({
					type: "error",
					text: "Product with SKU: "+escapeHtml(q)+" was not found. Check SKU and try again."
				});
			}
		}
		
		prodList.refresh();
	}, "json");
	jqxhr.always(function(){
		layoutObj.cells(cn_form).progressOff();
		layoutObj.cells(cn_list).progressOff();
	});
}

function updateDropsHeader(id, count)
{
    dropsTabs.tabs(id).setText(id[0].toUpperCase() + id.substring(1) + "(" + count + ")");
}
var autoDropsLoad = true;
function loadPriceDrops(id, n)
{
    var url = "https://stocktrack.ca/wm/drops_data.php?t="+id;
    var dropTB = dropsTabs.tabs(id).getAttachedToolbar();
    if (dropTB != null)
    {
        if(dropTB.cats != undefined)
        {
            var excat = Object.keys(dropTB.cats).filter(function(key){return dropTB.cats[key] === true;});
            //var incat = Object.keys(dropTB.cats).filter(function(key){return dropTB.cats[key] === false;});
            if(excat.length > 0)
                url += "&excat="+encodeURIComponent(excat.join("|"));
            //else
            //    url += "&incat="+encodeURIComponent(incat.join("|"));
        }
        if(dropTB.sort != undefined)
        {
            url += "&sort="+dropTB.sort+"&dir="+(dropTB.asc?"asc":"desc");
        }
        if(dropTB.search != undefined && dropTB.search.length > 0)
        {
            url += "&search="+encodeURIComponent(dropTB.search);
        }
    
        url += "&oos="+dropTB.getItemState(id+"OOS");
    }
    if(n != undefined)
    {
        url += "&count="+n;
        $.get(url, function(data){
            if(data && data.total_count != undefined)
                updateDropsHeader(id, data.total_count);
        }, "json");
    }
    else
    {
        var list = getDropsList(id);
        list.clearAll();
        list.load(url, function(res){
            if(n == undefined || n > 0)
            {
                var wasAuto = autoDropsLoad;
                autoDropsLoad = false;
                if(wasAuto && list.dataCount() == 0)
                {
                    if(id=="today")
                    {
                        autoDropsLoad = true;
                        setTimeout(function(){dropsTabs.cells("yesterday").setActive(true);}, 1000);
                    }
                    else if(id=="yesterday")
                    {
                        autoDropsLoad = true;
                        setTimeout(function(){dropsTabs.cells("weekly").setActive(true);}, 1000);
                    }
                }
            }
            list.refresh();
            updateDropsHeader(id, list.dataCount());
            
            
            if(dropTB == null)
            {
                dropTB = dropsTabs.tabs(id).attachToolbar();
                //dropTB.setIconset("awesome");
                
                let chk_icons = ['/square_black.svg', '/check_black.svg'];
				let radio_icons = ['/circle.svg', '/circle_dot.svg'];
                
                //Categories
                res = JSON.parse(res);
                if(res.categories != undefined && res.categories.length > 0)
                {
                    var opts = [["All", 'obj', "All ("+list.dataCount()+")", '']];
                    for(var i = 0; i < res.categories.length; i++)
                    {
                        var cat = res.categories[i];
                        opts.push([cat.name, 'obj', cat.name + " ("+cat.count+")", chk_icons[1]]);
                    }
                    dropTB.addButtonSelect(id+"Categories", null, "Categories", opts, null, null, 'disabled', true);
                    
                    //HACK ALERT: we have to manipulate internal html directly here to handle clicking on image
                    $(dropTB.objPull[dropTB.idPrefix+id+"Categories"].polygon).find(".td_btn_img").click(function( event ) {
                      if(!event.currentTarget.firstElementChild)
                          return;
                      event.stopPropagation();
                      var imgSrc = event.currentTarget.firstElementChild.getAttribute("src");
                      var text = event.currentTarget.nextElementSibling.textContent;
                      var m = text.match(/(.+)\s\(\d+\)/);
                      if(m)
                      {
                        var tb_id = m[1];
                        var cats = dropTB.cats || {};
                        var state = (imgSrc == chk_icons[1]);
                        cats[tb_id] = state;
                        dropTB.cats = cats;
                        dropTB.setListOptionImage(id+"Categories", tb_id, chk_icons[state?0:1]);
                        // reload drops with new set of items
                        loadPriceDrops(id);
                      }
                    });
                    dropTB.addSeparator(id+"SepCat", null);
                }
                //Sort
                var opts = [
                    ["save_p", 'obj', "Save %", radio_icons[1]],
                    ["save_a", 'obj', "Save $", radio_icons[0]],
                    ["price", 'obj', "Price", radio_icons[0]],
                    ["cat", 'obj', "Category", radio_icons[0]],
                    ["upd", 'obj', "Update time", radio_icons[0]]
                ];
                dropTB.addButtonSelect(id+"Sort", null, "<i class = '"+arrows_icons[0]+"'> </i> " + "Sort", opts, null, null, 'disabled', false);
				dropTB.sort = opts[0][0];
                dropTB.attachEvent("onClick", function(tb_id){
                    if(this.getListOptionSelected(id+"Categories") == tb_id)
                    {
                        var cats = this.cats || {};
                        var all_opts = dropTB.getAllListOptions(id+"Categories");
                        for(var i=0;i<all_opts.length;i++){
                            var optionId = all_opts[i];
                            var is_it = optionId==tb_id || tb_id == "All";
                            if(optionId != "All")
                            {
                                cats[optionId] = !is_it;
                                dropTB.setListOptionImage(id+"Categories", optionId, chk_icons[is_it?1:0]);
                            }
						}
                        this.cats = cats;
                        // reload drops with new set of items
                        loadPriceDrops(id);
                    }
                    else if(this.getListOptionSelected(id+"Sort") == tb_id)
                    {
                        dropTB.sort = tb_id;
						dropTB.forEachListOption(id+"Sort", function(optionId){
							dropTB.setListOptionImage(id+"Sort", optionId, radio_icons[optionId==tb_id?1:0]);
						});
                        loadPriceDrops(id); // reload drops with new sort
                    }
					else if(tb_id == id+"Sort")
					{
						dropTB.asc = !(dropTB.asc);
						dropTB.setItemText(tb_id, "<i class = '"+arrows_icons[dropTB.asc?1:0]+"'> </i> " + "Sort");
                        loadPriceDrops(id); // reload drops with new sort
					}
                });
                dropTB.addSeparator(id+"SepSort", null);

                //Search input
				dropTB.addInput(id+"Search", null, "", 70);
				dropTB.setItemToolTip(id+"Search", "Search");
				dropTB.attachEvent("onEnter", function(tb_id, value){
					var inp = dropTB.getInput(tb_id);
					if(tb_id == id+"Search" && inp)
					{
						dropTB.search = inp.value;
					}
					loadPriceDrops(id);
				});
                
                var inp = dropTB.getInput(id+"Search");
                inp.addEventListener('input', function(e) {
                    dropTB.search = e.target.value;
                    if(inp.tm)
                        clearTimeout(inp.tm);
                    inp.tm = setTimeout(function(){ loadPriceDrops(id); }, 500);
                });
                dropTB.addSeparator(id+"Search", null);
                
                var chk_icons_white = ['/square_white.svg', '/check_white.svg'];
                dropTB.addButtonTwoState(id+"OOS", null, "OOS", chk_icons_white[0]);
                dropTB.setItemState(id+"OOS", false, false);
                dropTB.attachEvent("onStateChange", function(tb_id, state){
                    if(tb_id == id+"OOS")
					{
                        dropTB.setItemImage(tb_id, chk_icons_white[state?1:0]);
                        loadPriceDrops(id); // reload drops with new sort
					}
                });
            }
        });
    }
}

function updateGridWithStores(do_next)
{
    let stores_data = {
        rows:[]
    };
    
    for(var i = 0; i < getStoresNum(); i++)
    {
		var store_info = stores[i];
        if(getStoreDistance(store_info) == Number.MAX_VALUE)
            continue;
        
        let storeId = store_info.id;
        
        let desc = "<a href='https://www.walmart.ca/en/stores-near-me/"+storeId+"' target='_blanc'>";
		if(store_info.displayName)
			desc += store_info.displayName;
        desc += "</a>";
        
        let addr = [];
		if(store_info.address)
        {
            if(store_info.address.address1)
                addr.push(store_info.address.address1)
            if(store_info.address.address2)
                addr.push(store_info.address.address2)
            if(store_info.address.city)
                addr.push(store_info.address.city)
            if(store_info.address.state)
                addr.push(store_info.address.state)
            if(store_info.address.postalCode)
                addr.push(store_info.address.postalCode)
        }
        addr = addr.join(", ");
        
        let dist = getStoreDistance(store_info).toFixed(1) + " km";
        
        stores_data.rows.push({id: storeId, data: [storeId, desc, addr, dist]});
        
	}
    sysgrid.parse(stores_data,"json");
	
	if(do_next != undefined)
		do_next();
}

function loadStockInfo(src) {
	
	isLoading = true;
    
	upcSrc = src;
	if(src == undefined)
		upcSrc = paramForm.getItemValue("type");
	
    sysgrid.clearAll();
	
	getNearestStores(function(){
		updateGridWithStores(updateStorePriceInfo);
	});
}

function refresh_grid(src) {
    if((!paramForm.isItemHidden("q") && paramForm.getItemValue("q").length == 0))
        return;
    
    CheckIsLoading(paramForm.getItemValue("q")).then(function(result) {
        if(!result)
            return;
	
        prodList.clearAll();
        
        if(paramForm.getItemValue("type")=="upc")
        {
            loadStockInfo(src);
            setTimeout(getProductPreview, 500);
        }
        else
            getProductPreview();
    });
}

function getItemInfo(id, checkStock)
{
    if(!prodList.get(id).Sku || prodList.get(id).Sku.length == 0 || prodList.get(id).Sku == "?")
        return;
    
	if(checkStock)
		layoutObj.cells(cn_list).progressOn();
	
	var jqxhr = $.get(
		"get-upc.php?sku="+prodList.get(id).Sku,
		function(result){
			if(!result || !result.status || result.status !="ok" || !result.result || !result.result.upc)
			{
				//error
				return;
			}
			result = result.result
			prodList.get(id).Upc = result.upc;
			if(result.feat && result.feat.length > 0)
			{
				prodList.get(id).Features = result.feat;
			}
			else if(result.desc && result.desc.length > 0)
			{
				prodList.get(id).Features = result.desc;
			}
			if(result.images)
			{
				prodList.get(id).Images = result.images.map(x => fixImage(x));
			}
			prodList.refresh(id);
			if(checkStock)
			{
				loadStockInfo();
			}
		});
		
	if(checkStock)
		jqxhr.always(function(){layoutObj.cells(cn_list).progressOff();})
}
    
var formData = [{
        type: "settings",
        position: "label-left",
        labelWidth: 120
	},{type:"block", blockOffset:0, list:[
        {
            type: "select", label: "Type", name: "type", options:[
                {text: "UPC", value: "upc", selected:true},
                {text: "Search", value: "search"},
                {text: "SKU", value: "sku"},
                {text: "Deals", value: "deals"}
            ],
        },{type:"newcolumn"},
        {
            type: 	"button",
            name:	"b_options",
            width:  30,
            value: 	"...",
            hidden: true
        }]
    },
	{type: "fieldset",  name: "deals_opt", width:250, label: "Deals Options", offsetLeft:20, hidden:true,
		list:[
			{
				type: "settings",
				position: "label-right",
				labelWidth: 160
			},
			{
				type: "select", label: "Store:", name: "deals_store", position: "label-left", labelWidth:50, inputWidth:100, options:[],
			},
			{
				type: "input",
				position: "label-left",
				labelWidth: 50,
				label: "Keyword:",
                width:100,
				name: "deals_keyword"
			},
			{
				type: "select", label: "Sort by:", name: "deals_order", position: "label-left", labelWidth:50, options:[
					{text: "Last checked", value: "date", selected:true},
					{text: "Max discount", value: "max_save"},
					{text: "Min sale price", value: "min_sale"},
					{text: "Max reg. price", value: "max_price"},
					{text: "Min reg. price", value: "min_price"}
				],
			},
			{
				type: "select", label: "Min save:", name: "save", position: "label-left", labelWidth:50, options:[
					{text: "5%", value: "5"},
					{text: "25%", value: "25"},
					{text: "50%", value: "50", selected:true},
					{text: "75%", value: "75"},
					{text: "90%", value: "90"}
				],
			},
			{
				type: "checkbox",
				label: "Include OOS",
				name: "deals_oos",
				checked: false
			}
            		]
	},
	{type: "fieldset",  name: "search_opt", width:250, label: "Search Options", offsetLeft:20, hidden:true,
		list:[
			{
				type: "settings",
				position: "label-right",
				labelWidth: 160
			},
			{
				type: "checkbox",
				label: "Only sold by Walmart",
				name: "wm_only",
				checked: true
			},{
            type: "combo", label: "Savings", name: "savings", comboType: "checkbox", hidden:true,
            labelWidth: 50, width: 150, position: "label-left", options:[
                {text: "Clearance", value: "32", checked: false},
                {text: "Rollback", value: "31", checked: false},
                {text: "Reduced Price", value: "38", checked: false},
                {text: "Save", value: "36", checked: false},
                {text: "Autosave", value: "37", checked: false}
                ]
            },{
				type: "select", label: "Sort by:", name: "search_order", position: "label-left", labelWidth:50, options:[
					{text: "Relevance", value: "relevance", selected:true},
					{text: "Newest", value: "newest"},
					{text: "Popular", value: "popular"},
					{text: "Rating", value: "rating"},
					{text: "Min price", value: "min_price"},
					{text: "Max price", value: "max_price"}
				],
			}
		]
	},
	{type:"block", blockOffset:0, list:[
		{
			type: "input",
			label: "UPC",
			name: "q",
            "padding-right": "30px",
			value: (searchParams.has("upc") && !isNaN(parseInt(searchParams.get("upc"))))?parseInt(searchParams.get("upc")).toString():""
		},{type:"newcolumn"},
		{
		   type: 	"button",
		   name:	"b_scan",
		   width:   30,
           "margin-left": "-30px",
		   value: 	"<img src='barcode_icon.png' class='button'>"
		}
		]},
	{
        type: "input",
        label: "Location",
	    name: "loc",
	    value: ""
    },{
        type: "input",
        label: "Stores number",
	    name: "num",
	    value: (searchParams.has("num") && !isNaN(parseInt(searchParams.get("num"))))?parseInt(searchParams.get("num")):"10"
    },{
        type: "checkbox",
        label: "Hide 'NotSold' stores",
	    name: "hide",
	    checked: true
    }, {
	   type: 	"button",
	   name:	"b_check",
	   value: 	"Check"
	}];

	
function doOnLoad() {
	
	var l_pat = "4A"
	cn_form = "a";
	cn_list = "b";
	cn_grid = "c";
	cn_drops = "d";
    
	if(window.top.innerHeight > window.top.innerWidth || window.top.innerWidth < 1280){
        dropsWidthNum = Math.floor(window.innerWidth/302);
		l_pat = "4F"
		cn_form = "a";
		cn_list = "b";
		cn_grid = "c";
        cn_drops = "d";
	}
    if(window.top.innerHeight < 660+(dropsWidthNum>1?200:0))
    {
        document.getElementById("layoutObj").style.height = (660+(dropsWidthNum>1?200:0))+"px";
    }
    if(window.top.innerWidth < 900)
    {
        document.getElementById("layoutObj").style.width = "900px";
    }
	layoutObj = new dhtmlXLayoutObject("layoutObj", l_pat);
	layoutObj.cells(cn_form).setText("Walmart Inventory Checker");
	//layoutObj.cells(cn_list).setText("Product Search Results");
	//layoutObj.cells(cn_drops).setText("Walmart Online Price Drops");
	paramForm = layoutObj.cells(cn_form).attachForm(formData);
	layoutObj.cells(cn_form).setWidth(cameraAvailable?360:320);
	layoutObj.cells(cn_form).setMinWidth(320);
	layoutObj.cells(cn_form).setHeight(regFormHeight);
	onTypeChange();
    
    m_layout = layoutObj.cells(cn_grid).attachLayout("3U");
    cn_stores = "a";
    cn_a = "b";
    cn_trends = "c";
    sysgrid = m_layout.cells(cn_stores).attachGrid();
	m_layout.cells(cn_stores).setHeight(140);
	//m_layout.cells(cn_stores).setText("Walmart Stores Inventory");
     
    //sysgrid.enableAutoHeight(true);
	layoutObj.cells(cn_grid).setMinWidth(600);
	layoutObj.cells(cn_grid).setWidth(600);
	layoutObj.cells(cn_grid).setMinHeight(480);
	//layoutObj.cells(cn_grid).setHeight(600);
	
	prodList = layoutObj.cells(cn_list).attachList({
		edit:false,
		type:{
			height:"auto"
		}
	});
    
	prodList.attachEvent("onItemClick", function (id){
        CheckIsLoading(prodList.get(id).Upc).then(function(result) {
            if(!result)
                return;
            prodList.select(id);
            if(prodList.get(id).Upc != "?")
            {
                loadStockInfo();
            }
            else
            {
                getItemInfo(id, true);
            }
            call_prnt(layoutObj.cells(cn_grid));
        });
		return true;
	});
	prodPop = new dhtmlXPopup({
		mode : "right"
		});
	var image_timer = undefined;
	var cur_item = undefined;
	function stopShowingImages()
	{
		if(image_timer != undefined)
		{
			clearInterval(image_timer);
			image_timer = undefined;
		}
		if(cur_item != undefined)
		{
			var pageItem = prodList.get(cur_item);
			if(pageItem.ImageData != undefined && pageItem.Image != pageItem.ImageData)
			{
				pageItem.Image = pageItem.ImageData;
				prodList.refresh(cur_item);
			}
			cur_item = undefined;
		}
	}
	prodList.attachEvent("onMouseMove", function (id, ev, html){
		var item = prodList.get(id);
        if(item.History)
        {
            showChart(item.History, html, "Walmart.ca Price and Stock History");
        }
        else if(item.Sku && item.Sku.length > 0 && item.Sku != "?")
        {
            $.get("price_hist.php?sku="+item.Sku.split(",")[0], function(data){
                item.History = data;
                showChart(data, html, "Walmart.ca Price and Stock History");
            }, "json");
        }
        /*
		if(item.Features != undefined)
		{
			prodPop.attachHTML(item.Features);
			prodPop.showNextTo(html);
		}
        */
		if(item.Images != undefined && item.Images.length > 1)
		{
			cur_item = id;
			if(!image_timer)
			{
				image_timer = setInterval(function(){
					var this_item = prodList.get(cur_item);
                    if(this_item)
                    {
                        var image_idx = this_item.ImgIdx || 0;
                        image_idx = (image_idx+1)%this_item.Images.length;
                        this_item.ImgIdx = image_idx;
                        this_item.Image = this_item.Images[image_idx].replace("Large","Thumbnails");
                        prodList.refresh(cur_item);
                    }
				}, 1000);
			}
		}
		else
			stopShowingImages();
		return true;
	});
	prodList.attachEvent("onMouseOut", function (ev){
        if(!histPop.showit)
            histPop.hide();
        histPop.showit = false;
        
		//prodPop.hide();
		stopShowingImages();
		return true;
	});

    dropsTabs = layoutObj.cells(cn_drops).attachTabbar({
        parent: "drops_tabs",
        tabs: [
            {id: "today", text: "Today"},
            {id: "yesterday", text: "Yesterday"},
            {id: "weekly", text: "Weekly"}
        ]
    });
    layoutObj.cells(cn_drops).showHeader();
    
    dropsTabs.attachEvent("onSelect", function(newId, lastId){
        loadPriceDrops(newId);
        return true;
    });
    
	histPop = new dhtmlXPopup({
		mode : "left"
		});
    histPop.attachHTML("<div style='width:350px;height:180px;border:1px solid #A4BED4; background-color:white;'><canvas id='hist_prod'/></div>");
    $( "#hist_prod" ).hover(
      function() {
          histPop.showit = true;
          histPop.show();
      }, function() {
        if(!histPop.showit)
            histPop.hide();
        histPop.showit = false;
      }
    );
    
    histChart = new Chart("hist_prod", chartConfig);
    
    dropsTabs.cells("today").setActive(true);
    loadPriceDrops("yesterday", 0);
    loadPriceDrops("weekly", 0);
    
	layoutObj.cells(cn_drops).setMinWidth(320);
	layoutObj.cells(cn_drops).setWidth(380);
	layoutObj.cells(cn_drops).setMinHeight(140);
    
    layoutObj.attachEvent("onDblClick", function(name){
        if(name==cn_drops)
        {
            loadPriceDrops(dropsTabs.getActiveTab());
            return false;
        }
    });
	
paramForm.attachEvent("onButtonClick", function(name) {
	if(name == "b_check")
    {
		refresh_grid();
        call_prnt(layoutObj.cells(cn_grid));
    }
	else if(name == "b_scan")
    {
		scan_barcode(function(code)
		{
          paramForm.setItemValue("q", code.slice(0, -1));//remove last digit
          refresh_grid("barcode");
          call_prnt(layoutObj.cells(cn_grid));
		});
    }
	else if(name == "b_options")
    {
        if(paramForm.getItemValue("type") == "deals")
        {
            if(paramForm.isItemHidden("deals_opt"))
            {
                paramForm.showItem("deals_opt");
                var extraHeight = 165;
                layoutObj.cells(cn_form).setHeight(regFormHeight+extraHeight);
            }
            else
            {
                paramForm.hideItem("deals_opt");
                layoutObj.cells(cn_form).setHeight(regFormHeight-25);
            }
        }
        else if(paramForm.getItemValue("type") == "search")
        {
            if(paramForm.isItemHidden("search_opt"))
            {
                paramForm.showItem("search_opt");
                layoutObj.cells(cn_form).setHeight(regFormHeight+110);
            }
            else
            {
                paramForm.hideItem("search_opt");
                layoutObj.cells(cn_form).setHeight(regFormHeight);
            }
        }
    }
});
paramForm.attachEvent("onEnter", function(id, value){
    refresh_grid();
	call_prnt(layoutObj.cells(cn_grid));
});

function loadTrends()
{
    let id = "trends";
    let url = "trends_data.php?v=3";
    let trendsTB = m_layout.cells(cn_trends).getAttachedToolbar();
    if (trendsTB != null)
    {
        if(trendsTB.search != undefined && trendsTB.search.length > 0)
        {
            url += "&search="+encodeURIComponent(trendsTB.search);
        }
        
        if(trendsTB.store != undefined && trendsTB.store.length > 0)
        {
            url += "&store="+trendsTB.store;
            if(trendsTB.getItemState(id+"InStock"))
                url += "&instock";
            if(trendsTB.getItemState(id+"OnSale"))
                url += "&onsale";
        }
    }
    trends.clearAll();
    trends.load(url, function(res){
        if(trendsTB == null)
        {
            
            trendsTB = m_layout.cells(cn_trends).attachToolbar();
            m_layout.cells(cn_trends).hideToolbar();
            trendsTB.setIconset("awesome");
            
            //Store
            AddTrendsStoreSelect();
            trendsTB.attachEvent("onClick", function(tb_id){
                if(this.getListOptionSelected(id+"Store") == tb_id)
                {
                    if(tb_id == "any_store")
                    {
                        trendsTB.store = "";
                        trendsTB.hideItem(id+"InStock");
                        trendsTB.hideItem(id+"SepInStock");
                        trendsTB.hideItem(id+"OnSale");
                        trendsTB.hideItem(id+"SepOnSale");
                    }
                    else
                    {
                        trendsTB.store = tb_id;
                        trendsTB.showItem(id+"InStock");
                        trendsTB.showItem(id+"SepInStock");
                        trendsTB.showItem(id+"OnSale");
                        trendsTB.showItem(id+"SepOnSale");
                    }
                    trendsTB.setItemText(id+"Store", trendsTB.getListOptionText(id+"Store",tb_id));
                    trendsTB.forEachListOption(id+"Store", function(optionId){
                        trendsTB.setListOptionImage(id+"Store", optionId, radio_icons[optionId==tb_id?1:0]);
                    });
                    loadTrends(); // reload trends with new store
                }
            });
            trendsTB.addSeparator(id+"SepStore", null);
            
            trendsTB.addButtonTwoState(id+"InStock", null, "In Stock", chk_icons[0], chk_icons[0]);
            trendsTB.setItemState(id+"InStock", false, false);
            trendsTB.attachEvent("onStateChange", function(tb_id, state){
                if(tb_id == id+"InStock" || tb_id == id+"OnSale")
                {
                    trendsTB.setItemImage(tb_id, chk_icons[state?1:0]);
                    loadTrends();
                }
            });
            trendsTB.addSeparator(id+"SepInStock", null);
            
            trendsTB.addButtonTwoState(id+"OnSale", null, "On Sale", chk_icons[0], chk_icons[0]);
            trendsTB.setItemState(id+"OnSale", false, false);
            trendsTB.addSeparator(id+"SepOnSale", null);
            
            trendsTB.hideItem(id+"InStock");
            trendsTB.hideItem(id+"SepInStock");
            trendsTB.hideItem(id+"OnSale");
            trendsTB.hideItem(id+"SepOnSale");

            //Search input
            trendsTB.addInput(id+"Search", null, "", 70);
            trendsTB.setItemToolTip(id+"Search", "Search");
            
            let inp = trendsTB.getInput(id+"Search");
            inp.addEventListener('input', function(e) {
                trendsTB.search = e.target.value;
                if(inp.tm)
                    clearTimeout(inp.tm);
                inp.tm = setTimeout(function(){ loadTrends(); }, 500);
            });
        }
    });
}

function AddTrendsStoreSelect()
{
    let trendsTB = m_layout?m_layout.cells(cn_trends).getAttachedToolbar():null;
    if(trendsTB)
    {
        let id = "trends";
        let selected = trendsTB.getListOptionSelected(id+"Store");
        if(selected == undefined)
            selected = "any_store";
            
        let isSelected = false;
        let opts = stores.slice(0, 10).map(obj => {
            if(obj.id == selected)
                isSelected = true;
           return [obj.id, 'obj', obj.displayName, radio_icons[0]];
        });
        opts.unshift(["any_store",'obj',"Any store",radio_icons[0]]);
        trendsTB.removeItem(id+"Store");
        trendsTB.addButtonSelect(id+"Store", 0, "", opts, null, null, 'true', false);
        if(!isSelected && selected != "any_store") //reload with any_store
        {
            selected = "any_store";
            trendsTB.store = "";
            trendsTB.hideItem(id+"InStock");
            trendsTB.hideItem(id+"SepInStock");
            trendsTB.hideItem(id+"OnSale");
            trendsTB.hideItem(id+"SepOnSale");
            loadTrends();
        }
        trendsTB.setListOptionSelected(id+"Store",selected);
        trendsTB.setListOptionImage(id+"Store",selected, radio_icons[1]);
        trendsTB.setItemText(id+"Store", trendsTB.getListOptionText(id+"Store",selected));
    }
}
window.addEventListener('stores_changed', function (e) {
    var new_stores = stores.slice(0, 10).map(obj => {
       let rObj = {};
       rObj.text = obj.displayName;
       rObj.value = obj.id;
       return rObj;
    });
    new_stores[0].selected = true;
        paramForm.reloadOptions("deals_store", new_stores);
    
    AddTrendsStoreSelect();
});

if(paramForm.getItemValue('loc').length > 0 && localStorage.getItem('loc') != paramForm.getItemValue('loc'))
{
    localStorage.setItem('loc', paramForm.getItemValue('loc'));
    setLongLat(0,0);
}
else
{
    if(localStorage.getItem('loc'))
        paramForm.setItemValue('loc', localStorage.getItem('loc'));
    
    if(localStorage.getItem('lat') && localStorage.getItem('lng'))
    {
        setLongLat(parseFloat(localStorage.getItem('lng')), parseFloat(localStorage.getItem('lat')));
    }
}

function sort_stock(a,b,order){
    var n=0;
    $($.parseHTML(a)).find('span.fa-custom-counter').each(function(){n+=parseInt(this.innerText) || 0;});
    var m=0;
    $($.parseHTML(b)).find('span.fa-custom-counter').each(function(){m+=parseInt(this.innerText) || 0;});
    if(order=="asc")
        return n>m?1:-1;
    else
        return n<m?1:-1;
}

sysgrid.setHeader("#,Name,Address,Distance,Price,Status,Stock,Extra");
sysgrid.setInitWidths("40,160,200,60,50,80,50,60");
sysgrid.enableAutoWidth(true);
sysgrid.setColAlign("left,left,left,left,left,left,left,left");
sysgrid.setColTypes("ro,ro,ro,ro,price,ro,ro,ro");
sysgrid.setColSorting("int,str,str,int,int,str,int,str");
sysgrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,#numeric_filter,#select_filter,#numeric_filter,&nbsp;");
sysgrid.setCustomSorting(sort_stock,7);
sysgrid.enableSmartRendering(false);
sysgrid.enableBlockSelection();
sysgrid.enableStableSorting(true);
sysgrid.setColumnHidden(7, true);
sysgrid.setColumnHidden(6, true);
if(localStorage["wm-no-count"] == undefined || localStorage["wm-no-count"] != "1")
{
    sysgrid.attachFooter("<span id=\"message-note-wm-no-count\">Note: Walmart stopped reporting in-store stock numbers\\, only status is available.<span class=\"dismiss-message-btn\"><i class=\"fas fa-window-close\"></i></span></span>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan", ["background-color:white; color:orange; font-style: normal; border-style:none"]);
}
//sysgrid.attachFooter("Warning: Walmart inventory numbers can be behind up to 3 hours or innacurate. Do NOT use StockTrack to argue with Walmart employees.,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan", ["background-color:white; color:red; font-style: normal; border-style:none"]);
sysgrid.init();


$('.dismiss-message-btn').click(function(){
    sysgrid.detachFooter(0);
    localStorage["wm-no-count"] = "1";
    resizeGridCell();
});

sysgrid.attachEvent("onBeforeSelect", function(new_row,old_row,new_col_index){
	if((new_col_index >= 4 && new_col_index <= 6) && sysgrid.upc && !isStoreLoading(new_row))
	{
		setStoreLoading(new_row);
		updateStore(sysgrid.upc, [new_row], 0, true);
	}
    return false;
});
sysgrid.attachEvent("onAfterSorting", function(index,type,direction){
	gridSort = {
		i: index,
		t: type,
		d: direction
	};
	this.setSortImgState(true,index,direction);
});

function resizeGridCell()
{
    var shown = 0;
	for(var i = 0; i < sysgrid.getRowsNum(); i++)
	{
        if(sysgrid.getRowById(sysgrid.getRowId(i)).style.display!='none')
			shown++;	//still loading
	}
    
	m_layout.cells(cn_stores).setHeight(shown*30+120+(localStorage["wm-no-count"] == "1"?0:20));
    return true;
}
sysgrid.attachEvent("onRowAdded",resizeGridCell);
sysgrid.attachEvent("onRowHide",resizeGridCell);
sysgrid.attachEvent("onClearAll",resizeGridCell);
sysgrid.attachEvent("onXLE",resizeGridCell);

var prev_hist_id = null;
sysgrid.attachEvent("onMouseOver", function (id, ind){
    if(ind == 4 || ind == 6)
    {
        histPop.showit = true;
        if(!prev_hist_id || prev_hist_id != id)
        {
            prev_hist_id = id;
            var cell_obj = sysgrid.cellById(id,ind);
            if(sysgrid.rowsAr[id].History && sysgrid.rowsAr[id].History[ind])
            {
                showChart(sysgrid.rowsAr[id].History[ind], cell_obj.cell, "Walmart Store #"+id+" Price and Stock History");
            }
            else if(sysgrid.upc)
            {
                $.get("price_hist.php?"+"upc="+sysgrid.upc+"&store="+id, function(data){ //(ind == 4?"upc=":"gtin=")
                    if(!sysgrid.rowsAr[id].History)
                        sysgrid.rowsAr[id].History = [];
                    sysgrid.rowsAr[id].History[ind] = data;
                    showChart(data, cell_obj.cell, "Walmart Store #"+id+" Price and Stock History");
                }, "json");
            }
        }
    }
    else
    {
        setTimeout(function(){
            if(!histPop.showit)
            {
                histPop.hide();
                prev_hist_id = null;
            }
        }, 500);
        histPop.showit = false;
    }
    return true;
});

dhtmlxEvent(sysgrid.obj,("mouseout"),function(e){
    setTimeout(function(){
        if(!histPop.showit)
        {
            histPop.hide();
            prev_hist_id = null;
        }
    }, 500);
    histPop.showit = false;
});

paramForm.attachEvent("onChange", function(name, value, is_checked){
    if(name == "hide")
    {
        sysgrid.forEachRow(function(storeId){
            if(sysgrid.cellById(storeId, 5).getValue() == "NotSold")
                sysgrid.setRowHidden(storeId, is_checked);
        });
    }
	else if(name == "type")
	{
		onTypeChange();
	}
});

if(searchParams.has("search"))
{
	paramForm.setItemValue("type", "search");
	paramForm.setItemValue("q", searchParams.get("search"));
	onTypeChange();
}
else if(searchParams.has("sku"))
{
	paramForm.setItemValue("type", "sku");
	paramForm.setItemValue("q", searchParams.get("sku"));
	onTypeChange();
}

var max_stores = Math.min(stores.length, 1000);
paramForm.attachEvent("onInputChange", function(name, value, form){
    if(name == "loc")
    {
        setLongLat(0,0);
        localStorage.setItem('loc', value);
    }
	else if(name == "num" || (name == "q"&&paramForm.getItemValue("type")=="upc"))
	{
		//only numbers are allowed
        var n = parseInt(value);
        if(name == "num" && (!n || n > max_stores || n < 0) )
        {
            if(n > max_stores)
            {
                if(max_stores < stores.length)
                {
                    dhtmlx.message({
                        type: "error",
                        text: "The maximum number of stores that can be checked is currently set to " + max_stores + " due to Walmart captcha restrictions"
                    });
                }
                n = max_stores;
            }
            else
                n = "";
        }
		paramForm.setItemValue(name, n||"");
	}
});
    
    layoutObj.setSizes();
    
    
    trends = m_layout.cells(cn_trends).attachDataView({
        type:{
            template:"<span style='display:#ad_display#'>#AdIns#</span><table class='prev' style='display:#item_display#'><tr><td class='prev_td'>#Img#</td><td><a href='#Href#' target='_blank'>#Name#</a><br/>UPC: #Upc##StorePrice##StoresOnSale##StoresInStock##OnlinePrice##OnlineStock#</td></td></tr></table>",
            template_loading:loading_div
        },
        autowidth: Math.floor(layoutObj.cells(cn_grid).getWidth()/302),
        datatype: "json"
    });
	m_layout.cells(cn_trends).setText("Walmart Trending Products  <button id='trends_opt'><i class='fas fa-cogs'></i><i class='fas fa-caret-down caret'></i></button>");
    trends.attachEvent("onItemClick", function (id){
        CheckIsLoading(trends.get(id).Upc).then(function(result) {
            if(!result)
                return;
            //get selected
            if(trends.get(id).Upc != undefined && trends.get(id).Upc != "")
            {
                trends.select(id);
                paramForm.setItemValue("type", "upc");
                paramForm.setItemValue("q", trends.get(id).Upc);
                onTypeChange();
                
                prodList.clearAll();
                getProductPreview();
                loadStockInfo("trends");
                call_prnt(layoutObj.cells(cn_grid));
            }
        });
        return true;
    });
    m_layout.cells(cn_trends).setMinHeight(140);
    
    $('#trends_opt').click(function(){
        $(this).toggleClass("clicked");
        $(this).children().remove(".caret");
        if($(this).hasClass("clicked"))
        {
            m_layout.cells(cn_trends).showToolbar();
            $(this).append("<i class='fas fa-caret-up caret'></i>");
        }
        else
        {
            m_layout.cells(cn_trends).hideToolbar();
            $(this).append("<i class='fas fa-caret-down caret'></i>");
        }
    });
    
    loadTrends();
    m_layout.attachEvent("onDblClick", function(name){
        if(name==cn_trends)
        {
            loadTrends();
        }
    });
    
    trends.attachEvent("onItemRender", function(obj){
        if(obj.checked)
        {
            if(obj.Ads && $("#ads_trends_"+obj.id).children().length == 0)
            {
                if(Math.floor((Date.now()-obj.lastLoad)) > 2000)
                {
                    obj.lastLoad = new Date();
                    setTimeout(function(){
                        try {
                          (adsbygoogle = window.adsbygoogle || []).push({});
                        }
                        catch(err) {
                          
                        }
                    }, 100);
                }
            }
            return;
        }
        obj.checked = true;
        obj.Img = "";
        
        obj.ad_display = obj.Ads?"block":"none";
        obj.item_display = obj.Ads?"none":"block";
        if(obj.Ads)
        {
            $.ajax({type: "GET",
                    url: "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js",
                    dataType: "script",
                    cache: true
            });
            obj.AdIns = '<ins id="ads_trends_'+obj.id+'" class="adsbygoogle"\
                 style="display:block"\
                 data-ad-format="fluid"\
                 data-ad-layout-key="-ee+7u-2p-dp+ym"\
                 data-ad-client="ca-pub-3041049844308359"\
                 data-ad-slot="7734249120"></ins>';
            obj.lastLoad = new Date();
            setTimeout(function(){(adsbygoogle = window.adsbygoogle || []).push({});}, 100);
        }
        else
        {
            if(obj.Image)
            {
                obj.Img = "<img src='"+obj.Image+"'/>";
            }
            
            if(obj.OnlineStock && obj.OnlineStock.length > 0)
            {
                obj.OnlineStock = "<br/>Online stock: "+obj.OnlineStock;
                if(obj.Status)
                {
                    var stat_class = "oos";
                    if(obj.Status == "Available" || obj.Status == "AcceptingPreorder")
                        stat_class = "instock";
                    obj.OnlineStock += "(<span class='"+stat_class+"'>"+obj.Status+"</span>)";
                }
            }
            else
                obj.OnlineStock = "";
            
            if(obj.OnlinePrice && obj.OnlinePrice.length > 0)
            {
                if(obj.OnlineWasPrice && obj.OnlineWasPrice.length > 0)
                {
                    obj.OnlinePrice = "<strike>"+obj.OnlineWasPrice+"</strike> <font color='red'>$"+obj.OnlinePrice+"</font>";
                }
                obj.OnlinePrice = "<br/>Online price: $" + obj.OnlinePrice;
            }
            else
                obj.OnlinePrice = "";
            
            if(obj.StorePrice && obj.StorePrice.length > 0)
            {
                if(obj.StoreMaxPrice && obj.StoreMaxPrice.length > 0)
                {
                    obj.StorePrice += "-" + obj.StoreMaxPrice;
                }
                if(obj.StoreWasPrice && obj.StoreWasPrice.length > 0)
                {
                    obj.StorePrice = "<strike>" + obj.StoreWasPrice + "</strike> <font color='red'>$"+obj.StorePrice+"</font>"; 
                }
                obj.StorePrice = "<br/>Store price: $" + obj.StorePrice;
            }
            else
                obj.StorePrice = "";
            
            if(obj.StoresOnSale && obj.StoresOnSale.length > 0 && obj.StoresTotal)
            {
                var storesCount = parseInt(obj.StoresOnSale);
                var perc = parseInt(obj.StoresTotal) > 0?Math.round(storesCount/parseInt(obj.StoresTotal)*100, 1):0;
                obj.StoresOnSale = "<br/>On sale in <span class='instock'>"+obj.StoresOnSale+"</span>("+perc+"%) store"+(storesCount > 1?"s":"");
            }
            else
                obj.StoresOnSale = "";
            
            if(obj.StoresInStock && obj.StoresInStock.length > 0 && obj.StoresTotal)
            {
                var storesCount = parseInt(obj.StoresInStock);
                var perc = parseInt(obj.StoresTotal) > 0?Math.round(storesCount/parseInt(obj.StoresTotal)*100, 1):0;
                obj.StoresInStock = "<br/>In stock in <span class='instock'>"+obj.StoresInStock+"</span>("+perc+"%) store"+(storesCount > 1?"s":"");
            }
            else
                obj.StoresInStock = "";
        }
    });
    
    setTimeout(function(){attachAdsToTable(layoutObj.cells(cn_grid), paramForm.getItemValue("q").length > 0?undefined:layoutObj.cells(cn_list));}, 500);
    
    m_layout.cells(cn_a).hideHeader();
    m_layout.cells(cn_a).setCollapsedText("Ads");
    m_layout.cells(cn_a).collapse();
setupAutocomplete();

if(paramForm.getItemValue("q").length > 0)
    refresh_grid();

}

$(document).keydown(function(e){
    if(e.keyCode==67&&e.ctrlKey){
		if(window.getSelection().toString())	//check if something is selected
			return;
		
        if (sysgrid._selectionArea)
		{
            sysgrid.setCSVDelimiter("\t");
            sysgrid.copyBlockToClipboard();
		}
		else if(prodList.getSelected() && prodList.get(prodList.getSelected()).Upc != "?")
		{
			copyToClipboard(window.location.protocol + "//"+window.location.hostname + "/?s=wm&upc="+prodList.get(prodList.getSelected()).Upc);
		}
	}
});

</script>
</head>
<body onload="doOnLoad();">

    <div id="layoutObj"></div>
<?//<script src="link_utils_skm.js" type="text/javascript"></script>?>

</body>
</html>
