// script for view/pinnacle/physician/index.phtml

var fulist = new Array();
var fuid = "";
var fupages = "";
var furl = "";
var fustate = "";
var futotal = 0;
var fucp = 0;
var murl = "";

function phyAppend( url,id,name,title,city,state,status,spec ) {
    var row = [url,id,name,spec,city,state,status,title];
    return fulist.push( row );
}

function phyAsString( i ) {
    var a = fulist[i];
    var s = "<td><a href=\""+a[0]+"\"> "+a[1]+" </a></td><td>"+a[2]+", "+a[7]+"</td><td>"+a[3]+"</td><td>"+a[4]+", "+a[5]+"</td><td>"+a[6]+"</td>";
    return s;
}

function fuDump() {
    var s = "";
    for(var i=0; i<fulist.length; i++) {
        s += "<tr>" + phyAsString(i) + "</tr>";
    }
    s += '       <tr><td colspan="5"> </td></tr>';
    return s;
}

function fuBody() {
    $(fuid).html(fuDump());
}

function fuRestoreState() {
    try {
        var s = sessionStorage.getItem(fustate);
        if( !s ) return;
        var fuy = JSON.parse(s);
        fucp = fuy.cp;
        futotal = fuy.tot;
        fulist = fuy.list;
        var y = fuy.y;
        var form = document.forms[0];
        for( var key in y ) {
            var oid = form[key];
            if( "id" in oid ) $(oid).val(y[key]);
            else { // checkbox or radio;
                   // only checkboxen with checked value "1" are supported for now
                var obj = oid[1];
                if( obj.tagName.toLowerCase() == "input" && obj.type.toLowerCase() == "checkbox" ) {
                    obj.checked = y[key] == "1";
                    $(obj).change();
                }
            }
        }
        fuBody(); fuPagesSetup();
        $("#my-pagesize").change();
    } catch(e) {
        // sessionStorage is not supported: too bad
    }
}

function fuStoreState(obj) {
    try {
        var fuy;
        var s = sessionStorage.getItem(fustate);
        if( s ) {
            fuy = JSON.parse(s);
            fuy.cp = fucp; fuy.tot = futotal;
            fuy.list = fulist;
        }
        else fuy = {cp: fucp, tot: futotal, list: fulist};
        if( obj ) fuy.y = obj;
        sessionStorage.setItem(fustate, JSON.stringify(fuy));
    } catch(e) {
        // sessionStorage is not supported: too bad
    }
}

var fuidarr = [];

function fuPagesSetup() {
    if( futotal ) {
        var s = '<span class="pagination"><ul><li id="li-phy-prev"><a href="#" id="phy-prev" title="Previous Page"><img src="/img/images/ARW06LT.png" alt="Prev Page"> </a></li> ';
        var sn = '<li id="li-phy-next"><a href="#" id="phy-next" title="Next Page"><img src="/img/images/ARW06RT.png" alt="Next Page"></a></li></span>';
        var s1 = '<li id="li-phy-1st"><a href="#" id="phy-1st">1</a></li> ';
        var st = '<li id="li-phy-last"><a href="#" id="phy-last">'+futotal+"</a></li>";
        var curr = "phy-1st";
        var shel = '<li><span>&hellip;</span></li> ';
        $.each(fuidarr, function(k,v) { $(v.id).off("click") });
        fuidarr = [];
        if( fucp < 1 || fucp > futotal ) fucp = 1;
        s += s1; 
        if( futotal == 1 ) st = "";
        else if( futotal > 2 ) {
            // [...] n-2 ... n ... n+2 [...]
            var minp = (fucp - 2 < 2)? 2: fucp-2;
            if( minp > 2 ) s += shel;
            var maxp = (minp + 5 > futotal-1)? futotal-1: minp+5;
            for(var i=minp; i<=maxp; i++) {
                var idd = "phy-"+i+"th";
                if( i == fucp ) { curr = idd; }
                else fuidarr.push({id: "#"+idd, ix: i});
                s += '<li id="li-phy-'+i+'th"><a href="#" id="phy-'+i+'th">'+i+"</a></li>";
            }
            if( maxp < futotal-1 ) s += shel;
            if( fucp == futotal ) curr = "phy-last";
        }
        else if( fucp == futotal ) curr = "phy-last";

        s += st + sn;
        $(fupages).html(s);
        $("#phy-1st").off("click").click(function(){ fuFetch(1) });
        if( st != "" ) $("#phy-last").off("click").click(function(){ fuFetch(futotal) });
        $("#phy-prev").off("click").click(function(){ fuFetch(fucp - 1) });
        $("#phy-next").off("click").click(function(){ fuFetch(fucp + 1) });
        $.each(fuidarr, function(k,v) {
            var p = v.ix;
            $(v.id).click(function(){ fuFetch(p) });
        });
        $("#li-"+curr).addClass("active").css("font-weight","bold");
    }
    else {
        $(fupages).text('');
        $("#searchstatus").html( '<span class="label label-warning">Not Found!</span>' );
    }
}

function fuFetch(page) {
    if( page < 1 ) page = 1;
    if( page > futotal ) page = futotal;
    if( fucp == page ) return; // already got teh
    fulist = []; fucp = page;
    var url = furl + "/" + page;
    $.getJSON(url, function(result, status) {
        if( status == "success" && result.code == 200 ) {
//            $(fupages).text(JSON.stringify(result));
            $.each(result.report, function(k,v) {
                phyAppend(  v.url, v.ph_id, v.ctct_name, v.ctct_title, v.ctct_addr_c, v.ctct_st_code, v.st_name, v.ph_spec_main );
            });
            fuBody(); fuPagesSetup();
            fuStoreState(null);
        }
    });
}

function fuSubmit() {
    fucp = 0; futotal = 0; fulist = [];
    var x=$("form").serializeArray();
    var y = {};
    $.each(x, function(i, field){
      y[field.name] = field.value;
    });
    var url = furl + "/0";
    $.post(url, y, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            futotal = result.pages;
            if( futotal ) fuFetch(1);
            else fuBody();
            fuPagesSetup();
            fuStoreState(y);
            $("#my-savelist-div").show();
        }
    });
}

var fuTimeo = null;
var fuInstant = true;

function fuEvent() {
    if( !fuInstant ) return true;
    window.clearTimeout(fuTimeo);
    fuTimeo = window.setTimeout(fuSubmit,1200);
    return true;
}

function phyInit( id, id2, url, redir, url2 ) {
    fuid = id; fupages = id2; furl = url; fustate = "fustorePhysicians";
    murl = url2;
    $(fupages).ajaxError(function(e,xhr,opt) {
        if( xhr.status == 403 && confirm("Your session was closed (time out): please log in!") ) {
            window.location = redir;
        }
        else alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
    });
}

function mlMainAdd() {
    var y = { name: $("#my-new-name").val() };
    var url = murl + "add/0";
    if( y.name == '' ) alert('Please provide list name')
    else $.post(url, y, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            var lid = result.list_id;
            if( lid ) {
                var s = '<option value="'+lid+'"> '+result.name + ' </option>'
                $("#my-list-sel").append(s).val(lid);
                $("#my-new-name").val('');
            }
        }
    });
}

function mlMainSave() {
    var ml = $("#my-list-sel").val();
    if( !ml ) { alert('Please create or select list name'); return; }
    var y = { part: 'physician', list: ml };
    var url = murl + "save/0";
    $.post(url, y, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            var lid = result.list_id;
            if( lid ) {
                $("#searchstatus").html('<span class="label label-success">Saved!</span>');
            }
            else if( result.error == 'Search again' ) {
                $("#my-savelist-div").hide();
                alert('Please click Search button and try again!');
            }
        }
    });
}

$(document).ready(function(){
  $("#my-pagesize").change(function(){
        $("#pagespan").text($(this).val());
    });
  $("#submitbutton").click(fuSubmit);
  $("#searchstatus").ajaxStart(function(){
        $("#searchstatus").html('<span class="label label-info">Loading... Please wait</span>');
        $("#submitbutton").fubutton('loading');
  }).ajaxComplete(function(){
        $("#searchstatus").text('');
        $("#submitbutton").fubutton('reset');
  });
  $("#resetbutton").click(function(){
        sessionStorage.removeItem(fustate);
        $("#submitbutton").fubutton('reset');
        return true
  });
  $("#my-instant").change(function(){
        fuInstant = $(this).attr("checked");
        if( !fuInstant ) window.clearTimeout(fuTimeo);
  });
  fuRestoreState();
  $("input:not(.myml-class)").change(fuEvent);
  $("select:not(.myml-class)").change(fuEvent);
  $("input:not(.myml-class)").keyup(fuEvent);

  $("#my-list-add").click(mlMainAdd);
  $("#my-list-save").click(mlMainSave);
});


