// script for view/pinnacle/report/retained.phtml

var fulist = new Array();
var fuorder = new Array();
/// Note: set sort order strings!!!
var fucols = 10;

function retAppend(spec, url, ctrno, ruclass, runame, city, st, cliurl, client, prourl, marclass, marname, clirat) {
    var row = [0,spec, url, ctrno, ruclass, runame, city, st, cliurl, client, prourl, marclass, marname, clirat];
    return fulist.push( row );
}

function macAppend(spec, url, ctrno, ruclass, runame, city, st, cliurl, client, profile) {
    var row = [1,spec, url, ctrno, ruclass, runame, city, st, cliurl, client, profile];
    return fulist.push( row );
}
                   
function fuAsString( i ) {
    var a = fulist[i];
    var s = "<td>"+a[1]+"</td><td><a href=\""+a[2]+"\">"+a[3]+"</a></td>";
    if( a[0] ) { // mac
        s += "<td "+a[4]+">"+a[5]+"</td><td>"+a[6]+"</td><td>"+a[7]+"</td><td><a href=\""+a[8]+"\">"+a[9]+"</a></td><td>"+a[10]+"</td>";
    } else {
        s += "<td class=\"onscr\"><a href=\""+a[10]+"\">View</a></td><td "+a[4]+">"+a[5]+"</td><td>"+a[6]+"</td><td>"+a[7]+"</td><td><a href=\""+a[8]+"\">"+a[9]+"</a></td><td "+a[11]+">"+a[12]+"</td><td>"+a[13]+"</td>";
    }
    return s;
}

function fuDump() {
    var s = "";
    for(var i=0; i<fulist.length; i++) {
        s += "<tr><td>"+ (i+1) + "</td>" + fuAsString(i) + "</tr>";
    }
    s += '       <tr><td colspan="'+fucols+'"> </td></tr>';
    return s;
}

function fuSort0(id2) { // nothing
    //$(id2).text(fuorder[0]);
    return;
}

function fuMakeSort1(ix,dx) { // arg: 0/1 DESC
    var d1 = dx? 1: -1;
    var d2 = dx? -1: 1;
    return function ( a, b ) { return ( a[ix] < b[ix]?d1:(a[ix] > b[ix]?d2:0) ); };
}

function fuMakeSort2(ix,iy,dx,dy) { // d args: 0/1 DESC
    var d1 = dx? 1: -1;
    var d2 = dx? -1: 1;
    var d3 = dy? 1: -1;
    var d4 = dy? -1: 1;
    return function ( a, b ) { return a[ix] < b[ix]?d1:(a[ix] > b[ix]?d2:( a[iy] < b[iy]?d3:(a[iy] > b[iy]?d4:0) )); };
}

function fuMakeSort3(ix,iy,iz,dx,dy,dz) {
    var d1 = dx? 1: -1;
    var d2 = dx? -1: 1;
    var d3 = dy? 1: -1;
    var d4 = dy? -1: 1;
    var d5 = dz? 1: -1;
    var d6 = dz? -1: 1;
    return function ( a, b ) { return a[ix] < b[ix]?d1:(a[ix] > b[ix]?d2:( a[iy] < b[iy]?d3:(a[iy] > b[iy]?d4:( a[iz] < b[iz]?d5:(a[iz] > b[iz]?d6:0) )) )); };
}

function fuSort1(id2) { // spec, st, city
    fulist.sort(fuMakeSort3(1,7,6,0,0,0));
    $(id2).text(fuorder[0]);
}
    
function fuSort2(id2) { // ctr no
    fulist.sort(fuMakeSort1(3,0));
    $(id2).text(fuorder[1]);
}

function fuSort3(id2) { // rec, st, city
    fulist.sort(fuMakeSort3(5,7,6,0,0,0));
    $(id2).text(fuorder[2]);
}

function fuSort4(id2) { // city, st, client
    fulist.sort(fuMakeSort3(6,7,9,0,0,0));
    $(id2).text(fuorder[3]);
}

function fuSort5(id2) { // st, city, spec
    fulist.sort(fuMakeSort3(7,6,1,0,0,0));
    $(id2).text(fuorder[4]);
}

function fuSort6(id2) { // length, facility
    fulist.sort(fuMakeSort2(9,1,0,0));
    $(id2).text(fuorder[5]);
}

function fuSort7(id2) { // mar, st, city
    fulist.sort(fuMakeSort3(12,7,6,0,0,0));
    $(id2).text(fuorder[6]);
}

function fuSort8(id2) { // rating DESC, mar, city
    fulist.sort(fuMakeSort3(13,12,6,1,0,0));
    $(id2).text(fuorder[7]);
}

function fuBody( id, id2, sortfun ) {
    sortfun(id2);
    $(id).html(fuDump());
}

function retInit( id, id2, sortord, part, url ) {
    $(id2).ajaxError(function(e,xhr,opt) {
        if( xhr.status == 403 ) {
            alert("Your session was closed (time out): please log in!")
        }
        else alert("Error requesting " + opt.url + ": " + xhr.status + " " + xhr.statusText);
    });
    fucols = (part=='mac'? 8:10);
    $.getJSON(url, function(result, status) {
        if( status == "success" && result.code == 200 ) {
            fuorder = result.order;
            $(id2).text(fuorder[sortord]);
            $.each(result.report, function(k,v) {
                if( part == 'mac' ) {
                    macAppend(v.ctr_spec, v.url, v.ctr_no, v.ruclass, v.rec_uname, v.ctct_addr_c, v.ctct_st_code, v.cliurl, v.ctct_name, v.pro_practice);
                } else {
                    retAppend(v.ctr_spec, v.url, v.ctr_no, v.ruclass, v.rec_uname, v.ctct_addr_c, v.ctct_st_code, v.cliurl, v.ctct_name, v.prourl, v.marclass, v.mark_uname, v.cli_rating);
                }
            });
            $(id).html(fuDump());
        }
        else {
            $(id2).text( "ERROR! " ); $(id2).addClass("text-error");
            if( result ) $(id2).append( result.error );
        }
    });
}

