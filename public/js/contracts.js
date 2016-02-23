// script for view/pinnacle/contract/index.phtml

var fulist = new Array();
var fuid = "";
var fupages = "";
var furl = "";
var fustate = "";
var futotal = 0;
var fucp = 0;

function ctrAppend( ctrurl,nom,fac,url,city,state,status,spec,rec,ctrdate ) {
    var row = [ctrurl,nom,ctrdate,spec,fac,url,city,state,status,rec];
    return fulist.push( row );
}

function ctrAsString( i ) {
    var a = fulist[i];
    var s = "<td><a href=\""+a[0]+"\">"+a[1]+"</a></td><td>"+a[2]+"</td><td>"+a[3]+"</td><td>"+a[4]+" <a class=\"onscr\" href=\""+a[5]+"\"><img src=\"/public/img/images/ARW01RT.png\"></a></td><td>"+a[6]+"</td><td>"+a[7]+"</td><td>"+a[8]+"</td><td>"+a[9]+"</td>";
    return s;
}

function fuDump() {
    var s = "";
    for(var i=0; i<fulist.length; i++) {
        s += "<tr>" + ctrAsString(i) + "</tr>";
    }
    s += '       <tr><td colspan="8"> </td></tr>';
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
        var s = '<span class="pagination"><ul><li id="li-ctr-prev"><a href="#" id="ctr-prev" title="Previous Page"><img src="/public/img/images/ARW06LT.png" alt="Prev Page"> </a></li> ';
        var sn = '<li id="li-ctr-next"><a href="#" id="ctr-next" title="Next Page"><img src="/public/img/images/ARW06RT.png" alt="Next Page"></a></li></span>';
        var s1 = '<li id="li-ctr-1st"><a href="#" id="ctr-1st">1</a></li> ';
        var st = '<li id="li-ctr-last"><a href="#" id="ctr-last">'+futotal+"</a></li>";
        var curr = "ctr-1st";
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
                var idd = "ctr-"+i+"th";
                if( i == fucp ) { curr = idd; }
                else fuidarr.push({id: "#"+idd, ix: i});
                s += '<li id="li-ctr-'+i+'th"><a href="#" id="ctr-'+i+'th">'+i+"</a></li>";
            }
            if( maxp < futotal-1 ) s += shel;
            if( fucp == futotal ) curr = "ctr-last";
        }
        else if( fucp == futotal ) curr = "ctr-last";

        s += st + sn;
        $(fupages).html(s);
        $("#ctr-1st").off("click").click(function(){ fuFetch(1) });
        if( st != "" ) $("#ctr-last").off("click").click(function(){ fuFetch(futotal) });
        $("#ctr-prev").off("click").click(function(){ fuFetch(fucp - 1) });
        $("#ctr-next").off("click").click(function(){ fuFetch(fucp + 1) });
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
            $.each(result.report, function(k,v) {
                ctrAppend(  v.ctrurl, v.ctr_no, v.ctr_cli, v.cliurl, v.ctr_city, v.ctr_state, v.st_name, v.ctr_spec, v.uname, v.ctr_date0 );
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
        }
    });
}

var fuTimeo = null;
var fuInstant = false;
var ctrMidl = false;

function fuEvent() {
    if( !fuInstant ) return true;
    window.clearTimeout(fuTimeo);
    fuTimeo = window.setTimeout(fuSubmit,1200);
    return true;
}

function ctrInit( id, id2, url, redir ) {
    fuid = id; fupages = id2; furl = url; fustate = "fustoreContracts";
    $(fupages).ajaxError(function(e,xhr,opt) {
        if( xhr.status == 403 && confirm("Your session was closed (time out): please log in!") ) {
            window.location = redir;
        }
        else alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
    });
}

$(document).ready(function(){
  $("#my-midl").change(function(){
        ctrMidl = $(this).attr("checked")? true: false;
        document.getElementById("my-spec").disabled = ctrMidl;
        document.getElementById("mid-cat").disabled = !ctrMidl;
    });
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
  $("input").change(fuEvent);
  $("select").change(fuEvent);
  $("input").keyup(fuEvent);
});


