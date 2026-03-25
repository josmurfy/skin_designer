function dsplad_fnct()
{
    if(!adsBlocked && $("#ads_cont").length)
    {
        if(localStorage["ab"] == "false")
        {
            $("#ads_cont").html('<span\>');
            return;
        }
        
        $("#ads_cont").html('<span align="center" data-freestar-ad="__320x100" id="stocktrack_leaderboard_bottom"></span>');//Footer 320x100
        window.freestar = window.freestar || {queue: []};
        freestar.queue.push(function(){ freestar.newAdSlots([{ placementName: "stocktrack_leaderboard_bottom", slotId: "stocktrack_leaderboard_bottom" }])});
        
    }
}

function attachAdsToTable(table_cell, search_cell)
{
    lastCall = Date.now();
    
    table_cell.detachStatusBar();
    if(search_cell != undefined)
        search_cell.detachStatusBar();
    
    if(adsBlocked || localStorage["aa"] == "false")
    {
        return;
    }
    let ad_height = 50;
    let nNumAds = Math.min(3, Math.floor(table_cell.getWidth()/300));
    if(nNumAds > 0)
    {
        let ad_width = Math.floor(table_cell.getWidth()/nNumAds);
        
        let table_ad_ids = [];
        let ad_tags = "";
        for(let i = 0; i < nNumAds; i++)
        {
            let search_id = "stocktrack_search_"+(i+1);
            table_ad_ids.push({ placementName: search_id, slotId: search_id });
            ad_tags += '<div data-freestar-ad="__320x50" class="inline_ad" style="width:'+ad_width+'px;height:'+ad_height+'px" id="'+search_id+'"></div>';
        }
            
        table_cell.attachStatusBar({
                    height: ad_height,
                    text: '<div id="google_ads" style="width:100%;height:100%">'+ad_tags+'</div>'
                });
 
        window.freestar = window.freestar || {queue: []};
        freestar.queue.push(function(){ freestar.newAdSlots(table_ad_ids)});
    }
    
    refr_vid(table_cell);
    
    if(search_cell != undefined)
    {
        var obj = search_cell.getAttachedObject();
        if (obj != undefined && obj instanceof dhtmlXList && obj.dataCount() == 0)
        {
            let placement = "stocktrack_rail_left";
            let ad_size = "__300x600";
            if(search_cell.getHeight() < search_cell.getWidth())
            {
                placement = "stocktrack_leaderboard_top";
                ad_size = "__320x250";
            }
            search_cell.attachStatusBar({
                height: search_cell.getHeight(),
                text: '<div style="line-height:21px;padding:5px 5px;"><div align="center" data-freestar-ad="'+ad_size+'" id="stocktrack_rail_left"\></div>'
            });
            window.freestar = window.freestar || {queue: []};
            freestar.queue.push(function(){ freestar.newAdSlots([{ placementName: placement, slotId: "stocktrack_rail_left" }])});
        }
    }
    
	if(ttn != null)
		clearTimeout(ttn);
	ttn = setTimeout(function(){ call_prnt(table_cell, search_cell); }, 30000);
}

var ttn = null;
var lastCall = null;
var lastVid = null;
var refr = false;
var mT = 59;
function refr_all(c, sc)
{
    lastCall = Date.now();
    if(typeof(parent.onCheckButtonClick) === typeof(Function))
    {
        parent.onCheckButtonClick(c);
    }
    if(c != undefined)
    {
        attachAdsToTable(c, sc);
    }
}

function call_prnt(c, sc)
{
    if(localStorage.getItem("at") && !isNaN(localStorage.getItem("at")))
        mT = parseInt(localStorage.getItem("at"));
    
    var lastIa = null;
    if(localStorage.getItem("ali"))
        lastIa = new Date(JSON.parse(localStorage.getItem("ali")));
    
	if(!document.hidden && refr && (lastIa == null || Math.floor((Date.now()-lastIa)/1000) > 59))
    {
        localStorage["ali"] = JSON.stringify(Date.now());
        $.get("/ia.php?r="+Date.now()).then(function(data) {
            localStorage["aa"] = data.a != undefined?data.a:true;
            localStorage["ab"] = data.ab != undefined?data.ab:true;
            if(data.t != undefined)
                mT = data.t;
            localStorage["at"] = mT;
            if(lastCall == null || (refr && Math.floor((Date.now()-lastCall)/1000) > mT))
                refr_all(c, sc);
        });
    }
    else if(!document.hidden && (lastCall == null || (refr && Math.floor((Date.now()-lastCall)/1000) > mT)))
	{
        refr_all(c, sc);
	}
    else if(!document.hidden && (lastVid == null || (refr && Math.floor((Date.now()-lastVid)/1000) > mT)))
	{
        refr_vid(c);
	}
	else
	{
		if(ttn != null)
			clearTimeout(ttn);
		ttn = setTimeout(function(){ call_prnt(c, sc); }, 30*1000);
	}
}

function refr_vid(cell)
{
    if(adsBlocked || localStorage["aa"] == "false")
    {
        return;
    }
    var obj = cell.getAttachedObject();
    if (obj != undefined && typeof(window.dhtmlXLayoutObject) == "function" && obj instanceof dhtmlXLayoutObject &&
        obj.cells("b") != undefined && obj.cells("b").getHeight() > 230 && obj.cells("b").getCollapsedText() == "Ads")
    {
        let grid_width = 0;
        for(let i = 0; i < obj.cells("a").getAttachedObject().getColumnsNum(); i++)
            grid_width += obj.cells("a").getAttachedObject().getColWidth(i);
        
        obj.cells("b").collapse();
        let adWidth = obj.cells("a").getWidth()-grid_width-50;
        if(adWidth > 240)
        {
            lastVid = Date.now();
            obj.cells("b").expand();
            obj.cells("b").setWidth(adWidth);
            attachResponsive(obj.cells("b"));
        }
    }
}

function attachResponsive(c)
{
    var ad = '<div class="ads-container"><div align="center" data-freestar-ad="__320x250" id="stocktrack_leaderboard_top"></div></div>';
    c.attachHTMLString(ad);
    window.freestar = window.freestar || {queue: []};
    freestar.queue.push(function(){ freestar.newAdSlots([{ placementName: "stocktrack_leaderboard_top", slotId: "stocktrack_leaderboard_top" }])});
}

function onCheckButtonClick(c)
{
    dsplad_fnct();
}

var adsBlocked = localStorage.getItem('ads_blocked') === 'true';
(function(){
    function adBlockDetected() {
        adsBlocked = true;
        localStorage['ads_blocked'] = true;
    }
    function adBlockNotDetected() {
        adsBlocked = false;
        localStorage['ads_blocked'] = false;
    }
    
    if(typeof window.adblockDetector === 'undefined') {
        adBlockDetected();
    } else {
        window.adblockDetector.init(
            {
                found: function(){
                    adBlockDetected();
                },
                notFound: function(){
                    adBlockNotDetected();
                }
            }
        );
    }
}());