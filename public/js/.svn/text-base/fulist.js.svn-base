// script for view/pinnacle/report/fulist.phtml

var fulist = new Array();
var fuorder = new Array();

function fuAppend( fac,url,st,city,stat,acc,dates,le,renew,inv,stdate,amo,amostr ) {
    var row = [url,fac,city,stat,acc,dates,le,renew,inv,stdate,amostr,amo,st];
    return fulist.push( row );
}

function fuAsString( i ) {
    var a = fulist[i];
    var s = "<td><a href=\""+a[0]+"\">"+a[1]+"</a></td><td>"+a[2]+", "+a[12]+"</td><td>"+a[3]+"</td><td>"+a[4]+"</td><td>"+a[5]+"</td><td>"+a[6]+"</td><td>"+a[7]+"</td><td>"+a[8]+"</td><td>"+a[9]+"</td><td>"+a[10]+"</td>";
    return s;
}

function fuDump() {
    var s = "";
    for(var i=0; i<fulist.length; i++) {
        s += "<tr><td>"+ (i+1) + "</td>" + fuAsString(i) + "</tr>";
    }
    s += '       <tr><td colspan="11"> </td></tr>';
    return s;
}

function fuSort0(id2) { // nothing
    //$(id2).text(fuorder[0]);
    return;
}

function fuMakeSort1(ix, dx) { // arg: 0/1 DESC
    var d1 = dx? 1: -1;
    var d2 = dx? -1: 1;
    return function ( a, b ) { return ( a[ix] < b[ix]?d1:(a[ix] > b[ix]?d2:0) ); };
}

function fuMakeSort2(ix,iy, dx,dy) { // d args: 0/1 DESC
    var d1 = dx? 1: -1;
    var d2 = dx? -1: 1;
    var d3 = dy? 1: -1;
    var d4 = dy? -1: 1;
    return function ( a, b ) { return a[ix] < b[ix]?d1:(a[ix] > b[ix]?d2:( a[iy] < b[iy]?d3:(a[iy] > b[iy]?d4:0) )); };
}

function fuMakeSort3(ix,iy,iz, dx,dy,dz) {
    var d1 = dx? 1: -1;
    var d2 = dx? -1: 1;
    var d3 = dy? 1: -1;
    var d4 = dy? -1: 1;
    var d5 = dz? 1: -1;
    var d6 = dz? -1: 1;
    return function ( a, b ) { return a[ix] < b[ix]?d1:(a[ix] > b[ix]?d2:( a[iy] < b[iy]?d3:(a[iy] > b[iy]?d4:( a[iz] < b[iz]?d5:(a[iz] > b[iz]?d6:0) )) )); };
}

function fuSort1(id2) { // facility
    fulist.sort(function ( a, b ) { return a[1] < b[1]?-1:(a[1] > b[1]?1:0); });
    fulist.sort(fuMakeSort1(1, 0));
    $(id2).text(fuorder[1]);
}
    
function fuSort2(id2) { // state, then city, facility
    fulist.sort(fuMakeSort3(12,2,1, 0,0,0));
    $(id2).text(fuorder[2]);
}

function fuSort3(id2) { // status, facility
    fulist.sort(function ( a, b ) { return a[3] < b[3]?-1:(a[3] > b[3]?1:( a[1] < b[1]?-1:(a[1] > b[1]?1:0) )); });
    fulist.sort(fuMakeSort2(3,1, 0,0));
    $(id2).text(fuorder[3]);
}

function fuSort4(id2) { // account, facility
    fulist.sort(fuMakeSort2(4,1, 0,0));
    $(id2).text(fuorder[4]);
}

function fuSort5(id2) { // date sig DESC, facility
    fulist.sort(fuMakeSort2(5,1, 1,0));
    $(id2).text(fuorder[5]);
}

function fuSort6(id2) { // length, facility
    fulist.sort(fuMakeSort2(6,1, 0,0));
    $(id2).text(fuorder[6]);
}

function fuSort7(id2) { // renew date DESC, facility
    fulist.sort(fuMakeSort2(7,1, 1,0));
    $(id2).text(fuorder[7]);
}

function fuSort8(id2) { // inv date DESC, facility
    fulist.sort(fuMakeSort2(8,1, 1,0));
    $(id2).text(fuorder[0]); // !
}

function fuSort9(id2) { // st date DESC, facility
    fulist.sort(fuMakeSort2(9,1, 1,0));
    $(id2).text(fuorder[8]); // !
}

function fuSort10(id2) { // amount DESC, facility
    fulist.sort(function ( a, b ) { var x = new Number(b[11]) - new Number(a[11]); return x!=0?x:( a[1] < b[1]?-1:(a[1] > b[1]?1:0) ); });
    $(id2).text(fuorder[9]); // !
}

function fuBody( id, id2, sortfun ) {
    sortfun(id2);
    $(id).html(fuDump());
}

function fuInit( id, id2, sortord, url ) {
    $(id2).ajaxError(function(e,xhr,opt) {
        if( xhr.status == 403 ) {
            alert("Your session was closed (time out): please log in!")
        }
        else alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
    });
    $.getJSON(url, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            fuorder = result.order;
            $(id2).text(fuorder[sortord]);
            $.each(result.report, function(k,v) {
                fuAppend( v.ctct_name, v.url, v.ctct_st_code, v.ctct_addr_c, v.fu_status, v.fu_acct, v.fu_date, v.fu_length, v.fu_renewal, v.fu_invoice, v.fu_start, v.fu_amount, v.amostr );
            });
            $(id).html(fuDump());
        }
        else {
            $(id2).text( "ERROR! " ); $(id2).addClass("text-error");
            if( result ) $(id2).append( result.error );
        }
    });
}


